<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // 2. Akun Dosen Dummy + profil dosen yang terhubung
        $dosenUser = User::firstOrCreate(
            ['email' => 'dosen@gmail.com'],
            [
                'name'     => 'Ir. Arman Wijaya S.T., M.T.',
                'password' => Hash::make('password'),
                'role'     => 'dosen',
            ]
        );

        Dosen::firstOrCreate(
            ['user_id' => $dosenUser->id],
            [
                'nip'        => '199001012020121001',
                'nama_dosen' => $dosenUser->name,
            ]
        );

        // 3. Akun dummy mahasiswa untuk testing API
        $this->call(MahasiswaSeeder::class);

        // 4. Skenario testing sesi presensi (kelas + jadwal + sesi historis)
        $this->call(SesiTestingSeeder::class);
    }
}