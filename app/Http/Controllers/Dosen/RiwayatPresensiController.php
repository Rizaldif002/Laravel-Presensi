<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\KelasPerkuliahan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class RiwayatPresensiController extends Controller
{
    private function getDosen(): Dosen
    {
        $dosen = auth()->user()->dosen;
        abort_unless($dosen, 403, 'Profil dosen Anda belum ditautkan ke akun ini.');
        return $dosen;
    }

    public function index(Request $request)
    {
        $dosen = $this->getDosen();

        $query = KelasPerkuliahan::with(['mataKuliah', 'dosen', 'tahunAjaran'])
            ->selectRaw('kelas_perkuliahans.*, (
                SELECT COUNT(*) FROM sesi_presensis sp
                INNER JOIN jadwal_perkuliahans jp ON sp.jadwal_perkuliahan_id = jp.id
                WHERE jp.kelas_perkuliahan_id = kelas_perkuliahans.id
                AND sp.status = "selesai"
            ) as total_pertemuan, (
                SELECT COUNT(DISTINCT p.mahasiswa_id) FROM presensis p
                INNER JOIN sesi_presensis sp ON p.sesi_presensi_id = sp.id
                INNER JOIN jadwal_perkuliahans jp ON sp.jadwal_perkuliahan_id = jp.id
                WHERE jp.kelas_perkuliahan_id = kelas_perkuliahans.id
                AND sp.status = "selesai"
            ) as total_peserta')
            ->where('dosen_id', $dosen->id)
            ->whereHas('jadwalPerkuliahans.sesiPresensis', fn($q) => $q->where('status', 'selesai'))
            ->latest('kelas_perkuliahans.created_at');

        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        }
        if ($request->filled('mata_kuliah_id')) {
            $query->where('mata_kuliah_id', $request->mata_kuliah_id);
        }

        $kelasList    = $query->paginate(15)->withQueryString();
        $tahunAjarans = TahunAjaran::orderByDesc('tahun_ajaran')->get();
        $mataKuliahs  = MataKuliah::where('id', function ($sub) use ($dosen) {
            $sub->select('mata_kuliah_id')->from('kelas_perkuliahans')->where('dosen_id', $dosen->id);
        })->orderBy('nama_mk')->get();

        return view('dosen.riwayat.index', compact('kelasList', 'tahunAjarans', 'mataKuliahs'));
    }

    public function show(Request $request, KelasPerkuliahan $kelas)
    {
        $dosen = $this->getDosen();
        abort_if($kelas->dosen_id !== $dosen->id, 403);

        $kelas->load(['mataKuliah', 'dosen', 'tahunAjaran']);

        $sesiList = SesiPresensi::whereHas('jadwalPerkuliahan', fn($q) => $q->where('kelas_perkuliahan_id', $kelas->id))
            ->where('status', 'selesai')
            ->oldest('waktu_buka')
            ->get();

        $sesiIds = $sesiList->pluck('id');

        $mahasiswaIds = Presensi::whereIn('sesi_presensi_id', $sesiIds)
            ->select('mahasiswa_id')->distinct()->pluck('mahasiswa_id');

        $mahasiswas = Mahasiswa::whereIn('id', $mahasiswaIds)->orderBy('nama_lengkap')->get();

        $presensiMap = Presensi::whereIn('sesi_presensi_id', $sesiIds)
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->get()
            ->groupBy('mahasiswa_id')
            ->map(fn($rows) => $rows->keyBy('sesi_presensi_id'));

        $matrix = [];
        foreach ($mahasiswas as $m) {
            $hadir = 0;
            $alpa  = 0;
            $sesiStatus = [];
            foreach ($sesiList as $s) {
                $p      = $presensiMap[$m->id][$s->id] ?? null;
                $status = ($p && $p->status_kehadiran === 'Hadir') ? 'H' : 'A';
                $sesiStatus[$s->id] = $status;
                $status === 'H' ? $hadir++ : $alpa++;
            }
            $matrix[$m->id] = ['hadir' => $hadir, 'alpa' => $alpa, 'sesi' => $sesiStatus];
        }

        return view('dosen.riwayat.show', compact('kelas', 'sesiList', 'mahasiswas', 'matrix'));
    }
}
