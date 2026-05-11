<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\KelasPerkuliahan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\PesertaKelas;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Models\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;
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
            $total = $sesiList->count();
            $pct   = $total > 0 ? round($hadir / $total * 100) : 0;
            $matrix[$m->id] = ['hadir' => $hadir, 'alpa' => $alpa, 'pct' => $pct, 'sesi' => $sesiStatus];
        }

        return view('dosen.riwayat.show', compact('kelas', 'sesiList', 'mahasiswas', 'matrix'));
    }

    public function overridePresensi(Request $request)
    {
        $dosen = $this->getDosen();

        $request->validate([
            'sesi_id'      => 'required|exists:sesi_presensis,id',
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'status'       => 'required|in:H,A,S,I',
        ]);

        $sesi = SesiPresensi::with('jadwalPerkuliahan.kelasPerkuliahan')->findOrFail($request->sesi_id);
        abort_if($sesi->jadwalPerkuliahan->kelasPerkuliahan->dosen_id !== $dosen->id, 403);

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
                    'override_by'      => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Status presensi berhasil diperbarui.');
    }

    public function exportPdf(KelasPerkuliahan $kelas)
    {
        $dosen = $this->getDosen();
        abort_if($kelas->dosen_id !== $dosen->id, 403);

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
            $total = $sesiList->count();
            $pct   = $total > 0 ? round($hadir / $total * 100) : 0;
            $matrix[$m->id] = ['hadir' => $hadir, 'alpa' => $alpa, 'pct' => $pct, 'sesi' => $sesiStatus];
        }

        $pdf = Pdf::loadView('admin.laporan.pdf', compact('kelas', 'sesiList', 'mahasiswas', 'matrix'))
            ->setPaper('a4', 'landscape');

        $filename = 'Laporan_' . str_replace([' ', '/'], '_', $kelas->mataKuliah->nama_mk ?? 'kelas')
            . '_' . $kelas->nama_kelas . '.pdf';

        return $pdf->download($filename);
    }
}
