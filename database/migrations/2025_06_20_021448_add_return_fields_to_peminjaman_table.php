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
            $table->enum('kondisi_barang', ['baik', 'rusak_ringan', 'rusak_berat'])->nullable()->after('status');
            $table->text('catatan_pengembalian')->nullable()->after('kondisi_barang');
            $table->foreignId('returned_by')->nullable()->constrained('users')->after('catatan_pengembalian');
            $table->timestamp('returned_at')->nullable()->after('returned_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['returned_by']);
            $table->dropColumn(['kondisi_barang', 'catatan_pengembalian', 'returned_by', 'returned_at']);
        });
    }
};
