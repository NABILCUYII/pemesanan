<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\Barang;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PermintaanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // If user is admin, show all requests
        // If user is regular user, show only their requests
        $permintaan = Permintaan::with(['user', 'barang'])
            ->when(!$user->isAdmin(), function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->latest()
            ->get();
        
        $grouped = $permintaan->groupBy('user.name');
        
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
                        'created_at' => $item->created_at,
                        'keterangan' => $item->keterangan,
                    ];
                })->values()
            ];
        })->values();
        
        return Inertia::render('permintaan/index', [
            'permintaan' => $result,
            'isAdmin' => $user->isAdmin()
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
            'keterangan' => 'nullable|string|max:500'
        ]);

        Permintaan::create([
            'barang_id' => $request->barang_id,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'status' => 'pending',
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
            'requests.*.keterangan' => 'nullable|string|max:500'
        ]);

        $requests = collect($request->requests)->map(function ($item) {
            return [
                'barang_id' => $item['barang_id'],
                'jumlah' => $item['jumlah'],
                'keterangan' => $item['keterangan'] ?? '',
                'status' => 'pending',
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
            'keterangan' => 'nullable|string|max:500'
        ]);

        $permintaan->update([
            'barang_id' => $request->barang_id,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan
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
        $permintaan = Permintaan::with(['user', 'barang', 'approvedBy'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $peminjaman = Peminjaman::with(['user', 'barang'])
            ->where('status', 'pending')
            ->latest()
            ->get();
    
        return Inertia::render('permintaan/approval', [
            'permintaan' => $permintaan,
            'peminjaman' => $peminjaman
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

        DB::beginTransaction();
        try {
            $permintaan = Permintaan::findOrFail($request->permintaan_id);
            
            if ($request->action === 'approve') {
                $barang = $permintaan->barang;
                
                if ($barang->stok < $permintaan->jumlah) {
                    throw new \Exception('Stok tidak mencukupi');
                }
                
                $permintaan->update([
                    'status' => 'approved',
                    'alasan_approval' => $request->alasan,
                    'catatan_approval' => $request->catatan,
                    'approved_by' => auth()->id(),
                    'approved_at' => now()
                ]);
                
                $barang->update([
                    'stok' => $barang->stok - $permintaan->jumlah
                ]);
                
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
            
            DB::commit();
            return redirect()->back()->with('message', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
