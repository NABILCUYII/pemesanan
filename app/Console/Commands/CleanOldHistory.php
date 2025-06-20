<?php

namespace App\Console\Commands;

use App\Models\Permintaan;
use App\Models\Peminjaman;
use App\Models\StokLog;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanOldHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'history:clean {--days=30 : Number of days to keep history}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old history records (permintaan, peminjaman, stok logs) older than specified days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Cleaning history records older than {$days} days (before {$cutoffDate->format('Y-m-d H:i:s')})");

        // Clean old permintaan records
        $deletedPermintaan = Permintaan::where('created_at', '<', $cutoffDate)
            ->whereIn('status', ['completed', 'rejected'])
            ->delete();

        // Clean old peminjaman records
        $deletedPeminjaman = Peminjaman::where('created_at', '<', $cutoffDate)
            ->whereIn('status', ['returned', 'rejected'])
            ->delete();

        // Clean old stok logs (keep for 90 days by default)
        $stokLogDays = 90;
        $stokLogCutoffDate = Carbon::now()->subDays($stokLogDays);
        $deletedStokLogs = StokLog::where('created_at', '<', $stokLogCutoffDate)->delete();

        $this->info("Cleaned {$deletedPermintaan} permintaan records");
        $this->info("Cleaned {$deletedPeminjaman} peminjaman records");
        $this->info("Cleaned {$deletedStokLogs} stok log records (older than {$stokLogDays} days)");

        $this->info('History cleanup completed successfully!');
    }
} 