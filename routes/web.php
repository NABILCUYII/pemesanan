<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\SelamatDatangController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\StokLogController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Rute SelamatDatang
Route::middleware(['auth'])->group(function () {
    Route::get('selamat-datang', [SelamatDatangController::class, 'index'])->name('selamat-datang.index');
});

// Rute Users
Route::middleware(['auth'])->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// Rute Barang
Route::middleware(['auth'])->group(function () {
    Route::get('barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::post('barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
    Route::put('barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');
    Route::get('barang/stok', [BarangController::class, 'stok'])->name('barang.stok');
    Route::post('barang/{barang}/add-stok', [BarangController::class, 'addStok'])->name('barang.add-stok');
});

// Rute Permintaan
Route::middleware(['auth'])->group(function () {
    Route::get('permintaan', [PermintaanController::class, 'index'])->name('permintaan.index');
    Route::get('permintaan/create', [PermintaanController::class, 'create'])->name('permintaan.create');
    Route::post('permintaan', [PermintaanController::class, 'store'])->name('permintaan.store');
    Route::post('permintaan/multiple', [PermintaanController::class, 'storeMultiple'])->name('permintaan.store-multiple');
    Route::get('permintaan/approval', [PermintaanController::class, 'approval'])->name('permintaan.approval');
    Route::post('permintaan/approve', [PermintaanController::class, 'approve'])->name('permintaan.approve');
    Route::get('permintaan/{permintaan}/edit', [PermintaanController::class, 'edit'])->name('permintaan.edit');
    Route::put('permintaan/{permintaan}', [PermintaanController::class, 'update'])->name('permintaan.update');
    Route::delete('permintaan/{permintaan}', [PermintaanController::class, 'destroy'])->name('permintaan.destroy');
});

// Rute Peminjaman
Route::middleware(['auth'])->group(function () {
    Route::get('peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::post('peminjaman/approve', [PeminjamanController::class, 'approve'])->name('peminjaman.approve');
    Route::get('peminjaman/{peminjaman}/edit', [PeminjamanController::class, 'edit'])->name('peminjaman.edit');
    Route::put('peminjaman/{peminjaman}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
    Route::delete('peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');
    Route::get('peminjaman/{peminjaman}/return', [PeminjamanController::class, 'return'])->name('peminjaman.return');
    Route::post('peminjaman/{peminjaman}/return', [PeminjamanController::class, 'processReturn'])->name('peminjaman.process-return');
    Route::get('peminjaman-returns', [PeminjamanController::class, 'returns'])->name('peminjaman.returns');
    Route::get('/peminjaman/returns', [PeminjamanController::class, 'returns'])->name('peminjaman.returns');
    Route::post('/peminjaman/{peminjaman}/return', [PeminjamanController::class, 'processReturn'])->name('peminjaman.process-return');
});

// Rute Riwayat
Route::middleware(['auth'])->group(function () {
    Route::get('riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
});

// Rute Laporan
Route::middleware(['auth'])->group(function () {
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/download', [LaporanController::class, 'download'])->name('laporan.download');
    Route::get('laporan/download-user', [LaporanController::class, 'downloadUser'])->name('laporan.download-user');
    Route::get('laporan/test-pdf', [LaporanController::class, 'testPdf'])->name('laporan.test-pdf');
});

// Rute Stok Log
Route::middleware(['auth'])->group(function () {
    Route::get('stok-log', [StokLogController::class, 'index'])->name('stok-log.index');
    Route::get('stok-log/{id}', [StokLogController::class, 'show'])->name('stok-log.show');
    Route::get('stok-log/barang/{barangId}', [StokLogController::class, 'barang'])->name('stok-log.barang');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
