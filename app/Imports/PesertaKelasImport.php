<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use App\Models\PesertaKelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PesertaKelasImport implements ToModel, WithStartRow
{
    public function __construct(private int $kelasId) {}

    public function startRow(): int
    {
        return 2;
    }

    /**
     * Format Excel: [0] => NIM
     */
    public function model(array $row)
    {
        if (empty($row[0])) {
            return null;
        }

        $mahasiswa = Mahasiswa::where('nim', trim($row[0]))->first();

        if (! $mahasiswa) {
            return null;
        }

        $sudahAda = PesertaKelas::where('kelas_perkuliahan_id', $this->kelasId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->exists();

        if ($sudahAda) {
            return null;
        }

        return new PesertaKelas([
            'kelas_perkuliahan_id' => $this->kelasId,
            'mahasiswa_id'         => $mahasiswa->id,
        ]);
    }
}
