<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiPresensi extends Model
{
    use HasFactory;

    // 1. Pastikan jadwal_perkuliahan_id masuk di sini!
    protected $fillable = [
        'jadwal_perkuliahan_id',
        'status',
        'waktu_buka',
        'waktu_tutup'
    ];

    // 2. Sesuaikan juga nama relasinya agar tidak error di kemudian hari
    public function jadwalPerkuliahan()
    {
        return $this->belongsTo(JadwalPerkuliahan::class, 'jadwal_perkuliahan_id');
    }
}