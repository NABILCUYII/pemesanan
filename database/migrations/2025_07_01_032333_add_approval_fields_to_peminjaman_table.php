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
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->string('alasan_approval')->nullable()->after('status');
            $table->string('catatan_approval')->nullable()->after('alasan_approval');
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('catatan_approval');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['alasan_approval', 'catatan_approval', 'approved_by', 'approved_at']);
        });
    }
};
