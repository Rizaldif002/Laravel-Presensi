<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class MahasiswaImport implements ToModel, WithStartRow
{
    /**
     * Mulai pembacaan dari baris ke-2 (lewati header).
     *
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Membuat atau memperbarui data Mahasiswa berdasarkan NIM.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Cegah eksekusi jika baris kosong (NIM tidak ada)
        if (empty($row[0])) {
            return null;
        }

        // Format: [0] => NIM, [1] => Nama, [2] => Program Studi, [3] => No HP (optional)
        return Mahasiswa::updateOrCreate(
            ['nim' => $row[0]],
            [
                'nama_lengkap'  => $row[1] ?? null,
                'program_studi' => $row[2] ?? null,
                'no_hp'         => $row[3] ?? null,
            ]
        );
    }
}