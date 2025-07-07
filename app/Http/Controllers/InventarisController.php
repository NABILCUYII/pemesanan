<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\Barang;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class InventarisController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // If user is admin, show all inventory loans
        // If user is regular user, show only their loans
        $inventaris = Inventaris::with(['user', 'barang'])
            ->when(!$user->isAdmin(), function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->latest()
            ->get();
        
        $grouped = $inventaris->groupBy('user.name');
        
        $result = $grouped->map(function ($items, $userName) {
            $firstItem = $items->first();
            return [
                'user' => $userName,
                'user_photo' => $firstItem->user->photo,
                'items' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nomor_inventaris' => $item->nomor_inventaris,
                        'nama_barang' => $item->barang->nama_barang,
                        'kode_barang' => $item->barang->kode_barang,
                        'jumlah' => $item->jumlah,
                        'status' => $item->status,
                        'status_text' => $item->status_text,
                        'tanggal_peminjaman' => $item->tanggal_peminjaman,
                        'tanggal_pengembalian' => $item->tanggal_pengembalian,
                        'due_date' => $item->due_date,
                        'keterangan' => $item->keterangan,
                        'lokasi_peminjaman' => $item->lokasi_peminjaman,
                        'alasan_approval' => $item->alasan_approval,
                        'catatan_approval' => $item->catatan_approval,
                        'kondisi_barang' => $item->kondisi_barang,
                        'kondisi_barang_text' => $item->kondisi_barang_text,
                        'catatan_pengembalian' => $item->catatan_pengembalian,
                        'returned_at' => $item->returned_at,
                        'is_overdue' => $item->isOverdue(),
                    ];
                })->values()
            ];
        })->values();
        
        return Inertia::render('inventaris/index', [
            'inventaris' => $result,
            'isAdmin' => $user->isAdmin()
        ]);
    }

    public function create()
    {
        $barang = Barang::where('kategori', 'peminjaman')->get();
        return Inertia::render('inventaris/create', [
            'barang' => $barang
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:500',
            'lokasi_peminjaman' => 'nullable|string|max:255',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        DB::beginTransaction();
        try {
            $inventaris = Inventaris::create([
                'user_id' => auth()->id(),
                'barang_id' => $request->barang_id,
                'nomor_inventaris' => Inventaris::generateNomorInventaris(),
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan,
                'lokasi_peminjaman' => $request->lokasi_peminjaman,
                'tanggal_peminjaman' => now(),
                'due_date' => $request->due_date,
                'status' => 'pending'
            ]);

            DB::commit();
            return redirect()->route('inventaris.index')
                ->with('message', 'Permintaan inventaris berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan permintaan inventaris: ' . $e->getMessage());
        }
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
            'inventaris_id' => 'required|exists:inventaris,id',
            'action' => 'required|in:approve,reject',
            'alasan' => 'required|string|max:500',
            'catatan' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $inventaris = Inventaris::findOrFail($request->inventaris_id);
            
            if ($request->action === 'approve') {
                $barang = $inventaris->barang;
                
                if ($barang->stok < $inventaris->jumlah) {
                    throw new \Exception('Stok tidak mencukupi');
                }
                
                $inventaris->update([
                    'status' => 'approved',
                    'alasan_approval' => $request->alasan,
                    'catatan_approval' => $request->catatan,
                    'approved_by' => auth()->id(),
                    'approved_at' => now()
                ]);
                
                // Menggunakan sistem log stok
                $keterangan = "Inventaris disetujui oleh " . auth()->user()->name;
                if ($request->catatan) {
                    $keterangan .= " - " . $request->catatan;
                }
                
                $barang->addStokLog(
                    'keluar', 
                    $inventaris->jumlah, 
                    $keterangan,
                    "Inventaris #" . $inventaris->nomor_inventaris,
                    $inventaris->user_id
                );
                
                $message = 'Permintaan inventaris berhasil disetujui';
            } else {
                $inventaris->update([
                    'status' => 'rejected',
                    'alasan_approval' => $request->alasan,
                    'catatan_approval' => $request->catatan,
                    'approved_by' => auth()->id(),
                    'approved_at' => now()
                ]);
                
                $message = 'Permintaan inventaris ditolak';
            }
            
            DB::commit();
            return redirect()->back()->with('message', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses permintaan: ' . $e->getMessage());
        }
    }

    public function edit(Inventaris $inventaris)
    {
        // Check if user is authorized to update this inventaris
        if (auth()->id() !== $inventaris->user_id && !auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }

        // Only allow updating if status is pending
        if ($inventaris->status !== 'pending') {
            return redirect()->route('inventaris.index')
                ->with('error', 'Inventaris yang sudah disetujui/ditolak tidak dapat diubah');
        }

        $barang = Barang::where('kategori', 'peminjaman')->get();
        
        return Inertia::render('inventaris/edit', [
            'inventaris' => $inventaris->load(['user', 'barang']),
            'barang' => $barang
        ]);
    }

    public function update(Request $request, Inventaris $inventaris)
    {
        // Check if user is authorized to update this inventaris
        if (auth()->id() !== $inventaris->user_id && !auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }

        // Only allow updating if status is pending
        if ($inventaris->status !== 'pending') {
            return redirect()->route('inventaris.index')
                ->with('error', 'Inventaris yang sudah disetujui/ditolak tidak dapat diubah');
        }

        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:500',
            'lokasi_peminjaman' => 'nullable|string|max:255',
            'due_date' => 'required|date|after_or_equal:today'
        ]);

        $inventaris->update([
            'barang_id' => $request->barang_id,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'lokasi_peminjaman' => $request->lokasi_peminjaman,
            'due_date' => $request->due_date
        ]);

        return redirect()->route('inventaris.index')
            ->with('message', 'Inventaris berhasil diperbarui');
    }

    public function destroy(Inventaris $inventaris)
    {
        // Check if user is authorized to delete this inventaris
        if (auth()->id() !== $inventaris->user_id && !auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }

        // Only allow deletion if status is pending
        if ($inventaris->status !== 'pending') {
            return redirect()->route('inventaris.index')
                ->with('error', 'Inventaris yang sudah disetujui/ditolak tidak dapat dihapus');
        }

        DB::beginTransaction();
        try {
            $inventaris->delete();
            DB::commit();
            
            return redirect()->route('inventaris.index')
                ->with('message', 'Inventaris berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus inventaris: ' . $e->getMessage());
        }
    }

    public function startProgress(Inventaris $inventaris)
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

        // Check if inventaris is approved and not already started
        if ($inventaris->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya inventaris yang disetujui yang dapat dimulai');
        }

        if ($inventaris->started_at) {
            return redirect()->back()->with('error', 'Inventaris ini sudah dimulai');
        }

        $inventaris->update([
            'status' => 'in_progress',
            'started_at' => now(),
            'started_by' => auth()->id()
        ]);

        return redirect()->back()->with('message', 'Proses inventaris telah dimulai');
    }

    public function return(Inventaris $inventaris)
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

        // Check if inventaris is approved and not already returned
        if ($inventaris->status !== 'approved' && $inventaris->status !== 'in_progress') {
            return redirect()->back()->with('error', 'Hanya inventaris yang disetujui yang dapat dikembalikan');
        }

        if ($inventaris->tanggal_pengembalian) {
            return redirect()->back()->with('error', 'Inventaris ini sudah dikembalikan');
        }

        return Inertia::render('inventaris/return', [
            'inventaris' => $inventaris->load(['user', 'barang'])
        ]);
    }

    public function processReturn(Request $request, Inventaris $inventaris)
    {
        // Validasi input
        $request->validate([
            'tanggal_pengembalian' => 'required|date|after_or_equal:' . $inventaris->tanggal_peminjaman,
            'kondisi_barang' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'catatan_pengembalian' => 'nullable|string|max:500'
        ]);

        // Hitung jumlah yang dikembalikan berdasarkan kondisi
        $jumlahDikembalikan = match($request->kondisi_barang) {
            'baik' => $inventaris->jumlah,
            'rusak_ringan' => floor($inventaris->jumlah * 0.5),
            'rusak_berat', 'hilang' => 0,
            default => 0
        };

        // Update inventaris
        $inventaris->update([
            'status' => 'returned',
            'tanggal_pengembalian' => $request->tanggal_pengembalian,
            'kondisi_barang' => $request->kondisi_barang,
            'catatan_pengembalian' => $request->catatan_pengembalian,
            'returned_at' => now(),
            'returned_by' => auth()->id()
        ]);

        // Update stok barang dan buat log
        $barang = $inventaris->barang;
        $keterangan = "Pengembalian inventaris - Kondisi: " . ucfirst(str_replace('_', ' ', $request->kondisi_barang));
        if ($request->catatan_pengembalian) {
            $keterangan .= " - " . $request->catatan_pengembalian;
        }
        
        $barang->addStokLog(
            'masuk',
            $jumlahDikembalikan,
            $keterangan,
            "Pengembalian Inventaris #" . $inventaris->nomor_inventaris,
            $inventaris->user_id
        );

        return redirect()->back()->with('success', 'Pengembalian inventaris berhasil diproses!');
    }

    public function returns()
    {
        $returns = Inventaris::with(['barang', 'user'])
            ->where('status', 'returned')
            ->orderBy('returned_at', 'desc')
            ->get()
            ->map(function ($inventaris) {
                return [
                    'id' => $inventaris->id,
                    'nomor_inventaris' => $inventaris->nomor_inventaris,
                    'nama_barang' => $inventaris->barang->nama_barang,
                    'kode_barang' => $inventaris->barang->kode_barang,
                    'nama_peminjam' => $inventaris->user->name,
                    'jumlah' => $inventaris->jumlah,
                    'kondisi_barang' => $inventaris->kondisi_barang,
                    'kondisi_barang_text' => $inventaris->kondisi_barang_text,
                    'tanggal_peminjaman' => $inventaris->tanggal_peminjaman,
                    'tanggal_pengembalian' => $inventaris->tanggal_pengembalian,
                    'catatan_pengembalian' => $inventaris->catatan_pengembalian,
                ];
            });

        return Inertia::render('inventaris/returns', [
            'returns' => $returns
        ]);
    }

    public function show(Inventaris $inventaris)
    {
        // Check if user is authorized to view this inventaris
        if (auth()->id() !== $inventaris->user_id && !auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }

        return Inertia::render('inventaris/show', [
            'inventaris' => $inventaris->load(['user', 'barang', 'approvedBy', 'returnedBy', 'startedBy'])
        ]);
    }

    /**
     * Get count of pending inventaris items (admin only)
     */
    public function pendingCount()
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return response()->json(['count' => 0], 403);
        }
        
        // Count all pending inventaris for admin
        $count = Inventaris::where('status', 'pending')->count();
        
        return response()->json(['count' => $count]);
    }
} 