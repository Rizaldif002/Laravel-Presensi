<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\MataKuliahController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\RuanganController;
use App\Http\Controllers\Admin\DosenController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\KelasPerkuliahanController;
use App\Http\Controllers\Admin\JadwalPerkuliahanController;
use App\Http\Controllers\Admin\SesiPresensiController;
use App\Http\Controllers\Dosen\SesiPresensiController as DosenSesiController;
use App\Http\Controllers\Admin\RiwayatPresensiController as AdminRiwayatController;
use App\Http\Controllers\Admin\LaporanPresensiController;
use App\Http\Controllers\Admin\PesertaKelasController;
use App\Http\Controllers\Dosen\RiwayatPresensiController as DosenRiwayatController;

// ==========================================
// 1. Halaman Depan (Public)
// ==========================================
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ==========================================
// 2. AREA BERSAMA (Admin & Dosen)
// ==========================================
Route::middleware(['auth', 'role:admin,dosen'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/foto', [ProfileController::class, 'updatePhoto'])->name('profile.update-foto');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// 3. AREA ADMIN
// ==========================================
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {

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

    // Ruangan (Geofencing)
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

    // Kelas & Jadwal Perkuliahan
    Route::post('/kelas/import', [KelasPerkuliahanController::class, 'import'])->name('admin.kelas.import');
    Route::resource('kelas', KelasPerkuliahanController::class)->names('admin.kelas');
    Route::resource('jadwal', JadwalPerkuliahanController::class)->names('admin.jadwal');

    // Peserta Kelas (nested under kelas)
    Route::prefix('kelas/{kelas}')->name('admin.kelas.peserta.')->group(function () {
        Route::get('peserta',              [PesertaKelasController::class, 'index'])->name('index');
        Route::post('peserta',             [PesertaKelasController::class, 'store'])->name('store');
        Route::post('peserta/import',      [PesertaKelasController::class, 'import'])->name('import');
        Route::delete('peserta/{peserta_id}', [PesertaKelasController::class, 'destroy'])->name('destroy');
    });

    // Riwayat & Laporan
    Route::get('/riwayat-presensi', [AdminRiwayatController::class, 'index'])->name('admin.riwayat.index');
    Route::get('/riwayat-presensi/{kelas}', [AdminRiwayatController::class, 'show'])->name('admin.riwayat.show');
    Route::post('riwayat/override', [AdminRiwayatController::class, 'overridePresensi'])->name('admin.riwayat.override');
    Route::get('/laporan-presensi', [LaporanPresensiController::class, 'index'])->name('admin.laporan.presensi');
    Route::get('/laporan-presensi/{kelas}/pdf', [LaporanPresensiController::class, 'exportPdf'])->name('admin.laporan.pdf');

    // Sesi Presensi (untuk admin monitor)
    Route::get('/sesi-presensi', [SesiPresensiController::class, 'index'])->name('admin.sesi.index');
    Route::post('/sesi-presensi/buka', [SesiPresensiController::class, 'store'])->name('admin.sesi.buka');
    Route::get('/sesi-presensi/{id}/live', [SesiPresensiController::class, 'show'])->name('admin.sesi.live');
    Route::post('/sesi-presensi/{id}/tutup', [SesiPresensiController::class, 'tutup'])->name('admin.sesi.tutup');
});

// ==========================================
// 4. AREA DOSEN
// ==========================================
Route::prefix('dosen')->middleware(['auth', 'role:dosen'])->name('dosen.')->group(function () {
    Route::get('/sesi',                       [DosenSesiController::class, 'index'])->name('sesi.index');
    Route::post('/sesi',                      [DosenSesiController::class, 'store'])->name('sesi.store');
    Route::get('/sesi/{sesi}',                [DosenSesiController::class, 'show'])->name('sesi.show');
    Route::post('/sesi/{sesi}/tutup',         [DosenSesiController::class, 'tutup'])->name('sesi.tutup');
    Route::get('/sesi/{sesi}/live',           [DosenSesiController::class, 'live'])->name('sesi.live');
    Route::get('/sesi/{sesi}/live-data',      [DosenSesiController::class, 'liveData'])->name('sesi.live-data');
    Route::get('/riwayat-presensi',              [DosenRiwayatController::class, 'index'])->name('riwayat.index');
    Route::post('/riwayat-presensi/override',    [DosenRiwayatController::class, 'overridePresensi'])->name('riwayat.override');
    Route::get('/riwayat-presensi/{kelas}',      [DosenRiwayatController::class, 'show'])->name('riwayat.show');
    Route::get('/riwayat-presensi/{kelas}/pdf',  [DosenRiwayatController::class, 'exportPdf'])->name('riwayat.pdf');
});

require __DIR__ . '/auth.php';
