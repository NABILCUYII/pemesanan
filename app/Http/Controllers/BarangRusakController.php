<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Inventaris;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BarangRusakController extends Controller
{
    public function index()
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
        
        // Get barang rusak from barang table
        $barangRusak = Barang::whereIn('status', ['rusak', 'hilang'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get barang rusak from inventaris table
        $inventarisRusak = Inventaris::with(['barang', 'user'])
            ->whereIn('kondisi_barang', ['rusak_ringan', 'rusak_berat', 'hilang'])
            ->where('status', 'returned')
            ->orderBy('returned_at', 'desc')
            ->get()
            ->map(function($inventaris) {
                return [
                    'id' => 'inv_' . $inventaris->id, // Prefix to distinguish from barang
                    'kode_barang' => $inventaris->barang->kode_barang,
                    'nama_barang' => $inventaris->barang->nama_barang,
                    'deskripsi' => $inventaris->barang->deskripsi,
                    'kategori' => $inventaris->barang->kategori,
                    'lokasi' => $inventaris->barang->lokasi,
                    'stok' => $inventaris->barang->stok,
                    'status' => $inventaris->kondisi_barang === 'hilang' ? 'hilang' : 'rusak',
                    'source' => 'inventaris',
                    'nomor_inventaris' => $inventaris->nomor_inventaris,
                    'nama_peminjam' => $inventaris->user->name,
                    'jumlah_dipinjam' => $inventaris->jumlah,
                    'tanggal_peminjaman' => $inventaris->tanggal_peminjaman,
                    'tanggal_pengembalian' => $inventaris->tanggal_pengembalian,
                    'catatan_pengembalian' => $inventaris->catatan_pengembalian,
                    'returned_at' => $inventaris->returned_at,
                ];
            });

        // Combine both data sources
        $allBarangRusak = $barangRusak->map(function($barang) {
            return [
                'id' => $barang->id,
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang,
                'deskripsi' => $barang->deskripsi,
                'kategori' => $barang->kategori,
                'lokasi' => $barang->lokasi,
                'stok' => $barang->stok,
                'status' => $barang->status,
                'source' => 'barang',
                'nomor_inventaris' => null,
                'nama_peminjam' => null,
                'jumlah_dipinjam' => null,
                'tanggal_peminjaman' => null,
                'tanggal_pengembalian' => null,
                'catatan_pengembalian' => null,
                'returned_at' => null,
            ];
        })->concat($inventarisRusak);

        return Inertia::render('BarangRusak/Index', [
            'barangRusak' => $allBarangRusak
        ]);
    }

    public function create()
    {

        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        $barangList = Barang::where('status', 'baik')
            ->orderBy('nama_barang', 'asc')
            ->get(['id', 'kode_barang', 'nama_barang', 'stok']);

        return Inertia::render('BarangRusak/Create', [
            'barangList' => $barangList
        ]);
    }

    public function createHilang()
    {
        $barangList = Barang::where('status', 'baik')
            ->orderBy('nama_barang', 'asc')
            ->get(['id', 'kode_barang', 'nama_barang', 'stok']);

        return Inertia::render('BarangRusak/CreateHilang', [
            'barangList' => $barangList
        ]);
    }

    public function createFromInventaris()
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        // Get active inventory items that are currently borrowed
        $inventarisList = Inventaris::with('barang')
            ->whereIn('status', ['approved', 'in_progress'])
            ->whereHas('barang', function($query) {
                $query->where('stok', '>', 0);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($inventaris) {
                return [
                    'id' => $inventaris->id,
                    'nomor_inventaris' => $inventaris->nomor_inventaris,
                    'barang_id' => $inventaris->barang->id,
                    'kode_barang' => $inventaris->barang->kode_barang,
                    'nama_barang' => $inventaris->barang->nama_barang,
                    'jumlah_dipinjam' => $inventaris->jumlah,
                    'nama_peminjam' => $inventaris->user->name,
                    'tanggal_peminjaman' => $inventaris->tanggal_peminjaman,
                    'due_date' => $inventaris->due_date,
                    'keterangan' => $inventaris->keterangan,
                    'lokasi_peminjaman' => $inventaris->lokasi_peminjaman,
                ];
            });

        return Inertia::render('BarangRusak/CreateFromInventaris', [
            'inventarisList' => $inventarisList
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah_rusak' => 'required|integer|min:1',
            'keterangan' => 'nullable|string'
        ]);

        $barang = Barang::findOrFail($request->barang_id);
        
        // Check if available stok is sufficient
        if ($barang->stok < $request->jumlah_rusak) {
            return back()->withErrors(['jumlah_rusak' => 'Jumlah rusak tidak boleh melebihi stok yang tersedia']);
        }

        // Update barang stok and status
        $barang->addStokLog('keluar', $request->jumlah_rusak, 'Barang rusak: ' . $request->keterangan);
        
        // If all stok is damaged, update status to rusak
        if ($barang->stok == 0) {
            $barang->update(['status' => 'rusak']);
        }

        return redirect()->route('barang-rusak.index')->with('success', 'Barang berhasil ditandai sebagai rusak');
    }

    public function storeHilang(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah_hilang' => 'required|integer|min:1',
            'keterangan' => 'nullable|string'
        ]);

        $barang = Barang::findOrFail($request->barang_id);
        
        // Check if available stok is sufficient
        if ($barang->stok < $request->jumlah_hilang) {
            return back()->withErrors(['jumlah_hilang' => 'Jumlah hilang tidak boleh melebihi stok yang tersedia']);
        }

        // Update barang stok and status
        $barang->addStokLog('keluar', $request->jumlah_hilang, 'Barang hilang: ' . $request->keterangan);
        
        // If all stok is lost, update status to hilang
        if ($barang->stok == 0) {
            $barang->update(['status' => 'hilang']);
        }

        return redirect()->route('barang-rusak.index')->with('success', 'Barang berhasil ditandai sebagai hilang');
    }

    public function storeFromInventaris(Request $request)
    {
        $request->validate([
            'inventaris_id' => 'required|exists:inventaris,id',
            'jumlah_rusak' => 'required|integer|min:1',
            'kondisi_barang' => 'required|in:rusak_ringan,rusak_berat,hilang',
            'keterangan' => 'nullable|string|max:500'
        ]);

        $inventaris = Inventaris::with('barang')->findOrFail($request->inventaris_id);
        
        // Check if the inventory item is still active
        if (!in_array($inventaris->status, ['approved', 'in_progress'])) {
            return back()->withErrors(['inventaris_id' => 'Item inventaris tidak aktif atau sudah dikembalikan']);
        }
        
        // Check if jumlah rusak doesn't exceed borrowed amount
        if ($request->jumlah_rusak > $inventaris->jumlah) {
            return back()->withErrors(['jumlah_rusak' => 'Jumlah rusak tidak boleh melebihi jumlah yang dipinjam']);
        }

        // Update inventory status to returned with damaged condition
        $inventaris->update([
            'status' => 'returned',
            'kondisi_barang' => $request->kondisi_barang,
            'catatan_pengembalian' => $request->keterangan,
            'tanggal_pengembalian' => now(),
            'returned_at' => now(),
            'returned_by' => auth()->id()
        ]);

        // Calculate how much can be returned to stock based on condition
        $jumlahDikembalikan = match($request->kondisi_barang) {
            'rusak_ringan' => floor($inventaris->jumlah * 0.5), // 50% can be repaired
            'rusak_berat', 'hilang' => 0, // Nothing can be returned
            default => 0
        };

        // Update barang stok
        $barang = $inventaris->barang;
        $keterangan = "Barang rusak dari inventaris - Kondisi: " . ucfirst(str_replace('_', ' ', $request->kondisi_barang));
        if ($request->keterangan) {
            $keterangan .= " - " . $request->keterangan;
        }
        
        $barang->addStokLog(
            'keluar', 
            $inventaris->jumlah - $jumlahDikembalikan, 
            $keterangan,
            "Inventaris #" . $inventaris->nomor_inventaris,
            $inventaris->user_id
        );

        // If some items can be returned, add them back
        if ($jumlahDikembalikan > 0) {
            $barang->addStokLog(
                'masuk',
                $jumlahDikembalikan,
                "Pengembalian barang yang dapat diperbaiki dari inventaris #" . $inventaris->nomor_inventaris,
                "Inventaris #" . $inventaris->nomor_inventaris,
                $inventaris->user_id
            );
        }

        // Update barang status if all stock is damaged
        if ($barang->stok == 0) {
            $barang->update(['status' => 'rusak']);
        }

        return redirect()->route('barang-rusak.index')
            ->with('success', 'Barang dari inventaris berhasil ditandai sebagai rusak');
    }

    public function updateStatus(Request $request, Barang $barang)
    {
        $request->validate([
            'status' => 'required|in:baik,rusak,hilang'
        ]);

        $barang->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status barang berhasil diperbarui');
    }
}
