<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswas = [
            ['nim' => '2109076002', 'name' => 'Muhammad Rivai'],
            ['nim' => '2109076011', 'name' => 'Vincent Candi'],
            ['nim' => '2109076012', 'name' => 'Zaidan Alfikri'],
            ['nim' => '2109076017', 'name' => 'Muhammad Rizaldi Nur'],
            ['nim' => '2109076027', 'name' => 'Muhammad Ridwan'],
        ];

        foreach ($mahasiswas as $data) {
            User::create([
                'name'     => $data['name'],
                'email'    => $data['nim'] . '@mahasiswa.unmul.ac.id',
                'nim'      => $data['nim'],
                'password' => Hash::make('password'),
                'role'     => 'mahasiswa',
            ]);
        }
    }
}
