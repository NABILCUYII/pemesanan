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
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|in:peminjaman,permintaan',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string'
        ]);

        $last = Barang::orderBy('id', 'desc')->first();
        $lastId = $last ? $last->id + 1 : 1;
        $kodeBarang = 'BRG' . str_pad($lastId, 3, '0', STR_PAD_LEFT);

        Barang::create([
            'kode_barang' => $kodeBarang,
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
}
