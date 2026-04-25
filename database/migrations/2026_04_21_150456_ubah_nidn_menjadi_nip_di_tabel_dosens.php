<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dosens', function (Blueprint $table) {
            // Perintah untuk mengganti nama kolom
            $table->renameColumn('nidn', 'nip');
        });
    }

    public function down(): void
    {
        Schema::table('dosens', function (Blueprint $table) {
            // Perintah untuk mengembalikan nama kolom jika di-rollback
            $table->renameColumn('nip', 'nidn');
        });
    }
};