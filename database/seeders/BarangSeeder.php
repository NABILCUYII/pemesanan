<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $barangData = [
            [
                'kode_barang' => 'BRG001',
                'nama_barang' => 'Laptop Dell Inspiron',
                'deskripsi' => 'Laptop untuk keperluan kantor',
                'kategori' => 'peminjaman',
                'stok' => 5,
            ],
            [
                'kode_barang' => 'BRG002',
                'nama_barang' => 'Proyektor Epson',
                'deskripsi' => 'Proyektor untuk presentasi',
                'kategori' => 'peminjaman',
                'stok' => 3,
            ],
            [
                'kode_barang' => 'BRG003',
                'nama_barang' => 'Printer HP LaserJet',
                'deskripsi' => 'Printer laser untuk cetak dokumen',
                'kategori' => 'peminjaman',
                'stok' => 2,
            ],
            [
                'kode_barang' => 'BRG004',
                'nama_barang' => 'Scanner Canon',
                'deskripsi' => 'Scanner untuk digitalisasi dokumen',
                'kategori' => 'peminjaman',
                'stok' => 1,
            ],
            [
                'kode_barang' => 'BRG005',
                'nama_barang' => 'Kamera DSLR Nikon',
                'deskripsi' => 'Kamera untuk dokumentasi acara',
                'kategori' => 'peminjaman',
                'stok' => 2,
            ],
            [
                'kode_barang' => 'BRG006',
                'nama_barang' => 'Mikrofon Wireless',
                'deskripsi' => 'Mikrofon untuk presentasi',
                'kategori' => 'peminjaman',
                'stok' => 4,
            ],
            [
                'kode_barang' => 'BRG007',
                'nama_barang' => 'Speaker Portable',
                'deskripsi' => 'Speaker untuk acara outdoor',
                'kategori' => 'peminjaman',
                'stok' => 3,
            ],
            [
                'kode_barang' => 'BRG008',
                'nama_barang' => 'Tablet Samsung',
                'deskripsi' => 'Tablet untuk demo aplikasi',
                'kategori' => 'peminjaman',
                'stok' => 2,
            ],
        ];

        foreach ($barangData as $barang) {
            Barang::create($barang);
        }
    }
} 