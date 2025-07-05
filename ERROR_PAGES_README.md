# Halaman Error dan Loading

Dokumentasi untuk halaman error dan loading yang telah dibuat untuk aplikasi Laravel + Inertia.js.

## Status: ✅ BERHASIL DITERAPKAN

Halaman error dan loading sudah berhasil diintegrasikan dengan Laravel dan akan menggantikan halaman 404 default Laravel.

## Halaman yang Tersedia

### 1. Loading.vue
Halaman loading penuh dengan animasi spinner dan teks loading.

**Lokasi:** `resources/js/pages/Loading.vue`

**Fitur:**
- Spinner animasi dengan efek pulse
- Teks loading dalam bahasa Indonesia
- Animasi dots yang bergerak
- Auto redirect setelah 3 detik (opsional)

**Cara Penggunaan:**
```vue
<template>
  <Loading />
</template>

<script setup>
import Loading from '@/pages/Loading.vue'
</script>
```

### 2. Unauthorized.vue
Halaman 403 - Akses Ditolak untuk user yang tidak memiliki izin.

**Lokasi:** `resources/js/pages/Unauthorized.vue`

**Fitur:**
- Icon warning dengan animasi
- Pesan error yang jelas
- Tombol navigasi (Kembali, Beranda, Hubungi Admin)
- Tips untuk user
- Email template untuk menghubungi admin

### 3. Forbidden.vue
Halaman 403 - Akses Dilarang untuk user yang tidak memiliki permission khusus.

**Lokasi:** `resources/js/pages/Forbidden.vue`

**Fitur:**
- Icon forbidden dengan animasi
- Pesan error yang lebih spesifik
- Tombol navigasi (Kembali, Beranda, Minta Akses, Hubungi Support)
- Informasi detail mengapa akses dilarang
- Menampilkan info user yang sedang login
- Email template untuk request akses

**Cara Penggunaan:**
```vue
<template>
  <Unauthorized />
</template>

<script setup>
import Unauthorized from '@/pages/Unauthorized.vue'
</script>
```

**Cara Penggunaan:**
```vue
<template>
  <Forbidden />
</template>

<script setup>
import Forbidden from '@/pages/Forbidden.vue'
</script>
```

### 4. NotFound.vue
Halaman 404 - Halaman Tidak Ditemukan.

**Lokasi:** `resources/js/pages/NotFound.vue`

**Fitur:**
- Icon 404 dengan animasi
- Search box untuk mencari halaman
- Tombol navigasi (Kembali, Beranda, Bantuan)
- Quick links ke halaman populer
- Email template untuk support

**Cara Penggunaan:**
```vue
<template>
  <NotFound />
</template>

<script setup>
import NotFound from '@/pages/NotFound.vue'
</script>
```

### 5. ServerError.vue
Halaman 500 - Kesalahan Server.

**Lokasi:** `resources/js/pages/ServerError.vue`

**Fitur:**
- Icon error dengan animasi
- Tombol "Coba Lagi" untuk reload halaman
- Tombol navigasi (Kembali, Beranda, Laporkan Masalah)
- Status server indicator
- Error ID generator untuk tracking
- Email template dengan error ID

**Cara Penggunaan:**
```vue
<template>
  <ServerError />
</template>

<script setup>
import ServerError from '@/pages/ServerError.vue'
</script>
```

### 6. LoadingSpinner.vue
Komponen loading spinner yang dapat digunakan di seluruh aplikasi.

**Lokasi:** `resources/js/components/LoadingSpinner.vue`

**Props:**
- `show`: Boolean - Menampilkan/menyembunyikan spinner
- `text`: String - Teks loading (default: "Memuat...")
- `fullscreen`: Boolean - Fullscreen overlay atau tidak
- `showProgress`: Boolean - Menampilkan progress bar
- `progress`: Number - Nilai progress (0-100)

**Cara Penggunaan:**
```vue
<template>
  <!-- Basic usage -->
  <LoadingSpinner :show="isLoading" />
  
  <!-- With custom text -->
  <LoadingSpinner 
    :show="isLoading" 
    text="Menyimpan data..." 
  />
  
  <!-- With progress -->
  <LoadingSpinner 
    :show="isLoading" 
    :show-progress="true"
    :progress="uploadProgress"
    text="Mengupload file..."
  />
  
  <!-- Fullscreen -->
  <LoadingSpinner 
    :show="isLoading" 
    :fullscreen="true"
    text="Memuat aplikasi..."
  />
</template>

<script setup>
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import { ref } from 'vue'

const isLoading = ref(false)
const uploadProgress = ref(0)
</script>
```

## Integrasi dengan Laravel

### 1. Error Controller
**Lokasi:** `app/Http/Controllers/ErrorController.php`

Controller untuk menangani semua halaman error:
```php
// Menampilkan halaman 404
Route::get('/404', [ErrorController::class, 'notFound']);

// Menampilkan halaman 403
Route::get('/403', [ErrorController::class, 'unauthorized']);

// Menampilkan halaman forbidden
Route::get('/forbidden', [ErrorController::class, 'forbidden']);

// Menampilkan halaman 500
Route::get('/500', [ErrorController::class, 'serverError']);

// Menampilkan halaman loading
Route::get('/loading', [ErrorController::class, 'loading']);
```

### 2. Fallback Route untuk 404
**Lokasi:** `routes/web.php`

Route fallback yang akan menangani semua URL yang tidak ditemukan:
```php
// Fallback route untuk 404 - harus di akhir file
Route::fallback(function () {
    return Inertia::render('NotFound');
});
```

### 3. Exception Handler
**Lokasi:** `app/Exceptions/Handler.php`

Handler untuk menangani error 500 dan exception lainnya:
```php
public function register(): void
{
    $this->renderable(function (Throwable $e, Request $request) {
        if ($request->header('X-Inertia')) {
            // Untuk Inertia requests, render halaman 500 custom
            return Inertia::render('ServerError');
        }
    });
}
```

### 4. Middleware untuk Unauthorized
**Lokasi:** `app/Http/Middleware/HandleUnauthorized.php`

Middleware untuk menangani akses yang tidak diizinkan:
```php
public function handle(Request $request, Closure $next): Response
{
    try {
        $response = $next($request);
        
        // Jika response adalah 403, render halaman unauthorized
        if ($response->getStatusCode() === 403) {
            return Inertia::render('Unauthorized');
        }
        
        return $response;
    } catch (\Exception $e) {
        if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return Inertia::render('Unauthorized');
        }
        
        throw $e;
    }
}
```

## Cara Testing

### 1. Halaman Test
Akses `/test-error-pages` untuk melihat semua halaman error dan loading.

### 2. Test Manual
- **404:** Akses URL yang tidak ada, misalnya `/halaman-tidak-ada`
- **403:** Akses `/403`
- **Forbidden:** Akses `/forbidden`
- **500:** Akses `/500`
- **Loading:** Akses `/loading`

### 3. Test di Controller
```php
// Di controller
public function someMethod()
{
    try {
        // Your logic here
    } catch (Exception $e) {
        return Inertia::render('ServerError');
    }
}
```

## Kustomisasi

### 1. Mengubah Email Contact
Edit file halaman error dan ganti email address:
```javascript
// Ganti dengan email yang sesuai
window.open('mailto:your-email@domain.com?subject=...', '_blank')
```

### 2. Mengubah Warna dan Styling
Setiap halaman menggunakan Tailwind CSS. Anda dapat mengubah warna dengan mengganti class CSS:
- `from-blue-50 to-indigo-100` untuk background gradient
- `text-blue-600` untuk warna primary
- `bg-red-600` untuk warna error

### 3. Menambahkan Logo
Tambahkan logo perusahaan di setiap halaman:
```vue
<template>
  <div class="text-center">
    <img src="/logo.png" alt="Logo" class="w-32 h-32 mx-auto mb-8" />
    <!-- Existing content -->
  </div>
</template>
```

## Best Practices

1. **Gunakan LoadingSpinner** untuk operasi async yang membutuhkan waktu
2. **Handle error dengan try-catch** di controller
3. **Gunakan middleware** untuk permission checking
4. **Customize email templates** sesuai kebutuhan
5. **Test semua halaman error** untuk memastikan UX yang baik

## Troubleshooting

### Loading tidak muncul
- Pastikan prop `show` bernilai `true`
- Periksa z-index jika ada elemen yang menutupi

### Error page tidak redirect
- Pastikan route sudah terdaftar di `web.php`
- Periksa middleware yang mungkin memblokir akses

### Email tidak terbuka
- Pastikan browser mendukung `mailto:` protocol
- Ganti dengan redirect ke halaman contact jika diperlukan

### Halaman 404 masih Laravel default
- Pastikan fallback route sudah ditambahkan di akhir `web.php`
- Clear cache dengan `php artisan route:clear`
- Restart server Laravel

## File yang Dibuat/Dimodifikasi

### File Baru:
- `resources/js/pages/Loading.vue`
- `resources/js/pages/Unauthorized.vue`
- `resources/js/pages/NotFound.vue`
- `resources/js/pages/ServerError.vue`
- `resources/js/components/LoadingSpinner.vue`
- `resources/js/pages/TestErrorPages.vue`
- `app/Http/Controllers/ErrorController.php`
- `app/Http/Middleware/HandleUnauthorized.php`
- `app/Exceptions/Handler.php`

### File yang Dimodifikasi:
- `routes/web.php` - Menambahkan error routes dan fallback
- `app/Http/Kernel.php` - Mendaftarkan middleware baru

## Kesimpulan

Sekarang aplikasi Anda memiliki halaman error dan loading yang modern dan user-friendly. Halaman 404 default Laravel sudah digantikan dengan halaman Vue yang lebih menarik dan informatif. 