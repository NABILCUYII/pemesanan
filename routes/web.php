<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\LaporanController;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin only routes
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    // User management
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Barang management
    Route::get('barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::post('barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
    Route::put('barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');
    Route::get('barang/stok', [BarangController::class, 'stok'])->name('barang.stok');
    Route::post('barang/{barang}/add-stok', [BarangController::class, 'addStok'])->name('barang.add-stok');

    // Approval routes
    Route::get('permintaan/approval', [PermintaanController::class, 'approval'])->name('permintaan.approval');
    Route::post('permintaan/approve', [PermintaanController::class, 'approve'])->name('permintaan.approve');
    Route::post('peminjaman/approve', [PeminjamanController::class, 'approve'])->name('peminjaman.approve');

    // Laporan routes
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
});

// Regular user routes
Route::middleware(['auth'])->group(function () {
    // Permintaan routes
    Route::get('permintaan', [PermintaanController::class, 'index'])->name('permintaan.index');
    Route::get('permintaan/create', [PermintaanController::class, 'create'])->name('permintaan.create');
    Route::post('permintaan', [PermintaanController::class, 'store'])->name('permintaan.store');
    Route::post('permintaan/multiple', [PermintaanController::class, 'storeMultiple'])->name('permintaan.store-multiple');
    Route::get('permintaan/{permintaan}/edit', [PermintaanController::class, 'edit'])->name('permintaan.edit');
    Route::put('permintaan/{permintaan}', [PermintaanController::class, 'update'])->name('permintaan.update');
    Route::delete('permintaan/{permintaan}', [PermintaanController::class, 'destroy'])->name('permintaan.destroy');

    // Peminjaman routes
    Route::get('peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::get('peminjaman/{peminjaman}/edit', [PeminjamanController::class, 'edit'])->name('peminjaman.edit');
    Route::put('peminjaman/{peminjaman}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
    Route::delete('peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');

    // Riwayat routes
    Route::get('riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::post('riwayat/cancel', [RiwayatController::class, 'cancel'])->name('riwayat.cancel');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
