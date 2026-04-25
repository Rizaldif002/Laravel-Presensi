<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosens';

    protected $fillable = [
        'user_id',
        'nip',       
        'nama_dosen',
        'no_telp',
    ];

    /**
     * Relasi: 1 Data Dosen dimiliki oleh 1 Akun User (Login)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi: 1 Dosen bisa mengajar Banyak Kelas Perkuliahan
     */
    public function kelasPerkuliahan()
    {
        return $this->hasMany(KelasPerkuliahan::class, 'dosen_id');
    }
}