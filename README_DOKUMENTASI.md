# 📚 DOKUMENTASI SISTEM PEMESANAN DAN PEMINJAMAN BARANG

---

## 📋 DAFTAR DOKUMENTASI

### 1. 📖 **USER_MANUAL_PEMESANAN_DAN_PEMINJAMAN.md**
**Deskripsi**: Buku panduan lengkap untuk pengguna sistem
**Isi**:
- Pendahuluan dan perbedaan permintaan vs peminjaman
- Langkah-langkah detail cara memesan barang
- Langkah-langkah detail cara meminjam barang
- Penjelasan status dan tracking
- Proses pengembalian barang
- Troubleshooting dan FAQ
- Kontak dukungan

**Target Pembaca**: Semua pengguna sistem (pemula hingga mahir)

### 2. ⚡ **QUICK_REFERENCE_GUIDE.md**
**Deskripsi**: Panduan cepat untuk akses informasi penting
**Isi**:
- Langkah-langkah singkat
- Tabel status barang
- Troubleshooting cepat
- Tips penting
- Kontak darurat

**Target Pembaca**: Pengguna yang sudah familiar dengan sistem

---

## 🎯 CARA MENGGUNAKAN DOKUMENTASI

### Untuk Pengguna Baru
1. Mulai dengan **USER_MANUAL_PEMESANAN_DAN_PEMINJAMAN.md**
2. Baca dari awal hingga akhir
3. Praktikkan langkah-langkah yang dijelaskan
4. Simpan **QUICK_REFERENCE_GUIDE.md** untuk referensi cepat

### Untuk Pengguna Berpengalaman
1. Gunakan **QUICK_REFERENCE_GUIDE.md** untuk akses cepat
2. Konsultasi **USER_MANUAL_PEMESANAN_DAN_PEMINJAMAN.md** jika ada masalah

### Untuk Admin/Support
1. Pelajari kedua dokumen untuk memberikan bantuan yang tepat
2. Gunakan FAQ di manual utama untuk menjawab pertanyaan umum
3. Referensikan troubleshooting guide untuk masalah teknis

---

## 📁 STRUKTUR SISTEM

```
Sistem Pemesanan dan Peminjaman Barang
├── 📦 Permintaan Barang (Request)
│   ├── Barang konsumabel
│   ├── Tidak perlu dikembalikan
│   └── Stok berkurang setelah approval
│
└── 🔄 Peminjaman Barang (Borrowing)
    ├── Aset/barang berharga
    ├── Harus dikembalikan
    └── Ada tracking kondisi barang
```

---

## 🔄 WORKFLOW SISTEM

### Workflow Permintaan
```
User Request → Admin Approval → Item Preparation → Completion
```

### Workflow Peminjaman
```
User Request → Admin Approval → Item Pickup → Usage → Return
```

---

## 📊 STATUS SISTEM

### Status Permintaan
- **Pending**: Menunggu persetujuan admin
- **Approved**: Disetujui, barang disiapkan
- **Completed**: Barang sudah diterima
- **Rejected**: Ditolak dengan alasan

### Status Peminjaman
- **Pending**: Menunggu persetujuan admin
- **Approved**: Disetujui, siap diambil
- **In Progress**: Barang sedang dipinjam
- **Returned**: Barang sudah dikembalikan
- **Rejected**: Ditolak dengan alasan

---

## 🛠️ FITUR UTAMA SISTEM

### Untuk User
- ✅ Membuat permintaan barang
- ✅ Membuat peminjaman barang
- ✅ Tracking status real-time
- ✅ Melihat riwayat transaksi
- ✅ Pengembalian barang
- ✅ Notifikasi status

### Untuk Admin
- ✅ Approval/rejection permintaan
- ✅ Manajemen stok barang
- ✅ Monitoring peminjaman
- ✅ Laporan dan analytics
- ✅ Manajemen user

---

## 📞 DUKUNGAN TEKNIS

### Kontak Support
- **Email**: support@perusahaan.com
- **Telepon**: (021) 1234-5678
- **Jam Kerja**: Senin-Jumat, 08:00-17:00 WIB

### Menu Bantuan di Sistem
- Akses menu **"Bantuan"** di sidebar
- FAQ dan panduan interaktif
- Video tutorial (jika tersedia)

---

## 🔄 PEMELIHARAAN DOKUMENTASI

### Update Rutin
- Dokumentasi diperbarui setiap ada perubahan sistem
- Versi dokumen tercantum di footer setiap file
- Tanggal update terakhir selalu diperbarui

### Feedback
- Jika ada kesalahan atau saran perbaikan, hubungi tim IT
- Feedback pengguna sangat dihargai untuk perbaikan dokumentasi

---

## 📝 CATATAN PENTING

1. **Backup**: Selalu simpan salinan dokumen ini
2. **Versi**: Pastikan menggunakan versi terbaru dokumentasi
3. **Training**: Dokumentasi ini bisa digunakan untuk training user baru
4. **Localization**: Dokumen tersedia dalam bahasa Indonesia

---

*Dokumentasi ini dibuat untuk memudahkan penggunaan sistem pemesanan dan peminjaman barang. Terima kasih telah menggunakan sistem kami!*

---

**Versi**: 1.0  
**Terakhir Diperbarui**: [Tanggal]  
**Dibuat Oleh**: Tim IT  
**Kontak**: support@perusahaan.com 