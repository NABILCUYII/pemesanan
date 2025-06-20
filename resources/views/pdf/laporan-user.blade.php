<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
        }
        .pendahuluan {
            margin-bottom: 25px;
            text-align: justify;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #007bff;
        }
        .pendahuluan h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #333;
        }
        .pendahuluan p {
            margin: 0 0 8px 0;
            text-indent: 20px;
            font-size: 11px;
        }
        .user-info {
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
        }
        .status {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
        .status-completed { background-color: #d1ecf1; color: #0c5460; }
        .status-returned { background-color: #e2e3e5; color: #383d41; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SISTEM PEMESANAN BARANG</h1>
        <p>{{ $title }}</p>
        <p>Periode: {{ $month }} {{ $year }}</p>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="pendahuluan">
        <h3>Pendahuluan</h3>
        <p>Laporan ini dibuat untuk memberikan gambaran tentang aktivitas pengguna dalam menggunakan sistem pemesanan barang. Laporan ini mencakup detail permintaan barang dan peminjaman barang yang telah dilakukan oleh pengguna.</p>
        <p>Laporan ini dapat digunakan untuk analisis tren permintaan barang, efektivitas peminjaman barang, dan evaluasi kinerja pengguna.</p>
    </div>

    <div class="user-info">
        <h3>Informasi Pengguna:</h3>
        <p><strong>Nama:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Total Permintaan:</strong> {{ $permintaan->count() }}</p>
        <p><strong>Total Peminjaman:</strong> {{ $peminjaman->count() }}</p>
    </div>

    <div class="section">
        <h3>Detail Permintaan Barang</h3>
        @if($permintaan->count() > 0)
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
                @foreach($permintaan as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->barang->nama_barang }}</td>
                    <td>{{ $item->barang->kode_barang }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>
                        <span class="status status-{{ $item->status }}">
                            @switch($item->status)
                                @case('pending') Menunggu @break
                                @case('approved') Disetujui @break
                                @case('rejected') Ditolak @break
                                @case('completed') Selesai @break
                                @default {{ $item->status }}
                            @endswitch
                        </span>
                    </td>
                    <td>{{ date('d/m/Y', strtotime($item->created_at)) }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">Tidak ada data permintaan untuk periode ini</div>
        @endif
    </div>

    <div class="section">
        <h3>Detail Peminjaman Barang</h3>
        @if($peminjaman->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Barang</th>
                    <th>Kode</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($peminjaman as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->barang->nama_barang }}</td>
                    <td>{{ $item->barang->kode_barang }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>
                        <span class="status status-{{ $item->status }}">
                            @switch($item->status)
                                @case('pending') Menunggu @break
                                @case('approved') Disetujui @break
                                @case('rejected') Ditolak @break
                                @case('returned') Dikembalikan @break
                                @case('not_returned') Belum Dikembalikan @break
                                @default {{ $item->status }}
                            @endswitch
                        </span>
                    </td>
                    <td>{{ $item->tanggal_peminjaman ? date('d/m/Y', strtotime($item->tanggal_peminjaman)) : '-' }}</td>
                    <td>{{ $item->tanggal_pengembalian ? date('d/m/Y', strtotime($item->tanggal_pengembalian)) : '-' }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">Tidak ada data peminjaman untuk periode ini</div>
        @endif
    </div>

    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem pada {{ date('d/m/Y H:i:s') }}</p>
        <p>Sistem Pemesanan Barang - {{ date('Y') }}</p>
    </div>
</body>
</html> 