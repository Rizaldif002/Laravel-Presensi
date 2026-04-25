<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensis';

    // Kolom yang diizinkan untuk diisi secara massal
    protected $fillable = [
        'sesi_presensi_id',
        'mahasiswa_id',
        'waktu_absen',
        'latitude',
        'longitude',
        'foto_wajah',
        'status_kehadiran',
    ];

    // Relasi: Data absen ini milik Sesi yang mana?
    public function sesiPresensi()
    {
        return $this->belongsTo(SesiPresensi::class);
    }

    // Relasi: Data absen ini milik Mahasiswa siapa?
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}