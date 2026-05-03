<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Akun Admin Otomatis Anti-Hilang
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // 2. Akun dummy mahasiswa untuk testing API
        $this->call(MahasiswaSeeder::class);
    }
}