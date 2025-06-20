<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\Peminjaman;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        // Get all users with their requests and loans
        $users = DB::table('users')
            ->select('users.id', 'users.name')
            ->get()
            ->map(function ($user) use ($month, $year) {
                // Get user's requests
                $permintaanQuery = Permintaan::with(['barang'])->where('user_id', $user->id);
                if ($month) $permintaanQuery->whereMonth('created_at', $month);
                if ($year) $permintaanQuery->whereYear('created_at', $year);
                $permintaan = $permintaanQuery->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'permintaan',
                        'nama_barang' => $item->barang->nama_barang,
                        'kode_barang' => $item->barang->kode_barang,
                        'jumlah' => $item->jumlah,
                        'status' => $item->status,
                        'created_at' => $item->created_at,
                        'keterangan' => $item->keterangan,
                    ];
                });

                // Get user's loans
                $peminjamanQuery = Peminjaman::with(['barang'])->where('user_id', $user->id);
                if ($month) $peminjamanQuery->whereMonth('created_at', $month);
                if ($year) $peminjamanQuery->whereYear('created_at', $year);
                $peminjaman = $peminjamanQuery->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'peminjaman',
                        'nama_barang' => $item->barang->nama_barang,
                        'kode_barang' => $item->barang->kode_barang,
                        'jumlah' => $item->jumlah,
                        'status' => $item->status,
                        'created_at' => $item->created_at,
                        'tanggal_peminjaman' => $item->tanggal_peminjaman,
                        'tanggal_pengembalian' => $item->tanggal_pengembalian,
                        'due_date' => $item->due_date,
                        'keterangan' => $item->keterangan,
                    ];
                });

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'permintaan' => $permintaan,
                    'peminjaman' => $peminjaman,
                    'total_permintaan' => $permintaan->count(),
                    'total_peminjaman' => $peminjaman->count(),
                ];
            });

        // Get stock movement data
        $stokAwal = Barang::sum('stok');
        $stokChanges = DB::table('barang')
            ->select('barang.id', 'barang.nama_barang', 'barang.kode_barang', 'barang.stok')
            ->get()
            ->map(function ($barang) use ($month, $year) {
                $permintaanKeluarQuery = Permintaan::where('barang_id', $barang->id)->where('status', 'approved');
                $peminjamanKeluarQuery = Peminjaman::where('barang_id', $barang->id)->where('status', 'approved');
                $peminjamanKembaliQuery = Peminjaman::where('barang_id', $barang->id)->where('status', 'returned');
                if ($month) {
                    $permintaanKeluarQuery->whereMonth('created_at', $month);
                    $peminjamanKeluarQuery->whereMonth('created_at', $month);
                    $peminjamanKembaliQuery->whereMonth('tanggal_pengembalian', $month);
                }
                if ($year) {
                    $permintaanKeluarQuery->whereYear('created_at', $year);
                    $peminjamanKeluarQuery->whereYear('created_at', $year);
                    $peminjamanKembaliQuery->whereYear('tanggal_pengembalian', $year);
                }
                $permintaanKeluar = $permintaanKeluarQuery->sum('jumlah');
                $peminjamanKeluar = $peminjamanKeluarQuery->sum('jumlah');
                $peminjamanKembali = $peminjamanKembaliQuery->sum('jumlah');
                return [
                    'id' => $barang->id,
                    'nama_barang' => $barang->nama_barang,
                    'kode_barang' => $barang->kode_barang,
                    'stok_awal' => $barang->stok,
                    'permintaan_keluar' => $permintaanKeluar,
                    'peminjaman_keluar' => $peminjamanKeluar,
                    'peminjaman_kembali' => $peminjamanKembali,
                    'stok_akhir' => $barang->stok - $permintaanKeluar - $peminjamanKeluar + $peminjamanKembali,
                ];
            });

        return Inertia::render('laporan/index', [
            'users' => $users,
            'stokChanges' => $stokChanges,
            'month' => $month,
            'year' => $year,
            'months' => [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember',
            ],
            'years' => range(2024, Carbon::now()->year),
        ]);
    }

    public function download(Request $request)
    {
        try {
            $month = $request->input('month');
            $year = $request->input('year');
            
            // Get data for PDF - similar to index method
            $users = DB::table('users')
                ->select('users.id', 'users.name')
                ->get()
                ->map(function ($user) use ($month, $year) {
                    // Get user's requests
                    $permintaanQuery = Permintaan::with(['barang'])->where('user_id', $user->id);
                    if ($month) $permintaanQuery->whereMonth('created_at', $month);
                    if ($year) $permintaanQuery->whereYear('created_at', $year);
                    $permintaan = $permintaanQuery->get()->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'nama_barang' => $item->barang->nama_barang,
                            'kode_barang' => $item->barang->kode_barang,
                            'jumlah' => $item->jumlah,
                            'status' => $item->status,
                            'created_at' => $item->created_at,
                            'keterangan' => $item->keterangan,
                        ];
                    });

                    // Get user's loans
                    $peminjamanQuery = Peminjaman::with(['barang'])->where('user_id', $user->id);
                    if ($month) $peminjamanQuery->whereMonth('created_at', $month);
                    if ($year) $peminjamanQuery->whereYear('created_at', $year);
                    $peminjaman = $peminjamanQuery->get()->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'nama_barang' => $item->barang->nama_barang,
                            'kode_barang' => $item->barang->kode_barang,
                            'jumlah' => $item->jumlah,
                            'status' => $item->status,
                            'created_at' => $item->created_at,
                            'tanggal_peminjaman' => $item->tanggal_peminjaman,
                            'tanggal_pengembalian' => $item->tanggal_pengembalian,
                            'due_date' => $item->due_date,
                            'keterangan' => $item->keterangan,
                        ];
                    });

                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'permintaan' => $permintaan,
                        'peminjaman' => $peminjaman,
                        'total_permintaan' => $permintaan->count(),
                        'total_peminjaman' => $peminjaman->count(),
                    ];
                });

            // Get stock movement data
            $stokChanges = DB::table('barang')
                ->select('barang.id', 'barang.nama_barang', 'barang.kode_barang', 'barang.stok')
                ->get()
                ->map(function ($barang) use ($month, $year) {
                    $permintaanKeluarQuery = Permintaan::where('barang_id', $barang->id)->where('status', 'approved');
                    $peminjamanKeluarQuery = Peminjaman::where('barang_id', $barang->id)->where('status', 'approved');
                    $peminjamanKembaliQuery = Peminjaman::where('barang_id', $barang->id)->where('status', 'returned');
                    if ($month) {
                        $permintaanKeluarQuery->whereMonth('created_at', $month);
                        $peminjamanKeluarQuery->whereMonth('created_at', $month);
                        $peminjamanKembaliQuery->whereMonth('tanggal_pengembalian', $month);
                    }
                    if ($year) {
                        $permintaanKeluarQuery->whereYear('created_at', $year);
                        $peminjamanKeluarQuery->whereYear('created_at', $year);
                        $peminjamanKembaliQuery->whereYear('tanggal_pengembalian', $year);
                    }
                    $permintaanKeluar = $permintaanKeluarQuery->sum('jumlah');
                    $peminjamanKeluar = $peminjamanKeluarQuery->sum('jumlah');
                    $peminjamanKembali = $peminjamanKembaliQuery->sum('jumlah');
                    return [
                        'id' => $barang->id,
                        'nama_barang' => $barang->nama_barang,
                        'kode_barang' => $barang->kode_barang,
                        'stok_awal' => $barang->stok,
                        'permintaan_keluar' => $permintaanKeluar,
                        'peminjaman_keluar' => $peminjamanKeluar,
                        'peminjaman_kembali' => $peminjamanKembali,
                        'stok_akhir' => $barang->stok - $permintaanKeluar - $peminjamanKeluar + $peminjamanKembali,
                    ];
                });

            $monthName = $this->getMonthName($month);
            $title = "Laporan Umum - {$monthName} {$year}";
            
            // Debug: Log the data being passed
            \Log::info('PDF Data:', [
                'users_count' => $users->count(),
                'month' => $month,
                'year' => $year,
                'monthName' => $monthName,
                'title' => $title,
                'first_user' => $users->first()
            ]);
            
            // Generate HTML first
            $html = view('pdf.laporan-umum', [
                'users' => $users,
                'stokChanges' => $stokChanges,
                'title' => $title,
                'month' => $monthName,
                'year' => $year,
                'generatedAt' => now(),
            ])->render();
            
            \Log::info('Generated HTML length: ' . strlen($html));
            
            $pdf = Pdf::loadHTML($html);

            // Set PDF options
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'Arial'
            ]);

            $filename = "laporan-umum-{$month}-{$year}.pdf";
            
            // Use download for actual file download
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Gagal menghasilkan PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadUser(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $month = $request->input('month');
            $year = $request->input('year');
            
            $user = User::findOrFail($userId);
            
            // Get user's detailed data
            $permintaanQuery = Permintaan::with(['barang'])->where('user_id', $userId);
            $peminjamanQuery = Peminjaman::with(['barang'])->where('user_id', $userId);
            
            if ($month) {
                $permintaanQuery->whereMonth('created_at', $month);
                $peminjamanQuery->whereMonth('created_at', $month);
            }
            if ($year) {
                $permintaanQuery->whereYear('created_at', $year);
                $peminjamanQuery->whereYear('created_at', $year);
            }
            
            $permintaan = $permintaanQuery->get();
            $peminjaman = $peminjamanQuery->get();
            
            $monthName = $this->getMonthName($month);
            $title = "Laporan {$user->name} - {$monthName} {$year}";
            
            $pdf = Pdf::loadView('pdf.laporan-user', [
                'user' => $user,
                'permintaan' => $permintaan,
                'peminjaman' => $peminjaman,
                'title' => $title,
                'month' => $monthName,
                'year' => $year,
            ]);

            // Set PDF options
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'Arial'
            ]);

            $filename = "laporan-{$user->name}-{$month}-{$year}.pdf";
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Gagal menghasilkan PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testPdf()
    {
        try {
            // First, let's try to render the view as HTML to see if it works
            $html = view('pdf.test')->render();
            
            \Log::info('Generated HTML:', ['html' => $html]);
            
            $pdf = Pdf::loadHTML($html);

            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'Arial'
            ]);

            return $pdf->stream('test-laporan.pdf');
            
        } catch (\Exception $e) {
            \Log::error('Test PDF Generation Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Test PDF Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getMonthName($month)
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        
        return $months[$month] ?? 'Semua Bulan';
    }
} 