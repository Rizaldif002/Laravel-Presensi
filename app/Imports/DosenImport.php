<?php

namespace App\Imports;

use App\Models\Dosen;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class DosenImport implements ToModel, WithStartRow
{
    public function startRow(): int { return 2; } // Lewati Baris 1 (Header)

    public function model(array $row)
    {
        if (!isset($row[0])) return null;

        return Dosen::updateOrCreate(
            ['nip' => $row[0]], // Patokan NIP (Bukan NIDN lagi)
            [
                'nama_dosen' => $row[1],
                'no_hp'      => $row[2] ?? null,
            ]
        );
    }
}