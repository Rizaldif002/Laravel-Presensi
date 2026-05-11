<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Normalkan data lama sebelum mempersempit enum
        // Terlambat → Hadir (tetap hadir, hanya terlambat)
        DB::statement("UPDATE presensis SET status_kehadiran = 'Hadir' WHERE status_kehadiran = 'Terlambat'");
        // Alfa → hapus record (Alfa = tidak hadir = tidak ada record di sistem baru)
        DB::statement("DELETE FROM presensis WHERE status_kehadiran = 'Alfa'");

        // Perbaiki tipe kolom GPS: string → DECIMAL
        DB::statement('ALTER TABLE presensis MODIFY COLUMN latitude DECIMAL(10,8) NULL');
        DB::statement('ALTER TABLE presensis MODIFY COLUMN longitude DECIMAL(11,8) NULL');

        // Buat foto_wajah nullable (override manual tidak punya foto selfie)
        DB::statement('ALTER TABLE presensis MODIFY COLUMN foto_wajah VARCHAR(255) NULL');

        // Seragamkan enum: hapus Terlambat & Alfa yang tidak dipakai
        DB::statement("ALTER TABLE presensis MODIFY COLUMN status_kehadiran ENUM('Hadir','Sakit','Izin') NOT NULL DEFAULT 'Hadir'");

        // Tambah kolom untuk validasi API dan audit
        Schema::table('presensis', function (Blueprint $table) {
            $table->decimal('jarak_meter', 8, 2)->nullable()->after('longitude');
            $table->decimal('face_confidence', 5, 2)->nullable()->after('foto_wajah');
            $table->boolean('face_verified')->default(false)->after('face_confidence');
            $table->string('alasan_ditolak', 100)->nullable()->after('face_verified');
            $table->foreignId('override_by')->nullable()->after('alasan_ditolak')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->dropForeign(['override_by']);
            $table->dropColumn(['jarak_meter', 'face_confidence', 'face_verified', 'alasan_ditolak', 'override_by']);
        });

        DB::statement('ALTER TABLE presensis MODIFY COLUMN latitude VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE presensis MODIFY COLUMN longitude VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE presensis MODIFY COLUMN foto_wajah VARCHAR(255) NOT NULL');
        DB::statement("ALTER TABLE presensis MODIFY COLUMN status_kehadiran ENUM('Hadir','Terlambat','Izin','Sakit','Alfa') NOT NULL DEFAULT 'Hadir'");
    }
};
