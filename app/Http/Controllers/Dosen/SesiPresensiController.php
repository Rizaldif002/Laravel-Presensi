<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalPerkuliahan;
use App\Models\KelasPerkuliahan;
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

    public function index()
    {
        $dosen = $this->getDosen();

        $kelasList = KelasPerkuliahan::with([
            'mataKuliah',
            'tahunAjaran',
            'jadwalPerkuliahans.ruangan',
            'jadwalPerkuliahans.sesiPresensis' => fn ($q) => $q->where('status', 'aktif'),
        ])->where('dosen_id', $dosen->id)->latest()->get();

        return view('dosen.sesi.index', compact('kelasList', 'dosen'));
    }

    public function buka(Request $request)
    {
        $dosen = $this->getDosen();

        $request->validate([
            'jadwal_perkuliahan_id' => 'required|exists:jadwal_perkuliahans,id',
        ]);

        $jadwal = JadwalPerkuliahan::with('kelasPerkuliahan')->findOrFail($request->jadwal_perkuliahan_id);
        abort_if($jadwal->kelasPerkuliahan->dosen_id !== $dosen->id, 403, 'Anda tidak mengampu kelas ini.');

        $sesiAktif = SesiPresensi::where('jadwal_perkuliahan_id', $jadwal->id)
            ->where('status', 'aktif')
            ->first();

        if ($sesiAktif) {
            return redirect()->route('dosen.sesi.show', $sesiAktif->id)
                ->with('info', 'Sesi presensi untuk jadwal ini sudah aktif.');
        }

        $sesi = SesiPresensi::create([
            'jadwal_perkuliahan_id' => $jadwal->id,
            'waktu_buka'            => Carbon::now(),
            'status'                => 'aktif',
        ]);

        return redirect()->route('dosen.sesi.show', $sesi->id)
            ->with('success', 'Sesi presensi berhasil dibuka! Mahasiswa dapat mulai absen.');
    }

    public function show(int $id)
    {
        $dosen = $this->getDosen();

        $sesi = SesiPresensi::with([
            'jadwalPerkuliahan.kelasPerkuliahan.mataKuliah',
            'jadwalPerkuliahan.kelasPerkuliahan.tahunAjaran',
            'jadwalPerkuliahan.ruangan',
            'presensis.mahasiswa',
        ])->findOrFail($id);

        abort_if(
            $sesi->jadwalPerkuliahan->kelasPerkuliahan->dosen_id !== $dosen->id,
            403,
            'Anda tidak memiliki akses ke sesi ini.'
        );

        return view('dosen.sesi.show', compact('sesi'));
    }

    public function tutup(int $id)
    {
        $dosen = $this->getDosen();

        $sesi = SesiPresensi::with('jadwalPerkuliahan.kelasPerkuliahan')->findOrFail($id);

        abort_if(
            $sesi->jadwalPerkuliahan->kelasPerkuliahan->dosen_id !== $dosen->id,
            403
        );

        $sesi->update([
            'waktu_tutup' => Carbon::now(),
            'status'      => 'selesai',
        ]);

        return redirect()->route('dosen.sesi.index')
            ->with('success', 'Sesi presensi berhasil ditutup. Data kehadiran tersimpan.');
    }
}
