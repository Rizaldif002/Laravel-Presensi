<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nim')->nullable()->unique()->after('email');
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'dosen', 'mahasiswa') NOT NULL DEFAULT 'dosen'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'dosen') NOT NULL DEFAULT 'dosen'");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nim');
        });
    }
};
