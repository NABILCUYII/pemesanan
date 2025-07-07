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
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->string('nomor_inventaris')->unique(); // Nomor inventaris unik
            $table->integer('jumlah');
            $table->text('keterangan')->nullable();
            $table->date('tanggal_peminjaman');
            $table->date('tanggal_pengembalian')->nullable();
            $table->date('due_date');
            $table->enum('status', ['pending', 'approved', 'in_progress', 'returned', 'rejected', 'overdue', 'maintenance'])->default('pending');
            $table->enum('kondisi_barang', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])->nullable();
            $table->text('catatan_pengembalian')->nullable();
            $table->string('lokasi_peminjaman')->nullable(); // Lokasi dimana barang dipinjam
            $table->string('alasan_approval')->nullable();
            $table->text('catatan_approval')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('returned_by')->nullable()->constrained('users');
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->foreignId('started_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
}; 