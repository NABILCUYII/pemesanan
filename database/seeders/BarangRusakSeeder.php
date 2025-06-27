<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Barang;

class BarangRusakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update beberapa barang yang sudah ada menjadi rusak
        $barangToUpdate = Barang::take(3)->get();
        
        foreach ($barangToUpdate as $barang) {
            $barang->update(['status' => 'rusak']);
        }

        // Atau buat barang baru dengan status rusak
        $barangRusak = [
            [
                'kode_barang' => 'BR001',
                'nama_barang' => 'Laptop Dell Rusak',
                'deskripsi' => 'Laptop Dell yang mengalami kerusakan pada layar',
                'kategori' => 'peminjaman',
                'stok' => 1,
                'status' => 'rusak'
            ],
            [
                'kode_barang' => 'BR002',
                'nama_barang' => 'Printer HP Rusak',
                'deskripsi' => 'Printer HP yang tidak bisa mencetak',
                'kategori' => 'peminjaman',
                'stok' => 1,
                'status' => 'rusak'
            ],
            [
                'kode_barang' => 'BR003',
                'nama_barang' => 'Kabel HDMI Putus',
                'deskripsi' => 'Kabel HDMI yang sudah putus dan tidak bisa digunakan',
                'kategori' => 'permintaan',
                'stok' => 5,
                'status' => 'rusak'
            ]
        ];

        foreach ($barangRusak as $barang) {
            Barang::create($barang);
        }
    }
}
