<?php

namespace App\Imports;

use App\Models\MataKuliah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class MataKuliahImport implements ToModel, WithStartRow
{
    public function startRow(): int { return 2; }

    public function model(array $row)
    {
        if (!isset($row[0])) return null;

        return MataKuliah::updateOrCreate(
            ['kode_mk' => $row[0]], 
            [
                'nama_mk'  => $row[1],
                'sks'      => $row[2],
                'semester' => $row[3],
            ]
        );
    }
}