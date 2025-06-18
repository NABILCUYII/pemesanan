<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Barang;
use App\Models\Permintaan;
use App\Models\Peminjaman;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang = Barang::count();
        $totalPermintaan = Permintaan::count();
        $totalPeminjaman = Peminjaman::count();
        $totalUsers = User::count();

        $recentPermintaan = Permintaan::with(['user', 'barang'])
            ->latest()
            ->take(5)
            ->get();

        $recentPeminjaman = Peminjaman::with(['user', 'barang'])
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalBarang' => $totalBarang,
                'totalPermintaan' => $totalPermintaan,
                'totalPeminjaman' => $totalPeminjaman,
                'totalUsers' => $totalUsers,
            ],
            'recentPermintaan' => $recentPermintaan,
            'recentPeminjaman' => $recentPeminjaman,
        ]);
    }
} 