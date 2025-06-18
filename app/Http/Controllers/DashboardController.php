<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Barang;
use App\Models\Permintaan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_barang' => Barang::count(),
            'barang_dipinjam' => Permintaan::where('status', 'dipinjam')->count(),
            'total_pengguna' => User::count(),
            'barang_rusak' => Barang::where('kondisi', 'rusak')->count(),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    }
} 