<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Ringkasan Antropometri</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 14px;
            color: #666;
        }

        .info {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 10px;
            color: #2c3e50;
            border-left: 4px solid #3498db;
            padding-left: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .stats-grid {
            display: block;
            width: 100%;
        }

        .stat-box {
            display: inline-block;
            width: 45%;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            margin-right: 10px;
            background-color: #fff;
        }

        .stat-label {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #2980b9;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #95a5a6;
            padding: 10px 0;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }

        .bg-success {
            background-color: #27ae60;
        }

        .bg-warning {
            background-color: #f39c12;
        }

        .bg-danger {
            background-color: #e74c3c;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">Laporan Ringkasan Antropometri</div>
        <div class="subtitle">Sistem Informasi Pengukuran Status Gizi</div>
    </div>

    <div class="info">
        <p><strong>{{ $period }}</strong></p>
        <p>Dicetak pada: {{ $date }}</p>
    </div>

    <div class="section-title">Ringkasan Umum</div>
    <table>
        <tr>
            <th>Parameter</th>
            <th>Total</th>
        </tr>
        <tr>
            <td>Total Pengguna (Petugas)</td>
            <td>{{ $stats['total_users'] }}</td>
        </tr>
        <tr>
            <td>Total Subjek Terdaftar</td>
            <td>{{ $stats['total_subjects'] }}</td>
        </tr>
        <tr>
            <td>Total Pengukuran Tercatat</td>
            <td>{{ $stats['total_measurements'] }}</td>
        </tr>
        <tr>
            <td>Pengukuran Hari Ini</td>
            <td>{{ $stats['measurements_today'] }}</td>
        </tr>
    </table>

    <div class="section-title">Distribusi Kategori</div>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Jumlah Pengukuran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stats['by_category'] as $category => $count)
                <tr>
                    <td>{{ ucfirst($category) }}</td>
                    <td>{{ $count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Distribusi Status Gizi</div>
    <table>
        <thead>
            <tr>
                <th>Status Kesehatan/Gizi</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gizi Buruk / Sangat Pendek / Berat</td>
                <td>{{ $stats['by_status']['gizi_buruk'] }}</td>
            </tr>
            <tr>
                <td>Gizi Kurang / Pendek / Ringan</td>
                <td>{{ $stats['by_status']['gizi_kurang'] }}</td>
            </tr>
            <tr>
                <td>Gizi Baik / Normal</td>
                <td>{{ $stats['by_status']['gizi_baik'] }}</td>
            </tr>
            <tr>
                <td>Gizi Lebih / Gemuk</td>
                <td>{{ $stats['by_status']['gizi_lebih'] }}</td>
            </tr>
            <tr>
                <td>Obesitas</td>
                <td>{{ $stats['by_status']['obesitas'] }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        ANTROPOMETRI Academic Dashboard &copy; 2026 - Halaman ringkasan eksekutif
    </div>
</body>

</html>