<?php

namespace App\Console\Commands;

use App\Models\StokLog;
use App\Models\Permintaan;
use App\Models\Peminjaman;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixStokLogUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stok-log:fix-users {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix stok log user_id to show the actual user who requested/borrowed items instead of the admin who approved them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        $this->info('Starting to fix stok log user_id values...');

        // Fix permintaan-related stok logs
        $this->fixPermintaanStokLogs($isDryRun);

        // Fix peminjaman-related stok logs
        $this->fixPeminjamanStokLogs($isDryRun);

        // Fix return-related stok logs
        $this->fixReturnStokLogs($isDryRun);

        $this->info('Stok log user_id fix completed!');
    }

    private function fixPermintaanStokLogs($isDryRun)
    {
        $this->info('Fixing permintaan-related stok logs...');

        $stokLogs = StokLog::where('jenis', 'keluar')
            ->where('referensi', 'like', 'Permintaan #%')
            ->get();

        $fixedCount = 0;

        foreach ($stokLogs as $stokLog) {
            // Extract permintaan ID from referensi
            $permintaanId = (int) str_replace('Permintaan #', '', $stokLog->referensi);
            
            $permintaan = Permintaan::find($permintaanId);
            
            if ($permintaan && $permintaan->user_id !== $stokLog->user_id) {
                $this->line("Permintaan #{$permintaanId}: Changing user_id from {$stokLog->user_id} to {$permintaan->user_id}");
                
                if (!$isDryRun) {
                    $stokLog->update(['user_id' => $permintaan->user_id]);
                }
                
                $fixedCount++;
            }
        }

        $this->info("Fixed {$fixedCount} permintaan-related stok logs");
    }

    private function fixPeminjamanStokLogs($isDryRun)
    {
        $this->info('Fixing peminjaman-related stok logs...');

        $stokLogs = StokLog::where('jenis', 'keluar')
            ->where('referensi', 'like', 'Peminjaman #%')
            ->get();

        $fixedCount = 0;

        foreach ($stokLogs as $stokLog) {
            // Extract peminjaman ID from referensi
            $peminjamanId = (int) str_replace('Peminjaman #', '', $stokLog->referensi);
            
            $peminjaman = Peminjaman::find($peminjamanId);
            
            if ($peminjaman && $peminjaman->user_id !== $stokLog->user_id) {
                $this->line("Peminjaman #{$peminjamanId}: Changing user_id from {$stokLog->user_id} to {$peminjaman->user_id}");
                
                if (!$isDryRun) {
                    $stokLog->update(['user_id' => $peminjaman->user_id]);
                }
                
                $fixedCount++;
            }
        }

        $this->info("Fixed {$fixedCount} peminjaman-related stok logs");
    }

    private function fixReturnStokLogs($isDryRun)
    {
        $this->info('Fixing return-related stok logs...');

        $stokLogs = StokLog::where('jenis', 'masuk')
            ->where('referensi', 'like', 'Pengembalian Peminjaman #%')
            ->get();

        $fixedCount = 0;

        foreach ($stokLogs as $stokLog) {
            // Extract peminjaman ID from referensi
            $peminjamanId = (int) str_replace('Pengembalian Peminjaman #', '', $stokLog->referensi);
            
            $peminjaman = Peminjaman::find($peminjamanId);
            
            if ($peminjaman && $peminjaman->user_id !== $stokLog->user_id) {
                $this->line("Pengembalian Peminjaman #{$peminjamanId}: Changing user_id from {$stokLog->user_id} to {$peminjaman->user_id}");
                
                if (!$isDryRun) {
                    $stokLog->update(['user_id' => $peminjaman->user_id]);
                }
                
                $fixedCount++;
            }
        }

        $this->info("Fixed {$fixedCount} return-related stok logs");
    }
} 