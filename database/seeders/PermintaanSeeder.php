<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permintaan;
use App\Models\User;
use App\Models\Barang;

class PermintaanSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada user dan barang
        $user = User::first();
        $barang = Barang::first();

        if (!$user || !$barang) {
            $this->command->error('User atau Barang tidak ditemukan. Silakan jalankan UserSeeder dan BarangSeeder terlebih dahulu.');
            return;
        }

        // Buat beberapa permintaan
        $statuses = ['pending', 'approved', 'rejected', 'completed'];
        
        for ($i = 0; $i < 5; $i++) {
            Permintaan::create([
                'user_id' => $user->id,
                'barang_id' => $barang->id,
                'jumlah' => rand(1, 10),
                'keterangan' => 'Permintaan untuk testing',
                'status' => $statuses[array_rand($statuses)],
            ]);
        }
    }
} 