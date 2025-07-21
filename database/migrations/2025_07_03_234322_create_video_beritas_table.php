<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_beritas');
    }
};
