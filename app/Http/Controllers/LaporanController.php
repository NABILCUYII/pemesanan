<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Barang;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PDF;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $users = User::with(['permintaan' => function ($query) use ($month, $year) {
            $query->whereMonth('created_at', $month)
                  ->whereYear('created_at', $year);
        }, 'peminjaman' => function ($query) use ($month, $year) {
            $query->whereMonth('created_at', $month)
                  ->whereYear('created_at', $year);
        }])->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'permintaan' => $user->permintaan->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nama_barang' => $item->barang->nama_barang,
                        'kode_barang' => $item->barang->kode_barang,
                        'jumlah' => $item->jumlah,
                        'status' => $item->status,
                        'created_at' => $item->created_at,
                        'keterangan' => $item->keterangan,
                    ];
                }),
                'peminjaman' => $user->peminjaman->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nama_barang' => $item->barang->nama_barang,
                        'kode_barang' => $item->barang->kode_barang,
                        'jumlah' => $item->jumlah,
                        'status' => $item->status,
                        'created_at' => $item->created_at,
                        'due_date' => $item->due_date,
                        'keterangan' => $item->keterangan,
                    ];
                }),
                'total_permintaan' => $user->permintaan->count(),
                'total_peminjaman' => $user->peminjaman->count(),
            ];
        });

        $stokChanges = Barang::with(['permintaan' => function ($query) use ($month, $year) {
            $query->whereMonth('created_at', $month)
                  ->whereYear('created_at', $year);
        }, 'peminjaman' => function ($query) use ($month, $year) {
            $query->whereMonth('created_at', $month)
                  ->whereYear('created_at', $year);
        }])->get()->map(function ($barang) {
            $permintaanKeluar = $barang->permintaan->where('status', 'approved')->sum('jumlah');
            $peminjamanKeluar = $barang->peminjaman->where('status', 'approved')->sum('jumlah');
            $peminjamanKembali = $barang->peminjaman->where('status', 'returned')->sum('jumlah');
            
            return [
                'id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'kode_barang' => $barang->kode_barang,
                'stok_awal' => $barang->stok + $permintaanKeluar + $peminjamanKeluar - $peminjamanKembali,
                'permintaan_keluar' => $permintaanKeluar,
                'peminjaman_keluar' => $peminjamanKeluar,
                'peminjaman_kembali' => $peminjamanKembali,
                'stok_akhir' => $barang->stok,
            ];
        });

        $months = [
            '1' => 'Januari',
            '2' => 'Februari',
            '3' => 'Maret',
            '4' => 'April',
            '5' => 'Mei',
            '6' => 'Juni',
            '7' => 'Juli',
            '8' => 'Agustus',
            '9' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        $years = range(2020, now()->year);

        return Inertia::render('laporan/index', [
            'users' => $users,
            'stokChanges' => $stokChanges,
            'month' => (int) $month,
            'year' => (int) $year,
            'months' => $months,
            'years' => $years,
        ]);
    }

    public function download(Request $request)
    {
        try {
            $month = (int) $request->input('month', Carbon::now()->month);
            $year = (int) $request->input('year', Carbon::now()->year);
            
            // Create a Carbon instance with the specified month and year
            $date = Carbon::create($year, $month, 1);
            $monthName = $date->locale('id')->isoFormat('MMMM');

            $users = User::with(['permintaan.barang', 'peminjaman.barang'])
                ->whereHas('permintaan', function ($query) use ($month, $year) {
                    $query->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year);
                })
                ->orWhereHas('peminjaman', function ($query) use ($month, $year) {
                    $query->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year);
                })
                ->get();

            $users = $users->map(function ($user) use ($month, $year) {
                $permintaan = $user->permintaan()
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->with('barang')
                    ->get()
                    ->map(function ($item) {
                        if (!$item->barang) return null;
                        return [
                            'id' => $item->id,
                            'nama_barang' => $item->barang->nama_barang ?? 'Unknown',
                            'kode_barang' => $item->barang->kode_barang ?? 'Unknown',
                            'jumlah' => $item->jumlah,
                            'status' => $item->status,
                            'keterangan' => $item->keterangan,
                            'created_at' => $item->created_at,
                        ];
                    })->filter();

                $peminjaman = $user->peminjaman()
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->with('barang')
                    ->get()
                    ->map(function ($item) {
                        if (!$item->barang) return null;
                        return [
                            'id' => $item->id,
                            'nama_barang' => $item->barang->nama_barang ?? 'Unknown',
                            'kode_barang' => $item->barang->kode_barang ?? 'Unknown',
                            'jumlah' => $item->jumlah,
                            'status' => $item->status,
                            'keterangan' => $item->keterangan,
                            'due_date' => $item->due_date,
                            'created_at' => $item->created_at,
                        ];
                    })->filter();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'total_permintaan' => $permintaan->count(),
                    'total_peminjaman' => $peminjaman->count(),
                    'permintaan' => $permintaan,
                    'peminjaman' => $peminjaman,
                ];
            })->filter();

            $stokChanges = Barang::with(['permintaan', 'peminjaman'])
                ->get()
                ->map(function ($barang) use ($month, $year) {
                    $permintaanKeluar = $barang->permintaan()
                        ->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year)
                        ->where('status', 'approved')
                        ->sum('jumlah') ?? 0;

                    $peminjamanKeluar = $barang->peminjaman()
                        ->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year)
                        ->where('status', 'approved')
                        ->sum('jumlah') ?? 0;

                    $peminjamanKembali = $barang->peminjaman()
                        ->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year)
                        ->where('status', 'returned')
                        ->sum('jumlah') ?? 0;

                    $stokAwal = ($barang->stok ?? 0) + $permintaanKeluar + $peminjamanKeluar - $peminjamanKembali;
                    $stokAkhir = $barang->stok ?? 0;

                    return [
                        'id' => $barang->id,
                        'nama_barang' => $barang->nama_barang ?? 'Unknown',
                        'kode_barang' => $barang->kode_barang ?? 'Unknown',
                        'stok_awal' => $stokAwal,
                        'permintaan_keluar' => $permintaanKeluar,
                        'peminjaman_keluar' => $peminjamanKeluar,
                        'peminjaman_kembali' => $peminjamanKembali,
                        'stok_akhir' => $stokAkhir,
                    ];
                })->filter();

            $data = [
                'users' => $users,
                'stokChanges' => $stokChanges,
                'month' => $monthName,
                'year' => $year,
                'generatedAt' => Carbon::now()->locale('id')->isoFormat('D MMMM YYYY HH:mm')
            ];

            $pdf = PDF::loadView('laporan.pdf', $data);
            return $pdf->download("laporan-{$monthName}-{$year}.pdf");
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to generate PDF',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function exportToGoogleSheets(Request $request)
    {
        try {
            \Log::info('Starting Google Sheets export process');
            
            // Get the Google Sheet URL from config
            $sheetUrl = config('services.google.sheet_url');
            
            \Log::info('Google Sheet URL from config:', ['url' => $sheetUrl]);
            
            if (!$sheetUrl) {
                throw new \Exception('Google Sheet URL not configured');
            }

            return response()->json([
                'success' => true,
                'sheetUrl' => $sheetUrl
            ]);

        } catch (\Exception $e) {
            \Log::error('Google Sheets Export Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to get Google Sheet URL',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 