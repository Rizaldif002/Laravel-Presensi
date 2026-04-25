<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\Ruangan;
use App\Models\KelasPerkuliahan;
use App\Models\JadwalPerkuliahan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung total data master
        $totalDosen   = Dosen::count();
        $totalMatkul  = MataKuliah::count();
        $totalRuangan = Ruangan::count();
        $totalKelas   = KelasPerkuliahan::count();

        // Ambil jadwal kelas khusus hari ini dari tabel JadwalPerkuliahan
        $hariIni = ucfirst(Carbon::now()->locale('id')->isoFormat('dddd'));

        $jadwalHariIni = JadwalPerkuliahan::with([
                'kelasPerkuliahan.dosen',
                'kelasPerkuliahan.mataKuliah',
                'ruangan'
            ])
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();

        return view('admin.dashboard', [
            'totalDosen'   => $totalDosen,
            'totalMatkul'  => $totalMatkul,
            'totalRuangan' => $totalRuangan,
            'totalKelas'   => $totalKelas,
            'kelasHariIni' => $jadwalHariIni,
        ]);
    }
}