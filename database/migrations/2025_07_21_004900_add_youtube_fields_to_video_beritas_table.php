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
        Schema::table('video_beritas', function (Blueprint $table) {
            $table->string('youtube_id')->nullable()->after('urutan');
            $table->string('embed_url')->nullable()->after('youtube_id');
            $table->string('youtube_thumbnail')->nullable()->after('embed_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_beritas', function (Blueprint $table) {
            $table->dropColumn(['youtube_id', 'embed_url', 'youtube_thumbnail']);
        });
    }
}; 