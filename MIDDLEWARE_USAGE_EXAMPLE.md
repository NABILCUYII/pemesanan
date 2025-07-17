# Contoh Penggunaan Middleware NoPermission

## Deskripsi

Middleware `NoPermissionMiddleware` dapat digunakan untuk menangani user yang belum memiliki izin masuk atau user dengan role yang tidak sesuai untuk mengakses halaman tertentu.

## Cara Penggunaan

### 1. Penggunaan di Route Group

```php
// Di routes/web.php

// Route untuk admin yang memerlukan permission khusus
Route::middleware(['auth', 'no.permission'])->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('stok-log', [StokLogController::class, 'index'])->name('stok-log.index');
    Route::get('video-berita', [VideoBeritaController::class, 'index'])->name('video-berita.index');
});
```

### 2. Penggunaan di Route Individual

```php
// Di routes/web.php

Route::get('admin/dashboard', [AdminController::class, 'dashboard'])
    ->middleware(['auth', 'no.permission'])
    ->name('admin.dashboard');
```

### 3. Penggunaan di Controller

```php
// Di controller
public function __construct()
{
    $this->middleware('no.permission');
}

// Atau untuk method tertentu
public function adminOnly()
{
    $this->middleware('no.permission');
    
    // Logic untuk admin
}
```

## Kapan Middleware Ini Digunakan

### 1. User dengan Role 'newUser'
User yang baru registrasi dan belum disetujui oleh admin akan diarahkan ke halaman NoPermission.

### 2. User dengan Role yang Tidak Sesuai
User yang mencoba mengakses halaman yang memerlukan role tertentu (misalnya admin) akan diarahkan ke halaman NoPermission.

## Mapping Role yang Sudah Dikonfigurasi

Middleware ini sudah dikonfigurasi untuk route-route berikut:

| Route Name | Required Role | Keterangan |
|------------|---------------|------------|
| `users.*` | `admin` | Semua route untuk manajemen user |
| `laporan.*` | `admin` | Semua route untuk laporan |
| `stok-log.*` | `admin` | Semua route untuk stok log |
| `video-berita.*` | `admin` | Semua route untuk video berita |

## Menambahkan Route Baru

Untuk menambahkan route baru yang memerlukan permission khusus, edit file `NoPermissionMiddleware.php`:

```php
private function getRequiredRole(Request $request): ?string
{
    $routeName = $request->route()->getName();
    
    $roleMapping = [
        // Route yang sudah ada
        'users.index' => 'admin',
        'users.create' => 'admin',
        
        // Route baru
        'my-new-route' => 'admin',
        'another-route' => 'user',
    ];
    
    return $roleMapping[$routeName] ?? null;
}
```

## Testing

### 1. Test dengan User 'newUser'
```bash
# Login dengan user yang memiliki role 'newUser'
# Kemudian akses halaman yang memerlukan permission
http://localhost/users
# Akan diarahkan ke halaman NoPermission
```

### 2. Test dengan User 'user' yang Mencoba Akses Halaman Admin
```bash
# Login dengan user yang memiliki role 'user'
# Kemudian akses halaman admin
http://localhost/laporan
# Akan diarahkan ke halaman NoPermission
```

### 3. Test dengan User 'admin'
```bash
# Login dengan user yang memiliki role 'admin'
# Kemudian akses halaman admin
http://localhost/users
# Akan berhasil diakses
```

## Integrasi dengan Middleware Lain

### 1. Kombinasi dengan AdminMiddleware
```php
Route::middleware(['auth', 'admin', 'no.permission'])->group(function () {
    // Route yang memerlukan admin dan permission khusus
});
```

### 2. Kombinasi dengan NewUserBlockMiddleware
```php
Route::middleware(['auth', 'newuser.block', 'no.permission'])->group(function () {
    // Route yang memblokir newUser dan memerlukan permission khusus
});
```

## Customization

### 1. Mengubah Pesan Error
Edit file `NoPermissionMiddleware.php` untuk mengubah pesan atau logic:

```php
// Mengubah kondisi untuk menampilkan halaman NoPermission
if ($user->role && $user->role->role === 'newUser') {
    // Custom logic
    return Inertia::render('NoPermission', [
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role->role ?? 'User'
        ],
        'message' => 'Akun Anda masih dalam proses verifikasi'
    ]);
}
```

### 2. Menambahkan Logging
```php
// Di middleware
if ($user->role && $user->role->role === 'newUser') {
    \Log::warning('User tanpa permission mencoba akses', [
        'user_id' => $user->id,
        'user_name' => $user->name,
        'route' => $request->route()->getName(),
        'url' => $request->url()
    ]);
    
    return Inertia::render('NoPermission', [
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role->role ?? 'User'
        ]
    ]);
}
```

## Best Practices

1. **Gunakan untuk Route Sensitif:** Terapkan middleware ini untuk route yang memerlukan permission khusus
2. **Kombinasikan dengan Middleware Lain:** Gunakan bersama middleware auth dan admin untuk keamanan maksimal
3. **Logging:** Tambahkan logging untuk monitoring akses yang ditolak
4. **Pesan yang Jelas:** Pastikan halaman NoPermission memberikan informasi yang jelas kepada user
5. **Testing:** Test dengan berbagai role user untuk memastikan middleware berfungsi dengan benar

## Troubleshooting

### Middleware tidak berfungsi
- Pastikan middleware sudah terdaftar di `app/Http/Kernel.php`
- Clear cache dengan `php artisan config:clear`
- Periksa apakah route name sudah benar di mapping

### User masih bisa akses halaman terlarang
- Periksa apakah user memiliki role yang benar di database
- Pastikan logic di middleware sudah sesuai dengan struktur role
- Test dengan user yang berbeda

### Halaman NoPermission tidak muncul
- Pastikan file `NoPermission.vue` sudah ada
- Periksa apakah route `error.no-permission` sudah terdaftar
- Clear cache dengan `php artisan route:clear` 