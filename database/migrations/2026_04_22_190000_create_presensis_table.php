<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            // Relasi ke sesi mana mahasiswa ini absen
            $table->foreignId('sesi_presensi_id')->constrained('sesi_presensis')->cascadeOnDelete();
            // Relasi ke mahasiswa yang melakukan absen
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            
            // Waktu persis saat wajah dan GPS berhasil tervalidasi
            $table->dateTime('waktu_absen');
            
            // Kolom Geofencing (Titik Kordinat saat mahasiswa absen)
            $table->string('latitude');
            $table->string('longitude');
            
            // Kolom Face Recognition (Menyimpan nama file foto selfie)
            $table->string('foto_wajah');
            
            // Status kehadiran
            $table->enum('status_kehadiran', ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alfa'])->default('Hadir');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};