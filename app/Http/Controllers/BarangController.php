<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\RiwayatStok;
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
        $barang = Barang::with(['riwayatStok' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->get();

        return Inertia::render('Barang/stok', [
            'barang' => $barang,
            'riwayat_stok' => RiwayatStok::with('barang')
                ->orderBy('created_at', 'desc')
                ->get()
        ]);
    }

    public function addStok(Request $request, Barang $barang)
    {
        $request->validate([
            'stok_tambah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $barang->stok += $request->stok_tambah;
        $barang->save();

        // Create riwayat stok record
        RiwayatStok::create([
            'barang_id' => $barang->id,
            'jumlah' => $request->stok_tambah,
            'tipe' => 'tambah',
            'keterangan' => $request->keterangan
        ]);

        return redirect()->back()->with('success', 'Stok berhasil ditambahkan');
    }

    public function kurangiStok(Request $request, Barang $barang)
    {
        $request->validate([
            'stok_kurang' => 'required|integer|min:1|max:' . $barang->stok,
            'keterangan' => 'nullable|string|max:255'
        ]);

        $barang->stok -= $request->stok_kurang;
        $barang->save();

        // Create riwayat stok record
        RiwayatStok::create([
            'barang_id' => $barang->id,
            'jumlah' => $request->stok_kurang,
            'tipe' => 'kurang',
            'keterangan' => $request->keterangan
        ]);

        return redirect()->back()->with('success', 'Stok berhasil dikurangi');
    }

    public function getRiwayatStok(Barang $barang)
    {
        $riwayat = RiwayatStok::where('barang_id', $barang->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($riwayat);
    }
}
