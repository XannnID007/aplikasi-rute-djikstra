<?php
// routes/web.php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DijkstraController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\LokasiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('beranda');

// Routes untuk pengguna yang sudah login
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard pengguna
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Routes pencarian rute
    Route::prefix('pencarian-rute')->name('pencarian-rute.')->group(function () {
        Route::get('/', [DijkstraController::class, 'index'])->name('index');
        Route::post('/cari', [DijkstraController::class, 'cariRute'])->name('cari');
        Route::get('/riwayat', [DijkstraController::class, 'riwayat'])->name('riwayat');
        Route::get('/riwayat/{pencarianRute}', [DijkstraController::class, 'detailRiwayat'])->name('detail-riwayat');
    });
});

// Routes untuk admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/pengguna', [AdminController::class, 'daftarPengguna'])->name('pengguna');
    Route::get('/riwayat-pencarian', [AdminController::class, 'riwayatPencarian'])->name('riwayat-pencarian');

    // Routes untuk manajemen lokasi
    Route::resource('lokasi', LokasiController::class);
    Route::get('/lokasi/{lokasi}/kelola-rute', [LokasiController::class, 'kelolaRute'])->name('lokasi.kelola-rute');
    Route::post('/lokasi/{lokasi}/simpan-rute', [LokasiController::class, 'simpanRute'])->name('lokasi.simpan-rute');
    Route::delete('/rute/{rute}', [LokasiController::class, 'hapusRute'])->name('rute.hapus');

    Route::post('/lokasi/auto-generate-routes', [LokasiController::class, 'autoGenerateRoutes'])->name('lokasi.auto-generate-routes');
});

require __DIR__ . '/auth.php';
