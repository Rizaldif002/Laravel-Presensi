<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\KelasPerkuliahan;
use App\Models\JadwalPerkuliahan;
use App\Models\MataKuliah;
use App\Models\Ruangan;
use App\Models\SesiPresensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isMahasiswa()) {
            return redirect()
                ->route('home')
                ->with('error', 'Dashboard web hanya untuk Administrator dan Dosen.');
        }

        if ($user->isDosen()) {
            return $this->dosenDashboard($user);
        }

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return redirect()
            ->route('login')
            ->with('error', 'Peran akun tidak valid.');
    }

    private function adminDashboard()
    {
        $hariIni = ucfirst(Carbon::now()->locale('id')->isoFormat('dddd'));

        $jadwalHariIni = JadwalPerkuliahan::with([
            'kelasPerkuliahan.dosen',
            'kelasPerkuliahan.mataKuliah',
            'ruangan',
        ])->where('hari', $hariIni)->orderBy('jam_mulai')->get();

        return view('admin.dashboard', [
            'totalDosen'   => Dosen::count(),
            'totalMatkul'  => MataKuliah::count(),
            'totalRuangan' => Ruangan::count(),
            'totalKelas'   => KelasPerkuliahan::count(),
            'kelasHariIni' => $jadwalHariIni,
        ]);
    }

    private function dosenDashboard($user)
    {
        $dosen   = $user->dosen;
        $hariIni = ucfirst(Carbon::now()->locale('id')->isoFormat('dddd'));

        if (! $dosen) {
            return view('dosen.dashboard', [
                'dosen'         => null,
                'hariIni'       => $hariIni,
                'jadwalHariIni' => collect(),
                'sesiAktif'     => collect(),
                'totalHadir'    => 0,
            ]);
        }

        $jadwalHariIni = JadwalPerkuliahan::with([
            'ruangan',
            'kelasPerkuliahan.mataKuliah',
            'sesiPresensis' => fn ($q) => $q->where('status', 'aktif'),
        ])->whereHas(
            'kelasPerkuliahan',
            fn ($q) => $q->where('dosen_id', $dosen->id)
        )->where('hari', $hariIni)->orderBy('jam_mulai')->get();

        $sesiAktif = SesiPresensi::with([
            'jadwalPerkuliahan.kelasPerkuliahan.mataKuliah',
            'jadwalPerkuliahan.ruangan',
            'presensis',
        ])->whereHas(
            'jadwalPerkuliahan.kelasPerkuliahan',
            fn ($q) => $q->where('dosen_id', $dosen->id)
        )->where('status', 'aktif')->get();

        return view('dosen.dashboard', [
            'dosen'         => $dosen,
            'hariIni'       => $hariIni,
            'jadwalHariIni' => $jadwalHariIni,
            'sesiAktif'     => $sesiAktif,
            'totalHadir'    => $sesiAktif->sum(fn ($s) => $s->presensis->count()),
        ]);
    }
}
