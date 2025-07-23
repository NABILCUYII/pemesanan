<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangRusakController;
use App\Http\Controllers\PermintaanController; 
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\SelamatDatangController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\StokLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\VideoBeritaController;
use App\Http\Middleware\PenggunaBaruBlockMiddleware as PenggunaBaruBlock;


// Route utama
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Dashboard hanya untuk user yang sudah diverifikasi (bukan pengguna baru)
Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', PenggunaBaruBlock::class])
    ->name('dashboard');

// Rute SelamatDatang (semua user yang sudah login)
Route::middleware(['auth'])->group(function () {
    Route::get('selamat-datang', [SelamatDatangController::class, 'index'])->name('selamat-datang.index');
});

// Rute Users (hanya untuk user yang sudah diverifikasi, bukan pengguna baru)
Route::middleware(['auth', PenggunaBaruBlock::class])->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// Rute Barang (hanya untuk user yang sudah diverifikasi, bukan pengguna baru)
Route::middleware(['auth', PenggunaBaruBlock::class])->group(function () {
    Route::get('barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('barang/aset', [BarangController::class, 'aset'])->name('barang.aset');
    Route::get('barang/permintaan', [BarangController::class, 'permintaan'])->name('barang.permintaan');
    Route::get('barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::post('barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
    Route::put('barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');
    Route::get('barang/stok', [BarangController::class, 'stok'])->name('barang.stok');
    Route::post('barang/{barang}/add-stok', [BarangController::class, 'addStok'])->name('barang.add-stok');
    Route::get('api/barang/stok-menipis-count', [BarangController::class, 'stokMenipisCount']);
    Route::get('api/barang/stok-habis-count', [BarangController::class, 'stokHabisCount']);
    Route::get('api/permintaan/pending-count', [PermintaanController::class, 'pendingCount']);
    Route::get('api/inventaris/pending-count', [InventarisController::class, 'pendingCount']);
    Route::get('barang/semua', [BarangController::class, 'semua'])->name('barang.semuaBRG');
});

// Rute Barang Rusak & Barang Hilang (hanya untuk user yang sudah diverifikasi, bukan pengguna baru)
Route::middleware(['auth', PenggunaBaruBlock::class])->group(function () {
    Route::get('barang-rusak', [BarangRusakController::class, 'index'])->name('barang-rusak.index');
    Route::get('barang-rusak/create', [BarangRusakController::class, 'create'])->name('barang-rusak.create');
    Route::post('barang-rusak', [BarangRusakController::class, 'store'])->name('barang-rusak.store');
    Route::put('barang-rusak/{barang}/status', [BarangRusakController::class, 'updateStatus'])->name('barang-rusak.update-status');
    // Rute Barang Hilang
    Route::get('barang-hilang/create', [BarangRusakController::class, 'createHilang'])->name('barang-hilang.create');
    Route::post('barang-hilang', [BarangRusakController::class, 'storeHilang'])->name('barang-hilang.store');
});

// Rute Permintaan (hanya untuk user yang sudah diverifikasi, bukan pengguna baru)
Route::middleware(['auth', PenggunaBaruBlock::class])->group(function () {
    Route::get('permintaan', [PermintaanController::class, 'index'])->name('permintaan.index');
    Route::get('permintaan/create', [PermintaanController::class, 'create'])->name('permintaan.create');
    Route::post('permintaan', [PermintaanController::class, 'store'])->name('permintaan.store');
    Route::post('permintaan/multiple', [PermintaanController::class, 'storeMultiple'])->name('permintaan.store-multiple');
    Route::get('permintaan/approval', [PermintaanController::class, 'approval'])->name('permintaan.approval');
    Route::post('permintaan/approve', [PermintaanController::class, 'approve'])->name('permintaan.approve');
    Route::patch('permintaan/{permintaan}/complete', [PermintaanController::class, 'complete'])->name('permintaan.complete');
    Route::get('permintaan/{permintaan}/edit', [PermintaanController::class, 'edit'])->name('permintaan.edit');
    Route::put('permintaan/{permintaan}', [PermintaanController::class, 'update'])->name('permintaan.update');
    Route::delete('permintaan/{permintaan}', [PermintaanController::class, 'destroy'])->name('permintaan.destroy');
});

// Rute Peminjaman (hanya untuk user yang sudah diverifikasi, bukan pengguna baru)
Route::middleware(['auth', PenggunaBaruBlock::class])->group(function () {
    Route::get('peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::post('peminjaman/approve', [PeminjamanController::class, 'approve'])->name('peminjaman.approve');
    Route::get('peminjaman/{peminjaman}/edit', [PeminjamanController::class, 'edit'])->name('peminjaman.edit');
    Route::put('peminjaman/{peminjaman}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
    Route::delete('peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');
    Route::patch('peminjaman/{peminjaman}/start-progress', [PeminjamanController::class, 'startProgress'])->name('peminjaman.start-progress');
    Route::get('peminjaman/{peminjaman}/return', [PeminjamanController::class, 'return'])->name('peminjaman.return');
    Route::post('peminjaman/{peminjaman}/return', [PeminjamanController::class, 'processReturn'])->name('peminjaman.process-return');
    Route::get('peminjaman-returns', [PeminjamanController::class, 'returns'])->name('peminjaman.returns');
    Route::get('/peminjaman/returns', [PeminjamanController::class, 'returns'])->name('peminjaman.returns');
    Route::post('/peminjaman/{peminjaman}/return', [PeminjamanController::class, 'processReturn'])->name('peminjaman.process-return');
});

// Rute Inventaris (hanya untuk user yang sudah diverifikasi, bukan pengguna baru)
Route::middleware(['auth', PenggunaBaruBlock::class])->group(function () {
    Route::get('inventaris', [InventarisController::class, 'index'])->name('inventaris.index');
    Route::get('inventaris/create', [InventarisController::class, 'create'])->name('inventaris.create');
    Route::post('inventaris', [InventarisController::class, 'store'])->name('inventaris.store');
    Route::post('inventaris/approve', [InventarisController::class, 'approve'])->name('inventaris.approve');
    Route::get('inventaris/{inventaris}/edit', [InventarisController::class, 'edit'])->name('inventaris.edit');
    Route::put('inventaris/{inventaris}', [InventarisController::class, 'update'])->name('inventaris.update');
    Route::delete('inventaris/{inventaris}', [InventarisController::class, 'destroy'])->name('inventaris.destroy');
    Route::patch('inventaris/{inventaris}/start-progress', [InventarisController::class, 'startProgress'])->name('inventaris.start-progress');
    Route::get('inventaris/{inventaris}/return', [InventarisController::class, 'return'])->name('inventaris.return');
    Route::post('inventaris/{inventaris}/return', [InventarisController::class, 'processReturn'])->name('inventaris.process-return');
    Route::get('inventaris-returns', [InventarisController::class, 'returns'])->name('inventaris.returns');
    Route::get('inventaris/{inventaris}', [InventarisController::class, 'show'])->name('inventaris.show');
});

// Rute Riwayat (hanya untuk user yang sudah diverifikasi, bukan pengguna baru)
Route::middleware(['auth', PenggunaBaruBlock::class])->group(function () {
    Route::get('riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
});

// Rute Laporan (hanya untuk user yang sudah diverifikasi, bukan pengguna baru)
Route::middleware(['auth', PenggunaBaruBlock::class])->group(function () {
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/download', [LaporanController::class, 'download'])->name('laporan.download');
    Route::get('laporan/download-permintaan', [LaporanController::class, 'downloadPermintaan'])->name('laporan.download-permintaan');
    Route::get('laporan/download-peminjaman', [LaporanController::class, 'downloadPeminjaman'])->name('laporan.download-peminjaman');
    Route::get('laporan/download-user', [LaporanController::class, 'downloadUser'])->name('laporan.download-user');
    Route::get('laporan/download-excel', [LaporanController::class, 'downloadExcel'])->name('laporan.download-excel');
    Route::get('laporan/download-permintaan-excel', [LaporanController::class, 'downloadPermintaanExcel'])->name('laporan.download-permintaan-excel');
    Route::get('laporan/download-peminjaman-excel', [LaporanController::class, 'downloadPeminjamanExcel'])->name('laporan.download-peminjaman-excel');
    Route::get('laporan/download-user-excel', [LaporanController::class, 'downloadUserExcel'])->name('laporan.download-user-excel');
    Route::get('laporan/test-pdf', [LaporanController::class, 'testPdf'])->name('laporan.test-pdf');
});

// Rute Stok Log (hanya untuk user yang sudah diverifikasi, bukan pengguna baru)
Route::middleware(['auth', PenggunaBaruBlock::class])->group(function () {
    Route::get('stok-log', [StokLogController::class, 'index'])->name('stok-log.index');
    Route::get('stok-log/{id}', [StokLogController::class, 'show'])->name('stok-log.show');
    Route::get('stok-log/barang/{barangId}', [StokLogController::class, 'barang'])->name('stok-log.barang');
});

// Rute Video Berita (hanya untuk user yang sudah diverifikasi, bukan pengguna baru)
Route::middleware(['auth', PenggunaBaruBlock::class])->group(function () {
    Route::get('video-berita', [VideoBeritaController::class, 'index'])->name('video-berita.index');
    Route::get('video-berita/create', [VideoBeritaController::class, 'create'])->name('video-berita.create');
    Route::post('video-berita', [VideoBeritaController::class, 'store'])->name('video-berita.store');
    Route::get('video-berita/{videoBerita}/edit', [VideoBeritaController::class, 'edit'])->name('video-berita.edit');
    Route::put('video-berita/{videoBerita}', [VideoBeritaController::class, 'update'])->name('video-berita.update');
    Route::delete('video-berita/{videoBerita}', [VideoBeritaController::class, 'destroy'])->name('video-berita.destroy');
    Route::get('api/video-berita/active', [VideoBeritaController::class, 'getActiveVideos'])->name('video-berita.active');
});

// Video Berita Routes (2024) (hanya untuk user yang sudah diverifikasi, bukan pengguna baru)
Route::middleware(['auth', PenggunaBaruBlock::class])->group(function () {
    Route::get('/video-berita', [VideoBeritaController::class, 'index'])->name('video-berita.index');
    Route::get('/video-berita/create', [VideoBeritaController::class, 'create'])->name('video-berita.create');
    Route::post('/video-berita', [VideoBeritaController::class, 'store'])->name('video-berita.store');
    Route::get('/video-berita/{videoBerita}/edit', [VideoBeritaController::class, 'edit'])->name('video-berita.edit');
    Route::put('/video-berita/{videoBerita}', [VideoBeritaController::class, 'update'])->name('video-berita.update');
    Route::delete('/video-berita/{videoBerita}', [VideoBeritaController::class, 'destroy'])->name('video-berita.destroy');
    Route::patch('/video-berita/{videoBerita}/toggle-status', [VideoBeritaController::class, 'toggleStatus'])->name('video-berita.toggle-status');
});

// Footer Pages Routes (semua user yang sudah login)
Route::middleware(['auth'])->group(function () {
    Route::get('/privacy', function () {
        return Inertia::render('Privacy');
    })->name('privacy');
    
    Route::get('/terms', function () {
        return Inertia::render('Terms');
    })->name('terms');
    
    Route::get('/help', function () {
        return Inertia::render('Help');
    })->name('help');
    
    Route::get('/contact', function () {
        return Inertia::render('Contact');
    })->name('contact');
});

// Rute Halaman Informasi (semua user yang sudah login)
Route::middleware(['auth'])->group(function () {
    Route::get('tentang', function () {
        return Inertia::render('Tentang');
    })->name('tentang');
    
    Route::get('bantuan', function () {
        return Inertia::render('Bantuan');
    })->name('bantuan');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Error routes (tidak perlu middleware, biarkan terbuka)
Route::get('/404', [ErrorController::class, 'notFound'])->name('error.404');
Route::get('/403', [ErrorController::class, 'unauthorized'])->name('error.403');
Route::get('/forbidden', [ErrorController::class, 'forbidden'])->name('error.forbidden');
Route::get('/500', [ErrorController::class, 'serverError'])->name('error.500');
Route::get('/loading', [ErrorController::class, 'loading'])->name('error.loading');

// Test page untuk error pages
Route::get('/test-error-pages', function () {
    return Inertia::render('TestErrorPages');
})->name('test.error.pages');

// Test page untuk 403 error handling
Route::get('/test-403-page', function () {
    return Inertia::render('Test403');
})->name('test.403.page');

// Test routes untuk error handling
Route::get('/test-403', function () {
    abort(403, 'Access denied');
})->name('test.403');

Route::get('/test-403-auth', function () {
    throw new \Illuminate\Auth\Access\AuthorizationException('You are not authorized to access this page.');
})->name('test.403.auth');

Route::get('/test-403-http', function () {
    throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied');
})->name('test.403.http');
 
Route::get('/test-404', function () {
    abort(404, 'Page not found');
})->name('test.404');

Route::get('/test-500', function () {
    abort(500, 'Server error');
})->name('test.500');

// Fallback route untuk 404 - harus di akhir file
Route::fallback(function () {
    return Inertia::render('NotFound');
});

// Rute khusus untuk pengguna baru (hanya untuk user yang statusnya pengguna baru)
Route::middleware(['auth', PenggunaBaruBlock::class])->group(function () {
    Route::get('/users/newuser', [UserController::class, 'newuser'])->name('users.NEWuser');
    Route::get('/api/users/new', [UserController::class, 'getNewUsers']);
    Route::post('/users/approve/{id}', [UserController::class, 'approveNewUser'])->name('users.approve');
    Route::post('/users/reject/{id}', [UserController::class, 'rejectNewUser'])->name('users.reject');
});

// Route API users yang tidak perlu onlyPenggunaBaru/PenggunaBaruBlock::class
Route::get('api/users/{id}', [UserController::class, 'showApi']);
Route::get('/api/users/all', [UserController::class, 'getAllUsers']);
