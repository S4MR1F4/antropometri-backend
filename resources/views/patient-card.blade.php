<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Pasien - {{ $subject->name }} | Antropometri Indonesia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0D9488;
            --primary-dark: #0F766E;
            --primary-light: #CCFBF1;
            --primary-bg: #F0FDFA;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --border-color: #E2E8F0;
            --card-bg: #FFFFFF;
            --success: #10B981;
            --success-bg: #ECFDF5;
            --warning: #F59E0B;
            --warning-bg: #FFFBEB;
            --danger: #EF4444;
            --danger-bg: #FEF2F2;
            --info: #0284C7;
            --info-bg: #F0F9FF;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #F8FAFC;
            color: var(--text-main);
            line-height: 1.5;
            padding: 24px 16px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Top Action Bar (hidden when printing) */
        .top-action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: var(--primary-bg);
            border: 1px solid var(--primary-light);
            border-radius: 999px;
            color: var(--primary-dark);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .action-group {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-outline {
            background: #FFFFFF;
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }

        .btn-outline:hover {
            background: #F1F5F9;
        }

        /* Main Card */
        .patient-card {
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            margin-bottom: 24px;
        }

        /* Card Header */
        .card-header {
            background: linear-gradient(135deg, #0F766E 0%, #0D9488 100%);
            color: #FFFFFF;
            padding: 28px 32px;
            position: relative;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .header-title-box h1 {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .header-subtitle {
            font-size: 13px;
            color: #CCFBF1;
            font-weight: 500;
        }

        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            margin-top: 10px;
        }

        /* Biodata Section */
        .biodata-section {
            padding: 28px 32px;
            background: #FFFFFF;
            border-bottom: 1px solid var(--border-color);
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 24px;
            align-items: center;
        }

        .patient-main-info {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .patient-name-row {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .patient-name {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
        }

        .gender-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
        }

        .gender-l {
            background: #E0F2FE;
            color: #0369A1;
        }

        .gender-p {
            background: #FCE7F3;
            color: #BE185D;
        }

        .biodata-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 14px;
            margin-top: 6px;
        }

        .bio-item {
            background: #F8FAFC;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #EDF2F7;
        }

        .bio-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .bio-value {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-main);
        }

        /* QR Code Container */
        .qr-card-box {
            background: #FFFFFF;
            border: 2px dashed var(--primary);
            border-radius: 16px;
            padding: 14px;
            text-align: center;
            min-width: 140px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .qr-placeholder {
            width: 110px;
            height: 110px;
            background: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-label {
            font-size: 10px;
            font-weight: 800;
            color: var(--primary-dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Latest Measurement Summary Banner */
        .latest-summary-section {
            padding: 24px 32px;
            background: #FAFDFB;
            border-bottom: 1px solid var(--border-color);
        }

        .section-headline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .headline-title {
            font-size: 14px;
            font-weight: 800;
            color: var(--primary-dark);
            text-transform: uppercase;
            letter-spacing: 0.75px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .headline-date {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .metric-box {
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 12px 14px;
            text-align: center;
        }

        .metric-val {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-main);
        }

        .metric-unit {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .metric-lbl {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Status Pills Grid */
        .status-pills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .status-card {
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid transparent;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .status-normal {
            background: var(--success-bg);
            border-color: #A7F3D0;
            color: #065F46;
        }

        .status-warning {
            background: var(--warning-bg);
            border-color: #FDE68A;
            color: #92400E;
        }

        .status-danger {
            background: var(--danger-bg);
            border-color: #FECACA;
            color: #991B1B;
        }

        .status-card-header {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            opacity: 0.85;
            display: flex;
            justify-content: space-between;
        }

        .status-card-val {
            font-size: 14px;
            font-weight: 800;
        }

        .recommendation-box {
            background: #F0F9FF;
            border-left: 4px solid var(--info);
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 13px;
            color: #0C4A6E;
        }

        /* History Table Section */
        .history-section {
            padding: 28px 32px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-top: 12px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        table.history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            text-align: left;
            background: #FFFFFF;
        }

        table.history-table th {
            background: #F8FAFC;
            color: var(--text-muted);
            font-weight: 800;
            text-transform: uppercase;
            padding: 12px 14px;
            border-bottom: 1px solid var(--border-color);
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        table.history-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #F1F5F9;
            color: var(--text-main);
            font-weight: 600;
            white-space: nowrap;
        }

        table.history-table tr:last-child td {
            border-bottom: none;
        }

        table.history-table tr:hover td {
            background: #F8FAFC;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
        }

        .card-footer {
            padding: 20px 32px;
            background: #F8FAFC;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            color: var(--text-muted);
            flex-wrap: wrap;
            gap: 12px;
        }

        /* Print styles */
        @media print {
            body {
                background: #FFFFFF;
                padding: 0;
            }
            .container {
                max-width: 100%;
            }
            .top-action-bar {
                display: none !important;
            }
            .patient-card {
                border: 1px solid #CBD5E1;
                box-shadow: none;
                border-radius: 10px;
                page-break-after: avoid;
            }
            .card-header {
                padding: 18px 24px;
                background: #0F766E !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .biodata-section, .latest-summary-section, .history-section {
                padding: 16px 24px;
            }
            table.history-table th {
                background: #F1F5F9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .status-card {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @media (max-width: 640px) {
            .biodata-section {
                grid-template-columns: 1fr;
            }
            .qr-card-box {
                margin: 0 auto;
            }
            .card-header {
                padding: 20px 18px;
            }
            .biodata-section, .latest-summary-section, .history-section {
                padding: 18px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Top Action Bar -->
    <div class="top-action-bar">
        <div class="brand-badge">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
            SISTEM ANTROPOMETRI NASIONAL
        </div>
        <div class="action-group">
            <button onclick="window.print()" class="btn btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Cetak / Simpan PDF
            </button>
            <a href="antropometri://card/{{ $subject->id }}" class="btn btn-outline" id="appLink">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                    <line x1="12" y1="18" x2="12.01" y2="18"></line>
                </svg>
                Buka di Aplikasi
            </a>
        </div>
    </div>

    <!-- Patient Card -->
    <div class="patient-card">
        <!-- Card Header -->
        <div class="card-header">
            <div class="header-content">
                <div class="header-title-box">
                    <h1>Kartu Tumbuh Kembang Pasien</h1>
                    <div class="header-subtitle">Rekam Jejak Status Gizi & Pertumbuhan Standar Kemenkes RI</div>
                    <div class="verified-badge">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <polyline points="9 12 11 14 15 10"/>
                        </svg>
                        DATA RESMI TERVERIFIKASI
                    </div>
                </div>
            </div>
        </div>

        <!-- Biodata Section -->
        <div class="biodata-section">
            <div class="patient-main-info">
                <div class="patient-name-row">
                    <div class="patient-name">{{ $subject->name }}</div>
                    @if(strtoupper($subject->gender ?? '') === 'L')
                        <div class="gender-tag gender-l">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="10" cy="14" r="5"></circle>
                                <line x1="19" y1="5" x2="13.6" y2="10.4"></line>
                                <polyline points="19 10 19 5 14 5"></polyline>
                            </svg>
                            Laki-laki
                        </div>
                    @else
                        <div class="gender-tag gender-p">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="10" r="5"></circle>
                                <line x1="12" y1="15" x2="12" y2="21"></line>
                                <line x1="9" y1="18" x2="15" y2="18"></line>
                            </svg>
                            Perempuan
                        </div>
                    @endif
                </div>

                <div class="biodata-grid">
                    <div class="bio-item">
                        <div class="bio-label">Tanggal Lahir</div>
                        <div class="bio-value">
                            {{ $subject->date_of_birth ? $subject->date_of_birth->translatedFormat('d F Y') : '-' }}
                        </div>
                    </div>
                    <div class="bio-item">
                        <div class="bio-label">Usia Terkini</div>
                        <div class="bio-value">
                            @if($currentAgeYears > 0)
                                {{ $currentAgeYears }} Thn ({{ $currentAgeMonths }} Bln)
                            @else
                                {{ $currentAgeMonths }} Bulan
                            @endif
                        </div>
                    </div>
                    <div class="bio-item">
                        <div class="bio-label">Orang Tua / Wali</div>
                        <div class="bio-value">{{ $subject->parent_name ?: '-' }}</div>
                    </div>
                    <div class="bio-item">
                        <div class="bio-label">ID Pasien / Faskes</div>
                        <div class="bio-value">#{{ str_pad($subject->id, 5, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="qr-card-box">
                <div class="qr-placeholder" id="qrcode"></div>
                <div class="qr-label">Scan Verifikasi</div>
            </div>
        </div>

        @if($latest)
        <!-- Latest Measurement Summary -->
        <div class="latest-summary-section">
            <div class="section-headline">
                <div class="headline-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                    Pemeriksaan Terakhir
                </div>
                <div class="headline-date">
                    {{ $latest->measurement_date ? $latest->measurement_date->translatedFormat('d F Y') : '-' }}
                </div>
            </div>

            <div class="metrics-grid">
                <div class="metric-box">
                    <div class="metric-val">{{ $latest->weight ? number_format((float)$latest->weight, 1) : '-' }}</div>
                    <div class="metric-unit">kg</div>
                    <div class="metric-lbl">Berat Badan</div>
                </div>
                <div class="metric-box">
                    <div class="metric-val">{{ $latest->height ? number_format((float)$latest->height, 1) : '-' }}</div>
                    <div class="metric-unit">cm</div>
                    <div class="metric-lbl">Tinggi / Panjang</div>
                </div>
                @if($latest->arm_circumference)
                <div class="metric-box">
                    <div class="metric-val">{{ number_format((float)$latest->arm_circumference, 1) }}</div>
                    <div class="metric-unit">cm</div>
                    <div class="metric-lbl">Li. Lengan (LILA)</div>
                </div>
                @endif
                @if($latest->head_circumference)
                <div class="metric-box">
                    <div class="metric-val">{{ number_format((float)$latest->head_circumference, 1) }}</div>
                    <div class="metric-unit">cm</div>
                    <div class="metric-lbl">Li. Kepala</div>
                </div>
                @endif
                @if($latest->bmi)
                <div class="metric-box">
                    <div class="metric-val">{{ number_format((float)$latest->bmi, 1) }}</div>
                    <div class="metric-unit">kg/m²</div>
                    <div class="metric-lbl">IMT</div>
                </div>
                @endif
            </div>

            <!-- Status Badges -->
            <div class="status-pills-grid">
                @if($latest->status_bbu)
                    <div class="status-card {{ str_contains(strtolower($latest->status_bbu), 'normal') || str_contains(strtolower($latest->status_bbu), 'baik') ? 'status-normal' : 'status-warning' }}">
                        <div class="status-card-header">
                            <span>Berat / Umur (BB/U)</span>
                            <span>{{ $latest->zscore_bbu ? number_format((float)$latest->zscore_bbu, 2) . ' SD' : '' }}</span>
                        </div>
                        <div class="status-card-val">{{ $latest->status_bbu }}</div>
                    </div>
                @endif

                @if($latest->status_tbu)
                    <div class="status-card {{ str_contains(strtolower($latest->status_tbu), 'normal') ? 'status-normal' : 'status-danger' }}">
                        <div class="status-card-header">
                            <span>Tinggi / Umur (TB/U)</span>
                            <span>{{ $latest->zscore_tbu ? number_format((float)$latest->zscore_tbu, 2) . ' SD' : '' }}</span>
                        </div>
                        <div class="status-card-val">{{ $latest->status_tbu }}</div>
                    </div>
                @endif

                @if($latest->status_bbtb)
                    <div class="status-card {{ str_contains(strtolower($latest->status_bbtb), 'normal') || str_contains(strtolower($latest->status_bbtb), 'baik') ? 'status-normal' : 'status-warning' }}">
                        <div class="status-card-header">
                            <span>BB / TB (Wasting)</span>
                            <span>{{ $latest->zscore_bbtb ? number_format((float)$latest->zscore_bbtb, 2) . ' SD' : '' }}</span>
                        </div>
                        <div class="status-card-val">{{ $latest->status_bbtb }}</div>
                    </div>
                @endif

                @if($latest->status_imtu)
                    <div class="status-card {{ str_contains(strtolower($latest->status_imtu), 'normal') || str_contains(strtolower($latest->status_imtu), 'baik') ? 'status-normal' : 'status-warning' }}">
                        <div class="status-card-header">
                            <span>IMT / Umur</span>
                            <span>{{ $latest->zscore_imtu ? number_format((float)$latest->zscore_imtu, 2) . ' SD' : '' }}</span>
                        </div>
                        <div class="status-card-val">{{ $latest->status_imtu }}</div>
                    </div>
                @endif

                @if($latest->status_bmi)
                    <div class="status-card {{ str_contains(strtolower($latest->status_bmi), 'normal') ? 'status-normal' : 'status-warning' }}">
                        <div class="status-card-header"><span>Kategori IMT</span></div>
                        <div class="status-card-val">{{ $latest->status_bmi }}</div>
                    </div>
                @endif

                @if($latest->status_lila)
                    <div class="status-card {{ str_contains(strtolower($latest->status_lila), 'normal') ? 'status-normal' : 'status-danger' }}">
                        <div class="status-card-header"><span>Status LILA / KEK</span></div>
                        <div class="status-card-val">{{ $latest->status_lila }}</div>
                    </div>
                @endif
            </div>

            @if($latest->recommendation || $latest->notes)
            <div class="recommendation-box">
                <strong>Catatan / Saran Petugas:</strong>
                {{ $latest->recommendation ?: $latest->notes }}
            </div>
            @endif
        </div>
        @endif

        <!-- History Table Section -->
        <div class="history-section">
            <div class="section-headline">
                <div class="headline-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Riwayat Pemeriksaan Pasien ({{ $measurements->count() }} Data)
                </div>
            </div>

            @if($measurements->isEmpty())
                <p style="color: var(--text-muted); font-size: 13px; padding: 12px 0;">Belum ada catatan riwayat pemeriksaan untuk pasien ini.</p>
            @else
                <div class="table-responsive">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Usia</th>
                                <th>BB (kg)</th>
                                <th>TB (cm)</th>
                                <th>LILA (cm)</th>
                                <th>Status Utama</th>
                                <th>Tindak Lanjut / Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($measurements as $item)
                            <tr>
                                <td>{{ $item->measurement_date ? $item->measurement_date->format('d/m/Y') : '-' }}</td>
                                <td>{{ $item->age_in_months ? $item->age_in_months . ' bln' : '-' }}</td>
                                <td>{{ $item->weight ? number_format((float)$item->weight, 1) : '-' }}</td>
                                <td>{{ $item->height ? number_format((float)$item->height, 1) : '-' }}</td>
                                <td>{{ $item->arm_circumference ? number_format((float)$item->arm_circumference, 1) : '-' }}</td>
                                <td>
                                    @php
                                        $mainStatus = $item->status_bbtb ?: ($item->status_imtu ?: ($item->status_bmi ?: ($item->status_bbu ?: ($item->status_tbu ?: '-'))));
                                        $isNormal = str_contains(strtolower($mainStatus), 'normal') || str_contains(strtolower($mainStatus), 'baik');
                                    @endphp
                                    <span class="badge {{ $isNormal ? 'status-normal' : 'status-warning' }}">
                                        {{ $mainStatus }}
                                    </span>
                                </td>
                                <td style="max-width: 220px; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $item->recommendation ?: ($item->notes ?: '-') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Card Footer -->
        <div class="card-footer">
            <div>Standar Antropometri Anak & Dewasa (Permenkes RI).</div>
            <div>Dicetak via Sistem Antropometri &bull; {{ now()->translatedFormat('d F Y H:i') }}</div>
        </div>
    </div>
</div>

<!-- Lightweight inline QR code renderer -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrContainer = document.getElementById('qrcode');
        if (qrContainer && typeof QRCode !== 'undefined') {
            new QRCode(qrContainer, {
                text: "{{ $cardUrl }}",
                width: 110,
                height: 110,
                colorDark : "#0F766E",
                colorLight : "#FFFFFF",
                correctLevel : QRCode.CorrectLevel.M
            });
        }
    });
</script>
</body>
</html>
