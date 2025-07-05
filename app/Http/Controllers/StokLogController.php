<?php

namespace App\Http\Controllers;

use App\Models\StokLog;
use App\Models\Barang;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StokLogController extends Controller
{
    public function index(Request $request)
    {

        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }
        
        $query = StokLog::with(['barang', 'user'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan barang
        if ($request->filled('barang_id')) {
            $query->where('barang_id', $request->barang_id);
        }

        // Filter berdasarkan jenis (masuk/keluar)
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        $stokLogs = $query->paginate(20);

        // Transform the data to include user photo
        $stokLogs->getCollection()->transform(function ($stokLog) {
            $stokLog->user_photo = $stokLog->user->photo;
            return $stokLog;
        });

        $barang = Barang::orderBy('nama_barang')->get();

        return Inertia::render('stok-log/index', [
            'stokLogs' => $stokLogs,
            'barang' => $barang,
            'filters' => $request->only(['barang_id', 'jenis', 'tanggal_dari', 'tanggal_sampai'])
        ]);
    }

    public function show($id)
    {

        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }
        
        $stokLog = StokLog::with(['barang', 'user'])->findOrFail($id);
        
        return Inertia::render('stok-log/show', [
            'stokLog' => $stokLog
        ]);
    }

    public function barang($barangId)
    {

        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }
        
        $barang = Barang::findOrFail($barangId);
        $stokLogs = $barang->stokLogs()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('stok-log/barang', [
            'barang' => $barang,
            'stokLogs' => $stokLogs
        ]);
    }
}
