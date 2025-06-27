<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class LaporanPermintaanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    protected $permintaan;
    protected $month;
    protected $year;

    public function __construct($permintaan, $month, $year)
    {
        $this->permintaan = $permintaan;
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return $this->permintaan;
    }

    public function headings(): array
    {
        $monthName = $this->getMonthName($this->month);
        return [
            ['LAPORAN PERMINTAAN BARANG'],
            ['Periode: ' . $monthName . ' ' . $this->year],
            ['Tanggal Generate: ' . Carbon::now()->format('d/m/Y H:i:s')],
            [''],
            ['No', 'Nama Barang', 'Kode Barang', 'Nama Pemohon', 'Jumlah', 'Status', 'Tanggal Permintaan', 'Keterangan']
        ];
    }

    public function map($item): array
    {
        static $no = 1;
        $statusText = $this->getStatusText($item->status);
        
        return [
            $no++,
            $item->nama_barang,
            $item->kode_barang,
            $item->nama_user,
            $item->jumlah,
            $statusText,
            Carbon::parse($item->created_at)->format('d/m/Y H:i'),
            $item->keterangan ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = count($this->permintaan) + 5; // 5 for headers

        // Style untuk judul utama
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk periode
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk tanggal generate
        $sheet->mergeCells('A3:H3');
        $sheet->getStyle('A3')->getFont()->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk header tabel
        $sheet->getStyle('A5:H5')->getFont()->setBold(true);
        $sheet->getStyle('A5:H5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2E8F0');
        $sheet->getStyle('A5:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk data
        $sheet->getStyle('A6:H' . $totalRows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A6:H' . $totalRows)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Border untuk seluruh tabel
        $sheet->getStyle('A5:H' . $totalRows)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // No
            'B' => 25,  // Nama Barang
            'C' => 15,  // Kode Barang
            'D' => 20,  // Nama Pemohon
            'E' => 12,  // Jumlah
            'F' => 15,  // Status
            'G' => 20,  // Tanggal Permintaan
            'H' => 30,  // Keterangan
        ];
    }

    public function title(): string
    {
        return 'Laporan Permintaan';
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

    private function getStatusText($status)
    {
        $statusMap = [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai',
        ];
        
        return $statusMap[$status] ?? $status;
    }
} 