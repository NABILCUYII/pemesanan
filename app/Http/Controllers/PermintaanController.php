<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\Barang;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PermintaanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
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
            $firstItem = $items->first();
            return [
                'user' => $userName,
                'user_photo' => $firstItem->user->photo,
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
            'user_id' => Auth::id()
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
                'user_id' => Auth::id(),
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
        // Check if user is authorized to edit this permintaan
        if (auth()->id() !== $permintaan->user_id && !auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }

        // Only allow editing if status is pending
        if ($permintaan->status !== 'pending') {
            return redirect()->route('permintaan.index')
                ->with('error', 'Permintaan yang sudah disetujui/ditolak tidak dapat diubah');
        }

        $barang = Barang::where('kategori', 'permintaan')->get();
        return Inertia::render('permintaan/edit', [
            'permintaan' => $permintaan->load('barang'),
            'barang' => $barang
        ]);
    }

    public function update(Request $request, Permintaan $permintaan)
    {
        // Check if user is authorized to update this permintaan
        if (auth()->id() !== $permintaan->user_id && !auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }

        // Only allow updating if status is pending
        if ($permintaan->status !== 'pending') {
            return redirect()->route('permintaan.index')
                ->with('error', 'Permintaan yang sudah disetujui/ditolak tidak dapat diubah');
        }

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
        // Check if user is authorized to delete this permintaan
        if (auth()->id() !== $permintaan->user_id && !auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }

        // Only allow deletion if status is pending
        if ($permintaan->status !== 'pending') {
            return redirect()->route('permintaan.index')
                ->with('error', 'Permintaan yang sudah disetujui/ditolak tidak dapat dihapus');
        }

        $permintaan->delete();
        return redirect()->route('permintaan.index')
            ->with('message', 'Permintaan berhasil dihapus');
    }

    public function complete(Permintaan $permintaan)
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

        // Check if permintaan is approved
        if ($permintaan->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya permintaan yang disetujui yang dapat ditandai sebagai selesai');
        }

        $permintaan->update([
            'status' => 'completed'
        ]);

        return redirect()->route('permintaan.index')
            ->with('message', 'Permintaan berhasil ditandai sebagai selesai');
    }

    public function approval()
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
                    'approved_by' => Auth::id(),
                    'approved_at' => now()
                ]);
                
                // Menggunakan sistem log stok
                $keterangan = "Permintaan disetujui oleh " . auth()->user()->name;
                if ($request->catatan) {
                    $keterangan .= " - " . $request->catatan;
                }
                
                $barang->addStokLog(
                    'keluar', 
                    $permintaan->jumlah, 
                    $keterangan,
                    "Permintaan #" . $permintaan->id,
                    $permintaan->user_id
                );
                
                $message = 'Permintaan berhasil disetujui';
            } else {
                $permintaan->update([
                    'status' => 'rejected',
                    'alasan_approval' => $request->alasan,
                    'catatan_approval' => $request->catatan,
                    'approved_by' => Auth::id(),
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

    // Endpoint notifikasi permintaan pending
    public function pendingCount()
    {
        $permintaanCount = Permintaan::where('status', 'pending')->count();
        $peminjamanCount = Peminjaman::where('status', 'pending')->count();
        $totalCount = $permintaanCount + $peminjamanCount;
        return response()->json(['count' => $totalCount]);
    }
}
