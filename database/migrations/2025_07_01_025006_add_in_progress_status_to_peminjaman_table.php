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
            // Drop the existing enum and recreate it with the new status
            $table->enum('status', ['pending', 'approved', 'in_progress', 'returned', 'rejected', 'overdue'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Revert back to original enum
            $table->enum('status', ['pending', 'approved', 'returned', 'rejected', 'overdue'])->default('pending')->change();
        });
    }
};
