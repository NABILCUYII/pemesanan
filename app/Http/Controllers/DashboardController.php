<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Barang;
use App\Models\Permintaan;
use App\Models\Peminjaman;
use App\Models\StokLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Calculate statistics
        $stats = [
            [
                'title' => 'Total Barang',
                'value' => Barang::count(),
                'icon' => 'Package',
                'change' => '0%',
                'trend' => 'up',
                'color' => 'from-blue-500 to-blue-600',
            ],
            [
                'title' => 'Total Pinjaman',
                'value' => Peminjaman::count(),
                'icon' => 'Clock',
                'change' => '0%',
                'trend' => 'up',
                'color' => 'from-purple-500 to-purple-600',
            ],
            [
                'title' => 'Total Permintaan',
                'value' => Permintaan::count(),
                'icon' => 'FileText',
                'change' => '0%',
                'trend' => 'up',
                'color' => 'from-green-500 to-green-600',
            ],
            [
                'title' => 'Barang Rusak',
                'value' => Peminjaman::whereIn('kondisi_barang', ['rusak_ringan', 'rusak_berat'])->count(),
                'icon' => 'AlertCircle',
                'change' => '0%',
                'trend' => 'down',
                'color' => 'from-red-500 to-red-600',
            ],
            [
                'title' => 'Total Pengguna',
                'value' => User::count(),
                'icon' => 'Users',
                'change' => '0%',
                'trend' => 'up',
                'color' => 'from-indigo-500 to-indigo-600',
            ],
            [
                'title' => 'Pinjaman Aktif',
                'value' => Peminjaman::whereIn('status', ['approved', 'overdue'])->count(),
                'icon' => 'Activity',
                'change' => '0%',
                'trend' => 'up',
                'color' => 'from-yellow-500 to-yellow-600',
            ],
            [
                'title' => 'Permintaan Pending',
                'value' => Permintaan::where('status', 'pending')->count(),
                'icon' => 'Clock',
                'change' => '0%',
                'trend' => 'up',
                'color' => 'from-orange-500 to-orange-600',
            ],
            [
                'title' => 'Total Kategori',
                'value' => Barang::distinct('kategori')->count(),
                'icon' => 'Database',
                'change' => '0%',
                'trend' => 'up',
                'color' => 'from-pink-500 to-pink-600',
            ],
        ];

        // Get chart data from stok_logs
        $chartData = $this->getChartData();

        // Get recent activities
        $recentActivities = collect();

        // Recent loans
        $recentLoans = Peminjaman::with(['user', 'barang'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($loan) {
                return [
                    'title' => "Pinjaman: {$loan->barang->nama_barang}",
                    'description' => "Dipinjam oleh {$loan->user->name}",
                    'time' => $loan->created_at->diffForHumans(),
                ];
            });

        // Recent requests
        $recentRequests = Permintaan::with(['user', 'barang'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($request) {
                return [
                    'title' => "Permintaan: {$request->barang->nama_barang}",
                    'description' => "Diminta oleh {$request->user->name}",
                    'time' => $request->created_at->diffForHumans(),
                ];
            });

        // Recent items
        $recentItems = Barang::latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'title' => "Barang Baru: {$item->nama_barang}",
                    'description' => "Ditambahkan ke sistem",
                    'time' => $item->created_at->diffForHumans(),
                ];
            });

        // Combine and sort by time
        $recentActivities = $recentLoans->concat($recentRequests)->concat($recentItems)
            ->sortByDesc('time')
            ->take(10)
            ->values();

        // If no activities, provide default message
        if ($recentActivities->isEmpty()) {
            $recentActivities = collect([
                [
                    'title' => 'Tidak ada aktivitas',
                    'description' => 'Belum ada aktivitas terbaru.',
                    'time' => '',
                ]
            ]);
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'chartData' => $chartData
        ]);
    }

    private function getChartData()
    {
        // Get barang masuk data per month (last 12 months)
        $barangMasuk = StokLog::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(jumlah) as total')
            )
            ->where('jenis', 'masuk')
            ->where('created_at', '>=', now()->subMonths(12)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Get frequently requested items (top 6)
        $seringDiminta = StokLog::select('barang_id', DB::raw('SUM(jumlah) as total'))
            ->where('jenis', 'keluar')
            ->whereHas('barang', function($query) {
                $query->where('kategori', 'permintaan');
            })
            ->groupBy('barang_id')
            ->orderByDesc('total')
            ->with('barang')
            ->limit(6)
            ->get();

        // Get frequently borrowed items (top 6)
        $seringDipinjam = StokLog::select('barang_id', DB::raw('SUM(jumlah) as total'))
            ->where('jenis', 'keluar')
            ->whereHas('barang', function($query) {
                $query->where('kategori', 'peminjaman');
            })
            ->groupBy('barang_id')
            ->orderByDesc('total')
            ->with('barang')
            ->limit(6)
            ->get();

        return [
            'barangMasuk' => $barangMasuk,
            'seringDiminta' => $seringDiminta,
            'seringDipinjam' => $seringDipinjam
        ];
    }
} 