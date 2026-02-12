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
            <td>Total Pemeriksaan</td>
            <td>{{ $stats['total_measurements'] }}</td>
        </tr>
        <tr>
            <td>Pemeriksaan Hari Ini</td>
            <td>{{ $stats['measurements_today'] }}</td>
        </tr>
    </table>

    <!-- 1. DISTRIBUTION BY GENDER -->
    <div class="section-title">Distribusi Jenis Kelamin</div>
    <div class="stats-grid">
        @php
            $maxGender = 1;
            foreach($byGender as $val) if($val > $maxGender) $maxGender = $val;
        @endphp
        <table style="border: none; margin-bottom: 20px;">
            @foreach($byGender as $key => $val)
                @php 
                    $percent = ($val / $maxGender) * 100;
                    if($percent < 10) $percent = 10;
                    $label = $key == 'L' ? 'Laki-Laki' : ($key == 'P' ? 'Perempuan' : 'Lainnya');
                    $color = $key == 'L' ? '#3498db' : '#e91e63';
                @endphp
                <tr style="border: none;">
                    <td style="width: 150px; border: none; padding: 5px; font-weight: bold;">{{ $label }}</td>
                    <td style="border: none; padding: 5px;">
                        <div style="background-color: #ecf0f1; width: 100%; height: 20px; border-radius: 4px;">
                            <div style="background-color: {{ $color }}; width: {{ $percent }}%; height: 100%; border-radius: 4px; color: white; text-align: right; padding-right: 5px; font-size: 11px; line-height: 20px;">
                                {{ $val }}
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

    <!-- 2. DISTRIBUTION BY CATEGORY (Existing + Improved) -->
    <div class="section-title">Distribusi Kategori Usia</div>
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

    <!-- 3. MONTHLY TREND -->
    @if(count($monthlyTrend) > 0)
    <div class="section-break" style="page-break-before: auto;"></div>
    <div class="section-title">Tren Pemeriksaan Bulanan</div>
    <table style="font-size: 11px;">
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Jumlah</th>
                <th>Grafik</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $maxTrend = 1;
                foreach($monthlyTrend as $val) if($val > $maxTrend) $maxTrend = $val;
            @endphp
            @foreach($monthlyTrend as $month => $count)
                @php 
                    $percent = ($count / $maxTrend) * 100;
                    $monthName = \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y');
                @endphp
                <tr>
                    <td style="width: 120px;">{{ $monthName }}</td>
                    <td style="width: 50px; text-align: center;">{{ $count }}</td>
                    <td>
                        <div style="background-color: #34495e; width: {{ $percent }}%; height: 12px; border-radius: 2px;"></div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- 4. DETAILED NUTRITIONAL STATUS -->
    <div class="section-break" style="page-break-before: always;"></div>
    <div class="section-title">Rekapitulasi Status Gizi & Pertumbuhan</div>
    
    <div style="margin-bottom: 30px;">
        @php
            $maxVal = 1;
            foreach($statusCounts as $val) {
                if($val > $maxVal) $maxVal = $val;
            }
            $colors = [
                'Gizi Buruk' => '#c0392b',      // Dark Red
                'Gizi Kurang' => '#e67e22',     // Orange
                'Gizi Baik' => '#27ae60',       // Green
                'Gizi Lebih' => '#f1c40f',      // Yellow
                'Obesitas' => '#c0392b',        // Dark Red
                'Sangat Pendek' => '#e74c3c',   // Red
                'Pendek' => '#f39c12',          // Orange
                'Normal' => '#27ae60',          // Green
                'Tinggi' => '#8e44ad',          // Purple
            ];
        @endphp

        <table style="border: none;">
            @foreach($statusCounts as $key => $val)
                @php 
                    $percent = ($val / $maxVal) * 100;
                    if($percent < 1) $percent = 1; 
                @endphp
                <tr style="border: none;">
                    <td style="width: 150px; border: none; padding: 5px; font-size: 11px;">
                        {{ $key }}
                    </td>
                    <td style="border: none; padding: 5px;">
                        <div style="background-color: #ecf0f1; width: 100%; height: 20px; border-radius: 4px;">
                            <div style="background-color: {{ $colors[$key] ?? '#7f8c8d' }}; width: {{ $percent }}%; height: 100%; border-radius: 4px; line-height: 20px; text-align: right; padding-right: 5px; color: white; font-size: 10px; font-weight: bold;">
                                {{ $val > 0 ? $val : '' }}
                            </div>
                        </div>
                    </td>
                    <td style="width: 30px; border: none; padding: 5px; text-align: right; font-weight: bold;">
                        {{ $val }}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

    @if(isset($measurements) && count($measurements) > 0)
        <div class="section-break" style="page-break-before: always;"></div>
        <div class="section-title">Daftar Pemeriksaan Detail</div>
        
        <table class="detail-table">
            <thead>
                <tr>
                    <th style="width: 35%;">Informasi Pasien</th>
                    <th style="width: 25%;">Validasi Input</th>
                    <th style="width: 40%;">Hasil Pemeriksaan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($measurements as $m)
                    <tr>
                        <td style="vertical-align: top;">
                            <div style="font-weight: bold; font-size: 12px; margin-bottom: 4px;">{{ $m->subject->name ?? '-' }}</div>
                            <div style="font-size: 10px; color: #555;">
                                NIK: {{ $m->subject->nik ?? '-' }}<br>
                                JK: {{ $m->subject->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}<br>
                                Umur: {{ floor($m->age_in_months / 12) }} Thn {{ $m->age_in_months % 12 }} Bln
                            </div>
                            <div style="margin-top: 6px;">
                                <span class="badge" style="background-color: #34495e;">{{ ucfirst($m->category) }}</span>
                            </div>
                        </td>
                        <td style="vertical-align: top; font-size: 10px;">
                             <strong>Petugas:</strong><br>
                             {{ $m->user->name ?? 'Sistem' }}<br><br>
                             <strong>Tanggal Input:</strong><br>
                             {{ $m->created_at ? $m->created_at->format('d-m-Y H:i') : '-' }}
                        </td>
                        <td style="vertical-align: top;">
                             <div style="font-size: 11px;">
                                <strong>BB:</strong> {{ $m->weight }} kg | <strong>TB:</strong> {{ $m->height }} cm
                                @if($m->bmi) | <strong>IMT:</strong> {{ $m->bmi }} @endif
                             </div>
                            
                            <div style="margin-top: 5px; font-size: 10px; border-top: 1px dashed #ccc; padding-top: 4px;">
                                @if($m->status_bbu) BB/U: {{ $m->status_bbu }}<br> @endif
                                @if($m->status_tbu) TB/U: {{ $m->status_tbu }}<br> @endif
                                @if($m->status_bbtb) BB/TB: {{ $m->status_bbtb }}<br> @endif
                                @if($m->status_imtu) IMT/U: {{ $m->status_imtu }}<br> @endif
                                @if($m->status_bmi) BMI: {{ $m->status_bmi }} @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        ANTROPOMETRI Academic Dashboard &copy; {{ date('Y') }}
    </div>
</body>

</html>