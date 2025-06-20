<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan</title>
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
        .user-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .user-header {
            background-color: #f5f5f5;
            padding: 10px;
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Bulanan</h1>
        <p>Periode: {{ $month }} {{ $year }}</p>
        <p>Tanggal Cetak: {{ $generatedAt ?? date('d/m/Y H:i:s') }}</p>
    </div>

    <div class="section">
        <h2>Laporan Perorang</h2>
        @foreach($users as $index => $user)
            <div class="user-section">
                <div class="user-header">
                    <h3>{{ $user['name'] ?? 'N/A' }}</h3>
                    <p>Total Permintaan: {{ $user['total_permintaan'] ?? 0 }} | Total Peminjaman: {{ $user['total_peminjaman'] ?? 0 }}</p>
                </div>

                @if(count($user['permintaan']) > 0)
                    <h4>Daftar Permintaan</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user['permintaan'] as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y') }}</td>
                                    <td>
                                        {{ $item['nama_barang'] }}<br>
                                        <small>{{ $item['kode_barang'] }}</small>
                                    </td>
                                    <td>{{ $item['jumlah'] }}</td>
                                    <td>{{ $item['keterangan'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                @if(count($user['peminjaman']) > 0)
                    <h4>Daftar Peminjaman</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Tenggat</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user['peminjaman'] as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y') }}</td>
                                    <td>
                                        {{ $item['nama_barang'] }}<br>
                                        <small>{{ $item['kode_barang'] }}</small>
                                    </td>
                                    <td>{{ $item['jumlah'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item['due_date'])->format('d/m/Y') }}</td>
                                    <td>{{ $item['keterangan'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endforeach
    </div>

    <div class="section">
        <h2>Laporan Stok Barang</h2>
        <table>
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Stok Awal</th>
                    <th>Permintaan Keluar</th>
                    <th>Peminjaman Keluar</th>
                    <th>Peminjaman Kembali</th>
                    <th>Stok Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stokChanges as $item)
                    <tr>
                        <td>
                            {{ $item['nama_barang'] }}<br>
                            <small>{{ $item['kode_barang'] }}</small>
                        </td>
                        <td>{{ $item['stok_awal'] }}</td>
                        <td>{{ $item['permintaan_keluar'] }}</td>
                        <td>{{ $item['peminjaman_keluar'] }}</td>
                        <td>{{ $item['peminjaman_kembali'] }}</td>
                        <td>{{ $item['stok_akhir'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh sistem</p>
    </div>
</body>
</html>