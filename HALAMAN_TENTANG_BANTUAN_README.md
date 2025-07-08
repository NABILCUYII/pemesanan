# Halaman Tentang dan Bantuan

## Deskripsi
Halaman Tentang dan Bantuan telah dibuat untuk memberikan informasi lengkap tentang sistem pemesanan dan panduan penggunaan kepada pengguna.

## Halaman yang Dibuat

### 1. Halaman Tentang (`/tentang`)
**File:** `resources/js/pages/Tentang.vue`

#### Fitur:
- **Header Section**: Informasi umum tentang sistem
- **Fitur Utama**: 6 card yang menjelaskan fitur utama sistem:
  - Manajemen Barang
  - Sistem Peminjaman
  - Permintaan Barang
  - Inventaris Real-time
  - Laporan & Analytics
  - Manajemen User
- **Informasi Teknis**: Detail teknologi yang digunakan dan fitur keamanan
- **Versi dan Kontak**: Informasi versi sistem dan kontak

#### Komponen yang Digunakan:
- `AppLayout` - Layout utama aplikasi
- `Card`, `CardContent`, `CardHeader`, `CardTitle` - Komponen card
- Icon dari `lucide-vue-next`
- Breadcrumb navigation

### 2. Halaman Bantuan (`/bantuan`)
**File:** `resources/js/pages/Bantuan.vue`

#### Fitur:
- **Header Section**: Pusat bantuan dengan deskripsi
- **Panduan Cepat**: 4 card untuk akses cepat ke panduan:
  - Manajemen Barang
  - Peminjaman
  - Permintaan
  - Inventaris
- **FAQ Section**: 5 pertanyaan umum dengan jawaban detail:
  - Cara menambah barang baru
  - Proses peminjaman barang
  - Cara membuat permintaan barang
  - Cara melihat laporan
  - Cara reset password
- **Panduan Detail**: Penjelasan detail untuk manajemen barang dan sistem peminjaman
- **Kontak Support**: 3 opsi bantuan tambahan:
  - Video Tutorial
  - Dokumentasi
  - Hubungi Admin

#### Komponen yang Digunakan:
- `AppLayout` - Layout utama aplikasi
- `Card`, `CardContent`, `CardHeader`, `CardTitle` - Komponen card
- `Collapsible`, `CollapsibleContent`, `CollapsibleTrigger` - Komponen accordion untuk FAQ
- Icon dari `lucide-vue-next`
- Breadcrumb navigation

## Routing

### Routes yang Ditambahkan
**File:** `routes/web.php`

```php
// Rute Halaman Informasi
Route::middleware(['auth'])->group(function () {
    Route::get('tentang', function () {
        return Inertia::render('Tentang');
    })->name('tentang');
    
    Route::get('bantuan', function () {
        return Inertia::render('Bantuan');
    })->name('bantuan');
});
```

### Navigation
**File:** `resources/js/components/AppSidebar.vue`

Menu ditambahkan di bagian footer sidebar:
- **Tentang**: Icon `Info`, route `tentang`
- **Bantuan**: Icon `HelpCircle`, route `bantuan`

## Akses Halaman

### URL:
- Halaman Tentang: `http://localhost:8000/tentang`
- Halaman Bantuan: `http://localhost:8000/bantuan`

### Navigasi:
- Melalui sidebar: Klik menu "Tentang" atau "Bantuan" di bagian footer sidebar
- Melalui URL langsung: Ketik URL di browser

## Fitur Responsif

Kedua halaman didesain responsif dengan:
- **Mobile**: Layout single column
- **Tablet**: Layout 2 kolom untuk beberapa section
- **Desktop**: Layout multi-kolom optimal

## Styling

### Warna yang Digunakan:
- **Primary**: Blue gradient (`from-blue-500 to-cyan-500`)
- **Success**: Green gradient (`from-green-500 to-emerald-500`)
- **Info**: Blue tones
- **Warning**: Yellow tones
- **Danger**: Red tones

### Komponen UI:
- Menggunakan Tailwind CSS untuk styling
- Komponen UI yang konsisten dengan design system aplikasi
- Hover effects dan transitions untuk UX yang lebih baik

## Interaktivitas

### Halaman Bantuan:
- **FAQ Accordion**: Bisa dibuka/tutup dengan animasi
- **Hover Effects**: Card dan button memiliki hover effects
- **Responsive Navigation**: Menu yang responsif di berbagai ukuran layar

### State Management:
- Menggunakan Vue 3 Composition API
- Reactive state untuk FAQ sections
- Computed properties untuk dynamic content

## Keamanan

- Kedua halaman dilindungi dengan middleware `auth`
- Hanya user yang sudah login yang bisa mengakses
- Menggunakan Inertia.js untuk SPA experience

## Maintenance

### Update Konten:
1. **FAQ**: Edit array FAQ di halaman Bantuan
2. **Informasi Teknis**: Update section teknologi di halaman Tentang
3. **Versi**: Update informasi versi di halaman Tentang

### Styling:
- Semua styling menggunakan Tailwind CSS classes
- Custom CSS minimal untuk animasi khusus
- Konsisten dengan design system aplikasi

## Testing

### Manual Testing:
1. Login ke aplikasi
2. Akses halaman Tentang melalui sidebar
3. Akses halaman Bantuan melalui sidebar
4. Test FAQ accordion di halaman Bantuan
5. Test responsive design di berbagai ukuran layar

### Browser Compatibility:
- Chrome (recommended)
- Firefox
- Safari
- Edge

## Dependencies

### Frontend:
- Vue 3
- Inertia.js
- Tailwind CSS
- Lucide Vue Next (icons)
- Shadcn/ui components

### Backend:
- Laravel 10
- Inertia middleware

## Catatan Penting

1. **Middleware**: Pastikan user sudah login sebelum mengakses halaman
2. **Icons**: Semua icon menggunakan Lucide Vue Next
3. **Responsive**: Test di berbagai device untuk memastikan tampilan optimal
4. **Content**: Update konten sesuai dengan kebutuhan organisasi
5. **Links**: Pastikan semua link internal berfungsi dengan benar

## Troubleshooting

### Masalah Umum:
1. **Halaman tidak muncul**: Cek route sudah terdaftar dengan `php artisan route:list`
2. **Icon tidak muncul**: Pastikan import icon sudah benar
3. **Styling tidak konsisten**: Cek Tailwind CSS sudah ter-compile
4. **FAQ tidak berfungsi**: Cek Vue reactivity dan event handling

### Debug:
- Gunakan browser developer tools
- Cek console untuk error JavaScript
- Cek network tab untuk request yang gagal
- Gunakan Vue DevTools untuk debugging component state 