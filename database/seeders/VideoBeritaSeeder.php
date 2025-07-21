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
        $videos = [
            [
                'judul' => 'Teknologi AI Terbaru 2024',
                'deskripsi' => 'Perkembangan teknologi AI yang revolusioner di tahun 2024 dan dampaknya terhadap berbagai industri.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'sumber' => 'TechNews Indonesia',
                'tanggal_publish' => '2024-01-15',
                'is_active' => true,
                'urutan' => 1,
            ],
            [
                'judul' => 'Tips Keamanan Digital untuk Perusahaan',
                'deskripsi' => 'Panduan lengkap tentang keamanan digital yang wajib diketahui oleh setiap perusahaan.',
                'video_url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                'sumber' => 'CyberSecurity Weekly',
                'tanggal_publish' => '2024-01-20',
                'is_active' => true,
                'urutan' => 2,
            ],
            [
                'judul' => 'Tren E-commerce di Indonesia 2024',
                'deskripsi' => 'Analisis mendalam tentang perkembangan e-commerce di Indonesia dan prediksi untuk tahun 2024.',
                'video_url' => 'https://www.youtube.com/watch?v=kJQP7kiw5Fk',
                'sumber' => 'Business Insight',
                'tanggal_publish' => '2024-01-25',
                'is_active' => true,
                'urutan' => 3,
            ],
            [
                'judul' => 'Panduan Manajemen Proyek Modern',
                'deskripsi' => 'Teknik dan strategi manajemen proyek yang efektif untuk era digital.',
                'video_url' => 'https://www.youtube.com/watch?v=ZZ5LpwO-An4',
                'sumber' => 'Project Management Pro',
                'tanggal_publish' => '2024-02-01',
                'is_active' => true,
                'urutan' => 4,
            ],
            [
                'judul' => 'Inovasi dalam Dunia Pendidikan',
                'deskripsi' => 'Bagaimana teknologi mengubah cara belajar dan mengajar di era digital.',
                'video_url' => 'https://www.youtube.com/watch?v=JGwWNGJdvx8',
                'sumber' => 'Education Today',
                'tanggal_publish' => '2024-02-05',
                'is_active' => true,
                'urutan' => 5,
            ],
        ];

        foreach ($videos as $video) {
            VideoBerita::create($video);
        }
    }
}
