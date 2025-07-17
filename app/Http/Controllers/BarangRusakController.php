<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class BarangRusakController extends Controller
{
    public function index()
    {
        $block = $this->checkNewUserBlock();
        if ($block) return $block;

        // Check if user is admin
        if (!(Auth::user() instanceof \App\Models\User) || !Auth::user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        $barangRusak = Barang::whereIn('status', ['rusak', 'hilang'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('BarangRusak/Index', [
            'barangRusak' => $barangRusak
        ]);
    }

    public function create()
    {
        $block = $this->checkNewUserBlock();
        if ($block) return $block;

        // Check if user is admin
        if (!(Auth::user() instanceof \App\Models\User) || !Auth::user()->isAdmin()) {
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
        $block = $this->checkNewUserBlock();
        if ($block) return $block;

        $barangList = Barang::where('status', 'baik')
            ->orderBy('nama_barang', 'asc')
            ->get(['id', 'kode_barang', 'nama_barang', 'stok']);

        return Inertia::render('BarangRusak/CreateHilang', [
            'barangList' => $barangList
        ]);
    }

    public function store(Request $request)
    {
        $block = $this->checkNewUserBlock();
        if ($block) return $block;

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
        $block = $this->checkNewUserBlock();
        if ($block) return $block;

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

    public function updateStatus(Request $request, Barang $barang)
    {
        $block = $this->checkNewUserBlock();
        if ($block) return $block;

        $request->validate([
            'status' => 'required|in:baik,rusak,hilang'
        ]);

        $barang->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status barang berhasil diperbarui');
    }
}
