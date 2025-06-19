<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Barang;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // If user is admin, show all loans
        // If user is regular user, show only their loans
        $peminjaman = Peminjaman::with(['user', 'barang'])
            ->when(!$user->isAdmin(), function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->latest()
            ->get();
        
        $grouped = $peminjaman->groupBy('user.name');
        
        $result = $grouped->map(function ($items, $userName) {
            return [
                'user' => $userName,
                'items' => $items->map(function ($item) {
                    return [
                        'nama_barang' => $item->barang->nama_barang,
                        'kode_barang' => $item->barang->kode_barang,
                        'jumlah' => $item->jumlah,
                        'status' => $item->status,
                        'id' => $item->id,
                        'tanggal_peminjaman' => $item->tanggal_peminjaman,
                        'tanggal_pengembalian' => $item->tanggal_pengembalian,
                        'due_date' => $item->due_date,
                        'keterangan' => $item->keterangan,
                    ];
                })->values()
            ];
        })->values();
        
        return Inertia::render('peminjaman/index', [
            'peminjaman' => $result,
            'isAdmin' => $user->isAdmin()
        ]);
    }

    public function create()
    {
        $barang = Barang::where('kategori', 'peminjaman')->get();
        return Inertia::render('peminjaman/create', [
            'barang' => $barang
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:500',
            'due_date' => 'required|date|after_or_equal:today',
            
        ]);

        Peminjaman::create([
            'barang_id' => $request->barang_id,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'tanggal_peminjaman' => now(), // Tanggal peminjaman otomatis saat ini
            'tanggal_pengembalian' => $request->tanggal_pengembalian,
            'due_date' => $request->due_date,
            'user_id' => auth()->id(),
            'status' => 'pending'
        ]);

        return redirect()->route('peminjaman.index')
            ->with('message', 'Peminjaman berhasil ditambahkan');
    }

    public function approve(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'action' => 'required|in:approve,reject',
            'alasan' => 'required|string|max:500',
            'catatan' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::findOrFail($request->peminjaman_id);
            
            if ($request->action === 'approve') {
                $barang = $peminjaman->barang;
                
                if ($barang->stok < $peminjaman->jumlah) {
                    throw new \Exception('Stok tidak mencukupi');
                }
                
                $peminjaman->update([
                    'status' => 'approved',
                    'alasan_approval' => $request->alasan,
                    'catatan_approval' => $request->catatan,
                    'approved_by' => auth()->id(),
                    'approved_at' => now()
                ]);
                
                $barang->update([
                    'stok' => $barang->stok - $peminjaman->jumlah
                ]);
                
                $message = 'Peminjaman berhasil disetujui';
            } else {
                $peminjaman->update([
                    'status' => 'rejected',
                    'alasan_approval' => $request->alasan,
                    'catatan_approval' => $request->catatan,
                    'approved_by' => auth()->id(),
                    'approved_at' => now()
                ]);
                
                $message = 'Peminjaman berhasil ditolak';
            }
            
            DB::commit();
            return redirect()->back()->with('message', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit(Peminjaman $peminjaman)
    {
        // Check if user is authorized to edit this peminjaman
        if (auth()->id() !== $peminjaman->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        // Only allow editing if status is pending
        if ($peminjaman->status !== 'pending') {
            return redirect()->route('peminjaman.index')
                ->with('error', 'Peminjaman yang sudah disetujui/ditolak tidak dapat diubah');
        }

        $barang = Barang::where('kategori', 'peminjaman')->get();
        return Inertia::render('peminjaman/edit', [
            'peminjaman' => $peminjaman->load('barang'),
            'barang' => $barang
        ]);
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        // Check if user is authorized to update this peminjaman
        if (auth()->id() !== $peminjaman->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        // Only allow updating if status is pending
        if ($peminjaman->status !== 'pending') {
            return redirect()->route('peminjaman.index')
                ->with('error', 'Peminjaman yang sudah disetujui/ditolak tidak dapat diubah');
        }

        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:500',
            'tanggal_pengembalian' => 'required|date|after_or_equal:today',
            'due_date' => 'required|date|after_or_equal:today'
        ]);

        $peminjaman->update([
            'barang_id' => $request->barang_id,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'tanggal_pengembalian' => $request->tanggal_pengembalian,
            'due_date' => $request->due_date
        ]);

        return redirect()->route('peminjaman.index')
            ->with('message', 'Peminjaman berhasil diperbarui');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        // Check if user is authorized to delete this peminjaman
        if (auth()->id() !== $peminjaman->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        // Only allow deletion if status is pending
        if ($peminjaman->status !== 'pending') {
            return redirect()->route('peminjaman.index')
                ->with('error', 'Peminjaman yang sudah disetujui/ditolak tidak dapat dihapus');
        }

        DB::beginTransaction();
        try {
            $peminjaman->delete();
            DB::commit();
            
            return redirect()->route('peminjaman.index')
                ->with('message', 'Peminjaman berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus peminjaman: ' . $e->getMessage());
        }
    }

    public function returned()
    {
        $user = auth()->user();
        
        $peminjaman = Peminjaman::with(['user', 'barang'])
            ->where('status', 'returned')
            ->when(!$user->isAdmin(), function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->latest()
            ->get();
        
        $grouped = $peminjaman->groupBy('user.name');
        
        $result = $grouped->map(function ($items, $userName) {
            return [
                'user' => $userName,
                'items' => $items->map(function ($item) {
                    return [
                        'nama_barang' => $item->barang->nama_barang,
                        'kode_barang' => $item->barang->kode_barang,
                        'jumlah' => $item->jumlah,
                        'status' => $item->status,
                        'id' => $item->id,
                        'tanggal_peminjaman' => $item->tanggal_peminjaman,
                        'tanggal_pengembalian' => $item->tanggal_pengembalian,
                        'due_date' => $item->due_date,
                        'keterangan' => $item->keterangan,
                    ];
                })->values()
            ];
        })->values();
        
        return Inertia::render('peminjaman/index', [
            'peminjaman' => $result,
            'isAdmin' => $user->isAdmin()
        ]);
    }

    public function notReturned()
    {
        $user = auth()->user();
        
        $peminjaman = Peminjaman::with(['user', 'barang'])
            ->where('status', 'not_returned')
            ->when(!$user->isAdmin(), function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->latest()
            ->get();
        
        $grouped = $peminjaman->groupBy('user.name');
        
        $result = $grouped->map(function ($items, $userName) {
            return [
                'user' => $userName,
                'items' => $items->map(function ($item) {
                    return [
                        'nama_barang' => $item->barang->nama_barang,
                        'kode_barang' => $item->barang->kode_barang,
                        'jumlah' => $item->jumlah,
                        'status' => $item->status,
                        'id' => $item->id,
                        'tanggal_peminjaman' => $item->tanggal_peminjaman,
                        'tanggal_pengembalian' => $item->tanggal_pengembalian,
                        'due_date' => $item->due_date,
                        'keterangan' => $item->keterangan,
                    ];
                })->values()
            ];
        })->values();
        
        return Inertia::render('peminjaman/index', [
            'peminjaman' => $result,
            'isAdmin' => $user->isAdmin()
        ]);
    }

    public function markAsReturned(Peminjaman $peminjaman)
    {
        // Only admin can mark items as returned
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        // Only approved or not_returned items can be marked as returned
        if (!in_array($peminjaman->status, ['approved', 'not_returned'])) {
            return redirect()->back()->with('error', 'Hanya peminjaman yang sudah disetujui atau belum dikembalikan yang dapat ditandai sebagai dikembalikan');
        }

        DB::beginTransaction();
        try {
            // Update peminjaman status
            $peminjaman->update([
                'status' => 'returned',
                'tanggal_pengembalian' => now()
            ]);

            // Return the stock (only if it was previously approved and not returned)
            if ($peminjaman->status === 'approved') {
                $barang = $peminjaman->barang;
                $barang->update([
                    'stok' => $barang->stok + $peminjaman->jumlah
                ]);
            }

            DB::commit();
            return redirect()->back()->with('message', 'Item berhasil ditandai sebagai sudah dikembalikan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menandai item: ' . $e->getMessage());
        }
    }

    public function markAsNotReturned(Peminjaman $peminjaman)
    {
        // Only admin can mark items as not returned
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        // Only approved items can be marked as not returned
        if ($peminjaman->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya peminjaman yang sudah disetujui yang dapat ditandai sebagai belum dikembalikan');
        }

        DB::beginTransaction();
        try {
            // Update peminjaman status
            $peminjaman->update([
                'status' => 'not_returned'
            ]);

            DB::commit();
            return redirect()->back()->with('message', 'Item berhasil ditandai sebagai belum dikembalikan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menandai item: ' . $e->getMessage());
        }
    }
}
