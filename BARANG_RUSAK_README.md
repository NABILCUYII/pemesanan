# Fitur Daftar Barang Rusak

## Deskripsi
Fitur ini memungkinkan admin untuk melihat dan mengelola daftar barang yang memiliki status rusak dalam sistem pemesanan barang.

## Fitur yang Tersedia

### 1. Halaman Daftar Barang Rusak
- **URL**: `/barang-rusak`
- **Akses**: Admin only
- **Fungsi**: Menampilkan semua barang dengan status rusak

### 2. Tabel Barang Rusak
- Kode Barang
- Nama Barang
- Kategori (Peminjaman/Permintaan)
- Stok
- Status
- Dropdown untuk mengubah status

### 3. Update Status Barang
- Mengubah status dari rusak ke baik/hilang
- Validasi input status
- Feedback sukses setelah update

## Struktur Database

### Tabel `barang`
Kolom baru yang ditambahkan:
```sql
ALTER TABLE barang ADD COLUMN status ENUM('baik', 'rusak', 'hilang') DEFAULT 'baik' AFTER stok;
```

### Model Barang
```php
protected $fillable = [
    'kode_barang',
    'nama_barang', 
    'deskripsi',
    'kategori',
    'stok',
    'status', // Kolom baru
];
```

## File yang Dibuat/Dimodifikasi

### 1. Migration
- `database/migrations/2025_06_26_000136_add_status_to_barang_table.php`

### 2. Controller
- `app/Http/Controllers/BarangRusakController.php`

### 3. Vue Component
- `resources/js/pages/BarangRusak/Index.vue`

### 4. Routes
- Ditambahkan di `routes/web.php`:
  ```php
  Route::get('barang-rusak', [BarangRusakController::class, 'index'])->name('barang-rusak.index');
  Route::put('barang-rusak/{barang}/status', [BarangRusakController::class, 'updateStatus'])->name('barang-rusak.update-status');
  ```

### 5. Navigation
- Ditambahkan menu "Barang Rusak" di sidebar (`resources/js/components/AppSidebar.vue`)
- Ditambahkan tombol "Barang Rusak" di halaman barang (`resources/js/pages/Barang/index.vue`)

### 6. Seeder
- `database/seeders/BarangRusakSeeder.php` - untuk testing data

## Cara Penggunaan

### 1. Akses Halaman
1. Login sebagai admin
2. Klik menu "Barang Rusak" di sidebar
3. Atau klik tombol "Barang Rusak" di halaman daftar barang

### 2. Melihat Daftar Barang Rusak
- Halaman akan menampilkan semua barang dengan status rusak
- Informasi yang ditampilkan: kode, nama, kategori, stok, dan status

### 3. Mengubah Status Barang
1. Pilih status baru dari dropdown di kolom "Aksi"
2. Status akan otomatis terupdate
3. Halaman akan refresh untuk menampilkan perubahan

### 4. Empty State
- Jika tidak ada barang rusak, akan ditampilkan pesan "Tidak ada barang rusak"

## Status Barang
- **Baik**: Barang dalam kondisi normal
- **Rusak**: Barang mengalami kerusakan
- **Hilang**: Barang tidak dapat ditemukan

## Testing
Untuk testing, gunakan seeder yang sudah dibuat:
```bash
php artisan db:seed --class=BarangRusakSeeder
```

Seeder ini akan:
1. Mengubah 3 barang pertama menjadi status rusak
2. Menambahkan 3 barang baru dengan status rusak

## Keamanan
- Hanya admin yang dapat mengakses halaman ini
- Validasi input untuk update status
- CSRF protection untuk semua request

## Responsive Design
- Halaman responsive untuk desktop dan mobile
- Tabel dengan scroll horizontal pada layar kecil
- Tombol dan dropdown yang mudah digunakan di mobile 