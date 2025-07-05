<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Aset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }
        .status-pending { color: #f39c12; }
        .status-approved { color: #27ae60; }
        .status-rejected { color: #e74c3c; }
        .status-completed { color: #3498db; }
        .status-returned { color: #9b59b6; }
        .status-not_returned { color: #e67e22; }
    </style>
</head>
<body>
    <!-- Cover Page -->
    <div style="font-family: Arial, Helvetica, sans-serif; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; padding-top: 60px; box-sizing: border-box;">
        <div style="width: 100%; max-width: 600px; margin: 0 auto;">
            <div style="margin-bottom: 40px;">
                <p style="font-size: 12pt; margin: 0 0 8px 0; color: #222;">Laporan Aset</p>
                <p style="font-size: 16pt; font-weight: bold; margin: 0 0 24px 0;">
                    <span style="text-transform: uppercase;">UPT Puskesmas BONTANG UTARA 1</span>
                </p>
            </div>
            <div style="margin-bottom: 40px;">
                <p style="font-size: 12pt; color: #888; margin: 0 0 4px 0;">Periode :</p>
                <p style="font-size: 12pt; color: #888; margin: 0;">
                    {{ $month }} {{ $year }}
                </p>
            </div>
          
        </div>
    </div>

    <!-- Page Break for Report Content -->
    <div style="page-break-before: always;"></div>

    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Periode: {{ $month }} {{ $year }}</p>
        <p>Tanggal Cetak: {{ $generatedAt->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="section">
        <h2>Daftar Aset</h2>
        @if(count($peminjaman) > 0)
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Kode Barang</th>
                        <th>Nama Peminjam</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Due Date</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($peminjaman as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['nama_barang'] }}</td>
                            <td>{{ $item['kode_barang'] }}</td>
                            <td>{{ $item['nama_user'] }}</td>
                            <td>{{ $item['jumlah'] }}</td>
                            <td class="status-{{ $item['status'] }}">
                                @switch($item['status'])
                                    @case('pending')
                                        Menunggu
                                        @break
                                    @case('approved')
                                        Belum Dikembalikan
                                        @break
                                    @case('rejected')
                                        Ditolak
                                        @break
                                    @case('completed')
                                        Selesai
                                        @break
                                    @case('returned')
                                        Dikembalikan
                                        @break
                                    @case('not_returned')
                                        Belum Dikembalikan
                                        @break
                                    @default
                                        {{ $item['status'] }}
                                @endswitch
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item['tanggal_peminjaman'])->format('d/m/Y') }}</td>
                            <td>{{ $item['tanggal_pengembalian'] ? \Carbon\Carbon::parse($item['tanggal_pengembalian'])->format('d/m/Y') : '-' }}</td>
                            <td>{{ $item['due_date'] ? \Carbon\Carbon::parse($item['due_date'])->format('d/m/Y') : '-' }}</td>
                            <td>{{ $item['keterangan'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #666; font-style: italic;">Tidak ada data peminjaman barang untuk periode ini.</p>
        @endif
    </div>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem manajemen inventori UPT Puskesmas Bontang Utara 1</p>
        <p>Dicetak pada: {{ $generatedAt->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html> 