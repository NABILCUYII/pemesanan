<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Barang;
use Carbon\Carbon;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada user dan barang
        $users = User::all();
        $barangs = Barang::all();

        if ($users->isEmpty() || $barangs->isEmpty()) {
            $this->command->error('User atau Barang tidak ditemukan. Silakan jalankan UserSeeder dan BarangSeeder terlebih dahulu.');
            return;
        }

        $statuses = ['pending', 'approved', 'returned', 'overdue'];
        $kondisiBarang = ['baik', 'rusak_ringan', 'rusak_berat'];

        // Buat beberapa peminjaman
        for ($i = 0; $i < 10; $i++) {
            $user = $users->random();
            $barang = $barangs->random();
            $status = $statuses[array_rand($statuses)];
            $tanggalPeminjaman = Carbon::now()->subDays(rand(1, 30));
            $dueDate = $tanggalPeminjaman->copy()->addDays(rand(7, 14));
            
            $peminjaman = Peminjaman::create([
                'user_id' => $user->id,
                'barang_id' => $barang->id,
                'jumlah' => rand(1, min(3, $barang->stok)),
                'keterangan' => 'Peminjaman untuk keperluan ' . ['kerja', 'acara', 'presentasi', 'meeting'][array_rand(['kerja', 'acara', 'presentasi', 'meeting'])],
                'tanggal_peminjaman' => $tanggalPeminjaman,
                'due_date' => $dueDate,
                'status' => $status,
            ]);

            // Jika status returned, tambahkan data pengembalian
            if ($status === 'returned') {
                $peminjaman->update([
                    'tanggal_pengembalian' => $dueDate->copy()->subDays(rand(0, 3)),
                    'kondisi_barang' => $kondisiBarang[array_rand($kondisiBarang)],
                    'catatan_pengembalian' => 'Barang dikembalikan dalam kondisi ' . $peminjaman->kondisi_barang,
                    'returned_by' => $users->random()->id,
                    'returned_at' => Carbon::now(),
                ]);
            }
        }
    }
} 