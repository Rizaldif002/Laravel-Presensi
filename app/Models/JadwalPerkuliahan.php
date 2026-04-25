<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPerkuliahan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_perkuliahans';

    protected $fillable = [
        'kelas_perkuliahan_id',
        'ruangan_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function kelasPerkuliahan()
    {
        return $this->belongsTo(KelasPerkuliahan::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    // RELASI: 1 Jadwal bisa dibuka sesi absennya berkali-kali (tiap minggu)
    public function sesiPresensis()
    {
        return $this->hasMany(SesiPresensi::class);
    }
}