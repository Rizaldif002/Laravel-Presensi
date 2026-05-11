<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sesi_presensis', function (Blueprint $table) {
            $table->string('nama_pertemuan')->nullable()->after('jadwal_perkuliahan_id');
            $table->boolean('is_gps_enabled')->default(true)->after('nama_pertemuan');
            $table->text('gps_reason')->nullable()->after('is_gps_enabled');
            $table->foreignId('dibuka_oleh')->nullable()->after('gps_reason')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sesi_presensis', function (Blueprint $table) {
            $table->dropForeign(['dibuka_oleh']);
            $table->dropColumn(['nama_pertemuan', 'is_gps_enabled', 'gps_reason', 'dibuka_oleh']);
        });
    }
};
