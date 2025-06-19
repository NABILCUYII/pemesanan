<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perorang - {{ $user['name'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .user-info {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .user-info h2 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 18px;
        }
        .user-info p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h3 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 16px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
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
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .status {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-pending { background-color: #ffd700; }
        .status-approved { background-color: #90ee90; }
        .status-rejected { background-color: #ffcccb; }
        .status-completed { background-color: #87ceeb; }
        .status-returned { background-color: #98fb98; }
        .status-not_returned { background-color: #f0e68c; }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PERORANG</h1>
        <p>Sistem Pemesanan dan Peminjaman Barang</p>
        <p>Periode: {{ $month }} {{ $year }}</p>
        <p>Dibuat pada: {{ $generatedAt }}</p>
    </div>

    <div class="user-info">
        <h2>Informasi Pengguna</h2>
        <p><strong>Nama:</strong> {{ $user['name'] }}</p>
        <p><strong>Total Permintaan:</strong> {{ $user['total_permintaan'] }}</p>
        <p><strong>Total Peminjaman:</strong> {{ $user['total_peminjaman'] }}</p>
    </div>

    @if($user['permintaan']->count() > 0)
    <div class="section">
        <h3>Detail Permintaan</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Barang</th>
                    <th>Kode</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($user['permintaan'] as $index => $permintaan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $permintaan['nama_barang'] }}</td>
                    <td>{{ $permintaan['kode_barang'] }}</td>
                    <td>{{ $permintaan['jumlah'] }}</td>
                    <td>
                        <span class="status status-{{ $permintaan['status'] }}">
                            @switch($permintaan['status'])
                                @case('pending')
                                    Menunggu
                                    @break
                                @case('approved')
                                    Disetujui
                                    @break
                                @case('rejected')
                                    Ditolak
                                    @break
                                @case('completed')
                                    Selesai
                                    @break
                                @default
                                    {{ $permintaan['status'] }}
                            @endswitch
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($permintaan['created_at'])->format('d/m/Y') }}</td>
                    <td>{{ $permintaan['keterangan'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($user['peminjaman']->count() > 0)
    <div class="section">
        <h3>Detail Peminjaman</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Barang</th>
                    <th>Kode</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($user['peminjaman'] as $index => $peminjaman)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $peminjaman['nama_barang'] }}</td>
                    <td>{{ $peminjaman['kode_barang'] }}</td>
                    <td>{{ $peminjaman['jumlah'] }}</td>
                    <td>
                        <span class="status status-{{ $peminjaman['status'] }}">
                            @switch($peminjaman['status'])
                                @case('pending')
                                    Menunggu
                                    @break
                                @case('approved')
                                    Disetujui
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
                                    {{ $peminjaman['status'] }}
                            @endswitch
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($peminjaman['created_at'])->format('d/m/Y') }}</td>
                    <td>{{ $peminjaman['due_date'] ? \Carbon\Carbon::parse($peminjaman['due_date'])->format('d/m/Y') : '-' }}</td>
                    <td>{{ $peminjaman['keterangan'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($user['permintaan']->count() == 0 && $user['peminjaman']->count() == 0)
    <div class="no-data">
        <p>Tidak ada data permintaan atau peminjaman untuk periode ini.</p>
    </div>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem</p>
        <p>© {{ date('Y') }} Sistem Pemesanan dan Peminjaman Barang</p>
    </div>
</body>
</html> 