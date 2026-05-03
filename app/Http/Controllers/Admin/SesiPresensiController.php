<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SesiPresensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SesiPresensiController extends Controller
{
    // 1. Fungsi untuk Membuka Sesi Absen
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'jadwal_perkuliahan_id' => 'required|exists:jadwal_perkuliahans,id',
        ]);

        // Cek apakah jadwal ini sudah memiliki sesi yang masih 'aktif'
        $sesiAktif = SesiPresensi::where('jadwal_perkuliahan_id', $request->jadwal_perkuliahan_id)
            ->where('status', 'aktif')
            ->first();

        // JIKA SESI SUDAH ADA: Langsung arahkan ke halaman radar tanpa membuat baru
        if ($sesiAktif) {
            return redirect()->route('admin.sesi.live', $sesiAktif->id)
                ->with('info', 'Sesi presensi untuk kelas ini sudah aktif.');
        }

        // JIKA BELUM ADA: Buat sesi baru
        $sesiBaru = SesiPresensi::create([
            'jadwal_perkuliahan_id' => $request->jadwal_perkuliahan_id,
            'waktu_buka'            => Carbon::now(),
            'status'                => 'aktif'
        ]);

        return redirect()->route('admin.sesi.live', $sesiBaru->id)
            ->with('success', 'Sesi presensi berhasil dibuka!');
    }

    // 2. Fungsi untuk Menampilkan Halaman Live Radar & Foto
    public function show($id)
    {
        // Ambil data sesi beserta rantai relasinya.
        // Menggunakan 'jadwalPerkuliahan' karena patokan kita adalah jadwal_perkuliahan_id
        $sesi = SesiPresensi::with([
            'jadwalPerkuliahan.kelasPerkuliahan.mataKuliah', // Mengambil Matkul via Kelas
            'jadwalPerkuliahan.ruangan',                     // Mengambil Ruangan dari Jadwal
            // 'presensis.mahasiswa'                         // (Aktifkan ini nanti kalau tabel Presensi Mahasiswa sudah dibuat)
        ])->findOrFail($id);

        return view('admin.sesi.live', compact('sesi'));
    }

    // 3. Fungsi untuk Menutup Sesi
    public function tutup($id)
    {
        $sesi = SesiPresensi::findOrFail($id);

        // Update waktu tutup dan ubah status jadi selesai
        $sesi->update([
            'waktu_tutup' => Carbon::now(),
            'status'      => 'selesai'
        ]);

        // Arahkan kembali ke halaman awal/jadwal dengan pesan sukses
        return redirect()->route('admin.kelas.index')
            ->with('success', 'Sesi Presensi Telah Ditutup. Data tersimpan dengan aman.');
    }
}

