<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\SelamatDatangController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rute SelamatDatang
Route::middleware(['auth'])->group(function () {
    Route::get('selamat-datang', [SelamatDatangController::class, 'index'])->name('SelamatDatang');
});

Route::middleware(['auth'])->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

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

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
