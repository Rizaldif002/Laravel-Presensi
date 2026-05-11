<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\KelasPerkuliahan;
use App\Models\MataKuliah;
use App\Models\Mahasiswa;
use App\Models\PesertaKelas;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class RiwayatPresensiController extends Controller
{
    public function index(Request $request)
    {
        $query = KelasPerkuliahan::with(['mataKuliah', 'dosen', 'tahunAjaran'])
            ->selectRaw('kelas_perkuliahans.*, (
                SELECT COUNT(*) FROM sesi_presensis sp
                INNER JOIN jadwal_perkuliahans jp ON sp.jadwal_perkuliahan_id = jp.id
                WHERE jp.kelas_perkuliahan_id = kelas_perkuliahans.id
                AND sp.status = "selesai"
            ) as total_pertemuan, (
                SELECT COUNT(*) FROM peserta_kelas pk
                WHERE pk.kelas_perkuliahan_id = kelas_perkuliahans.id
            ) as total_peserta')
            ->whereHas('jadwalPerkuliahans.sesiPresensis', fn($q) => $q->where('status', 'selesai'))
            ->latest('kelas_perkuliahans.created_at');

        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        }
        if ($request->filled('dosen_id')) {
            $query->where('dosen_id', $request->dosen_id);
        }
        if ($request->filled('mata_kuliah_id')) {
            $query->where('mata_kuliah_id', $request->mata_kuliah_id);
        }

        $kelasList    = $query->paginate(15)->withQueryString();
        $tahunAjarans = TahunAjaran::orderByDesc('tahun_ajaran')->get();
        $dosens       = Dosen::orderBy('nama_dosen')->get();
        $mataKuliahs  = MataKuliah::orderBy('nama_mk')->get();

        return view('admin.riwayat.index', compact('kelasList', 'tahunAjarans', 'dosens', 'mataKuliahs'));
    }

    public function show(Request $request, KelasPerkuliahan $kelas)
    {
        $kelas->load(['mataKuliah', 'dosen', 'tahunAjaran']);

        $sesiList = SesiPresensi::whereHas('jadwalPerkuliahan', fn($q) => $q->where('kelas_perkuliahan_id', $kelas->id))
            ->where('status', 'selesai')
            ->oldest('waktu_buka')
            ->get();

        $sesiIds = $sesiList->pluck('id');

        $mahasiswaIds = PesertaKelas::where('kelas_perkuliahan_id', $kelas->id)
            ->pluck('mahasiswa_id');

        $mahasiswas = Mahasiswa::whereIn('id', $mahasiswaIds)->orderBy('nama_lengkap')->get();

        $presensiMap = Presensi::whereIn('sesi_presensi_id', $sesiIds)
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->get()
            ->groupBy('mahasiswa_id')
            ->map(fn($rows) => $rows->keyBy('sesi_presensi_id'));

        $statusMap = ['Hadir' => 'H', 'Sakit' => 'S', 'Izin' => 'I'];

        $matrix = [];
        foreach ($mahasiswas as $m) {
            $hadir = 0;
            $alpa  = 0;
            $sesiStatus = [];
            foreach ($sesiList as $s) {
                $p      = $presensiMap[$m->id][$s->id] ?? null;
                $status = $statusMap[$p?->status_kehadiran ?? ''] ?? 'A';
                $sesiStatus[$s->id] = $status;
                $status === 'H' ? $hadir++ : $alpa++;
            }
            $matrix[$m->id] = ['hadir' => $hadir, 'alpa' => $alpa, 'sesi' => $sesiStatus];
        }

        return view('admin.riwayat.show', compact('kelas', 'sesiList', 'mahasiswas', 'matrix'));
    }

    public function overridePresensi(Request $request)
    {
        $request->validate([
            'sesi_id'      => 'required|exists:sesi_presensis,id',
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'status'       => 'required|in:H,A,S,I',
        ]);

        $statusMap = ['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin'];

        if ($request->status === 'A') {
            Presensi::where('sesi_presensi_id', $request->sesi_id)
                ->where('mahasiswa_id', $request->mahasiswa_id)
                ->delete();
        } else {
            Presensi::updateOrCreate(
                [
                    'sesi_presensi_id' => $request->sesi_id,
                    'mahasiswa_id'     => $request->mahasiswa_id,
                ],
                [
                    'status_kehadiran' => $statusMap[$request->status],
                    'waktu_absen'      => now(),
                ]
            );
        }

        return back()->with('success', 'Status presensi mahasiswa berhasil diperbarui.');
    }
}
