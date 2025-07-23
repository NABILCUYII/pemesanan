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
        
        
        $barang = Barang::latest()->get();
        return Inertia::render('Barang/index', [
            'barang' => $barang
        ]);
    }

    public function aset()
    {

       
        
        $barang = Barang::latest()->get();
        return Inertia::render('Barang/aset', [
            'barang' => $barang
        ]);
    }

    public function permintaan()
    {

      
        
        $barang = Barang::latest()->get();
        return Inertia::render('Barang/permintaan', [
            'barang' => $barang
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

    public function stok()
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
        
        $barang = Barang::latest()->get();
        return Inertia::render('Barang/stok', [
            'barang' => $barang
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

    public function semua()
    {
        $barang = Barang::latest()->get();
        return Inertia::render('Barang/semuaBRG', [
            'barang' => $barang
        ]);
    }
}
