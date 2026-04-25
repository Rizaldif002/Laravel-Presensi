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
        Schema::create('ruangans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ruangan');
            
            // Tambahkan kolom gedung jika ada di ERD
            $table->string('gedung')->nullable(); 
            
            // Kolom wajib untuk GPS (Gunakan string atau decimal)
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            
            // INI KOLOM YANG TADI BIKIN ERROR (Wajib Ada)
            $table->integer('radius_meter')->default(20); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangans');
    }
};
