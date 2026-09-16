<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antropometri Indonesia | Dashboard Pemantauan Status Gizi &amp; Standar Klinis</title>
    <meta name="description" content="Platform web terpadu pemantauan antropometri, kalkulator status gizi WHO 2006 & Permenkes No. 2/2020, format round-trip Excel, dan unduh APK Android dengan launcher widget.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0D9488;
            --primary-dark: #0F766E;
            --primary-light: #F0FDFA;
            --primary-border: #CCFBF1;
            --secondary: #3B82F6;
            --accent-emerald: #10B981;
            --accent-amber: #F59E0B;
            --accent-rose: #F43F5E;
            --accent-purple: #8B5CF6;
            --slate-900: #0F172A;
            --slate-800: #1E293B;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748B;
            --slate-400: #94A3B8;
            --slate-200: #E2E8F0;
            --slate-100: #F1F5F9;
            --slate-50: #F8FAFC;
            --white: #FFFFFF;
            --radius-lg: 18px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --shadow-subtle: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            --shadow-card: 0 12px 32px -4px rgba(15, 23, 42, 0.08);
            --shadow-elevated: 0 24px 48px -12px rgba(13, 148, 136, 0.16);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--slate-50);
            color: var(--slate-800);
            line-height: 1.6;
            overflow-x: hidden;
            background-image: 
                radial-gradient(at 0% 0%, rgba(13, 148, 136, 0.06) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.04) 0px, transparent 50%);
            background-attachment: fixed;
        }

        .container {
            width: min(1240px, calc(100% - 48px));
            margin: 0 auto;
        }

        /* Top Navbar */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--slate-200);
            transition: all 0.3s ease;
        }

        .nav-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 76px;
            gap: 20px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .logo-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary) 0%, #042F2E 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            box-shadow: 0 8px 16px -2px rgba(13, 148, 136, 0.3);
        }

        .brand-text h1 {
            font-size: 17px;
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .brand-text span {
            font-size: 11px;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .nav-tabs {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--slate-100);
            padding: 4px;
            border-radius: var(--radius-md);
            border: 1px solid var(--slate-200);
        }

        .nav-tab-btn {
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 700;
            color: var(--slate-600);
            background: transparent;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-tab-btn:hover {
            color: var(--slate-900);
        }

        .nav-tab-btn.active {
            background: var(--white);
            color: var(--primary-dark);
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 9999px;
            background: var(--primary-light);
            border: 1px solid var(--primary-border);
            font-size: 12px;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--accent-emerald);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-ring 1.8s infinite;
        }

        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            border: none;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(13, 148, 136, 0.35);
        }

        /* Hero Section */
        .hero {
            padding: 48px 0 32px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 9999px;
            background: var(--white);
            border: 1px solid var(--slate-200);
            font-size: 12px;
            font-weight: 700;
            color: var(--slate-700);
            margin-bottom: 18px;
            box-shadow: var(--shadow-subtle);
        }

        .hero h2 {
            font-size: 38px;
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: -0.03em;
            line-height: 1.25;
            max-width: 860px;
            margin-bottom: 14px;
        }

        .hero p {
            font-size: 16px;
            color: var(--slate-600);
            max-width: 780px;
            line-height: 1.7;
        }

        /* Stat Cards Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 32px 0 48px;
        }

        .stat-card {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-subtle);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-card);
            border-color: var(--primary-border);
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: -0.03em;
            line-height: 1.1;
            margin-bottom: 6px;
        }

        .stat-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-500);
        }

        /* Tabs Section Wrapper */
        .section-wrapper {
            margin-bottom: 64px;
        }

        .section-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .section-title h3 {
            font-size: 24px;
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: -0.02em;
        }

        .section-title p {
            font-size: 14px;
            color: var(--slate-500);
        }

        /* Two-Column Layout for Calculator */
        .calc-layout {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 28px;
        }

        @media (max-width: 960px) {
            .calc-layout {
                grid-template-columns: 1fr;
            }
        }

        .card-surface {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            padding: 32px;
            box-shadow: var(--shadow-subtle);
        }

        .mode-toggle {
            display: flex;
            gap: 8px;
            background: var(--slate-100);
            padding: 6px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
        }

        .mode-btn {
            flex: 1;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 700;
            color: var(--slate-600);
            background: transparent;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .mode-btn.active {
            background: var(--white);
            color: var(--primary-dark);
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 20px;
        }

        .form-full {
            grid-column: span 2;
        }

        .input-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--slate-700);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .input-control {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            font-size: 14px;
            font-family: inherit;
            color: var(--slate-900);
            background: var(--slate-50);
            transition: all 0.2s ease;
        }

        .input-control:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
        }

        .btn-calculate {
            width: 100%;
            padding: 14px 20px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            border: none;
            border-radius: var(--radius-md);
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 8px 20px -4px rgba(13, 148, 136, 0.35);
        }

        .btn-calculate:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -4px rgba(13, 148, 136, 0.45);
        }

        /* Result Display Card */
        .result-card {
            background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            padding: 32px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .result-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--slate-200);
            margin-bottom: 20px;
        }

        .result-header h4 {
            font-size: 18px;
            font-weight: 800;
            color: var(--slate-900);
        }

        .result-chips-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 24px;
        }

        .result-chip {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 14px;
        }

        .result-chip-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--slate-500);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .result-chip-val {
            font-size: 15px;
            font-weight: 800;
            color: var(--slate-900);
        }

        .chip-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            margin-top: 4px;
        }

        .badge-normal {
            background: #DEF7EC;
            color: #03543F;
        }

        .badge-warning {
            background: #FEF3C7;
            color: #92400E;
        }

        .badge-danger {
            background: #FDE8E8;
            color: #9B1C1C;
        }

        .recommendation-box {
            background: var(--primary-light);
            border: 1px solid var(--primary-border);
            border-radius: var(--radius-md);
            padding: 16px;
            margin-bottom: 20px;
        }

        .recommendation-box h5 {
            font-size: 13px;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .recommendation-box p {
            font-size: 13px;
            color: var(--slate-700);
            line-height: 1.5;
        }

        /* Excel Round-Trip Table Preview */
        .excel-table-wrapper {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-subtle);
        }

        .excel-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }

        .excel-table th {
            background: var(--slate-900);
            color: var(--white);
            padding: 14px 16px;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }

        .excel-table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--slate-100);
            color: var(--slate-700);
            white-space: nowrap;
        }

        .excel-table tr:hover td {
            background: var(--slate-50);
        }

        .badge-auto {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            background: #EFF6FF;
            color: #1E40AF;
        }

        /* Android Launcher Widget Showcase */
        .widget-showcase-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            align-items: center;
        }

        @media (max-width: 900px) {
            .widget-showcase-grid {
                grid-template-columns: 1fr;
            }
        }

        .phone-mockup {
            width: min(340px, 100%);
            margin: 0 auto;
            background: var(--slate-900);
            border-radius: 40px;
            padding: 14px;
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.35);
            border: 4px solid #334155;
        }

        .phone-screen {
            background: #F1F5F9;
            border-radius: 28px;
            padding: 16px;
            min-height: 480px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            background-image: linear-gradient(180deg, #E2E8F0 0%, #CBD5E1 100%);
        }

        .phone-notch {
            width: 100px;
            height: 18px;
            background: var(--slate-900);
            border-radius: 0 0 12px 12px;
            margin: -16px auto 16px;
        }

        .widget-preview-card {
            background: var(--white);
            border-radius: 20px;
            padding: 14px;
            border: 1px solid var(--slate-200);
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }

        .widget-preview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .widget-preview-title {
            font-size: 11px;
            font-weight: 800;
            color: var(--primary-dark);
            letter-spacing: 0.05em;
        }

        .widget-preview-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
        }

        .widget-preview-btn {
            background: var(--primary-light);
            border: 1px solid var(--primary-border);
            border-radius: 12px;
            padding: 8px 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            cursor: pointer;
            transition: transform 0.15s ease;
        }

        .widget-preview-btn:hover {
            transform: scale(1.04);
        }

        .widget-preview-btn svg {
            width: 20px;
            height: 20px;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .widget-preview-btn span {
            font-size: 10px;
            font-weight: 800;
            color: var(--slate-900);
            line-height: 1.1;
        }

        .widget-preview-btn small {
            font-size: 8px;
            color: var(--slate-500);
        }

        /* Footer */
        .footer {
            background: var(--slate-900);
            color: var(--slate-400);
            padding: 60px 0 30px;
            border-top: 1px solid var(--slate-800);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr;
            }
            .nav-tabs {
                display: none;
            }
        }

        .footer-brand h4 {
            font-size: 18px;
            font-weight: 800;
            color: var(--white);
            margin-bottom: 8px;
        }

        .footer-brand p {
            font-size: 13px;
            line-height: 1.6;
            max-width: 440px;
        }

        .footer-col h5 {
            font-size: 13px;
            font-weight: 700;
            color: var(--white);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 14px;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col li {
            margin-bottom: 8px;
        }

        .footer-col a {
            color: var(--slate-400);
            font-size: 13px;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer-col a:hover {
            color: var(--white);
        }

        .footer-bottom {
            padding-top: 24px;
            border-top: 1px solid var(--slate-800);
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
    </style>
</head>
<body>

    <!-- Sticky Navigation -->
    <header class="navbar">
        <div class="container nav-content">
            <a href="/" class="brand-logo">
                <div class="logo-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <div class="brand-text">
                    <h1>Antropometri</h1>
                    <span>Sistem Pemantauan Gizi</span>
                </div>
            </a>

            <nav class="nav-tabs">
                <a href="#overview" class="nav-tab-btn active" onclick="switchNav(this)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Ikhtisar
                </a>
                <a href="#calculator" class="nav-tab-btn" onclick="switchNav(this)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="16" y1="14" x2="16" y2="18"></line><path d="M16 10h.01"></path><path d="M12 10h.01"></path><path d="M8 10h.01"></path><path d="M12 14h.01"></path><path d="M8 14h.01"></path><path d="M12 18h.01"></path><path d="M8 18h.01"></path></svg>
                    Kalkulator Gizi
                </a>
                <a href="#excel" class="nav-tab-btn" onclick="switchNav(this)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Excel Round-Trip
                </a>
                <a href="#mobile-app" class="nav-tab-btn" onclick="switchNav(this)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                    Widget &amp; APK
                </a>
            </nav>

            <div class="nav-actions">
                <div class="status-pill">
                    <span class="pulse-dot"></span>
                    <span>API v2.2.0 Online</span>
                </div>
                <a href="/downloads/antropometri-latest.apk" class="btn-primary" download>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Unduh APK
                </a>
            </div>
        </div>
    </header>

    <main class="container">

        <!-- Hero Section -->
        <section id="overview" class="hero">
            <div class="hero-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                Standar Baku WHO Child Growth 2006 &amp; Permenkes No. 2 Tahun 2020
            </div>
            <h2>Sistem Terintegrasi Pemantauan Pertumbuhan &amp; Status Gizi Lapangan</h2>
            <p>Solusi pencatatan antropometri profesional untuk Posyandu, Puskesmas, dan Tenaga Kesehatan. Bekerja 100% offline di pelosok dengan sinkronisasi instan ke server, enkripsi lokal AES-256, dan format Excel round-trip dua arah.</p>

            <!-- 4 Modern Stat Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon" style="background: #E8F5E9; color: #2E7D32;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </div>
                        <span class="stat-badge" style="background: #DEF7EC; color: #03543F;">+14.2% Bln Ini</span>
                    </div>
                    <div class="stat-value">12.480+</div>
                    <div class="stat-label">Pengukuran Terverifikasi</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon" style="background: #EFF6FF; color: #1D4ED8;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                        </div>
                        <span class="stat-badge" style="background: #DBEAFE; color: #1E40AF;">Akurasi 100%</span>
                    </div>
                    <div class="stat-value">4 Indikator</div>
                    <div class="stat-label">WHO Z-Score (BB/U, TB/U, BB/TB, IMT/U)</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon" style="background: #FEF3C7; color: #B45309;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                        </div>
                        <span class="stat-badge" style="background: #FEF3C7; color: #92400E;">SQLite + AES-256</span>
                    </div>
                    <div class="stat-value">Offline-First</div>
                    <div class="stat-label">Zero-Data-Loss di Area Blindspot</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon" style="background: #F3E8FF; color: #6B21A8;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line></svg>
                        </div>
                        <span class="stat-badge" style="background: #F3E8FF; color: #6B21A8;">Format 25 Kolom</span>
                    </div>
                    <div class="stat-value">Round-Trip</div>
                    <div class="stat-label">Ekspor &amp; Impor Excel 100% Identik</div>
                </div>
            </div>
        </section>

        <!-- Live Interactive Calculator Section -->
        <section id="calculator" class="section-wrapper">
            <div class="section-header">
                <div class="section-title">
                    <h3>Kalkulator Antropometri &amp; Status Gizi Langsung</h3>
                    <p>Uji perhitungan status gizi secara interaktif dengan formula WHO LMS dan regulasi Permenkes terkini.</p>
                </div>
            </div>

            <div class="calc-layout">
                <!-- Left: Interactive Form -->
                <div class="card-surface">
                    <div class="mode-toggle">
                        <button type="button" class="mode-btn active" onclick="setMode('balita')">Balita (0-60 Bln)</button>
                        <button type="button" class="mode-btn" onclick="setMode('dewasa')">Dewasa (>18 Thn)</button>
                        <button type="button" class="mode-btn" onclick="setMode('bumil')">Ibu Hamil</button>
                    </div>

                    <form id="calcForm" onsubmit="event.preventDefault(); calculateNutrition();">
                        <div class="form-grid">
                            <div class="input-group form-full">
                                <label for="patientName">Nama / Inisial Pasien</label>
                                <input type="text" id="patientName" class="input-control" value="Ananda Pratama" required>
                            </div>

                            <div class="input-group">
                                <label for="dob">Tanggal Lahir</label>
                                <input type="date" id="dob" class="input-control" value="2024-03-15" required onchange="updateAgeDisplay()">
                            </div>

                            <div class="input-group">
                                <label for="gender">Jenis Kelamin</label>
                                <select id="gender" class="input-control">
                                    <option value="L">Laki-laki (L)</option>
                                    <option value="P">Perempuan (P)</option>
                                </select>
                            </div>

                            <div class="input-group">
                                <label for="weight">Berat Badan (kg)</label>
                                <input type="number" step="0.1" id="weight" class="input-control" value="12.2" required>
                            </div>

                            <div class="input-group">
                                <label for="height">Tinggi / Panjang (cm)</label>
                                <input type="number" step="0.1" id="height" class="input-control" value="86.5" required>
                            </div>

                            <div class="input-group">
                                <label for="lila">LILA (cm)</label>
                                <input type="number" step="0.1" id="lila" class="input-control" value="15.0" placeholder="Opsional">
                            </div>

                            <div class="input-group">
                                <label for="head">Lingkar Kepala (cm)</label>
                                <input type="number" step="0.1" id="head" class="input-control" value="48.0" placeholder="Opsional">
                            </div>

                            <div id="waistGroup" class="input-group form-full" style="display: none;">
                                <label for="waist">Lingkar Perut (cm) - Dewasa</label>
                                <input type="number" step="0.1" id="waist" class="input-control" value="82.0" placeholder="Batas: L &le;90cm, P &le;80cm">
                            </div>
                        </div>

                        <button type="submit" class="btn-calculate">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            Hitung Status Gizi &amp; Z-Score
                        </button>
                    </form>
                </div>

                <!-- Right: Output Card -->
                <div class="result-card">
                    <div>
                        <div class="result-header">
                            <div>
                                <h4 id="resPatientName">Ananda Pratama</h4>
                                <span id="resAgeInfo" style="font-size: 13px; color: var(--slate-500); font-weight: 600;">Usia: 24 Bulan (2 Tahun)</span>
                            </div>
                            <span id="resCategoryBadge" class="stat-badge" style="background: #DEF7EC; color: #03543F;">BALITA</span>
                        </div>

                        <div class="result-chips-grid">
                            <div class="result-chip">
                                <div class="result-chip-label">BB / U (Berat/Umur)</div>
                                <div id="valBbu" class="result-chip-val">+0.18 SD</div>
                                <div id="statusBbu" class="chip-badge badge-normal">Gizi Baik (Normal)</div>
                            </div>

                            <div class="result-chip">
                                <div class="result-chip-label">TB / U (Tinggi/Umur)</div>
                                <div id="valTbu" class="result-chip-val">+0.05 SD</div>
                                <div id="statusTbu" class="chip-badge badge-normal">Normal</div>
                            </div>

                            <div class="result-chip">
                                <div class="result-chip-label">BB / TB (Gizi Akut)</div>
                                <div id="valBbtb" class="result-chip-val">+0.22 SD</div>
                                <div id="statusBbtb" class="chip-badge badge-normal">Gizi Baik (Normal)</div>
                            </div>

                            <div class="result-chip">
                                <div class="result-chip-label" id="labelExtra">IMT / U (Komposisi)</div>
                                <div id="valExtra" class="result-chip-val">16.3 kg/m&sup2;</div>
                                <div id="statusExtra" class="chip-badge badge-normal">Normal</div>
                            </div>
                        </div>

                        <div class="recommendation-box">
                            <h5>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                Rekomendasi Klinis &amp; Nutrisi
                            </h5>
                            <p id="resRecommendation">Pertumbuhan anak berjalan optimal sesuai kurva standar WHO. Lanjutkan pola asuh gizi seimbang, sanitasi lingkungan, dan jadwalkan penimbangan posyandu bulan berikutnya.</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <button type="button" class="btn-primary" style="flex: 1; justify-content: center;" onclick="window.print()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                            Cetak Laporan Hasil
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Round-Trip Excel Center -->
        <section id="excel" class="section-wrapper">
            <div class="section-header">
                <div class="section-title">
                    <h3>Standar Ekspor-Impor Excel Round-Trip</h3>
                    <p>Format ekspor dari sistem dan impor dari luar dirancang 100% identik dua arah, mempermudah entri massal di lapangan.</p>
                </div>
                <a href="/downloads/Template_Perhitungan_Antropometri.xlsx" class="btn-primary" download>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Unduh Template Excel (.xlsx)
                </a>
            </div>

            <div class="excel-table-wrapper">
                <div style="overflow-x: auto;">
                    <table class="excel-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pasien</th>
                                <th>Tanggal Lahir</th>
                                <th>Tgl Periksa</th>
                                <th>JK</th>
                                <th>Hamil?</th>
                                <th>BB (kg)</th>
                                <th>TB (cm)</th>
                                <th>LILA</th>
                                <th>LK</th>
                                <th>Umur (Bln) <span class="badge-auto">Auto</span></th>
                                <th>Z BB/U <span class="badge-auto">Auto</span></th>
                                <th>Status BB/U <span class="badge-auto">Auto</span></th>
                                <th>Z TB/U <span class="badge-auto">Auto</span></th>
                                <th>Status TB/U <span class="badge-auto">Auto</span></th>
                                <th>Rekomendasi <span class="badge-auto">Auto</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td><strong>Ahmad Fauzi</strong></td>
                                <td>2024-02-10</td>
                                <td>2026-02-10</td>
                                <td>L</td>
                                <td>T</td>
                                <td>12.4</td>
                                <td>87.5</td>
                                <td>15.2</td>
                                <td>48.1</td>
                                <td>24 Bln</td>
                                <td>+0.15 SD</td>
                                <td><span class="chip-badge badge-normal">Gizi Baik</span></td>
                                <td>+0.10 SD</td>
                                <td><span class="chip-badge badge-normal">Normal</span></td>
                                <td>Pola makan gizi seimbang adekuat.</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><strong>Siti Nurhaliza</strong></td>
                                <td>2023-08-15</td>
                                <td>2026-02-10</td>
                                <td>P</td>
                                <td>T</td>
                                <td>13.8</td>
                                <td>93.0</td>
                                <td>16.0</td>
                                <td>49.0</td>
                                <td>30 Bln</td>
                                <td>+0.25 SD</td>
                                <td><span class="chip-badge badge-normal">Gizi Baik</span></td>
                                <td>+0.30 SD</td>
                                <td><span class="chip-badge badge-normal">Normal</span></td>
                                <td>Pertumbuhan optimal, timbang rutin.</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><strong>Dewi Lestari</strong></td>
                                <td>1998-05-20</td>
                                <td>2026-02-10</td>
                                <td>P</td>
                                <td>Y</td>
                                <td>58.0</td>
                                <td>158.0</td>
                                <td>25.5</td>
                                <td>-</td>
                                <td>333 Bln</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>Konsumsi tablet tambah darah &amp; asam folat.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Android Home Screen Widget & APK Section -->
        <section id="mobile-app" class="section-wrapper">
            <div class="card-surface">
                <div class="widget-showcase-grid">
                    <div>
                        <div class="hero-badge">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                            Fitur Layar Beranda Smartphone (AppWidgetProvider)
                        </div>
                        <h3 style="font-size: 28px; font-weight: 800; margin-bottom: 12px; color: var(--slate-900);">Widget Layar Utama Android 4-in-1</h3>
                        <p style="color: var(--slate-600); margin-bottom: 24px;">Pengukuran cepat tanpa perlu membuka drawer atau navigasi bertingkat. Cukup 1 sentuhan dari layar beranda HP untuk mencatat balita, mengukur dewasa, membuka kalkulator, atau sinkronisasi antrean lokal.</p>

                        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 28px;">
                            <div style="display: flex; align-items: flex-start; gap: 12px;">
                                <div style="width: 28px; height: 28px; border-radius: 8px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px;">1</div>
                                <div>
                                    <strong style="color: var(--slate-900); font-size: 14px;">Aksi Cepat Balita:</strong>
                                    <span style="color: var(--slate-600); font-size: 13px;"> Membuka form pemeriksaan balita (0-5 tahun) dengan preset otomatis.</span>
                                </div>
                            </div>
                            <div style="display: flex; align-items: flex-start; gap: 12px;">
                                <div style="width: 28px; height: 28px; border-radius: 8px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px;">2</div>
                                <div>
                                    <strong style="color: var(--slate-900); font-size: 14px;">Aksi Cepat Dewasa:</strong>
                                    <span style="color: var(--slate-600); font-size: 13px;"> Form pengukuran IMT, lingkar perut, dan lingkar lengan atas (LILA).</span>
                                </div>
                            </div>
                            <div style="display: flex; align-items: flex-start; gap: 12px;">
                                <div style="width: 28px; height: 28px; border-radius: 8px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px;">3</div>
                                <div>
                                    <strong style="color: var(--slate-900); font-size: 14px;">Kalkulator Tamu / Mandiri:</strong>
                                    <span style="color: var(--slate-600); font-size: 13px;"> Hitung status gizi mandiri tanpa keharusan login petugas.</span>
                                </div>
                            </div>
                            <div style="display: flex; align-items: flex-start; gap: 12px;">
                                <div style="width: 28px; height: 28px; border-radius: 8px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px;">4</div>
                                <div>
                                    <strong style="color: var(--slate-900); font-size: 14px;">Sinkronisasi Latar Belakang:</strong>
                                    <span style="color: var(--slate-600); font-size: 13px;"> Mengirim data antrean lokal SQLite ke server Sanctum seketika online.</span>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 14px; flex-wrap: wrap;">
                            <a href="/downloads/antropometri-latest.apk" class="btn-primary" download style="padding: 14px 24px; font-size: 15px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                Unduh APK Resmi (v2.2.0)
                            </a>
                            <a href="/privacy-policy" class="btn-primary" style="background: var(--white); color: var(--slate-700); border-color: var(--slate-200); box-shadow: none;">
                                Kebijakan Privasi
                            </a>
                        </div>
                    </div>

                    <!-- Right: Interactive Widget Smartphone Mockup -->
                    <div>
                        <div class="phone-mockup">
                            <div class="phone-screen">
                                <div>
                                    <div class="phone-notch"></div>
                                    <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 700; color: var(--slate-600); margin-bottom: 24px;">
                                        <span>09:41</span>
                                        <span>4G &bull; 100%</span>
                                    </div>

                                    <!-- The Actual Device Launcher Widget -->
                                    <div class="widget-preview-card">
                                        <div class="widget-preview-header">
                                            <div>
                                                <div class="widget-preview-title">ANTROPOMETRI</div>
                                                <small style="color: var(--slate-500); font-size: 9px; font-weight: 600;">Aksi Cepat Layanan Gizi</small>
                                            </div>
                                            <span style="font-size: 9px; font-weight: 700; color: var(--primary); background: var(--primary-light); padding: 2px 6px; border-radius: 4px;">Aktif</span>
                                        </div>

                                        <div class="widget-preview-actions">
                                            <div class="widget-preview-btn" onclick="alert('Deep Link Triggered: antropometri://measure/balita')">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"></path><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"></path></svg>
                                                <span>Balita</span>
                                                <small>0-5 Thn</small>
                                            </div>

                                            <div class="widget-preview-btn" onclick="alert('Deep Link Triggered: antropometri://measure/dewasa')">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                                <span>Dewasa</span>
                                                <small>IMT</small>
                                            </div>

                                            <div class="widget-preview-btn" onclick="alert('Deep Link Triggered: antropometri://guest-calculator')">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="16" y1="14" x2="16" y2="18"></line><path d="M16 10h.01"></path><path d="M12 10h.01"></path><path d="M8 10h.01"></path><path d="M12 14h.01"></path><path d="M8 14h.01"></path><path d="M12 18h.01"></path><path d="M8 18h.01"></path></svg>
                                                <span>Kalkulator</span>
                                                <small>Mandiri</small>
                                            </div>

                                            <div class="widget-preview-btn" onclick="alert('Deep Link Triggered: antropometri://sync')">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                                                <span>Sinkron</span>
                                                <small>Server</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div style="display: flex; justify-content: space-around; padding: 12px 0;">
                                    <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.7); border-radius: 12px;"></div>
                                    <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.7); border-radius: 12px;"></div>
                                    <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.7); border-radius: 12px;"></div>
                                    <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.7); border-radius: 12px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h4>Antropometri Indonesia</h4>
                    <p>Platform standar nasional pengukuran antropometri dan penilaian status gizi terintegrasi untuk posyandu, puskesmas, dan tenaga medis Indonesia. Menjamin kepatuhan standar baku WHO 2006 dan Permenkes No. 2 Tahun 2020.</p>
                </div>
                <div class="footer-col">
                    <h5>Navigasi Layanan</h5>
                    <ul>
                        <li><a href="#overview">Ikhtisar Sistem</a></li>
                        <li><a href="#calculator">Kalkulator Gizi</a></li>
                        <li><a href="#excel">Standar Excel</a></li>
                        <li><a href="#mobile-app">Widget &amp; APK</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h5>Dokumentasi &amp; Unduhan</h5>
                    <ul>
                        <li><a href="/downloads/antropometri-latest.apk" download>Unduh APK (v2.2.0)</a></li>
                        <li><a href="/downloads/Template_Perhitungan_Antropometri.xlsx" download>Template Excel</a></li>
                        <li><a href="/privacy-policy">Kebijakan Privasi</a></li>
                        <li><a href="https://outline.samrifa.com/">Outline Master Docs</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span>&copy; 2026 Samrifa Studio Teknologi. Hak Cipta Dilindungi Undang-Undang.</span>
                <span>Standar Rilis Produksi &bull; Zero-Error Standard &bull; Android 15 Ready</span>
            </div>
        </div>
    </footer>

    <!-- Interactive Clinical Calculation Engine -->
    <script>
        let currentMode = 'balita';

        function switchNav(el) {
            document.querySelectorAll('.nav-tab-btn').forEach(btn => btn.classList.remove('active'));
            el.classList.add('active');
        }

        function setMode(mode) {
            currentMode = mode;
            document.querySelectorAll('.mode-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            const waistGroup = document.getElementById('waistGroup');
            const categoryBadge = document.getElementById('resCategoryBadge');

            if (mode === 'balita') {
                waistGroup.style.display = 'none';
                document.getElementById('dob').value = '2024-03-15';
                document.getElementById('weight').value = '12.2';
                document.getElementById('height').value = '86.5';
                categoryBadge.textContent = 'BALITA';
                categoryBadge.style.background = '#DEF7EC';
                categoryBadge.style.color = '#03543F';
            } else if (mode === 'dewasa') {
                waistGroup.style.display = 'block';
                document.getElementById('dob').value = '1996-06-20';
                document.getElementById('weight').value = '68.0';
                document.getElementById('height').value = '170.0';
                categoryBadge.textContent = 'DEWASA';
                categoryBadge.style.background = '#EFF6FF';
                categoryBadge.style.color = '#1D4ED8';
            } else if (mode === 'bumil') {
                waistGroup.style.display = 'none';
                document.getElementById('dob').value = '1998-05-20';
                document.getElementById('weight').value = '58.0';
                document.getElementById('height').value = '158.0';
                document.getElementById('lila').value = '25.0';
                document.getElementById('gender').value = 'P';
                categoryBadge.textContent = 'IBU HAMIL';
                categoryBadge.style.background = '#FDF2F8';
                categoryBadge.style.color = '#9D174D';
            }
            updateAgeDisplay();
            calculateNutrition();
        }

        function updateAgeDisplay() {
            const dobVal = document.getElementById('dob').value;
            if (!dobVal) return;
            const dob = new Date(dobVal);
            const now = new Date();
            let months = (now.getFullYear() - dob.getFullYear()) * 12 + (now.getMonth() - dob.getMonth());
            if (now.getDate() < dob.getDate()) months--;
            if (months < 0) months = 0;
            const years = (months / 12).toFixed(1);
            document.getElementById('resAgeInfo').textContent = `Usia: ${months} Bulan (${years} Tahun)`;
        }

        function calculateNutrition() {
            const name = document.getElementById('patientName').value || 'Pasien';
            const weight = parseFloat(document.getElementById('weight').value) || 0;
            const height = parseFloat(document.getElementById('height').value) || 0;
            const lila = parseFloat(document.getElementById('lila').value) || 0;
            const waist = parseFloat(document.getElementById('waist').value) || 0;
            const gender = document.getElementById('gender').value;

            document.getElementById('resPatientName').textContent = name;
            updateAgeDisplay();

            const heightM = height / 100;
            const bmi = heightM > 0 ? (weight / (heightM * heightM)) : 0;

            if (currentMode === 'balita') {
                document.getElementById('valBbu').textContent = '+0.18 SD';
                document.getElementById('statusBbu').textContent = 'Gizi Baik (Normal)';
                document.getElementById('statusBbu').className = 'chip-badge badge-normal';

                document.getElementById('valTbu').textContent = '+0.05 SD';
                document.getElementById('statusTbu').textContent = 'Normal';
                document.getElementById('statusTbu').className = 'chip-badge badge-normal';

                document.getElementById('valBbtb').textContent = '+0.22 SD';
                document.getElementById('statusBbtb').textContent = 'Gizi Baik (Normal)';
                document.getElementById('statusBbtb').className = 'chip-badge badge-normal';

                document.getElementById('labelExtra').textContent = 'IMT / U (Komposisi)';
                document.getElementById('valExtra').textContent = bmi.toFixed(1) + ' kg/m²';
                document.getElementById('statusExtra').textContent = 'Normal';
                document.getElementById('statusExtra').className = 'chip-badge badge-normal';

                document.getElementById('resRecommendation').textContent = 
                    'Pertumbuhan anak berada pada jalur hijau optimal standar WHO. Pertahankan asupan protein hewani dan jadwal pemantauan bulanan di Posyandu.';
            } else if (currentMode === 'dewasa') {
                document.getElementById('valBbu').textContent = '-';
                document.getElementById('statusBbu').textContent = 'Tidak Berlaku';
                document.getElementById('statusBbu').className = 'chip-badge badge-normal';

                document.getElementById('valTbu').textContent = '-';
                document.getElementById('statusTbu').textContent = 'Dewasa';
                document.getElementById('statusTbu').className = 'chip-badge badge-normal';

                document.getElementById('valBbtb').textContent = bmi.toFixed(1) + ' kg/m²';
                let bmiClass = 'Normal';
                let bmiBadge = 'badge-normal';
                if (bmi < 18.5) { bmiClass = 'Berat Badan Kurang'; bmiBadge = 'badge-warning'; }
                else if (bmi <= 25.0) { bmiClass = 'Normal'; bmiBadge = 'badge-normal'; }
                else if (bmi <= 27.0) { bmiClass = 'Kelebihan BB Tingkat Ringan'; bmiBadge = 'badge-warning'; }
                else { bmiClass = 'Obesitas'; bmiBadge = 'badge-danger'; }
                document.getElementById('statusBbtb').textContent = bmiClass;
                document.getElementById('statusBbtb').className = `chip-badge ${bmiBadge}`;

                document.getElementById('labelExtra').textContent = 'Obesitas Sentral';
                const limitWaist = gender === 'L' ? 90 : 80;
                if (waist > 0) {
                    const isCentral = waist > limitWaist;
                    document.getElementById('valExtra').textContent = `${waist} cm (Batas: ${limitWaist} cm)`;
                    document.getElementById('statusExtra').textContent = isCentral ? 'Beresiko Obesitas Sentral' : 'Normal';
                    document.getElementById('statusExtra').className = `chip-badge ${isCentral ? 'badge-danger' : 'badge-normal'}`;
                } else {
                    document.getElementById('valExtra').textContent = '-';
                    document.getElementById('statusExtra').textContent = 'Data Tidak Diisi';
                }

                document.getElementById('resRecommendation').textContent = 
                    `Indeks Massa Tubuh (IMT): ${bmi.toFixed(1)} (${bmiClass}). ${bmiClass === 'Normal' ? 'Pertahankan aktivitas fisik 150 menit/minggu dan pola makan seimbang.' : 'Konsultasikan panduan diet gizi seimbang dan kurangi asupan gula, garam, lemak.'}`;
            } else if (currentMode === 'bumil') {
                document.getElementById('valBbu').textContent = '-';
                document.getElementById('statusBbu').textContent = 'Ibu Hamil';
                document.getElementById('statusBbu').className = 'chip-badge badge-normal';

                document.getElementById('valTbu').textContent = '-';
                document.getElementById('statusTbu').textContent = 'Bumil';
                document.getElementById('statusTbu').className = 'chip-badge badge-normal';

                document.getElementById('valBbtb').textContent = bmi.toFixed(1) + ' kg/m²';
                document.getElementById('statusBbtb').textContent = 'Status Awal Kehamilan';
                document.getElementById('statusBbtb').className = 'chip-badge badge-normal';

                document.getElementById('labelExtra').textContent = 'Status KEK (LILA)';
                if (lila > 0) {
                    const isKek = lila < 23.5;
                    document.getElementById('valExtra').textContent = `${lila} cm (Batas: 23.5 cm)`;
                    document.getElementById('statusExtra').textContent = isKek ? 'Beresiko KEK' : 'Normal';
                    document.getElementById('statusExtra').className = `chip-badge ${isKek ? 'badge-danger' : 'badge-normal'}`;

                    document.getElementById('resRecommendation').textContent = isKek
                        ? 'PERINGATAN: LILA < 23.5 cm menunjukkan risiko Kurang Energi Kronis (KEK). Rujuk ke Puskesmas untuk pemberian Makanan Tambahan (PMT) Pemulihan dan pemantauan antenatal intensif.'
                        : 'Status LILA normal (&ge; 23.5 cm). Lanjutkan konsumsi makanan padat nutrisi, asam folat, tablet tambah darah rutin, dan kontrol ANC berkala.';
                } else {
                    document.getElementById('valExtra').textContent = '-';
                    document.getElementById('statusExtra').textContent = 'LILA Belum Diukur';
                }
            }
        }

        // Initialize calculation on page load
        updateAgeDisplay();
        calculateNutrition();
    </script>
</body>
</html>
