<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\Barang;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PermintaanController extends Controller
{
    public function index()
    {
        $permintaan = Permintaan::with(['user', 'barang'])->latest()->get();
        return Inertia::render('permintaan/index', [
            'permintaan' => $permintaan
        ]);
    }

    public function create()
    {
        $barang = Barang::where('kategori', 'permintaan')->get();
        return Inertia::render('permintaan/create', [
            'barang' => $barang
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:500',
            'status' => 'required|in:pending,approved,rejected,completed'
        ]);

        Permintaan::create([
            'barang_id' => $request->barang_id,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('permintaan.index')
            ->with('message', 'Permintaan berhasil ditambahkan');
    }

    public function storeMultiple(Request $request)
    {
        $request->validate([
            'requests' => 'required|array|min:1',
            'requests.*.barang_id' => 'required|exists:barang,id',
            'requests.*.jumlah' => 'required|integer|min:1',
            'requests.*.keterangan' => 'nullable|string|max:500',
            'requests.*.status' => 'required|in:pending,approved,rejected,completed'
        ]);

        $requests = collect($request->requests)->map(function ($item) {
            return [
                'barang_id' => $item['barang_id'],
                'jumlah' => $item['jumlah'],
                'keterangan' => $item['keterangan'] ?? '',
                'status' => $item['status'],
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ];
        });

        Permintaan::insert($requests->toArray());

        $count = count($request->requests);
        return redirect()->route('permintaan.index')
            ->with('message', "Berhasil membuat {$count} permintaan sekaligus");
    }

    public function edit(Permintaan $permintaan)
    {
        $barang = Barang::where('kategori', 'permintaan')->get();
        return Inertia::render('permintaan/edit', [
            'permintaan' => $permintaan->load('barang'),
            'barang' => $barang
        ]);
    }

    public function update(Request $request, Permintaan $permintaan)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:500',
            'status' => 'required|in:pending,approved,rejected,completed'
        ]);

        $permintaan->update([
            'barang_id' => $request->barang_id,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'status' => $request->status
        ]);

        return redirect()->route('permintaan.index')
            ->with('message', 'Permintaan berhasil diperbarui');
    }

    public function destroy(Permintaan $permintaan)
    {
        $permintaan->delete();
        return redirect()->route('permintaan.index')
            ->with('message', 'Permintaan berhasil dihapus');
    }

    public function approval()
    {
        $permintaan = Permintaan::with(['user', 'barang', 'approvedBy'])->latest()->get();
        return Inertia::render('permintaan/approval', [
            'permintaan' => $permintaan
        ]);
    }

    public function approve(Request $request)
    {
        $request->validate([
            'permintaan_id' => 'required|exists:permintaan,id',
            'action' => 'required|in:approve,reject',
            'alasan' => 'required|string|max:500',
            'catatan' => 'nullable|string|max:500'
        ]);

        $permintaan = Permintaan::findOrFail($request->permintaan_id);
        
        if ($request->action === 'approve') {
            $permintaan->update([
                'status' => 'approved',
                'alasan_approval' => $request->alasan,
                'catatan_approval' => $request->catatan,
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);
            
            // Update stok barang jika disetujui
            $barang = $permintaan->barang;
            if ($barang->stok >= $permintaan->jumlah) {
                $barang->update([
                    'stok' => $barang->stok - $permintaan->jumlah
                ]);
            }
            
            $message = 'Permintaan berhasil disetujui';
        } else {
            $permintaan->update([
                'status' => 'rejected',
                'alasan_approval' => $request->alasan,
                'catatan_approval' => $request->catatan,
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);
            
            $message = 'Permintaan berhasil ditolak';
        }

        // Here you could also log the approval action to a separate table
        // for audit purposes if needed

        return redirect()->back()
            ->with('message', $message);
    }
}
