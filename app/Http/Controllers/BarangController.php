<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::latest()->get();
        return Inertia::render('Barang/index', [
            'barang' => $barang
        ]);
    }

    public function create()
    {
        return Inertia::render('Barang/create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255|unique:barang',
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|in:peminjaman,permintaan',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string'
        ]);

        Barang::create([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
            'stok' => $request->stok,
        ]);

        return redirect()->route('barang.index')
            ->with('message', 'Barang berhasil ditambahkan');
    }

    public function edit(Barang $barang)
    {
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
            'deskripsi' => 'nullable|string'
        ]);

        $barang->update([
            'nama_barang' => $request->nama_barang,
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
            'stok' => $request->stok,
        ]);

        return redirect()->route('barang.index')
            ->with('message', 'Barang berhasil diperbarui');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();
        return redirect()->route('barang.index')
            ->with('message', 'Barang berhasil dihapus');
    }

    public function stok()
    {
        $barang = Barang::latest()->get();
        return Inertia::render('Barang/stok', [
            'barang' => $barang
        ]);
    }

    public function addStok(Request $request, Barang $barang)
    {
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
        $count = Barang::stokMenipis()->count();
        return response()->json(['count' => $count]);
    }
}
