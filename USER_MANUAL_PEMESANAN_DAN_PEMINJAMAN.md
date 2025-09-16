# BUKU PANDUAN PENGGUNA
## Sistem Pemesanan dan Peminjaman Barang

---

## DAFTAR ISI

1. [Pendahuluan](#pendahuluan)
2. [Perbedaan Permintaan dan Peminjaman](#perbedaan-permintaan-dan-peminjaman)
3. [Cara Memesan Barang (Permintaan)](#cara-memesan-barang-permintaan)
4. [Cara Meminjam Barang (Peminjaman)](#cara-meminjam-barang-peminjaman)
5. [Status dan Tracking](#status-dan-tracking)
6. [Pengembalian Barang](#pengembalian-barang)
7. [Troubleshooting](#troubleshooting)
8. [FAQ](#faq)

---

## PENDAHULUAN

Selamat datang di Sistem Pemesanan dan Peminjaman Barang! Sistem ini dirancang untuk memudahkan pengelolaan barang di organisasi Anda. Terdapat dua jenis transaksi utama:

- **Permintaan Barang**: Untuk barang konsumabel yang tidak perlu dikembalikan
- **Peminjaman Barang**: Untuk aset yang perlu dikembalikan setelah digunakan

---

## PERBEDAAN PERMINTAAN DAN PEMINJAMAN

### 📦 Permintaan Barang (Request)
- **Tujuan**: Memesan barang konsumabel
- **Contoh**: Kertas, pulpen, tinta printer, alat tulis
- **Karakteristik**: 
  - Tidak perlu dikembalikan
  - Langsung dikonsumsi/digunakan
  - Stok berkurang setelah disetujui
  - Tidak ada tanggal pengembalian

### 🔄 Peminjaman Barang (Borrowing)
- **Tujuan**: Meminjam aset untuk sementara
- **Contoh**: Laptop, projector, kamera, alat elektronik
- **Karakteristik**:
  - Harus dikembalikan
  - Ada tanggal pinjam dan tanggal kembali
  - Stok tidak berkurang (hanya status berubah)
  - Ada tracking kondisi barang

---

## CARA MEMESAN BARANG (PERMINTAAN)

### Langkah 1: Akses Menu Permintaan
1. Login ke sistem dengan akun Anda
2. Dari sidebar, klik menu **"Permintaan"**
3. Anda akan melihat daftar permintaan yang sudah dibuat

### Langkah 2: Buat Permintaan Baru
1. Klik tombol **"Tambah Permintaan"** (ikon +)
2. Sistem akan membuka halaman form permintaan

### Langkah 3: Isi Form Permintaan
1. **Pilih Barang**:
   - Klik dropdown "Pilih Barang"
   - Ketik nama atau kode barang untuk mencari
   - Pilih barang yang diinginkan dari daftar

2. **Isi Jumlah**:
   - Masukkan jumlah barang yang diminta
   - Pastikan tidak melebihi stok yang tersedia

3. **Tambahkan Keterangan** (Opsional):
   - Jelaskan alasan permintaan
   - Tambahkan informasi tambahan jika diperlukan

4. **Permintaan Multiple Barang** (Jika diperlukan):
   - Klik tombol **"Tambah Barang"** untuk menambah item lain
   - Ulangi langkah 1-3 untuk setiap barang

### Langkah 4: Kirim Permintaan
1. Periksa kembali semua data yang diisi
2. Klik tombol **"Kirim Permintaan"**
3. Sistem akan menampilkan pesan sukses

### Langkah 5: Tracking Status
1. Kembali ke halaman daftar permintaan
2. Lihat status permintaan Anda:
   - **Pending**: Menunggu persetujuan admin
   - **Approved**: Disetujui, barang akan disiapkan
   - **Completed**: Barang sudah diterima
   - **Rejected**: Ditolak (dengan alasan)

---

## CARA MEMINJAM BARANG (PEMINJAMAN)

### Langkah 1: Akses Menu Peminjaman
1. Login ke sistem dengan akun Anda
2. Dari sidebar, klik menu **"Peminjaman"**
3. Anda akan melihat daftar peminjaman yang sudah dibuat

### Langkah 2: Buat Peminjaman Baru
1. Klik tombol **"Tambah Peminjaman"** (ikon +)
2. Sistem akan membuka halaman form peminjaman

### Langkah 3: Isi Form Peminjaman
1. **Pilih Barang**:
   - Klik dropdown "Pilih Barang"
   - Hanya barang kategori "peminjaman" yang akan muncul
   - Ketik nama atau kode barang untuk mencari
   - Pilih barang yang diinginkan

2. **Isi Jumlah**:
   - Masukkan jumlah barang yang akan dipinjam
   - Pastikan tidak melebihi stok yang tersedia

3. **Tanggal Pengembalian**:
   - Pilih tanggal kapan barang akan dikembalikan
   - Tanggal harus minimal hari ini atau setelahnya

4. **Tambahkan Keterangan** (Opsional):
   - Jelaskan tujuan peminjaman
   - Tambahkan informasi tambahan jika diperlukan

### Langkah 4: Kirim Permintaan Peminjaman
1. Periksa kembali semua data yang diisi
2. Klik tombol **"Simpan"**
3. Sistem akan menampilkan pesan sukses

### Langkah 5: Tracking Status Peminjaman
1. Kembali ke halaman daftar peminjaman
2. Lihat status peminjaman Anda:
   - **Pending**: Menunggu persetujuan admin
   - **Approved**: Disetujui, barang siap diambil
   - **In Progress**: Barang sedang dipinjam
   - **Returned**: Barang sudah dikembalikan
   - **Rejected**: Ditolak (dengan alasan)

---

## STATUS DAN TRACKING

### Status Permintaan Barang
| Status | Arti | Tindakan |
|--------|------|----------|
| **Pending** | Menunggu persetujuan | Tunggu admin menyetujui |
| **Approved** | Disetujui | Barang akan disiapkan |
| **Completed** | Selesai | Barang sudah diterima |
| **Rejected** | Ditolak | Cek alasan penolakan |

### Status Peminjaman Barang
| Status | Arti | Tindakan |
|--------|------|----------|
| **Pending** | Menunggu persetujuan | Tunggu admin menyetujui |
| **Approved** | Disetujui | Ambil barang dari admin |
| **In Progress** | Sedang dipinjam | Gunakan barang sesuai jadwal |
| **Returned** | Dikembalikan | Proses selesai |
| **Rejected** | Ditolak | Cek alasan penolakan |

### Cara Melihat Detail Status
1. Klik pada baris permintaan/peminjaman
2. Lihat informasi detail termasuk:
   - Tanggal pembuatan
   - Admin yang memproses
   - Catatan/komentar
   - Alasan penolakan (jika ada)

---

## PENGEMBALIAN BARANG

### Untuk Peminjaman Barang
1. **Akses Menu Peminjaman**:
   - Buka halaman daftar peminjaman
   - Cari peminjaman dengan status "In Progress"

2. **Proses Pengembalian**:
   - Klik tombol **"Kembalikan"** pada peminjaman
   - Isi form pengembalian:
     - Tanggal pengembalian (otomatis hari ini)
     - Kondisi barang (Baik/Rusak/Hilang)
     - Catatan pengembalian (opsional)

3. **Konfirmasi Pengembalian**:
   - Klik **"Konfirmasi Pengembalian"**
   - Status akan berubah menjadi "Returned"

### Catatan Penting Pengembalian
- **Kondisi Baik**: Barang dalam keadaan normal
- **Kondisi Rusak**: Barang mengalami kerusakan
- **Kondisi Hilang**: Barang tidak dapat ditemukan
- Setiap kondisi akan dicatat dalam sistem

---

## TROUBLESHOOTING

### Masalah Umum dan Solusi

#### 1. Barang Tidak Muncul di Dropdown
**Penyebab**: 
- Barang tidak tersedia (stok = 0)
- Barang kategori tidak sesuai

**Solusi**:
- Cek stok barang di menu "Barang"
- Hubungi admin untuk menambah stok
- Pastikan memilih kategori yang benar

#### 2. Tidak Bisa Submit Form
**Penyebab**:
- Ada field yang belum diisi
- Format tanggal tidak valid
- Jumlah melebihi stok

**Solusi**:
- Periksa semua field wajib
- Pastikan format tanggal benar (YYYY-MM-DD)
- Kurangi jumlah permintaan

#### 3. Status Tidak Berubah
**Penyebab**:
- Admin belum memproses
- Ada masalah sistem

**Solusi**:
- Tunggu admin memproses (biasanya 1-2 hari kerja)
- Hubungi admin jika terlalu lama
- Refresh halaman untuk update status

#### 4. Lupa Password
**Solusi**:
1. Klik "Lupa Password" di halaman login
2. Masukkan email terdaftar
3. Cek email untuk link reset
4. Buat password baru

---

## FAQ (PERTANYAAN UMUM)

### Q: Berapa lama proses approval permintaan?
**A**: Biasanya 1-2 hari kerja, tergantung ketersediaan admin dan kompleksitas permintaan.

### Q: Bisa tidak mengubah permintaan setelah dikirim?
**A**: Tidak bisa langsung. Hubungi admin untuk perubahan atau buat permintaan baru.

### Q: Apa yang terjadi jika terlambat mengembalikan barang?
**A**: Sistem akan menandai sebagai terlambat. Hubungi admin untuk perpanjangan atau pengembalian.

### Q: Bisa meminjam barang yang sama berulang kali?
**A**: Bisa, selama barang tersedia dan tidak sedang dipinjam orang lain.

### Q: Bagaimana jika barang rusak saat dipinjam?
**A**: Laporkan kondisi rusak saat pengembalian. Admin akan mengevaluasi dan menentukan tindakan selanjutnya.

### Q: Bisa melihat riwayat permintaan/peminjaman lama?
**A**: Ya, semua riwayat tersimpan di sistem dan bisa dilihat di menu "Riwayat".

### Q: Bagaimana jika tidak ada barang yang dicari?
**A**: Hubungi admin untuk menambahkan barang baru ke sistem.

### Q: Bisa meminjam barang untuk orang lain?
**A**: Tidak, peminjaman terikat dengan akun yang login. Setiap user harus membuat permintaan sendiri.

---

## KONTAK DUKUNGAN

Jika mengalami masalah atau butuh bantuan:

- **Email**: vvvlll224@gmail.com
- **Telepon**: +62 857-5342-3258
               +62 812-5067-8630
- **Jam Kerja**: Senin-Jumat, 08:00-17:00 WIB
- **Menu Bantuan**: Klik menu "Bantuan" di sidebar untuk panduan lengkap

---

## PENUTUP

Sistem Pemesanan dan Peminjaman Barang dirancang untuk memudahkan pengelolaan inventaris organisasi. Dengan mengikuti panduan ini, Anda dapat menggunakan sistem dengan efektif dan efisien.

**Terima kasih telah menggunakan sistem kami!**

---

*Dokumen ini terakhir diperbarui: 16 09 2025
*Versi: 1.0* 