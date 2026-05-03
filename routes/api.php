<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\JadwalApiController;
use App\Http\Controllers\Api\PresensiApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Sistem Presensi Hybrid (Flutter)
|--------------------------------------------------------------------------
| Semua response format: {"status": bool, "message": "...", "data": {...}}
| Auth: Laravel Sanctum token-based (Bearer token di header Authorization)
*/

Route::prefix('v1')->group(function () {

    // === PUBLIC ===
    Route::post('/login', [AuthApiController::class, 'login']);

    // === PROTECTED (Sanctum) ===
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/logout',  [AuthApiController::class, 'logout']);
        Route::get('/profile',  [AuthApiController::class, 'profile']);

        // Jadwal & Sesi
        Route::get('/jadwal/hari-ini',           [JadwalApiController::class, 'hariIni']);
        Route::get('/sesi/aktif/{kelas_id}',     [JadwalApiController::class, 'sesiAktif']);

        // Presensi
        Route::post('/presensi',                 [PresensiApiController::class, 'store']);
        Route::get('/presensi/riwayat',          [PresensiApiController::class, 'riwayat']);
    });
});
