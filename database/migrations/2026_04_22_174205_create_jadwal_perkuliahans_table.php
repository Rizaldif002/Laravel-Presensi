<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwal_perkuliahans', function (Blueprint $table) {
            $table->id();
            // Relasi ke Kelas Induknya
            $table->foreignId('kelas_perkuliahan_id')->constrained('kelas_perkuliahans')->cascadeOnDelete();
            // Relasi ke Titik GPS (Ruangan)
            $table->foreignId('ruangan_id')->constrained('ruangans')->restrictOnDelete();
            
            // Detail Waktu
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_perkuliahans');
    }
};
