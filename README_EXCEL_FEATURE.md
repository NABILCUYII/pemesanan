# Fitur Download Excel - Sistem Pemesanan Barang

## Deskripsi
Fitur download Excel telah ditambahkan ke sistem pemesanan barang untuk memungkinkan pengguna mengunduh laporan dalam format Excel (.xlsx) selain format PDF yang sudah ada.

## Fitur yang Ditambahkan

### 1. Download Laporan Umum Excel
- **Lokasi**: Halaman Laporan (`/laporan`)
- **Tombol**: "Download Laporan Excel" (warna hijau dengan icon spreadsheet)
- **Fungsi**: Mengunduh laporan umum dalam format Excel dengan 3 sheet:
  - **Sheet Ringkasan**: Ringkasan aktivitas per pengguna
  - **Sheet Aktivitas Pengguna**: Detail aktivitas dengan persentase
  - **Sheet Pergerakan Stok**: Data pergerakan stok barang

### 2. Download Laporan Per User Excel
- **Lokasi**: Detail laporan per pengguna (mobile dan desktop view)
- **Tombol**: "Excel" (warna hijau dengan icon spreadsheet)
- **Fungsi**: Mengunduh laporan detail aktivitas pengguna tertentu dalam format Excel

## File yang Dimodifikasi/Ditambahkan

### Backend (Laravel)
1. **`composer.json`** - Menambahkan package `maatwebsite/excel`
2. **`app/Exports/LaporanUmumExport.php`** - Class untuk export laporan umum Excel
3. **`app/Exports/LaporanUserExport.php`** - Class untuk export laporan user Excel
4. **`app/Http/Controllers/LaporanController.php`** - Menambahkan method `downloadExcel()` dan `downloadUserExcel()`
5. **`routes/web.php`** - Menambahkan routes untuk download Excel

### Frontend (Vue.js)
1. **`resources/js/pages/laporan/index.vue`** - Menambahkan tombol download Excel

## Cara Penggunaan

### Download Laporan Umum Excel
1. Buka halaman Laporan (`/laporan`)
2. Pilih periode laporan (bulan dan tahun)
3. Klik tombol "Download Laporan Excel" (hijau)
4. File Excel akan otomatis terdownload dengan nama `laporan-umum-{bulan}-{tahun}.xlsx`

### Download Laporan Per User Excel
1. Buka halaman Laporan (`/laporan`)
2. Pilih periode laporan (bulan dan tahun)
3. Klik tombol expand (chevron) pada user yang ingin dilihat detailnya
4. Klik tombol "Excel" (hijau) di bagian detail user
5. File Excel akan otomatis terdownload dengan nama `laporan-{nama-user}-{bulan}-{tahun}.xlsx`

## Format Excel yang Dihasilkan

### Laporan Umum Excel (3 Sheet)

#### Sheet 1: Ringkasan
- Judul laporan dan periode
- Tabel ringkasan aktivitas per pengguna
- Status aktivitas (Aktif/Tidak Aktif)

#### Sheet 2: Aktivitas Pengguna
- Detail aktivitas per pengguna
- Total permintaan dan peminjaman
- Persentase aktivitas relatif terhadap total

#### Sheet 3: Pergerakan Stok
- Data pergerakan stok barang
- Stok awal, keluar, masuk, dan akhir
- Kode dan nama barang

### Laporan Per User Excel (1 Sheet)
- Detail aktivitas pengguna tertentu
- Jenis aktivitas (Permintaan/Peminjaman)
- Informasi barang, jumlah, status, dan tanggal
- Keterangan tambahan

## Styling Excel
- Header dengan background abu-abu
- Font bold untuk judul dan header
- Alignment center untuk data
- Auto-size column width
- Merge cells untuk judul

## Dependencies
- **Laravel Excel**: `maatwebsite/excel` v3.1.64
- **PhpSpreadsheet**: Untuk manipulasi file Excel
- **Carbon**: Untuk format tanggal

## Instalasi
Package Excel sudah diinstall melalui composer:
```bash
composer require maatwebsite/excel
```

## Troubleshooting
1. **Error "Class not found"**: Jalankan `composer dump-autoload`
2. **Error permission**: Pastikan folder storage memiliki permission write
3. **File tidak terdownload**: Cek browser settings untuk download otomatis

## Catatan
- Format file: `.xlsx` (Excel 2007+)
- Ukuran file bergantung pada jumlah data
- Download bersifat real-time berdasarkan data di database
- Periode laporan dapat dipilih (bulan dan tahun) 