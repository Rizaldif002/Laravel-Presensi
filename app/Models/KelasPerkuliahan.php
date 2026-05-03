<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasPerkuliahan extends Model
{
    use HasFactory;

    protected $table = 'kelas_perkuliahans';

    protected $fillable = [
        'tahun_ajaran_id',
        'mata_kuliah_id',
        'dosen_id',
        'nama_kelas',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function mataKuliah()
    {
        // Ingat, tabel Anda bernama mata_kuliah (tanpa s)
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id', 'id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function jadwalPerkuliahans()
    {
        return $this->hasMany(JadwalPerkuliahan::class, 'kelas_perkuliahan_id');
    }
}