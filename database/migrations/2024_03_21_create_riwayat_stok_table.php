<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riwayat_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->integer('jumlah');
            $table->enum('tipe', ['tambah', 'kurang']);
            $table->string('keterangan')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // Create a stored procedure to automatically delete records older than 1 month
        DB::unprepared('
            CREATE EVENT IF NOT EXISTS reset_riwayat_stok
            ON SCHEDULE EVERY 1 DAY
            DO
                DELETE FROM riwayat_stok 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 MONTH);
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the event first
        DB::unprepared('DROP EVENT IF EXISTS reset_riwayat_stok');
        
        Schema::dropIfExists('riwayat_stok');
    }
}; 