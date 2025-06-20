<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .content {
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Test PDF Generation</h1>
    
    <div class="content">
        <p><strong>Tanggal:</strong> {{ date('d/m/Y H:i:s') }}</p>
        <p><strong>Status:</strong> PDF berhasil dibuat!</p>
        
        <h2>Data Test</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Test User 1</td>
                    <td>5</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Test User 2</td>
                    <td>3</td>
                </tr>
            </tbody>
        </table>
        
        <p style="margin-top: 30px; text-align: center; color: #666;">
            Dokumen ini dibuat untuk testing PDF generation
        </p>
    </div>
</body>
</html> 