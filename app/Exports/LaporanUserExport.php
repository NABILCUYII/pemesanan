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

class LaporanUserExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    protected $user;
    protected $permintaan;
    protected $peminjaman;
    protected $month;
    protected $year;

    public function __construct($user, $permintaan, $peminjaman, $month, $year)
    {
        $this->user = $user;
        $this->permintaan = $permintaan;
        $this->peminjaman = $peminjaman;
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return collect($this->permintaan->merge($this->peminjaman));
    }

    public function headings(): array
    {
        $monthName = $this->getMonthName($this->month);
        return [
            ['LAPORAN DETAIL PENGGUNA'],
            ['Nama: ' . $this->user->name],
            ['Periode: ' . $monthName . ' ' . $this->year],
            ['Tanggal Generate: ' . Carbon::now()->format('d/m/Y H:i:s')],
            [''],
            ['JENIS AKTIVITAS', 'NAMA BARANG', 'KODE BARANG', 'JUMLAH', 'STATUS', 'TANGGAL', 'KETERANGAN']
        ];
    }

    public function map($item): array
    {
        $jenisAktivitas = isset($item->jumlah) && isset($item->tanggal_peminjaman) ? 'Peminjaman' : 'Permintaan';
        $tanggal = isset($item->tanggal_peminjaman) ? $item->tanggal_peminjaman : $item->created_at;
        $keterangan = isset($item->tanggal_pengembalian) ? 'Kembali: ' . $item->tanggal_pengembalian : ($item->keterangan ?? '-');
        
        return [
            $jenisAktivitas,
            $item->nama_barang,
            $item->kode_barang,
            $item->jumlah,
            $this->getStatusText($item->status),
            Carbon::parse($tanggal)->format('d/m/Y'),
            $keterangan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk judul utama
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk nama pengguna
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk periode
        $sheet->mergeCells('A3:G3');
        $sheet->getStyle('A3')->getFont()->setSize(12);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk tanggal generate
        $sheet->mergeCells('A4:G4');
        $sheet->getStyle('A4')->getFont()->setSize(10);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk header tabel
        $sheet->getStyle('A6:G6')->getFont()->setBold(true);
        $sheet->getStyle('A6:G6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2E8F0');
        $sheet->getStyle('A6:G6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk data
        $totalRows = $this->permintaan->count() + $this->peminjaman->count();
        if ($totalRows > 0) {
            $sheet->getStyle('A7:G' . (6 + $totalRows))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 25,
            'C' => 15,
            'D' => 10,
            'E' => 15,
            'F' => 15,
            'G' => 30,
        ];
    }

    public function title(): string
    {
        return 'Laporan ' . $this->user->name;
    }

    private function getStatusText($status)
    {
        switch ($status) {
            case 'pending': return 'Menunggu';
            case 'approved': return 'Disetujui';
            case 'rejected': return 'Ditolak';
            case 'completed': return 'Selesai';
            case 'returned': return 'Dikembalikan';
            case 'not_returned': return 'Belum Dikembalikan';
            default: return $status;
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
