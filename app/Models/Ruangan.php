<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    // Mengunci nama tabel dan mengizinkan semua kolom diisi
    protected $table = 'ruangans';
    protected $guarded = [];
}