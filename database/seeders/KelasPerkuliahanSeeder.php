<?php

namespace Database\Seeders;

use App\Models\KelasPerkuliahan;
use App\Models\TahunAjaran;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\Ruangan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasPerkuliahanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan tabel untuk menghindari duplikat
        // Urutan penghapusan harus benar karena ada Foreign Key
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        KelasPerkuliahan::truncate();
        TahunAjaran::truncate();
        MataKuliah::truncate();
        Dosen::truncate();
        Ruangan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Buat Data Master Contoh
        $ta = TahunAjaran::create(['tahun_ajaran' => '2024/2025', 'semester' => 'Ganjil', 'is_active' => true]);

        $ruangans = [
            ['nama_ruangan' => 'Lab Komputer Dasar', 'latitude' => -6.200000, 'longitude' => 106.816666, 'radius_meter' => 20],
            ['nama_ruangan' => 'Ruang Teori 101', 'latitude' => -6.200100, 'longitude' => 106.816700, 'radius_meter' => 15],
            ['nama_ruangan' => 'Lab Jaringan', 'latitude' => -6.200200, 'longitude' => 106.816800, 'radius_meter' => 25],
        ];
        foreach ($ruangans as $r) Ruangan::create($r);

        $dosens = [
            ['nama_dosen' => 'Dr. Andi Setiawan, S.T., M.Kom.', 'nip' => '198001012010011001'],
            ['nama_dosen' => 'Siti Aminah, S.Kom., M.T.', 'nip' => '198505122015022002'],
            ['nama_dosen' => 'Ir. Budi Santoso, Ph.D.', 'nip' => '197508202000031003'],
        ];
        foreach ($dosens as $d) Dosen::create($d);

        $matkuls = [
            ['kode_mk' => 'MK001', 'nama_mk' => 'Pemrograman Web Lanjut', 'sks' => 3, 'semester' => 5],
            ['kode_mk' => 'MK002', 'nama_mk' => 'Basis Data Relasional', 'sks' => 4, 'semester' => 3],
            ['kode_mk' => 'MK003', 'nama_mk' => 'Sistem Operasi', 'sks' => 3, 'semester' => 4],
        ];
        foreach ($matkuls as $m) MataKuliah::create($m);

        // 3. Buat 10 Data Kelas Perkuliahan (Jadwal)
        $dataKelas = [
            ['nama_kelas' => 'TI-A', 'hari' => 'Senin', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30'],
            ['nama_kelas' => 'TI-B', 'hari' => 'Senin', 'jam_mulai' => '13:00', 'jam_selesai' => '15:30'],
            ['nama_kelas' => 'Reguler', 'hari' => 'Selasa', 'jam_mulai' => '09:00', 'jam_selesai' => '11:30'],
            ['nama_kelas' => 'TI-A', 'hari' => 'Selasa', 'jam_mulai' => '13:00', 'jam_selesai' => '15:00'],
            ['nama_kelas' => 'TI-C', 'hari' => 'Rabu', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30'],
            ['nama_kelas' => 'Eksekutif', 'hari' => 'Rabu', 'jam_mulai' => '18:30', 'jam_selesai' => '21:00'],
            ['nama_kelas' => 'TI-B', 'hari' => 'Kamis', 'jam_mulai' => '07:30', 'jam_selesai' => '10:00'],
            ['nama_kelas' => 'TI-A', 'hari' => 'Kamis', 'jam_mulai' => '13:00', 'jam_selesai' => '15:30'],
            ['nama_kelas' => 'Reguler', 'hari' => 'Jumat', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30'],
            ['nama_kelas' => 'Malam', 'hari' => 'Sabtu', 'jam_mulai' => '09:00', 'jam_selesai' => '11:30'],
        ];

        foreach ($dataKelas as $k) {
            KelasPerkuliahan::create([
                'tahun_ajaran_id' => $ta->id,
                'mata_kuliah_id'  => MataKuliah::inRandomOrder()->first()->id,
                'dosen_id'        => Dosen::inRandomOrder()->first()->id,
                'ruangan_id'      => Ruangan::inRandomOrder()->first()->id,
                'nama_kelas'      => $k['nama_kelas'],
                'hari'            => $k['hari'],
                'jam_mulai'       => $k['jam_mulai'],
                'jam_selesai'     => $k['jam_selesai'],
            ]);
        }
    }
}