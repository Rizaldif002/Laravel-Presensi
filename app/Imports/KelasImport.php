<?php

namespace App\Imports;

use App\Models\KelasPerkuliahan;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\Ruangan;
use App\Models\TahunAjaran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KelasImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Cek Mata Kuliah
        $mataKuliah = MataKuliah::where('nama_mk', $row['mata_kuliah'])->first();
        if (!$mataKuliah) {
            dd('ERROR MATKUL: Di database tidak ada matkul bernama: "' . $row['mata_kuliah'] . '"');
        }

        // 2. Cek Dosen
        $dosen = Dosen::where('nama_dosen', $row['dosen'])->first();
        if (!$dosen) {
            dd('ERROR DOSEN: Di database tidak ada dosen bernama: "' . $row['dosen'] . '"');
        }

        // 3. Cek Tahun Ajaran
        $ta = TahunAjaran::where('tahun_ajaran', $row['tahun_ajaran'])->first();
        if (!$ta) {
            dd('ERROR TAHUN AJARAN: Di database tidak ada tahun ajaran: "' . $row['tahun_ajaran'] . '"');
        }

        return new KelasPerkuliahan([
            'tahun_ajaran_id' => $ta->id,
            'mata_kuliah_id'  => $mataKuliah->id,
            'dosen_id'        => $dosen->id,
            'nama_kelas'      => $row['nama_kelas'],
        ]);
    }
}
