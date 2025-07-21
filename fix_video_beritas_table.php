<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Memperbaiki tabel video_beritas...\n";

// Cek apakah tabel sudah ada
if (!Schema::hasTable('video_beritas')) {
    echo "Membuat tabel video_beritas...\n";
    Schema::create('video_beritas', function (Blueprint $table) {
        $table->id();
        $table->string('judul');
        $table->text('deskripsi')->nullable();
        $table->string('video_url');
        $table->string('thumbnail_url')->nullable();
        $table->string('sumber')->nullable();
        $table->date('tanggal_publish');
        $table->boolean('is_active')->default(true);
        $table->integer('urutan')->default(0);
        $table->string('youtube_id')->nullable();
        $table->string('embed_url')->nullable();
        $table->string('youtube_thumbnail')->nullable();
        $table->timestamps();
    });
    echo "Tabel video_beritas berhasil dibuat!\n";
} else {
    echo "Tabel video_beritas sudah ada, mengecek kolom...\n";
    
    // Cek dan tambah kolom yang hilang
    $columns = Schema::getColumnListing('video_beritas');
    
    if (!in_array('youtube_id', $columns)) {
        echo "Menambahkan kolom youtube_id...\n";
        Schema::table('video_beritas', function (Blueprint $table) {
            $table->string('youtube_id')->nullable()->after('urutan');
        });
    }
    
    if (!in_array('embed_url', $columns)) {
        echo "Menambahkan kolom embed_url...\n";
        Schema::table('video_beritas', function (Blueprint $table) {
            $table->string('embed_url')->nullable()->after('youtube_id');
        });
    }
    
    if (!in_array('youtube_thumbnail', $columns)) {
        echo "Menambahkan kolom youtube_thumbnail...\n";
        Schema::table('video_beritas', function (Blueprint $table) {
            $table->string('youtube_thumbnail')->nullable()->after('embed_url');
        });
    }
}

echo "Perbaikan selesai!\n"; 