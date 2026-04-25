<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_presensis', function (Blueprint $table) {
            $table->id();
            // UBAH BARIS INI: Sesi sekarang berpatokan pada Jadwal spesifik
            $table->foreignId('jadwal_perkuliahan_id')->constrained('jadwal_perkuliahans')->cascadeOnDelete();
            
            $table->dateTime('waktu_buka');
            $table->dateTime('waktu_tutup')->nullable();
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('sesi_presensis');
    }
};