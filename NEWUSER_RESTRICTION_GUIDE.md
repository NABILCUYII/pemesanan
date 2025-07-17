# Panduan Pembatasan Akses User NewUser

## Deskripsi

Sistem ini membatasi akses user dengan role 'newUser' agar hanya bisa mengakses halaman tertentu dan tidak bisa mengakses fitur-fitur utama aplikasi.

## 🔒 Pembatasan Akses

### User dengan Role 'newUser' HANYA BISA AKSES:

1. **Halaman Selamat Datang** (`selamat-datang.index`)
2. **Logout** (`logout`)
3. **Profile Settings** (`profile.edit`, `profile.update`)
4. **Password Settings** (`password.edit`, `password.update`)
5. **Settings Pages** (`settings.profile`, `settings.password`)
6. **Informasi** (`tentang`, `bantuan`)
7. **Notifikasi** (`notifications`)

### User dengan Role 'newUser' TIDAK BISA AKSES:

1. **Dashboard** (`dashboard`)
2. **Manajemen User** (`users.*`)
3. **Manajemen Barang** (`barang.*`)
4. **Peminjaman** (`peminjaman.*`)
5. **Permintaan** (`permintaan.*`)
6. **Inventaris** (`inventaris.*`)
7. **Barang Rusak** (`barang-rusak.*`)
8. **Laporan** (`laporan.*`)
9. **Stok Log** (`stok-log.*`)
10. **Video Berita** (`video-berita.*`)
11. **Riwayat** (`riwayat.*`)

## 🔧 Cara Kerja

### 1. Middleware NewUserBlockMiddleware

**File:** `app/Http/Middleware/NewUserBlockMiddleware.php`

Middleware ini akan:
- Mengecek apakah user memiliki role 'newUser'
- Jika ya, hanya mengizinkan akses ke route yang ada di `$allowedRoutes`
- Jika mencoba akses route terlarang:
  - Untuk Inertia requests: Menampilkan halaman `NoPermission.vue`
  - Untuk non-Inertia requests: Redirect ke `selamat-datang.index`

### 2. Route Configuration

**File:** `routes/web.php`

```php
// Route khusus untuk newUser (tanpa pembatasan)
Route::middleware(['auth'])->group(function () {
    Route::get('selamat-datang', [SelamatDatangController::class, 'index'])->name('selamat-datang.index');
});

// Semua route lain dengan pembatasan newUser
Route::middleware(['auth', 'newuser.block'])->group(function () {
    // Semua route yang dibatasi
});
```

## 🎯 Tujuan Pembatasan

### 1. Keamanan
- Mencegah user baru mengakses fitur yang belum seharusnya
- Memastikan user sudah disetujui admin sebelum bisa menggunakan sistem

### 2. User Experience
- Memberikan halaman selamat datang yang informatif
- Menjelaskan status akun dan langkah selanjutnya
- Memudahkan admin untuk mengelola user baru

### 3. Workflow
- User baru registrasi → Role 'newUser'
- Admin review dan approve → Role 'user' atau 'admin'
- User bisa akses fitur sesuai role

## 🚀 Testing

### 1. Test dengan User 'newUser'
```bash
# Login dengan user yang memiliki role 'newUser'
# Kemudian coba akses halaman yang dibatasi:

http://localhost/dashboard          # Akan diarahkan ke NoPermission
http://localhost/users              # Akan diarahkan ke NoPermission
http://localhost/peminjaman         # Akan diarahkan ke NoPermission
http://localhost/permintaan         # Akan diarahkan ke NoPermission
http://localhost/inventaris         # Akan diarahkan ke NoPermission
http://localhost/barang             # Akan diarahkan ke NoPermission
http://localhost/laporan            # Akan diarahkan ke NoPermission

# Halaman yang bisa diakses:
http://localhost/selamat-datang     # ✅ Bisa diakses
http://localhost/tentang            # ✅ Bisa diakses
http://localhost/bantuan            # ✅ Bisa diakses
```

### 2. Test dengan User 'user' atau 'admin'
```bash
# Login dengan user yang memiliki role 'user' atau 'admin'
# Semua halaman bisa diakses normal
```

## 🔄 Flow User NewUser

### 1. Registrasi
```
User mendaftar → Role otomatis 'newUser'
```

### 2. Login Pertama
```
Login → Diarahkan ke selamat-datang
```

### 3. Mencoba Akses Fitur
```
Coba akses dashboard/peminjaman/dll → Halaman NoPermission
```

### 4. Admin Approval
```
Admin approve → Role berubah ke 'user' → Bisa akses fitur
```

## 📱 Halaman NoPermission untuk NewUser

Ketika newUser mencoba akses halaman terlarang, mereka akan melihat:

- **Pesan:** "Anda Belum Memiliki Izin Masuk"
- **Status:** "Menunggu persetujuan administrator"
- **Tombol:** Kembali, Beranda, Minta Izin Akses, Hubungi Administrator
- **Tips:** Cara mendapatkan akses

## 🔧 Kustomisasi

### 1. Menambah Route yang Diizinkan
Edit file `NewUserBlockMiddleware.php`:

```php
$allowedRoutes = [
    'selamat-datang.index',
    'logout',
    'profile.edit',
    'profile.update',
    'password.edit',
    'password.update',
    'settings.profile',
    'settings.password',
    'tentang',
    'bantuan',
    'notifications',
    'my-new-allowed-route'  // Tambahkan route baru
];
```

### 2. Mengubah Halaman Redirect
```php
// Untuk non-Inertia requests
return redirect()->route('selamat-datang.index');

// Atau redirect ke halaman lain
return redirect()->route('custom-welcome-page');
```

### 3. Menambah Logging
```php
if (!in_array($currentRoute, $allowedRoutes)) {
    \Log::warning('NewUser mencoba akses route terlarang', [
        'user_id' => $user->id,
        'user_name' => $user->name,
        'route' => $currentRoute,
        'url' => $request->url()
    ]);
    
    // ... rest of the code
}
```

## 🎨 Halaman Selamat Datang

Halaman `selamat-datang` sebaiknya berisi:

1. **Pesan Selamat Datang**
2. **Status Akun:** "Akun Anda sedang dalam proses verifikasi"
3. **Informasi:** Apa yang bisa dilakukan sementara
4. **Kontak Admin:** Cara menghubungi admin untuk approval
5. **Fitur yang Tersedia:** Halaman yang bisa diakses

## ✅ Status Implementasi

**BERHASIL DITERAPKAN!**

Sistem pembatasan akses untuk user newUser sudah aktif:

- ✅ Middleware `NewUserBlockMiddleware` sudah dikonfigurasi
- ✅ Route dibatasi dengan middleware `newuser.block`
- ✅ Halaman `NoPermission.vue` untuk feedback user
- ✅ Route `selamat-datang` tetap bisa diakses
- ✅ Admin dan user normal tidak terpengaruh

## 🚀 Langkah Selanjutnya

1. **Test dengan user newUser** untuk memastikan pembatasan berfungsi
2. **Kustomisasi halaman selamat-datang** sesuai kebutuhan
3. **Setup sistem approval** untuk mengubah role newUser
4. **Monitor log** untuk tracking akses yang ditolak
5. **Update dokumentasi** jika ada perubahan workflow 