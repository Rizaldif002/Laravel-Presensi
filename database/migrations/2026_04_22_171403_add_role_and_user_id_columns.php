<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom 'role' di tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'dosen'])->default('dosen')->after('email');
        });

        // 2. Tambah kolom 'user_id' di tabel dosens untuk menautkan akun login ke profil dosen
        Schema::table('dosens', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('dosens', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};