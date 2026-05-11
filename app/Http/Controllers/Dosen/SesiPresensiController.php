<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalPerkuliahan;
use App\Models\KelasPerkuliahan;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SesiPresensiController extends Controller
{
    private function getDosen(): Dosen
    {
        $dosen = auth()->user()->dosen;
        abort_unless($dosen, 403, 'Profil dosen Anda belum ditautkan ke akun ini. Hubungi administrator.');
        return $dosen;
    }

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): int
    {
        $R    = 6371000;
        $phi1 = deg2rad($lat1); $phi2 = deg2rad($lat2);
        $dphi = deg2rad($lat2 - $lat1);
        $dlam = deg2rad($lon2 - $lon1);
        $a    = sin($dphi / 2) ** 2 + cos($phi1) * cos($phi2) * sin($dlam / 2) ** 2;
        return (int) round($R * 2 * atan2(sqrt($a), sqrt(1 - $a)));
    }

    public function index(Request $request)
    {
        $dosen = $this->getDosen();

        $jadwals = JadwalPerkuliahan::with(['kelasPerkuliahan.mataKuliah', 'ruangan'])
            ->whereHas('kelasPerkuliahan', fn($q) => $q->where('dosen_id', $dosen->id))
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->get();

        $sesiAktifs = SesiPresensi::with([
            'jadwalPerkuliahan.kelasPerkuliahan.mataKuliah',
            'jadwalPerkuliahan.ruangan',
            'presensis',
        ])
        ->where('status', 'aktif')
        ->whereHas('jadwalPerkuliahan.kelasPerkuliahan', fn($q) => $q->where('dosen_id', $dosen->id))
        ->latest()
        ->get();

        $riwayatQuery = SesiPresensi::with([
            'jadwalPerkuliahan.kelasPerkuliahan.mataKuliah',
            'presensis',
        ])
        ->whereHas('jadwalPerkuliahan.kelasPerkuliahan', fn($q) => $q->where('dosen_id', $dosen->id));

        if ($request->filled('filter_kelas')) {
            $riwayatQuery->whereHas('jadwalPerkuliahan', fn($q) => $q->where('kelas_perkuliahan_id', $request->filter_kelas));
        }
        if ($request->filled('filter_bulan')) {
            $riwayatQuery->whereMonth('created_at', $request->filter_bulan)
                         ->whereYear('created_at', now()->year);
        }

        $riwayat = $riwayatQuery->where('status', 'selesai')->latest()->paginate(10)->withQueryString();

        $statAktif        = $sesiAktifs->count();
        $statSesiBulanIni = SesiPresensi::whereHas('jadwalPerkuliahan.kelasPerkuliahan', fn($q) => $q->where('dosen_id', $dosen->id))
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $statHadirHariIni = Presensi::whereHas('sesiPresensi.jadwalPerkuliahan.kelasPerkuliahan', fn($q) => $q->where('dosen_id', $dosen->id))
            ->whereDate('waktu_absen', today())->count();
        $statKelasAmpuh   = KelasPerkuliahan::where('dosen_id', $dosen->id)->count();

        $kelases = KelasPerkuliahan::with('mataKuliah')->where('dosen_id', $dosen->id)->get();

        return view('dosen.sesi.index', compact(
            'dosen', 'jadwals', 'sesiAktifs', 'riwayat', 'kelases',
            'statAktif', 'statSesiBulanIni', 'statHadirHariIni', 'statKelasAmpuh'
        ));
    }

    public function store(Request $request)
    {
        $dosen = $this->getDosen();

        $request->validate([
            'jadwal_perkuliahan_id' => 'required|exists:jadwal_perkuliahans,id',
            'nama_pertemuan'        => 'required|string|max:100',
            'is_gps_enabled'        => 'boolean',
            'gps_reason'            => 'required_if:is_gps_enabled,0|nullable|string|max:500',
        ]);

        $jadwal = JadwalPerkuliahan::with('kelasPerkuliahan')->findOrFail($request->jadwal_perkuliahan_id);
        abort_if($jadwal->kelasPerkuliahan->dosen_id !== $dosen->id, 403, 'Anda tidak mengampu kelas ini.');

        $sesiAktif = SesiPresensi::where('jadwal_perkuliahan_id', $jadwal->id)
            ->where('status', 'aktif')
            ->whereDate('created_at', today())
            ->first();

        if ($sesiAktif) {
            return back()->with('error', 'Masih ada sesi aktif untuk jadwal ini hari ini.');
        }

        $sesi = SesiPresensi::create([
            'jadwal_perkuliahan_id' => $jadwal->id,
            'nama_pertemuan'        => $request->nama_pertemuan,
            'is_gps_enabled'        => $request->boolean('is_gps_enabled', true),
            'gps_reason'            => $request->gps_reason,
            'dibuka_oleh'           => auth()->id(),
            'waktu_buka'            => Carbon::now(),
            'status'                => 'aktif',
        ]);

        return redirect()->route('dosen.sesi.live', $sesi)
            ->with('success', 'Sesi presensi berhasil dibuka! Mahasiswa dapat mulai absen.');
    }

    public function tutup(SesiPresensi $sesi)
    {
        $dosen = $this->getDosen();
        $sesi->load('jadwalPerkuliahan.kelasPerkuliahan');
        abort_if($sesi->jadwalPerkuliahan->kelasPerkuliahan->dosen_id !== $dosen->id, 403);

        $sesi->update([
            'waktu_tutup' => Carbon::now(),
            'status'      => 'selesai',
        ]);

        return back()->with('success', 'Sesi berhasil ditutup. Data kehadiran tersimpan.');
    }

    public function show(SesiPresensi $sesi)
    {
        $dosen = $this->getDosen();

        $sesi->load([
            'jadwalPerkuliahan.kelasPerkuliahan.mataKuliah',
            'jadwalPerkuliahan.kelasPerkuliahan.tahunAjaran',
            'jadwalPerkuliahan.ruangan',
            'presensis.mahasiswa',
        ]);

        abort_if($sesi->jadwalPerkuliahan->kelasPerkuliahan->dosen_id !== $dosen->id, 403);

        return view('dosen.sesi.show', compact('sesi'));
    }

    public function live(SesiPresensi $sesi)
    {
        $dosen = $this->getDosen();

        $sesi->load([
            'jadwalPerkuliahan.kelasPerkuliahan.mataKuliah',
            'jadwalPerkuliahan.ruangan',
            'presensis.mahasiswa',
        ]);

        abort_if($sesi->jadwalPerkuliahan->kelasPerkuliahan->dosen_id !== $dosen->id, 403);

        if ($sesi->status !== 'aktif') {
            return redirect()->route('dosen.sesi.show', $sesi)
                ->with('info', 'Sesi ini sudah selesai. Menampilkan halaman detail.');
        }

        return view('dosen.sesi.live', compact('sesi'));
    }

    public function liveData(SesiPresensi $sesi)
    {
        $dosen = $this->getDosen();

        $sesi->load([
            'jadwalPerkuliahan.kelasPerkuliahan',
            'jadwalPerkuliahan.ruangan',
            'presensis.mahasiswa',
        ]);

        abort_if($sesi->jadwalPerkuliahan->kelasPerkuliahan->dosen_id !== $dosen->id, 403);

        $ruangan     = $sesi->jadwalPerkuliahan->ruangan;
        $latRuangan  = (float) ($ruangan->latitude ?? 0);
        $lngRuangan  = (float) ($ruangan->longitude ?? 0);
        $radius      = (int)   ($ruangan->radius_meter ?? 50);

        $presensis = $sesi->presensis->map(function ($p) use ($latRuangan, $lngRuangan, $radius) {
            $jarak        = null;
            $dalamRadius  = null;

            if ($p->latitude && $p->longitude && $latRuangan && $lngRuangan) {
                $jarak       = $this->haversine((float) $p->latitude, (float) $p->longitude, $latRuangan, $lngRuangan);
                $dalamRadius = $jarak <= $radius;
            }

            return [
                'nama'         => $p->mahasiswa->nama_lengkap ?? '-',
                'nim'          => $p->mahasiswa->nim ?? '-',
                'waktu'        => Carbon::parse($p->waktu_absen)->format('H:i:s'),
                'foto'         => $p->foto_wajah ? asset('storage/' . $p->foto_wajah) : null,
                'status'       => $p->status_kehadiran,
                'jarak_meter'  => $jarak,
                'dalam_radius' => $dalamRadius,
            ];
        });

        return response()->json([
            'count'     => $sesi->presensis->count(),
            'status'    => $sesi->status,
            'presensis' => $presensis,
        ]);
    }
}
