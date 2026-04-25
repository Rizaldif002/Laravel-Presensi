<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;

    // BARIS INI WAJIB DITAMBAHKAN UNTUK MENGUNCI NAMA TABEL
    protected $table = 'mata_kuliah'; 
    protected $guarded = [];
}