<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\KelasPerkuliahanController;
use App\Http\Controllers\JadwalPerkuliahanController;
use App\Http\Controllers\SesiPresensiController;

// 1. Route Halaman Depan
Route::get('/', function () {
    return view('welcome');
})->name('home');

// 2. Dashboard Utama (Pusat Komando Admin)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 3. Profil User Login (Bawaan Laravel)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// 4. AREA ADMIN (Data Master & Akademik)
// ==========================================
Route::prefix('admin')->middleware(['auth'])->group(function () {

    // Manajemen Dosen
    Route::get('/dosen', [DosenController::class, 'index'])->name('admin.dosen');
    Route::post('/dosen', [DosenController::class, 'store'])->name('admin.dosen.store');
    Route::put('/dosen/{id}', [DosenController::class, 'update'])->name('admin.dosen.update');
    Route::delete('/dosen/{id}', [DosenController::class, 'destroy'])->name('admin.dosen.destroy');
    Route::post('/dosen/import', [DosenController::class, 'import'])->name('admin.dosen.import');

    // Manajemen Mahasiswa
    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('admin.mahasiswa');
    Route::post('/mahasiswa', [MahasiswaController::class, 'store'])->name('admin.mahasiswa.store');
    Route::put('/mahasiswa/{id}', [MahasiswaController::class, 'update'])->name('admin.mahasiswa.update');
    Route::delete('/mahasiswa/{id}', [MahasiswaController::class, 'destroy'])->name('admin.mahasiswa.destroy');
    Route::post('/mahasiswa/import', [MahasiswaController::class, 'import'])->name('admin.mahasiswa.import');

    // Mata Kuliah
    Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])->name('admin.mata-kuliah');
    Route::post('/mata-kuliah', [MataKuliahController::class, 'store'])->name('admin.mata-kuliah.store');
    Route::put('/mata-kuliah/{id}', [MataKuliahController::class, 'update'])->name('admin.mata-kuliah.update');
    Route::delete('/mata-kuliah/{id}', [MataKuliahController::class, 'destroy'])->name('admin.mata-kuliah.destroy');
    Route::post('/mata-kuliah/import', [MataKuliahController::class, 'import'])->name('admin.mata-kuliah.import');

    // Data Ruangan (Geofencing)
    Route::get('/ruangan', [RuanganController::class, 'index'])->name('admin.ruangan');
    Route::post('/ruangan', [RuanganController::class, 'store'])->name('admin.ruangan.store');
    Route::put('/ruangan/{id}', [RuanganController::class, 'update'])->name('admin.ruangan.update');
    Route::delete('/ruangan/{id}', [RuanganController::class, 'destroy'])->name('admin.ruangan.destroy');

    // Tahun Ajaran
    Route::get('/tahun-ajaran', [TahunAjaranController::class, 'index'])->name('admin.tahun-ajaran');
    Route::post('/tahun-ajaran', [TahunAjaranController::class, 'store'])->name('admin.tahun-ajaran.store');
    Route::put('/tahun-ajaran/{id}', [TahunAjaranController::class, 'update'])->name('admin.tahun-ajaran.update');
    Route::delete('/tahun-ajaran/{id}', [TahunAjaranController::class, 'destroy'])->name('admin.tahun-ajaran.destroy');
    Route::post('/tahun-ajaran/{id}/aktif', [TahunAjaranController::class, 'setAktif'])->name('admin.tahun-ajaran.aktif');

    // ==========================================
    // REVISI: Manajemen Kelas & Jadwal (Clean Code)
    // ==========================================
    
    // Kelas Perkuliahan (Wadah)
    Route::post('/kelas/import', [KelasPerkuliahanController::class, 'import'])->name('admin.kelas.import');
    Route::resource('kelas', KelasPerkuliahanController::class)->names('admin.kelas');

    // Jadwal Perkuliahan (Waktu & Tempat)
    Route::resource('jadwal', JadwalPerkuliahanController::class)->names('admin.jadwal');


    // Riwayat Presensi Akademik (Placeholder)
    Route::get('/riwayat-presensi', function () {
        return 'Halaman Riwayat Presensi (Sedang Dibangun)';
    })->name('admin.riwayat-presensi');

    // Laporan Presensi (Placeholder)
    Route::get('/laporan-presensi', function () {
        return 'Halaman Laporan Presensi (Sedang Dibangun)';
    })->name('admin.laporan.presensi');

    // ==========================================
    // SISTEM SESI PRESENSI (GEOFENCING & KAMERA)
    // ==========================================
    Route::post('/sesi-presensi/buka', [SesiPresensiController::class, 'store'])->name('admin.sesi.buka');
    Route::get('/sesi-presensi/{id}/live', [SesiPresensiController::class, 'show'])->name('admin.sesi.live');
    Route::post('/sesi-presensi/{id}/tutup', [SesiPresensiController::class, 'tutup'])->name('admin.sesi.tutup');
});

require __DIR__ . '/auth.php';