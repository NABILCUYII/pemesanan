# Halaman No Permission - Anda Belum Memiliki Izin Masuk

## Deskripsi

Halaman `NoPermission.vue` adalah halaman khusus yang menampilkan pesan "Anda belum memiliki izin masuk" untuk user yang belum memiliki permission yang cukup untuk mengakses halaman tertentu.

## Fitur Utama

### 🎨 Desain Visual
- **Gradient Background:** Amber ke orange yang memberikan kesan peringatan namun tidak terlalu keras
- **Icon Kunci Terkunci:** Menggunakan SVG icon yang merepresentasikan akses yang dibatasi
- **Layout Responsif:** Menyesuaikan dengan berbagai ukuran layar
- **Shadow dan Border:** Memberikan kesan modern dan profesional

### 📱 Komponen UI
- **Pesan Utama:** "Anda Belum Memiliki Izin Masuk" dengan kode error 403
- **Kotak Informasi:** Menampilkan status akun user
- **Tombol Aksi:** Kembali, Beranda, Minta Izin Akses, Hubungi Administrator
- **Info Pengguna:** Menampilkan detail user yang sedang login
- **Tips:** Panduan untuk mendapatkan akses

### 🔧 Fungsi Interaktif
- **Navigasi:** Tombol kembali dan beranda
- **Request Access:** Tombol untuk meminta izin akses
- **Contact Admin:** Email template untuk menghubungi administrator
- **User Info:** Menampilkan informasi user yang sedang login

## Lokasi File

```
resources/js/pages/NoPermission.vue
```

## Cara Penggunaan

### 1. Melalui Route
```php
// Di routes/web.php
Route::get('/no-permission', [ErrorController::class, 'noPermission'])->name('error.no-permission');
```

### 2. Melalui Controller
```php
// Di controller
public function someMethod()
{
    if (!auth()->user()->hasPermission('admin')) {
        return Inertia::render('NoPermission', [
            'user' => [
                'id' => auth()->user()->id,
                'name' => auth()->user()->name,
                'role' => auth()->user()->role ?? 'User'
            ]
        ]);
    }
}
```

### 3. Melalui Middleware
```php
// Di middleware
if (!$user->hasPermission($requiredPermission)) {
    return Inertia::render('NoPermission', [
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role ?? 'User'
        ]
    ]);
}
```

## Props yang Diterima

```typescript
interface Props {
  user?: {
    id: number
    name: string
    role?: string
  }
}
```

## Testing

### 1. Test Route Langsung
```
http://localhost/no-permission
```

### 2. Test Route dengan Data User
```
http://localhost/test-no-permission
```

### 3. Test dari Halaman Test Error
```
http://localhost/test-error-pages
```
Kemudian klik tombol "Test No Permission"

## Kustomisasi

### 1. Mengubah Email Administrator
Edit bagian `contactAdmin()` di script:
```javascript
window.open(`mailto:your-admin@domain.com?subject=${subject}&body=${body}`, '_blank')
```

### 2. Mengubah Warna
Ganti class CSS untuk mengubah warna:
- Background: `from-amber-50 to-orange-100`
- Primary button: `bg-amber-600 hover:bg-amber-700`
- Info box: `bg-amber-50 border-amber-400`

### 3. Mengubah Pesan
Edit teks di template sesuai kebutuhan:
```vue
<h2 class="text-2xl font-semibold text-gray-800 mb-4">
  Pesan Kustom Anda
</h2>
```

### 4. Menambahkan Logo
```vue
<template>
  <div class="text-center">
    <img src="/logo.png" alt="Logo" class="w-32 h-32 mx-auto mb-8" />
    <!-- Existing content -->
  </div>
</template>
```

## Perbedaan dengan Halaman Error Lainnya

| Halaman | Tujuan | Warna | Keterangan |
|---------|--------|-------|------------|
| `Unauthorized.vue` | Akses ditolak | Merah-Pink | Untuk user yang tidak memiliki permission |
| `Forbidden.vue` | Akses dilarang | Merah-Oranye | Untuk user yang dilarang mengakses |
| `NoPermission.vue` | Belum ada izin | Amber-Oranye | Untuk user yang belum memiliki izin masuk |

## Integrasi dengan Sistem

### 1. Middleware Integration
Halaman ini dapat diintegrasikan dengan middleware permission:

```php
// Di middleware
if ($user->role === 'newUser' || !$user->hasPermission($requiredPermission)) {
    return Inertia::render('NoPermission', [
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role ?? 'User'
        ]
    ]);
}
```

### 2. Controller Integration
```php
// Di controller
public function adminOnly()
{
    if (!auth()->user()->hasRole('admin')) {
        return Inertia::render('NoPermission', [
            'user' => [
                'id' => auth()->user()->id,
                'name' => auth()->user()->name,
                'role' => auth()->user()->role ?? 'User'
            ]
        ]);
    }
    
    // Lanjutkan dengan logic admin
}
```

## Best Practices

1. **Gunakan untuk User Baru:** Tampilkan halaman ini untuk user yang baru registrasi dan belum disetujui
2. **Informasi Lengkap:** Selalu sertakan informasi user yang sedang login
3. **Email Template:** Gunakan email template yang informatif untuk admin
4. **Tips yang Berguna:** Berikan panduan yang jelas untuk user
5. **Responsive Design:** Pastikan halaman terlihat baik di semua device

## Troubleshooting

### Halaman tidak muncul
- Pastikan route sudah terdaftar di `web.php`
- Periksa apakah controller method `noPermission()` sudah ada
- Clear cache dengan `php artisan route:clear`

### Email tidak terbuka
- Pastikan browser mendukung `mailto:` protocol
- Ganti dengan redirect ke halaman contact jika diperlukan

### User info tidak muncul
- Pastikan user sudah login
- Periksa apakah data user dikirim dengan benar dari backend

## Status

✅ **BERHASIL DIBUAT!**

Halaman NoPermission sudah siap digunakan dan dapat diakses melalui:
- `/no-permission` - Halaman utama
- `/test-no-permission` - Halaman test dengan data user
- `/test-error-pages` - Halaman test semua error pages 