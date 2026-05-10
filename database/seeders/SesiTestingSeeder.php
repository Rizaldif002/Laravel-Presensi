<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\JadwalPerkuliahan;
use App\Models\KelasPerkuliahan;
use App\Models\MataKuliah;
use App\Models\Mahasiswa;
use App\Models\Presensi;
use App\Models\Ruangan;
use App\Models\SesiPresensi;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SesiTestingSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------------------------------------------
        // 0. Tutup semua sesi aktif agar kondisi bersih
        // ----------------------------------------------------------------
        SesiPresensi::where('status', 'aktif')->update([
            'status'      => 'selesai',
            'waktu_tutup' => Carbon::now(),
        ]);

        // ----------------------------------------------------------------
        // 1. Data Master — dibuat jika belum ada (admin setup)
        // ----------------------------------------------------------------

        // Tahun Ajaran aktif
        $tahunAjaran = TahunAjaran::firstOrCreate(
            ['tahun_ajaran' => '2024/2025', 'semester' => 'Ganjil'],
            ['is_active' => true]
        );
        // Pastikan is_active = true
        if (! $tahunAjaran->is_active) {
            TahunAjaran::where('is_active', true)->update(['is_active' => false]);
            $tahunAjaran->update(['is_active' => true]);
        }

        // Ruangan dengan koordinat GPS Gedung FT Unmul
        $ruangan = Ruangan::firstOrCreate(
            ['nama_ruangan' => 'Gedung FT Unmul - Lab Komputer'],
            [
                'gedung'       => 'Fakultas Teknik',
                'latitude'     => '-0.467185',
                'longitude'    => '117.157162',
                'radius_meter' => 50,
            ]
        );

        // Dosen (user_id=2 adalah akun dosen@gmail.com dari DatabaseSeeder)
        $dosen = Dosen::where('user_id', 2)->firstOrFail();

        // ----------------------------------------------------------------
        // 2. Mahasiswa di tabel mahasiswas (profil, bukan akun login)
        // ----------------------------------------------------------------
        $dataMahasiswa = [
            ['nim' => '2109076002', 'nama_lengkap' => 'Muhammad Rivai',      'program_studi' => 'Teknik Elektro', 'no_hp' => '081111111101'],
            ['nim' => '2109076011', 'nama_lengkap' => 'Vincent Candi',        'program_studi' => 'Teknik Elektro', 'no_hp' => '081111111102'],
            ['nim' => '2109076012', 'nama_lengkap' => 'Zaidan Alfikri',       'program_studi' => 'Teknik Elektro', 'no_hp' => '081111111103'],
            ['nim' => '2109076017', 'nama_lengkap' => 'Muhammad Rizaldi Nur', 'program_studi' => 'Teknik Elektro', 'no_hp' => '081111111104'],
            ['nim' => '2109076027', 'nama_lengkap' => 'Muhammad Ridwan',      'program_studi' => 'Teknik Elektro', 'no_hp' => '081111111105'],
        ];

        foreach ($dataMahasiswa as $data) {
            Mahasiswa::firstOrCreate(
                ['nim' => $data['nim']],
                [
                    'nama_lengkap'  => $data['nama_lengkap'],
                    'program_studi' => $data['program_studi'],
                    'no_hp'         => $data['no_hp'],
                ]
            );
        }

        $mahasiswas = Mahasiswa::whereIn('nim', array_column($dataMahasiswa, 'nim'))->get();

        // ----------------------------------------------------------------
        // 3. Mata Kuliah yang diampu Dosen
        // ----------------------------------------------------------------
        $mk1 = MataKuliah::firstOrCreate(
            ['kode_mk' => 'TEL-101'],
            ['nama_mk' => 'Pemrograman Berbasis Web', 'sks' => 3, 'semester' => 3]
        );

        $mk2 = MataKuliah::firstOrCreate(
            ['kode_mk' => 'TEL-102'],
            ['nama_mk' => 'Sistem Basis Data', 'sks' => 3, 'semester' => 4]
        );

        // ----------------------------------------------------------------
        // 4. Kelas Perkuliahan — admin yang membuat & menugaskan dosen
        // ----------------------------------------------------------------
        $kelas1 = KelasPerkuliahan::firstOrCreate(
            [
                'tahun_ajaran_id' => $tahunAjaran->id,
                'mata_kuliah_id'  => $mk1->id,
                'dosen_id'        => $dosen->id,
            ],
            ['nama_kelas' => 'TEE-D']
        );

        $kelas2 = KelasPerkuliahan::firstOrCreate(
            [
                'tahun_ajaran_id' => $tahunAjaran->id,
                'mata_kuliah_id'  => $mk2->id,
                'dosen_id'        => $dosen->id,
            ],
            ['nama_kelas' => 'TEE-D']
        );

        // ----------------------------------------------------------------
        // 5. Jadwal Perkuliahan — Senin, jam realistis
        //    Jadwal 1 : 08:00–09:40  → sesi historis selesai (demo riwayat)
        //    Jadwal 2 : 13:00–14:40  → belum ada sesi (dosen buka sendiri)
        // ----------------------------------------------------------------
        $jadwal1 = JadwalPerkuliahan::firstOrCreate(
            ['kelas_perkuliahan_id' => $kelas1->id, 'ruangan_id' => $ruangan->id],
            ['hari' => 'Senin', 'jam_mulai' => '08:00:00', 'jam_selesai' => '09:40:00']
        );

        $jadwal2 = JadwalPerkuliahan::firstOrCreate(
            ['kelas_perkuliahan_id' => $kelas2->id, 'ruangan_id' => $ruangan->id],
            ['hari' => 'Senin', 'jam_mulai' => '13:00:00', 'jam_selesai' => '14:40:00']
        );

        // ----------------------------------------------------------------
        // 6. Sesi historis (selesai) untuk Jadwal 1
        //    Merepresentasikan pertemuan minggu lalu yang sudah ditutup
        // ----------------------------------------------------------------
        $sudahAdaSesiLalu = SesiPresensi::where('jadwal_perkuliahan_id', $jadwal1->id)
            ->where('status', 'selesai')
            ->exists();

        if (! $sudahAdaSesiLalu) {
            $waktuBuka  = Carbon::now()->subWeek()->setTime(8, 0);
            $waktuTutup = Carbon::now()->subWeek()->setTime(9, 40);

            $sesiLalu = SesiPresensi::create([
                'jadwal_perkuliahan_id' => $jadwal1->id,
                'waktu_buka'            => $waktuBuka,
                'waktu_tutup'           => $waktuTutup,
                'status'                => 'selesai',
            ]);

            foreach ($mahasiswas as $i => $mhs) {
                $terlambat       = ($i === 4);
                $statusKehadiran = $terlambat ? 'Terlambat' : 'Hadir';
                $waktuAbsen      = $waktuBuka->copy()->addMinutes($i * 3 + ($terlambat ? 25 : 0));

                Presensi::create([
                    'sesi_presensi_id' => $sesiLalu->id,
                    'mahasiswa_id'     => $mhs->id,
                    'waktu_absen'      => $waktuAbsen,
                    'latitude'         => $ruangan->latitude,
                    'longitude'        => $ruangan->longitude,
                    'foto_wajah'       => 'testing/placeholder_' . $mhs->nim . '.jpg',
                    'status_kehadiran' => $statusKehadiran,
                ]);
            }

            $this->command->info("Sesi historis dibuat (sesi_id={$sesiLalu->id}) dengan {$mahasiswas->count()} presensi.");
        } else {
            $this->command->info('Sesi historis sudah ada, dilewati.');
        }

        // ----------------------------------------------------------------
        // Ringkasan
        // ----------------------------------------------------------------
        $this->command->newLine();
        $this->command->line('┌─────────────────────────────────────────────────────────┐');
        $this->command->line('│          SKENARIO TESTING SESI PRESENSI                 │');
        $this->command->line('├─────────────────────────────────────────────────────────┤');
        $this->command->line('│  Login Dosen : dosen@gmail.com / password               │');
        $this->command->line("│  Dosen       : {$dosen->nama_dosen}");
        $this->command->line("│  NIP         : {$dosen->nip}");
        $this->command->line("│  Tahun Ajaran: {$tahunAjaran->tahun_ajaran} {$tahunAjaran->semester}");
        $this->command->line("│  Kelas 1     : {$mk1->nama_mk} ({$kelas1->nama_kelas})");
        $this->command->line('│  Jadwal 1    : Senin 08:00–09:40 → sesi historis selesai│');
        $this->command->line("│  Kelas 2     : {$mk2->nama_mk} ({$kelas2->nama_kelas})");
        $this->command->line('│  Jadwal 2    : Senin 13:00–14:40 → belum ada sesi       │');
        $this->command->line("│  Ruangan     : {$ruangan->nama_ruangan}");
        $this->command->line("│  GPS         : {$ruangan->latitude}, {$ruangan->longitude} (r={$ruangan->radius_meter}m)");
        $this->command->line("│  Mahasiswa   : {$mahasiswas->count()} orang (4 Hadir, 1 Terlambat di sesi lalu)");
        $this->command->line('└─────────────────────────────────────────────────────────┘');
    }
}
