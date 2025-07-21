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
            ->when(!$user || !$user->isAdmin(), function ($query) use ($user) {
                return $query->where('user_id', $user ? $user->id : null);
            })
            ->latest()
            ->get();

        // Group by user id to avoid issues if user name is not unique or user is null
        $grouped = $peminjaman->groupBy('user_id');

        $result = $grouped->map(function ($items, $userId) {
            $firstItem = $items->first();
            $user = $firstItem->user;
            return [
                'user' => $user ? $user->name : 'Tidak diketahui',
                'user_photo' => $user ? $user->photo : null,
                'items' => $items->map(function ($item) {
                    return [
                        'nama_barang' => $item->barang ? $item->barang->nama_barang : null,
                        'kode_barang' => $item->barang ? $item->barang->kode_barang : null,
                        'jumlah' => $item->jumlah,
                        'status' => $item->status,
                        'id' => $item->id,
                        'tanggal_peminjaman' => $item->tanggal_peminjaman,
                        'tanggal_pengembalian' => $item->tanggal_pengembalian,
                        'due_date' => $item->due_date,
                        'keterangan' => $item->keterangan,
                        'alasan_approval' => $item->alasan_approval,
                        'catatan_approval' => $item->catatan_approval,
                        'kondisi_barang' => $item->kondisi_barang,
                        'catatan_pengembalian' => $item->catatan_pengembalian,
                        'returned_at' => $item->returned_at,
                    ];
                })->values()
            ];
        })->values();

        return Inertia::render('peminjaman/index', [
            'peminjaman' => $result,
            'isAdmin' => $user ? $user->isAdmin() : false
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

        $user = auth()->user();

        Peminjaman::create([
            'barang_id' => $request->barang_id,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'tanggal_peminjaman' => now(), // Tanggal peminjaman otomatis saat ini
            'tanggal_pengembalian' => $request->tanggal_pengembalian,
            'due_date' => $request->due_date,
            'user_id' => $user ? $user->id : null,
            'status' => 'pending'
        ]);

        return redirect()->route('peminjaman.index')
            ->with('message', 'Peminjaman berhasil ditambahkan');
    }

    public function approve(Request $request)
    {
        $user = auth()->user();
        // Check if user is admin
        if (!$user || !$user->isAdmin()) {
            return inertia('Forbidden', [
                'user' => $user ? [
                    'name' => $user->name,
                    'role' => $user->role ?? 'User'
                ] : null
            ]);
        }

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
                    'approved_by' => $user->id,
                    'approved_at' => now()
                ]);

                // Menggunakan sistem log stok
                $keterangan = "Peminjaman disetujui oleh " . $user->name;
                if ($request->catatan) {
                    $keterangan .= " - " . $request->catatan;
                }

                $barang->addStokLog(
                    'keluar',
                    $peminjaman->jumlah,
                    $keterangan,
                    "Peminjaman #" . $peminjaman->id,
                    $peminjaman->user_id
                );

                $message = 'Peminjaman berhasil disetujui';
            } else {
                $peminjaman->update([
                    'status' => 'rejected',
                    'alasan_approval' => $request->alasan,
                    'catatan_approval' => $request->catatan,
                    'approved_by' => $user->id,
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
        $user = auth()->user();
        // Check if user is authorized to edit this peminjaman
        if ((!$user || $user->id !== $peminjaman->user_id) && (!$user || !$user->isAdmin())) {
            return inertia('Forbidden', [
                'user' => $user ? [
                    'name' => $user->name,
                    'role' => $user->role ?? 'User'
                ] : null
            ]);
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
        $user = auth()->user();
        // Check if user is authorized to update this peminjaman
        if ((!$user || $user->id !== $peminjaman->user_id) && (!$user || !$user->isAdmin())) {
            return inertia('Forbidden', [
                'user' => $user ? [
                    'name' => $user->name,
                    'role' => $user->role ?? 'User'
                ] : null
            ]);
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
        $user = auth()->user();
        // Check if user is authorized to delete this peminjaman
        if ((!$user || $user->id !== $peminjaman->user_id) && (!$user || !$user->isAdmin())) {
            return inertia('Forbidden', [
                'user' => $user ? [
                    'name' => $user->name,
                    'role' => $user->role ?? 'User'
                ] : null
            ]);
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

    public function startProgress(Peminjaman $peminjaman)
    {
        $user = auth()->user();
        // Check if user is admin
        if (!$user || !$user->isAdmin()) {
            return inertia('Forbidden', [
                'user' => $user ? [
                    'name' => $user->name,
                    'role' => $user->role ?? 'User'
                ] : null
            ]);
        }

        // Check if peminjaman is approved
        if ($peminjaman->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya peminjaman yang disetujui yang dapat dimulai prosesnya');
        }

        DB::beginTransaction();
        try {
            $peminjaman->update([
                'status' => 'in_progress',
                'started_at' => now(),
                'started_by' => $user->id
            ]);

            // Buat log stok untuk menandakan peminjaman dimulai
            $barang = $peminjaman->barang;
            $keterangan = "Proses peminjaman dimulai oleh " . $user->name;

            $barang->addStokLog(
                'keluar',
                0, // Tidak ada perubahan stok, hanya log
                $keterangan,
                "Proses Peminjaman #" . $peminjaman->id,
                $peminjaman->user_id
            );

            DB::commit();
            return redirect()->back()->with('success', 'Proses peminjaman berhasil dimulai!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memulai proses peminjaman: ' . $e->getMessage());
        }
    }

    public function return(Peminjaman $peminjaman)
    {
        // Check if peminjaman is approved and not already returned
        if ($peminjaman->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya peminjaman yang disetujui yang dapat dikembalikan');
        }

        if ($peminjaman->tanggal_pengembalian) {
            return redirect()->back()->with('error', 'Peminjaman ini sudah dikembalikan');
        }

        return Inertia::render('peminjaman/return', [
            'peminjaman' => $peminjaman->load(['user', 'barang'])
        ]);
    }

    public function processReturn(Request $request, Peminjaman $peminjaman)
    {
        $user = auth()->user();
        // Validasi input
        $request->validate([
            'tanggal_pengembalian' => 'required|date|after_or_equal:' . $peminjaman->tanggal_peminjaman,
            'kondisi_barang' => 'required|in:baik,rusak_ringan,rusak_berat',
            'catatan_pengembalian' => 'nullable|string|max:500'
        ]);

        // Hitung jumlah yang dikembalikan berdasarkan kondisi
        $jumlahDikembalikan = match($request->kondisi_barang) {
            'baik' => $peminjaman->jumlah,
            'rusak_ringan' => floor($peminjaman->jumlah * 0.5),
            'rusak_berat' => 0,
            default => 0
        };

        // Update peminjaman
        $peminjaman->update([
            'status' => 'returned',
            'tanggal_pengembalian' => $request->tanggal_pengembalian,
            'kondisi_barang' => $request->kondisi_barang,
            'catatan_pengembalian' => $request->catatan_pengembalian,
            'jumlah_dikembalikan' => $jumlahDikembalikan,
            'returned_at' => now(),
            'returned_by' => $user ? $user->id : null
        ]);

        // Update stok barang dan buat log
        $barang = $peminjaman->barang;
        $keterangan = "Pengembalian peminjaman - Kondisi: " . ucfirst(str_replace('_', ' ', $request->kondisi_barang));
        if ($request->catatan_pengembalian) {
            $keterangan .= " - " . $request->catatan_pengembalian;
        }

        $barang->addStokLog(
            'masuk',
            $jumlahDikembalikan,
            $keterangan,
            "Pengembalian Peminjaman #" . $peminjaman->id,
            $peminjaman->user_id
        );

        return redirect()->back()->with('success', 'Pengembalian berhasil diproses!');
    }

    public function returns()
    {
        $returns = Peminjaman::with(['barang', 'user'])
            ->where('status', 'returned')
            ->orderBy('returned_at', 'desc')
            ->get()
            ->map(function ($peminjaman) {
                $user = $peminjaman->user;
                return [
                    'id' => $peminjaman->id,
                    'nama_barang' => $peminjaman->barang ? $peminjaman->barang->nama_barang : null,
                    'kode_barang' => $peminjaman->barang ? $peminjaman->barang->kode_barang : null,
                    'nama_peminjam' => $user ? $user->name : 'Tidak diketahui',
                    'jumlah' => $peminjaman->jumlah,
                    'jumlah_dikembalikan' => $peminjaman->jumlah_dikembalikan,
                    'kondisi_barang' => $peminjaman->kondisi_barang,
                    'tanggal_peminjaman' => $peminjaman->tanggal_peminjaman,
                    'tanggal_pengembalian' => $peminjaman->tanggal_pengembalian,
                    'catatan_pengembalian' => $peminjaman->catatan_pengembalian,
                ];
            });

        return Inertia::render('peminjaman/returns', [
            'returns' => $returns
        ]);
    }

    private function calculateReturnedAmount(Peminjaman $peminjaman)
    {
        if ($peminjaman->kondisi_barang === 'rusak_ringan') {
            return $peminjaman->jumlah * 0.5; // 50% stok kembali
        } elseif ($peminjaman->kondisi_barang === 'rusak_berat') {
            return 0; // Tidak ada stok yang dikembalikan
        } else {
            return $peminjaman->jumlah; // 100% stok kembali
        }
    }
}
