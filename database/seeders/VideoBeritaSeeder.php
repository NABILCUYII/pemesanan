<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VideoBerita;

class VideoBeritaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $videoBeritas = [
            [
                'judul' => 'Teknologi Terbaru dalam Manajemen Aset Perusahaan',
                'deskripsi' => 'Video ini membahas tentang inovasi teknologi terbaru yang dapat membantu perusahaan dalam mengelola aset dengan lebih efisien dan efektif.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'sumber' => 'TechNews Indonesia',
                'tanggal_publish' => '2024-01-15',
                'is_active' => true,
                'urutan' => 1,
            ],
            [
                'judul' => 'Panduan Lengkap Sistem Peminjaman Barang Digital',
                'deskripsi' => 'Tutorial lengkap tentang cara menggunakan sistem peminjaman barang digital untuk meningkatkan produktivitas kerja.',
                'video_url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                'sumber' => 'Digital Solutions',
                'tanggal_publish' => '2024-01-10',
                'is_active' => true,
                'urutan' => 2,
            ],
            [
                'judul' => 'Best Practices dalam Pengelolaan Inventaris',
                'deskripsi' => 'Tips dan trik terbaik dalam mengelola inventaris perusahaan untuk memastikan efisiensi dan akurasi data.',
                'video_url' => 'https://www.youtube.com/watch?v=kJQP7kiw5Fk',
                'sumber' => 'Inventory Management Pro',
                'tanggal_publish' => '2024-01-05',
                'is_active' => true,
                'urutan' => 3,
            ],
            [
                'judul' => 'Keamanan Data dalam Sistem Peminjaman',
                'deskripsi' => 'Pentingnya keamanan data dalam sistem peminjaman dan cara mengimplementasikan protokol keamanan yang tepat.',
                'video_url' => 'https://www.youtube.com/watch?v=ZZ5LpwO-An4',
                'sumber' => 'Cyber Security Today',
                'tanggal_publish' => '2024-01-01',
                'is_active' => true,
                'urutan' => 4,
            ],
            [
                'judul' => 'Analisis Data untuk Optimasi Peminjaman',
                'deskripsi' => 'Cara menggunakan analisis data untuk mengoptimalkan proses peminjaman dan meningkatkan efisiensi operasional.',
                'video_url' => 'https://www.youtube.com/watch?v=OPf0YbXqDm0',
                'sumber' => 'Data Analytics Hub',
                'tanggal_publish' => '2023-12-28',
                'is_active' => true,
                'urutan' => 5,
            ],
        ];

        foreach ($videoBeritas as $videoBerita) {
            VideoBerita::create($videoBerita);
        }
    }
}
