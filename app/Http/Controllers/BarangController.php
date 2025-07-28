<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BarangController extends Controller
{
    public function index()
    {
        // Check if user is admin
        // Check if user is admin
        $barang = Barang::latest()->paginate(10);
        // Tambahkan total kategori ke meta
        $assetCount = Barang::where('kategori', 'peminjaman')->count();
        $consumableCount = Barang::where('kategori', 'permintaan')->count();
        $barang->setCollection($barang->getCollection()); // pastikan collection tetap
        $meta = $barang->toArray();
        $meta['meta']['asset_count'] = $assetCount;
        $meta['meta']['consumable_count'] = $consumableCount;
        return Inertia::render('Barang/index', [
            'barang' => $meta
        ]);
    }

    public function aset()
    {
        $barang = Barang::where('kategori', 'peminjaman')->latest()->paginate(12); // 12 per page, bisa diubah
        
        // Get total counts for statistics
        $totalAset = Barang::where('kategori', 'peminjaman')->count();
        $totalTersedia = Barang::where('kategori', 'peminjaman')->where('stok', '>', 0)->count();
        $totalHabis = Barang::where('kategori', 'peminjaman')->where('stok', 0)->count();
        
        return Inertia::render('Barang/aset', [
            'barang' => $barang,
            'stats' => [
                'total_aset' => $totalAset,
                'total_tersedia' => $totalTersedia,
                'total_habis' => $totalHabis
            ]
        ]);
    }

    public function permintaan()
    {
        $barang = Barang::where('kategori', 'permintaan')->latest()->paginate(12); // 12 per page, bisa diubah
        
        // Get total counts for statistics
        $totalPermintaan = Barang::where('kategori', 'permintaan')->count();
        $totalTersedia = Barang::where('kategori', 'permintaan')->where('stok', '>', 0)->count();
        $totalHabis = Barang::where('kategori', 'permintaan')->where('stok', 0)->count();
        
        return Inertia::render('Barang/permintaan', [
            'barang' => $barang,
            'stats' => [
                'total_permintaan' => $totalPermintaan,
                'total_tersedia' => $totalTersedia,
                'total_habis' => $totalHabis
            ]
        ]);
    }

    public function create()
    {

        // Check if user is admin
       // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }
        
        return Inertia::render('Barang/create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255|unique:barang',
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|in:peminjaman,permintaan',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'satuan' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255'
        ]);

        Barang::create([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
            'stok' => $request->stok,
            'satuan' => $request->satuan,
            'lokasi' => $request->lokasi,
        ]);

        // Redirect berdasarkan kategori
        $redirectRoute = $request->kategori === 'peminjaman' ? 'barang.aset' : 'barang.permintaan';
        return redirect()->route($redirectRoute)
            ->with('message', 'Barang berhasil ditambahkan');
    }

    public function edit(Barang $barang)
    {

        // Check if user is admin
       // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }
        
        return Inertia::render('Barang/edit', [
            'barang' => $barang
        ]);
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255|unique:barang,kode_barang,' . $barang->id,
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|in:peminjaman,permintaan',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'satuan' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255'
        ]);

        $barang->update([
            'nama_barang' => $request->nama_barang,
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
            'stok' => $request->stok,
            'satuan' => $request->satuan,
            'lokasi' => $request->lokasi,
        ]);

        // Redirect berdasarkan kategori
        $redirectRoute = $request->kategori === 'peminjaman' ? 'barang.aset' : 'barang.permintaan';
        return redirect()->route($redirectRoute)
            ->with('message', 'Barang berhasil diperbarui');
    }

    public function destroy(Barang $barang)
    {

        // Check if user is admin
       // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }
        
        $kategori = $barang->kategori;
        $barang->delete();
        
        // Redirect berdasarkan kategori barang yang dihapus
        $redirectRoute = $kategori === 'peminjaman' ? 'barang.aset' : 'barang.permintaan';
        return redirect()->route($redirectRoute)
            ->with('message', 'Barang berhasil dihapus');
    }

    public function stok(Request $request)
    {

        // Check if user is admin
       // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }
        
        $query = Barang::latest();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }
        
        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }
        
        $barang = $query->paginate(12);
        
        // Get statistics for summary cards
        $totalBarang = Barang::count();
        $stokHabis = Barang::where('stok', 0)->count();
        $stokMenipis = Barang::where('stok', '>', 0)->where('stok', '<=', 5)->count();
        
        return Inertia::render('Barang/stok', [
            'barang' => $barang,
            'filters' => $request->only(['search', 'kategori']),
            'statistics' => [
                'total' => $totalBarang,
                'stok_habis' => $stokHabis,
                'stok_menipis' => $stokMenipis
            ]
        ]);
    }

    public function addStok(Request $request, Barang $barang)
    {

        // Check if user is admin
       // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }
        
        $request->validate([
            'stok_tambah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:500'
        ]);

        // Menggunakan sistem log stok
        $barang->addStokLog('masuk', $request->stok_tambah, $request->keterangan);

        return redirect()->back()
            ->with('message', "Stok berhasil ditambahkan. Stok baru: {$barang->stok}");
    }

    // Endpoint notifikasi stok menipis
    public function stokMenipisCount()
    {

        // Check if user is admin
       // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }
        
        $count = Barang::stokMenipis()->count();
        return response()->json(['count' => $count]);
    }

    // Endpoint notifikasi stok habis
    public function stokHabisCount()
    {

        // Check if user is admin
       // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }
        
        $count = Barang::where('stok', 0)->count();
        return response()->json(['count' => $count]);
    }

    public function semua(Request $request)
    {
        $query = Barang::latest();
        if ($request->has('q')) {
            $q = $request->input('q');
            $query->where('nama_barang', 'like', "%$q%");
        }
        $barang = $query->paginate(12);
        
        // Get total counts for statistics
        $totalBarang = Barang::count();
        $totalAset = Barang::where('kategori', 'peminjaman')->count();
        $totalPermintaan = Barang::where('kategori', 'permintaan')->count();
        $totalTersedia = Barang::where('stok', '>', 0)->count();
        $totalHabis = Barang::where('stok', 0)->count();
        
        if ($request->wantsJson()) {
            return response()->json($barang);
        }
        return Inertia::render('Barang/semuaBRG', [
            'barang' => $barang,
            'stats' => [
                'total_barang' => $totalBarang,
                'total_aset' => $totalAset,
                'total_permintaan' => $totalPermintaan,
                'total_tersedia' => $totalTersedia,
                'total_habis' => $totalHabis
            ]
        ]);
    }
}
