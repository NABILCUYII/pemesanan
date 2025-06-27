<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RiwayatController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get user's requests and loans
        $permintaan = Permintaan::with(['user', 'barang'])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'permintaan',
                    'nama_barang' => $item->barang->nama_barang,
                    'kode_barang' => $item->barang->kode_barang,
                    'jumlah' => $item->jumlah,
                    'status' => $item->status,
                    'created_at' => $item->created_at,
                    'keterangan' => $item->keterangan,
                    'alasan_approval' => $item->alasan_approval,
                    'catatan_approval' => $item->catatan_approval,
                    'user_photo' => $item->user->photo,
                ];
            });

        $peminjaman = Peminjaman::with(['user', 'barang'])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'peminjaman',
                    'nama_barang' => $item->barang->nama_barang,
                    'kode_barang' => $item->barang->kode_barang,
                    'jumlah' => $item->jumlah,
                    'status' => $item->status,
                    'created_at' => $item->created_at,
                    'tanggal_peminjaman' => $item->tanggal_peminjaman,
                    'tanggal_pengembalian' => $item->tanggal_pengembalian,
                    'due_date' => $item->due_date,
                    'keterangan' => $item->keterangan,
                    'alasan_approval' => $item->alasan_approval,
                    'catatan_approval' => $item->catatan_approval,
                    'user_photo' => $item->user->photo,
                ];
            });

        // Combine and sort by date
        $riwayat = $permintaan->concat($peminjaman)
            ->sortByDesc('created_at')
            ->values();

        return Inertia::render('riwayat/index', [
            'riwayat' => $riwayat
        ]);
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'type' => 'required|in:permintaan,peminjaman',
            'id' => 'required|integer'
        ]);

        if ($request->type === 'permintaan') {
            $item = Permintaan::where('user_id', auth()->id())
                ->where('status', 'pending')
                ->findOrFail($request->id);
            
            $item->delete();
            return redirect()->back()->with('message', 'Permintaan berhasil dibatalkan');
        } else {
            $item = Peminjaman::where('user_id', auth()->id())
                ->where('status', 'pending')
                ->findOrFail($request->id);
            
            $item->delete();
            return redirect()->back()->with('message', 'Peminjaman berhasil dibatalkan');
        }
    }
} 