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
        Schema::table('permintaan', function (Blueprint $table) {
            $table->string('alasan_approval')->nullable()->after('status');
            $table->text('catatan_approval')->nullable()->after('alasan_approval');
            $table->unsignedBigInteger('approved_by')->nullable()->after('catatan_approval');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permintaan', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['alasan_approval', 'catatan_approval', 'approved_by', 'approved_at']);
        });
    }
};
