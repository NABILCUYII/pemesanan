# Panduan Error Handling Laravel + Inertia.js

## Masalah yang Diperbaiki

Sebelumnya, halaman 403 masih menampilkan tampilan Laravel default:
```
403
Anda tidak memiliki akses ke halaman ini.
```

Sekarang sudah diperbaiki untuk menampilkan halaman Vue yang modern dan informatif.

## Solusi yang Diterapkan

### 1. Middleware Global Baru
**File:** `app/Http/Middleware/HandleAllErrors.php`

Middleware ini menangani semua jenis error (403, 404, 500) untuk request Inertia:
- Menangkap response dengan status code tertentu
- Menangkap exception yang terjadi
- Render halaman Vue yang sesuai

### 2. Exception Handler yang Diperbaiki
**File:** `app/Exceptions/Handler.php`

Menambahkan handler untuk:
- `AuthorizationException` (403)
- `AccessDeniedHttpException` (403)
- `NotFoundHttpException` (404)
- General exceptions (500)

### 3. Middleware Terdaftar Secara Global
**File:** `app/Http/Kernel.php`

Middleware `HandleAllErrors` sekarang terdaftar di `$middleware` array, sehingga akan berjalan untuk semua request.

## Cara Kerja

### Flow Error Handling:

1. **Request masuk** → Middleware `HandleAllErrors` menangkap
2. **Jika ada error 403** → Render halaman `Unauthorized.vue`
3. **Jika ada error 404** → Render halaman `NotFound.vue`
4. **Jika ada error 500** → Render halaman `ServerError.vue`
5. **Jika tidak ada error** → Lanjut ke request normal

### Exception Handling:

```php
try {
    $response = $next($request);
    
    // Handle HTTP status codes
    if ($request->header('X-Inertia')) {
        switch ($response->getStatusCode()) {
            case 403: return Inertia::render('Unauthorized');
            case 404: return Inertia::render('NotFound');
            case 500: return Inertia::render('ServerError');
        }
    }
    
    return $response;
} catch (AuthorizationException $e) {
    if ($request->header('X-Inertia')) {
        return Inertia::render('Unauthorized');
    }
    throw $e;
}
```

## Testing

### 1. Test Routes yang Tersedia:

```bash
# Test 403 error
http://localhost/test-403

# Test 404 error  
http://localhost/test-404

# Test 500 error
http://localhost/test-500

# Test halaman error pages
http://localhost/test-error-pages
```

### 2. Test Manual:

**Untuk 403:**
- Akses halaman yang memerlukan permission khusus
- Gunakan `abort(403)` di controller
- Akses `/test-403`

**Untuk 404:**
- Akses URL yang tidak ada
- Gunakan `abort(404)` di controller
- Akses `/test-404`

**Untuk 500:**
- Buat error di controller
- Gunakan `abort(500)` di controller
- Akses `/test-500`

## Halaman Error yang Tersedia

### 1. Unauthorized.vue (403)
- **URL:** `/403` atau otomatis saat ada 403 error
- **Desain:** Gradient merah-pink
- **Fitur:** Tombol navigasi, tips, email admin

### 2. Forbidden.vue (403)
- **URL:** `/forbidden`
- **Desain:** Gradient merah-oranye
- **Fitur:** Info user, request access, detail penyebab

### 3. NotFound.vue (404)
- **URL:** `/404` atau otomatis saat ada 404 error
- **Desain:** Gradient ungu-indigo
- **Fitur:** Search box, quick links, email support

### 4. ServerError.vue (500)
- **URL:** `/500` atau otomatis saat ada 500 error
- **Desain:** Gradient oranye-merah
- **Fitur:** Retry button, error ID, status server

## Troubleshooting

### Jika masih muncul halaman Laravel default:

1. **Clear cache:**
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

2. **Restart server:**
```bash
php artisan serve
```

3. **Periksa middleware:**
Pastikan `HandleAllErrors` terdaftar di `app/Http/Kernel.php`

4. **Periksa exception handler:**
Pastikan handler untuk 403 error ada di `app/Exceptions/Handler.php`

### Debug Mode:

Untuk debugging, tambahkan log di middleware:

```php
public function handle(Request $request, Closure $next): Response
{
    try {
        $response = $next($request);
        
        // Debug log
        \Log::info('Response status: ' . $response->getStatusCode());
        
        if ($request->header('X-Inertia')) {
            switch ($response->getStatusCode()) {
                case 403:
                    \Log::info('Rendering Unauthorized page');
                    return Inertia::render('Unauthorized');
                // ... other cases
            }
        }
        
        return $response;
    } catch (\Exception $e) {
        \Log::error('Exception caught: ' . $e->getMessage());
        // ... handle exception
    }
}
```

## Best Practices

1. **Gunakan abort() untuk testing:**
```php
abort(403, 'Access denied');
abort(404, 'Page not found');
abort(500, 'Server error');
```

2. **Gunakan middleware untuk protection:**
```php
Route::middleware(['auth', 'admin'])->group(function () {
    // Routes yang memerlukan admin access
});
```

3. **Custom error messages:**
```php
if (!auth()->user()->hasPermission('admin')) {
    abort(403, 'Admin access required');
}
```

4. **Log errors untuk monitoring:**
```php
\Log::warning('403 access attempt', [
    'user' => auth()->user()->id,
    'url' => request()->url(),
    'ip' => request()->ip()
]);
```

## Status

✅ **BERHASIL DIPERBAIKI!** 

Halaman 403 sekarang akan menampilkan halaman Vue yang modern dan informatif, bukan lagi tampilan Laravel default yang sederhana.

**Test sekarang dengan:**
- `/test-403` - Untuk test 403 error
- `/test-error-pages` - Untuk melihat semua halaman error
- Akses halaman yang memerlukan permission khusus 