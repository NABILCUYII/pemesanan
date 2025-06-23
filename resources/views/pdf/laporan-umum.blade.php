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
        .pendahuluan {
            margin-bottom: 30px;
            text-align: justify;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #007bff;
        }
        .pendahuluan h2 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #333;
        }
        .pendahuluan p {
            margin: 0 0 8px 0;
            text-indent: 20px;
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
    <!-- COB Report Section - First Page -->
    <div style="text-align: center; font-family: 'Times New Roman', Times, serif; font-size: 14pt; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: space-between; padding: 2cm; box-sizing: border-box;">
        <div style="margin-top: 2cm;">
            <p style="font-weight: bold; text-transform: uppercase; margin: 5px 0; padding: 0 1in;">Laporan Pendahuluan</p>
            <p style="font-weight: bold; text-transform: uppercase; margin: 5px 0; padding: 0 1in;">Asuhan Keperawatan Gadar dengan Diagnosa Medis COB (Cidera Otak Berat) di Ruang IGD RS William Booth Surabaya</p>
        </div>

        <div style="margin-top: 1.5cm; margin-bottom: 1.5cm;">
            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTUwIiBoZWlnaHQ9IjE1MCIgdmlld0JveD0iMCAwIDE1MCAxNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxNTAiIGhlaWdodD0iMTUwIiBmaWxsPSIjRkZGRkZGIi8+Cjx0ZXh0IHg9Ijc1IiB5PSI4MCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiBmaWxsPSIjMDAwMDAwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5Mb2dvPC90ZXh0Pgo8L3N2Zz4K" alt="Logo" style="width: 3.5cm; height: auto;">
        </div>

        <div style="margin-top: 1.5cm; flex-grow: 1;">
            <p style="margin: 0;">OLEH :</p>
            <p style="font-weight: bold; text-decoration: underline; margin-top: 1cm; margin-bottom: 0;">Erina Nuryta Kurniawati</p>
            <p style="font-weight: bold; margin: 0;">NIM : 2022.06.009</p>
        </div>

        <div style="margin-bottom: 2cm;">
            <p style="font-weight: bold; text-transform: uppercase; margin: 5px 0;">Program Studi Profesi Ners</p>
            <p style="font-weight: bold; text-transform: uppercase; margin: 5px 0;">Sekolah Tinggi Ilmu Kesehatan William Booth</p>
            <p style="font-weight: bold; text-transform: uppercase; margin: 5px 0;">Surabaya</p>
            <p style="font-weight: bold; text-transform: uppercase; margin: 5px 0;">2022</p>
        </div>
    </div>

    <!-- Page Break for General Report -->
    <div style="page-break-before: always;"></div>

    <div class="header">
        <h1>Laporan Bulanan</h1>
        <p>Periode: {{ $month }} {{ $year }}</p>
        <p>Tanggal Cetak: {{ $generatedAt ?? date('d/m/Y H:i:s') }}</p>
    </div>

    <div class="pendahuluan">
        <h2>PENDAHULUAN</h2>
        <p>Laporan bulanan ini disusun untuk memberikan gambaran komprehensif mengenai aktivitas pemesanan dan peminjaman barang dalam sistem manajemen inventori selama periode {{ $month }} {{ $year }}. Laporan ini mencakup data transaksi perorang dan pergerakan stok barang yang terjadi dalam sistem.</p>
        
        <p>Tujuan penyusunan laporan ini adalah untuk memantau efektivitas pengelolaan inventori, menganalisis pola penggunaan barang oleh pengguna, serta memberikan informasi yang akurat untuk pengambilan keputusan terkait pengadaan dan pengelolaan stok barang di masa mendatang.</p>
        
        <p>Ruang lingkup laporan meliputi seluruh transaksi permintaan dan peminjaman barang yang telah diproses dalam sistem, termasuk status persetujuan, jumlah barang yang diminta/dipinjam, serta pergerakan stok yang terjadi selama periode pelaporan.</p>
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
                    <th>Stok Awal<br><small>(Pertama Masuk)</small></th>
                    <th>Permintaan Keluar</th>
                    <th>Peminjaman Keluar</th>
                    <th>Peminjaman Kembali</th>
                    <th>Stok Akhir<br><small>(Total Saat Ini)</small></th>
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