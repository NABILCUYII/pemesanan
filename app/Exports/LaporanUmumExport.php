<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class LaporanUmumExport implements WithMultipleSheets
{
    protected $users;
    protected $stokChanges;
    protected $month;
    protected $year;

    public function __construct($users, $stokChanges, $month, $year)
    {
        $this->users = $users;
        $this->stokChanges = $stokChanges;
        $this->month = $month;
        $this->year = $year;
    }

    public function sheets(): array
    {
        return [
            'Ringkasan' => new RingkasanSheet($this->users, $this->stokChanges, $this->month, $this->year),
            'Aktivitas Pengguna' => new AktivitasPenggunaSheet($this->users, $this->month, $this->year),
            'Pergerakan Stok' => new PergerakanStokSheet($this->stokChanges, $this->month, $this->year),
        ];
    }
}

class RingkasanSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    protected $users;
    protected $stokChanges;
    protected $month;
    protected $year;

    public function __construct($users, $stokChanges, $month, $year)
    {
        $this->users = $users;
        $this->stokChanges = $stokChanges;
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return collect($this->users);
    }

    public function headings(): array
    {
        $monthName = $this->getMonthName($this->month);
        return [
            ['LAPORAN UMUM SISTEM PEMESANAN BARANG'],
            ['Periode: ' . $monthName . ' ' . $this->year],
            ['Tanggal Generate: ' . Carbon::now()->format('d/m/Y H:i:s')],
            [''],
            ['RINGKASAN AKTIVITAS PER PENGGUNA'],
            ['Nama Pengguna', 'Total Permintaan', 'Total Peminjaman', 'Status Aktivitas']
        ];
    }

    public function map($user): array
    {
        $totalAktivitas = $user->total_permintaan + $user->total_peminjaman;
        $statusAktivitas = $totalAktivitas > 0 ? 'Aktif' : 'Tidak Aktif';
        
        return [
            $user->name,
            $user->total_permintaan,
            $user->total_peminjaman,
            $statusAktivitas
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk judul utama
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk periode
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk tanggal generate
        $sheet->mergeCells('A3:D3');
        $sheet->getStyle('A3')->getFont()->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk sub judul
        $sheet->mergeCells('A5:D5');
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk header tabel
        $sheet->getStyle('A6:D6')->getFont()->setBold(true);
        $sheet->getStyle('A6:D6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2E8F0');
        $sheet->getStyle('A6:D6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk data
        $sheet->getStyle('A7:D' . (6 + count($this->users)))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 20,
            'C' => 20,
            'D' => 20,
        ];
    }

    public function title(): string
    {
        return 'Ringkasan';
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

class AktivitasPenggunaSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    protected $users;
    protected $month;
    protected $year;

    public function __construct($users, $month, $year)
    {
        $this->users = $users;
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return collect($this->users);
    }

    public function headings(): array
    {
        $monthName = $this->getMonthName($this->month);
        return [
            ['DETAIL AKTIVITAS PENGGUNA'],
            ['Periode: ' . $monthName . ' ' . $this->year],
            [''],
            ['Nama Pengguna', 'Total Permintaan', 'Total Peminjaman', 'Total Aktivitas', 'Persentase Aktivitas']
        ];
    }

    public function map($user): array
    {
        $totalAktivitas = $user->total_permintaan + $user->total_peminjaman;
        $totalSemua = collect($this->users)->sum(function($u) {
            return $u->total_permintaan + $u->total_peminjaman;
        });
        $persentase = $totalSemua > 0 ? round(($totalAktivitas / $totalSemua) * 100, 2) : 0;
        
        return [
            $user->name,
            $user->total_permintaan,
            $user->total_peminjaman,
            $totalAktivitas,
            $persentase . '%'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk judul
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk periode
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk header tabel
        $sheet->getStyle('A4:E4')->getFont()->setBold(true);
        $sheet->getStyle('A4:E4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2E8F0');
        $sheet->getStyle('A4:E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk data
        $sheet->getStyle('A5:E' . (4 + count($this->users)))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
        ];
    }

    public function title(): string
    {
        return 'Aktivitas Pengguna';
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

class PergerakanStokSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    protected $stokChanges;
    protected $month;
    protected $year;

    public function __construct($stokChanges, $month, $year)
    {
        $this->stokChanges = $stokChanges;
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return collect($this->stokChanges);
    }

    public function headings(): array
    {
        $monthName = $this->getMonthName($this->month);
        return [
            ['PERGERAKAN STOK BARANG'],
            ['Periode: ' . $monthName . ' ' . $this->year],
            [''],
            ['Nama Barang', 'Kode Barang', 'Stok Awal', 'Permintaan Keluar', 'Peminjaman Keluar', 'Peminjaman Kembali', 'Stok Akhir']
        ];
    }

    public function map($item): array
    {
        return [
            $item->nama_barang,
            $item->kode_barang,
            $item->stok_awal,
            $item->permintaan_keluar,
            $item->peminjaman_keluar,
            $item->peminjaman_kembali,
            $item->stok_akhir
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk judul
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk periode
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk header tabel
        $sheet->getStyle('A4:G4')->getFont()->setBold(true);
        $sheet->getStyle('A4:G4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2E8F0');
        $sheet->getStyle('A4:G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk data
        $sheet->getStyle('A5:G' . (4 + count($this->stokChanges)))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 15,
            'C' => 15,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 15,
        ];
    }

    public function title(): string
    {
        return 'Pergerakan Stok';
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
