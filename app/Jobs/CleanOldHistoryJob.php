<?php

namespace App\Jobs;

use App\Models\Permintaan;
use App\Models\Peminjaman;
use App\Models\StokLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanOldHistoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes timeout
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cutoffDate = Carbon::now()->subDays(30);
        $stokLogCutoffDate = Carbon::now()->subDays(90);

        Log::info('Starting automatic history cleanup', [
            'cutoff_date' => $cutoffDate->format('Y-m-d H:i:s'),
            'stok_log_cutoff_date' => $stokLogCutoffDate->format('Y-m-d H:i:s')
        ]);

        try {
            // Clean old permintaan records (completed or rejected)
            $deletedPermintaan = Permintaan::where('created_at', '<', $cutoffDate)
                ->whereIn('status', ['completed', 'rejected'])
                ->delete();

            // Clean old peminjaman records (returned or rejected)
            $deletedPeminjaman = Peminjaman::where('created_at', '<', $cutoffDate)
                ->whereIn('status', ['returned', 'rejected'])
                ->delete();

            // Clean old stok logs (keep for 90 days)
            $deletedStokLogs = StokLog::where('created_at', '<', $stokLogCutoffDate)->delete();

            Log::info('History cleanup completed successfully', [
                'deleted_permintaan' => $deletedPermintaan,
                'deleted_peminjaman' => $deletedPeminjaman,
                'deleted_stok_logs' => $deletedStokLogs
            ]);

        } catch (\Exception $e) {
            Log::error('History cleanup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
} 