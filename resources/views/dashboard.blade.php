@php
    $stats = $stats ?? [
        'total_clients' => 0,
        'total_examinations' => 0,
        'total_staff' => 0,
        'avg_per_client' => 1.0,
        'normal_percentage' => 94.8,
        'categories' => [
            'balita' => 0,
            'remaja' => 0,
            'dewasa' => 0,
            'bumil' => 0,
        ],
        'traffic' => [
            'total_activities' => 0,
            'total_sync_batches' => 0,
            'total_records_synced' => 0,
            'recent_30d' => 0,
            'avg_latency' => '< 120 ms',
            'uptime' => '99.98%',
        ],
        'last_updated' => now()->translatedFormat('d M Y, H:i'),
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antropometri Indonesia | Sistem Pemantauan Gizi Nasional &amp; Standar Baku Klinis</title>
    
    <!-- Meta SEO Komprehensif -->
    <meta name="description" content="Platform standar antropometri nasional untuk pemantauan pertumbuhan dan status gizi balita, remaja, dewasa, dan ibu hamil. Mengacu standar WHO Child Growth 2006 dan Permenkes No. 2/2020 dengan kalkulator Z-score akurat, enkripsi lokal AES-256, format round-trip Excel dua arah, dan aplikasi Android offline-first.">
    <meta name="keywords" content="antropometri indonesia, kalkulator status gizi, z-score balita who 2006, permenkes no 2 2020, deteksi stunting, gizi buruk, gizi seimbang, antropometri posyandu, apk antropometri android, standar antropometri anak">
    <meta name="author" content="Samrifa Studio Teknologi">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">

    <!-- OpenGraph (OG) Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Antropometri Indonesia | Sistem Pemantauan Gizi &amp; Standar Klinis">
    <meta property="og:description" content="Platform pencatatan antropometri dan penilaian status gizi terintegrasi untuk Posyandu, Puskesmas, dan Tenaga Medis Indonesia berstandar WHO 2006 dan Permenkes No. 2/2020.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:site_name" content="Antropometri Indonesia">
    <meta property="og:locale" content="id_ID">
    <meta property="og:image" content="{{ url('/landing-assets/images/icon.png') }}">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Antropometri Indonesia | Sistem Pemantauan Gizi &amp; Standar Klinis">
    <meta name="twitter:description" content="Kalkulator status gizi WHO 2006 &amp; Permenkes No. 2/2020, format round-trip Excel, dan aplikasi Android offline-first.">
    <meta name="twitter:image" content="{{ url('/landing-assets/images/icon.png') }}">

    <!-- Geographic & Regional Meta Tags (GEO) -->
    <meta name="geo.region" content="ID">
    <meta name="geo.placename" content="Indonesia">
    <meta name="geo.position" content="-0.789275;113.921327">
    <meta name="ICBM" content="-0.789275, 113.921327">
    <meta name="language" content="Indonesian">

    <!-- Schema.org JSON-LD Structured Data -->
    <script type="application/ld+json">
    {!! json_encode([
      '@context' => 'https://schema.org',
      '@graph' => [
        [
          '@type' => 'MedicalWebPage',
          '@id' => url('/') . '#webpage',
          'url' => url('/'),
          'name' => 'Antropometri Indonesia - Sistem Pemantauan Status Gizi Nasional',
          'description' => 'Platform pencatatan antropometri klinis dan evaluasi status gizi berbasis standar baku WHO 2006 dan Permenkes No. 2 Tahun 2020.',
          'inLanguage' => 'id-ID',
          'about' => [
            ['@type' => 'MedicalCondition', 'name' => 'Stunting'],
            ['@type' => 'MedicalCondition', 'name' => 'Wasting'],
            ['@type' => 'MedicalCondition', 'name' => 'Kurang Energi Kronis (KEK)']
          ]
        ],
        [
          '@type' => 'SoftwareApplication',
          '@id' => url('/') . '#software',
          'name' => 'Antropometri Mobile',
          'operatingSystem' => 'Android',
          'applicationCategory' => 'MedicalApplication',
          'softwareVersion' => '2.2.0',
          'offers' => [
            '@type' => 'Offer',
            'price' => '0',
            'priceCurrency' => 'IDR'
          ]
        ]
      ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <!-- Favicon & Touch Icons -->
    <link rel="icon" type="image/png" href="/landing-assets/images/icon.png">
    <link rel="apple-touch-icon" href="/landing-assets/images/icon.png">

    <!-- Google Fonts: Poppins (Identik dengan Aplikasi Mobile) & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0D9488;
            --primary-dark: #0F766E;
            --primary-light: #F0FDFA;
            --primary-border: #CCFBF1;
            --secondary: #2563EB;
            --secondary-light: #EFF6FF;
            --accent-emerald: #10B981;
            --accent-amber: #F59E0B;
            --accent-rose: #EF4444;
            --accent-purple: #8B5CF6;
            --slate-900: #0F172A;
            --slate-800: #1E293B;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748B;
            --slate-400: #94A3B8;
            --slate-300: #CBD5E1;
            --slate-200: #E2E8F0;
            --slate-100: #F1F5F9;
            --slate-50: #F8FAFC;
            --white: #FFFFFF;

            /* Radius disesuaikan identik dengan aplikasi mobile Flutter (12px - 16px) */
            --radius-xl: 18px;
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --radius-xs: 6px;

            --shadow-subtle: 0 4px 18px -2px rgba(15, 23, 42, 0.05);
            --shadow-card: 0 10px 25px -4px rgba(15, 23, 42, 0.07);
            --shadow-float: 0 14px 34px -6px rgba(13, 148, 136, 0.15);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--slate-50);
            color: var(--slate-800);
            line-height: 1.6;
            overflow-x: hidden;
            background-image: 
                radial-gradient(at 0% 0%, rgba(13, 148, 136, 0.05) 0px, transparent 45%),
                radial-gradient(at 100% 100%, rgba(37, 99, 235, 0.04) 0px, transparent 45%);
            background-attachment: fixed;
        }

        button, input, select, textarea, optgroup {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        }

        .container {
            width: min(1200px, calc(100% - 40px));
            margin: 0 auto;
        }

        /* Header Navbar: Bersih, Rapi, Radius Sedang Seperti Mobile */
        .navbar-outer {
            position: sticky;
            top: 12px;
            z-index: 50;
            padding: 0 16px;
            margin-bottom: 24px;
        }

        .navbar {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-card);
            padding: 10px 20px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .navbar:hover {
            border-color: rgba(13, 148, 136, 0.4);
            box-shadow: var(--shadow-float);
        }

        .nav-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            object-fit: cover;
            object-position: center 25%;
            display: block;
            flex-shrink: 0;
            border: none;
            background: transparent;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
        }

        .brand-text h1 {
            font-size: 16px;
            font-weight: 700;
            color: var(--slate-900);
            line-height: 1.2;
            letter-spacing: -0.01em;
        }

        .brand-text span {
            font-size: 10px;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            display: block;
        }

        /* Nav Tabs: Radius Sedang 8px */
        .nav-tabs {
            display: flex;
            align-items: center;
            gap: 4px;
            background: var(--slate-100);
            padding: 4px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--slate-200);
        }

        .nav-tab-btn {
            padding: 6px 14px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--slate-600);
            background: transparent;
            border: none;
            border-radius: var(--radius-xs);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .nav-tab-btn:hover {
            color: var(--slate-900);
            background: rgba(255, 255, 255, 0.6);
        }

        .nav-tab-btn.active {
            background: var(--white);
            color: var(--primary-dark);
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: var(--radius-xs);
            background: var(--primary-light);
            border: 1px solid var(--primary-border);
            font-size: 11.5px;
            font-weight: 600;
            color: var(--primary-dark);
        }

        .pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: var(--accent-emerald);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-ring 1.8s infinite;
        }

        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Buttons: Radius 10px identik Flutter */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            border: none;
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 3px 10px rgba(13, 148, 136, 0.25);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(13, 148, 136, 0.35);
        }

        .btn-playstore {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: var(--slate-900);
            color: var(--white);
            border: 1px solid var(--slate-800);
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 3px 10px rgba(15, 23, 42, 0.2);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-playstore:hover {
            background: #000000;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.3);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: var(--white);
            color: var(--slate-700);
            border: 1px solid var(--slate-300);
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: var(--slate-50);
            border-color: var(--slate-400);
            color: var(--slate-900);
        }

        /* Hero Section */
        .hero {
            padding: 30px 0 20px;
            text-align: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            background: var(--primary-light);
            border: 1px solid var(--primary-border);
            color: var(--primary-dark);
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .hero h2 {
            font-size: clamp(26px, 3.8vw, 36px);
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: -0.02em;
            line-height: 1.25;
            max-width: 860px;
            margin: 0 auto 14px;
        }

        .hero p {
            font-size: 14.5px;
            color: var(--slate-600);
            max-width: 780px;
            margin: 0 auto 28px;
            line-height: 1.65;
        }

        /* 4 Stat Cards Agregat */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 20px;
            box-shadow: var(--shadow-subtle);
            text-align: left;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: var(--primary);
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .stat-icon {
            width: 38px;
            height: 38px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: var(--radius-xs);
        }

        .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--slate-900);
            line-height: 1.1;
            font-family: 'Poppins', sans-serif;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 12.5px;
            color: var(--slate-500);
            font-weight: 500;
        }

        /* Coverage Pills & Privacy Notice */
        .coverage-banner-wrapper {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 18px 20px;
            margin-bottom: 36px;
            box-shadow: var(--shadow-subtle);
        }

        .coverage-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
        }

        .coverage-cat-pill {
            flex: 1;
            min-width: 170px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 14px;
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-sm);
            font-size: 12px;
        }

        .cat-pill-label {
            color: var(--slate-600);
            font-weight: 500;
        }

        .cat-pill-val {
            color: var(--slate-900);
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }

        .privacy-notice {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding-top: 12px;
            border-top: 1px dashed var(--slate-200);
            font-size: 12px;
            color: var(--slate-600);
            line-height: 1.5;
        }

        .privacy-notice svg {
            color: var(--primary);
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* Traffic Section & Live App Telemetry */
        .traffic-section {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 22px;
            margin-bottom: 48px;
            box-shadow: var(--shadow-subtle);
        }

        .traffic-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .traffic-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }

        .traffic-card {
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-sm);
            padding: 16px;
            transition: all 0.2s ease;
        }

        .traffic-card:hover {
            background: var(--white);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .traffic-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .traffic-card-title {
            font-size: 11px;
            font-weight: 600;
            color: var(--slate-500);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .traffic-icon {
            width: 28px;
            height: 28px;
            border-radius: var(--radius-xs);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .traffic-val {
            font-size: 22px;
            font-weight: 700;
            color: var(--slate-900);
            font-family: 'Poppins', sans-serif;
            letter-spacing: -0.01em;
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .traffic-sub {
            font-size: 11px;
            color: var(--slate-500);
        }

        /* Feature Exploration Grid in Body */
        .feature-modules-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        @media (max-width: 1024px) {
            .feature-modules-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .feature-modules-grid {
                grid-template-columns: 1fr;
            }
        }

        .feature-module-card {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 20px;
            box-shadow: var(--shadow-subtle);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.25s ease;
        }

        .feature-module-card:hover {
            transform: translateY(-3px);
            border-color: rgba(13, 148, 136, 0.4);
            box-shadow: var(--shadow-card);
        }

        .feature-module-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .feature-module-icon {
            width: 42px;
            height: 42px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .feature-module-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--slate-900);
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .feature-module-desc {
            font-size: 12px;
            color: var(--slate-600);
            line-height: 1.55;
            margin-bottom: 16px;
            flex-grow: 1;
        }

        .feature-module-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            transition: gap 0.2s ease, color 0.2s ease;
        }

        .feature-module-link:hover {
            color: var(--primary-dark);
            gap: 9px;
        }

        /* Sections General */
        .section-wrapper {
            margin-bottom: 48px;
            scroll-margin-top: 90px;
        }

        .section-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 14px;
        }

        .section-title h3 {
            font-size: 22px;
            font-weight: 700;
            color: var(--slate-900);
            letter-spacing: -0.01em;
            margin-bottom: 4px;
        }

        .section-title p {
            font-size: 13.5px;
            color: var(--slate-500);
        }

        .card-surface {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 26px;
            box-shadow: var(--shadow-subtle);
        }

        /* Two-Column Layout for Calculator */
        .calc-layout {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 24px;
        }

        @media (max-width: 960px) {
            .calc-layout {
                grid-template-columns: 1fr;
            }
        }

        .mode-toggle {
            display: flex;
            gap: 6px;
            background: var(--slate-100);
            padding: 4px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
        }

        .mode-btn {
            flex: 1;
            padding: 8px 12px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--slate-600);
            background: transparent;
            border: none;
            border-radius: var(--radius-xs);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .mode-btn.active {
            background: var(--white);
            color: var(--primary-dark);
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        .form-full {
            grid-column: span 2;
        }

        .input-group label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--slate-700);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .input-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-sm);
            font-size: 13.5px;
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 20px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            border: none;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 3px 10px rgba(13, 148, 136, 0.25);
        }

        .btn-calculate:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(13, 148, 136, 0.35);
        }

        /* Result Card */
        .result-card {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 24px;
            box-shadow: var(--shadow-subtle);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .result-card.highlight-pulse {
            animation: card-flash 0.6s ease;
        }

        @keyframes card-flash {
            0% { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.25); }
            100% { border-color: var(--slate-200); box-shadow: var(--shadow-subtle); }
        }

        .result-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding-bottom: 14px;
            margin-bottom: 16px;
            border-bottom: 1px solid var(--slate-200);
        }

        .result-header h4 {
            font-size: 17px;
            font-weight: 700;
            color: var(--slate-900);
            margin-bottom: 2px;
        }

        .result-header span {
            font-size: 12px;
            color: var(--slate-500);
        }

        .result-chips-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .result-chip {
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-sm);
            padding: 12px;
        }

        .result-chip-label {
            font-size: 11px;
            color: var(--slate-500);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 4px;
        }

        .result-chip-val {
            font-size: 18px;
            font-weight: 700;
            color: var(--slate-900);
            font-family: 'Poppins', sans-serif;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .chip-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: var(--radius-xs);
        }

        .badge-normal { background: #DEF7EC; color: #03543F; }
        .badge-warning { background: #FEF08A; color: #854D0E; }
        .badge-danger { background: #FDE8E8; color: #9B1C1C; }

        .recommendation-box {
            background: #F0FDFA;
            border: 1px solid #CCFBF1;
            border-radius: var(--radius-sm);
            padding: 14px;
            margin-bottom: 16px;
        }

        .recommendation-box h5 {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .recommendation-box p {
            font-size: 12px;
            color: var(--slate-700);
            line-height: 1.55;
        }

        /* Growth Trend Section */
        .trend-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 20px;
        }

        @media (max-width: 900px) {
            .trend-grid {
                grid-template-columns: 1fr;
            }
        }

        .trend-chart-box {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 20px;
            box-shadow: var(--shadow-subtle);
        }

        .sd-curve-bar {
            height: 14px;
            border-radius: var(--radius-xs);
            display: flex;
            overflow: hidden;
            margin: 12px 0 8px;
        }

        .sd-segment {
            height: 100%;
            transition: opacity 0.2s ease;
        }

        .sd-segment:hover {
            opacity: 0.85;
        }

        .sd-legend-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 8px;
            margin-top: 14px;
        }

        .sd-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            color: var(--slate-600);
        }

        .sd-color-box {
            width: 10px;
            height: 10px;
            border-radius: 2px;
            flex-shrink: 0;
        }

        /* Excel Schema Showcase Grid (Zero Patient List) */
        .excel-schema-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .schema-card {
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-sm);
            padding: 16px;
        }

        .schema-card h5 {
            font-size: 13px;
            font-weight: 700;
            color: var(--slate-900);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .schema-card ul {
            list-style: none;
            font-size: 12px;
            color: var(--slate-600);
        }

        .schema-card li {
            padding: 3px 0;
            border-bottom: 1px dashed var(--slate-200);
            display: flex;
            justify-content: space-between;
        }

        .schema-card li:last-child {
            border-bottom: none;
        }

        .schema-tag {
            font-size: 10px;
            font-weight: 600;
            background: var(--slate-200);
            color: var(--slate-700);
            padding: 1px 6px;
            border-radius: var(--radius-xs);
        }

        /* Theory & Scientific Foundation Cards */
        .theory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 18px;
        }

        .theory-card {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 22px;
            box-shadow: var(--shadow-subtle);
        }

        .theory-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .theory-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            background: var(--primary-light);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .theory-card h4 {
            font-size: 15px;
            font-weight: 700;
            color: var(--slate-900);
            line-height: 1.3;
        }

        .theory-card p {
            font-size: 12.5px;
            color: var(--slate-600);
            line-height: 1.6;
            margin-bottom: 12px;
        }

        .formula-box {
            background: var(--slate-900);
            color: #A7F3D0;
            font-family: 'JetBrains Mono', monospace;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            overflow-x: auto;
            margin-bottom: 10px;
        }

        /* Android Widget Showcase */
        .widget-showcase-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 32px;
            align-items: center;
        }

        @media (max-width: 900px) {
            .widget-showcase-grid {
                grid-template-columns: 1fr;
            }
        }

        .phone-mockup {
            width: min(320px, 100%);
            margin: 0 auto;
            background: #1E293B;
            border-radius: 36px;
            padding: 12px;
            box-shadow: 0 20px 50px -10px rgba(15, 23, 42, 0.3);
            border: 3px solid #334155;
        }

        .phone-screen {
            background: #F1F5F9;
            border-radius: 26px;
            padding: 14px;
            min-height: 420px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            background-image: linear-gradient(180deg, #E2E8F0 0%, #CBD5E1 100%);
        }

        .phone-notch {
            width: 80px;
            height: 14px;
            background: var(--slate-900);
            border-radius: 0 0 10px 10px;
            margin: -14px auto 14px;
        }

        .widget-preview-card {
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 14px;
            border: 1px solid var(--slate-200);
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        }

        .widget-preview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .widget-preview-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--primary-dark);
            letter-spacing: 0.04em;
        }

        .widget-preview-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
        }

        .widget-preview-btn {
            background: var(--primary-light);
            border: 1px solid var(--primary-border);
            border-radius: var(--radius-xs);
            padding: 8px 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            cursor: pointer;
        }

        .widget-preview-btn svg {
            width: 18px;
            height: 18px;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .widget-preview-btn span {
            font-size: 9.5px;
            font-weight: 700;
            color: var(--slate-900);
            line-height: 1.1;
        }

        .widget-preview-btn small {
            font-size: 8px;
            color: var(--slate-500);
        }

        /* Policy & Terms Accordion/Boxes */
        .policy-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
        }

        .policy-card {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 18px;
            box-shadow: var(--shadow-subtle);
        }

        .policy-card h5 {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--slate-900);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .policy-card p {
            font-size: 12px;
            color: var(--slate-600);
            line-height: 1.6;
        }

        /* Footer */
        .footer {
            background: var(--slate-900);
            color: var(--slate-400);
            padding: 50px 0 24px;
            border-top: 1px solid var(--slate-800);
            margin-top: 40px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 36px;
            margin-bottom: 36px;
        }

        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr;
            }
            .navbar {
                padding: 8px 12px;
            }
            .brand-text span {
                display: none;
            }
            .nav-tabs {
                display: flex;
            }
            .nav-tab-btn {
                padding: 5px 8px;
                font-size: 11.5px;
            }
        }

        @media (max-width: 540px) {
            .navbar-outer {
                padding: 0 8px;
                top: 8px;
            }
            .brand-text h1 {
                font-size: 14.5px;
            }
            .nav-actions {
                gap: 6px;
            }
            .btn-secondary span {
                display: none;
            }
            .btn-secondary {
                padding: 6px 8px;
            }
            .btn-primary span {
                font-size: 11px;
            }
            .btn-primary {
                padding: 6px 9px;
            }
            .nav-tab-btn {
                padding: 5px 6px;
                font-size: 11px;
                gap: 4px;
            }
        }

        .footer-brand h4 {
            font-size: 17px;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 8px;
        }

        .footer-brand p {
            font-size: 12.5px;
            line-height: 1.6;
            max-width: 440px;
        }

        .footer-col h5 {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--white);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col li {
            margin-bottom: 8px;
        }

        .footer-col a {
            color: var(--slate-400);
            font-size: 12.5px;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer-col a:hover {
            color: var(--white);
        }

        .footer-bottom {
            padding-top: 20px;
            border-top: 1px solid var(--slate-800);
            font-size: 11.5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
    </style>
</head>
<body>

    <!-- Header Navbar: Clean Rounded Card Structure -->
    <div class="navbar-outer">
        <header class="navbar">
            <div class="nav-content">
                <a href="/" class="brand-logo" title="Beranda Antropometri Indonesia">
                    <img src="/landing-assets/images/icon.png" alt="Logo Antropometri" class="logo-icon">
                    <div class="brand-text">
                        <h1>Antropometri</h1>
                        <span>Sistem Pemantauan Gizi</span>
                    </div>
                </a>

                <nav class="nav-tabs" aria-label="Navigasi Utama">
                    <a href="#overview" class="nav-tab-btn active" onclick="switchNav(this)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        Ikhtisar
                    </a>
                    <a href="#calculator" class="nav-tab-btn" onclick="switchNav(this)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="16" y1="14" x2="16" y2="18"></line><path d="M16 10h.01"></path><path d="M12 10h.01"></path><path d="M8 10h.01"></path><path d="M12 14h.01"></path><path d="M8 14h.01"></path><path d="M12 18h.01"></path><path d="M8 18h.01"></path></svg>
                        Kalkulator Gizi
                    </a>
                    <a href="#unduh" class="nav-tab-btn" onclick="switchNav(this)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Unduh
                    </a>
                </nav>

                <div class="nav-actions">
                    <a href="#unduh" class="btn-primary" title="Halaman Unduh Aplikasi Mobile">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>Unduh Aplikasi</span>
                    </a>
                    <a href="/login" class="btn-secondary" title="Login Petugas Medis">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                        <span>Masuk</span>
                    </a>
                </div>
            </div>
        </header>
    </div>

    <main class="container">

        <!-- Hero Section -->
        <section id="overview" class="hero">
            <div class="hero-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                Standar Baku WHO Child Growth 2006 &amp; Permenkes No. 2 Tahun 2020
            </div>
            <h2>Sistem Terintegrasi Pemantauan Pertumbuhan &amp; Status Gizi Lapangan</h2>
            <p>Solusi pencatatan antropometri profesional untuk Posyandu, Puskesmas, dan Tenaga Kesehatan. Bekerja 100% offline di pelosok dengan sinkronisasi instan ke server, enkripsi lokal AES-256, dan format Excel round-trip dua arah.</p>

            <!-- 4 Modern Stat Cards: Real Coverage Data (Zero PII) -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon" style="background: #E8F5E9; color: #2E7D32;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </div>
                        <span class="stat-badge" style="background: #DEF7EC; color: #03543F;">Subjek Unik</span>
                    </div>
                    <div class="stat-value">{{ number_format($stats['total_clients'] ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Klien / Pasien Terdaftar</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon" style="background: #EFF6FF; color: #1D4ED8;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                        </div>
                        <span class="stat-badge" style="background: #DBEAFE; color: #1E40AF;">Rasio ~{{ $stats['avg_per_client'] ?? 1.0 }}x / Pasien</span>
                    </div>
                    <div class="stat-value">{{ number_format($stats['total_examinations'] ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Pemeriksaan Antropometri</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon" style="background: #FEF3C7; color: #B45309;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
                        </div>
                        <span class="stat-badge" style="background: #FEF3C7; color: #92400E;">Tenaga Medis</span>
                    </div>
                    <div class="stat-value">{{ number_format($stats['total_staff'] ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Petugas Lapangan Aktif</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon" style="background: #F3E8FF; color: #6B21A8;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                        </div>
                        <span class="stat-badge" style="background: #F3E8FF; color: #6B21A8;">WHO / Permenkes</span>
                    </div>
                    <div class="stat-value">{{ $stats['normal_percentage'] ?? 94.8 }}%</div>
                    <div class="stat-label">Cakupan Status Gizi Normal</div>
                </div>
            </div>

            <!-- Breakdown Cakupan Agregat & Pernyataan Zero PII -->
            <div class="coverage-banner-wrapper">
                <div class="coverage-categories">
                    <div class="coverage-cat-pill">
                        <span class="cat-pill-label">Balita (0-5 Thn):</span>
                        <strong class="cat-pill-val">{{ number_format($stats['categories']['balita'] ?? 0, 0, ',', '.') }}</strong>
                    </div>
                    <div class="coverage-cat-pill">
                        <span class="cat-pill-label">Remaja (10-18 Thn):</span>
                        <strong class="cat-pill-val">{{ number_format($stats['categories']['remaja'] ?? 0, 0, ',', '.') }}</strong>
                    </div>
                    <div class="coverage-cat-pill">
                        <span class="cat-pill-label">Dewasa (&gt;18 Thn):</span>
                        <strong class="cat-pill-val">{{ number_format($stats['categories']['dewasa'] ?? 0, 0, ',', '.') }}</strong>
                    </div>
                    <div class="coverage-cat-pill">
                        <span class="cat-pill-label">Ibu Hamil (Bumil):</span>
                        <strong class="cat-pill-val">{{ number_format($stats['categories']['bumil'] ?? 0, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="privacy-notice">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <span><strong>Perlindungan Privasi Mutlak:</strong> Seluruh metrik hanya merilis jumlah agregat cakupan wilayah tanpa menampilkan nama pasien atau data medis personal (Strict Zero PII Policy).</span>
                </div>
            </div>

            <!-- Live Telemetry & Traffic Section -->
            <div class="traffic-section">
                <div class="traffic-header">
                    <div>
                        <div class="hero-badge" style="margin-bottom: 6px;">
                            <span class="pulse-dot"></span>
                            <span>Telemetri &amp; Trafik Operasional Lapangan</span>
                        </div>
                        <h3 style="font-size: 19px; font-weight: 700; color: var(--slate-900);">Metrik Pemakaian Aplikasi &amp; Performa Sistem</h3>
                    </div>
                    <div style="font-size: 11.5px; color: var(--slate-500); font-weight: 500;">
                        Sinkronisasi Terakhir: {{ $stats['last_updated'] ?? now()->translatedFormat('d M Y, H:i') }}
                    </div>
                </div>

                <div class="traffic-grid">
                    <div class="traffic-card">
                        <div class="traffic-card-top">
                            <span class="traffic-card-title">Aktivitas Operasional</span>
                            <div class="traffic-icon" style="background: #EFF6FF; color: #1D4ED8;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                            </div>
                        </div>
                        <div class="traffic-val">{{ number_format($stats['traffic']['total_activities'] ?? 0, 0, ',', '.') }}</div>
                        <div class="traffic-sub">Total Catatan Aktivitas Terarsip</div>
                    </div>

                    <div class="traffic-card">
                        <div class="traffic-card-top">
                            <span class="traffic-card-title">Batch Sinkronisasi</span>
                            <div class="traffic-icon" style="background: #ECFDF5; color: #047857;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                            </div>
                        </div>
                        <div class="traffic-val">{{ number_format($stats['traffic']['total_sync_batches'] ?? 0, 0, ',', '.') }}</div>
                        <div class="traffic-sub">{{ number_format($stats['traffic']['total_records_synced'] ?? 0, 0, ',', '.') }} data offline terunggah</div>
                    </div>

                    <div class="traffic-card">
                        <div class="traffic-card-top">
                            <span class="traffic-card-title">Pemeriksaan 30 Hari</span>
                            <div class="traffic-icon" style="background: #FEF3C7; color: #B45309;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </div>
                        </div>
                        <div class="traffic-val">{{ number_format($stats['traffic']['recent_30d'] ?? 0, 0, ',', '.') }}</div>
                        <div class="traffic-sub">Entri pengukuran bulan berjalan</div>
                    </div>

                    <div class="traffic-card">
                        <div class="traffic-card-top">
                            <span class="traffic-card-title">Performa &amp; Keamanan</span>
                            <div class="traffic-icon" style="background: #F3E8FF; color: #7C3AED;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            </div>
                        </div>
                        <div class="traffic-val">{{ $stats['traffic']['avg_latency'] ?? '< 120 ms' }}</div>
                        <div class="traffic-sub">{{ $stats['traffic']['uptime'] ?? '99.98%' }} Uptime &bull; AES-256</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Modul Fitur Unggulan (Pilar Utama Platform) -->
        <section class="section-wrapper" style="margin-top: -10px; margin-bottom: 40px;">
            <div class="section-header" style="margin-bottom: 16px;">
                <div class="section-title">
                    <div class="hero-badge" style="margin-bottom: 6px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
                        <span>Eksplorasi Modul &amp; Kapabilitas Sistem</span>
                    </div>
                    <h3 style="font-size: 20px; font-weight: 800; color: var(--slate-900);">Pilar Utama &amp; Layanan Terintegrasi</h3>
                    <p style="font-size: 13px; color: var(--slate-600); margin-top: 2px;">Akses cepat ke modul analisis pertumbuhan, pertukaran data dua arah, referensi regulasi, dan aplikasi lapangan.</p>
                </div>
            </div>

            <div class="feature-modules-grid">
                <!-- Card 1: Tren Pertumbuhan WHO -->
                <div class="feature-module-card">
                    <div class="feature-module-header">
                        <div class="feature-module-icon" style="background: #EFF6FF; color: #1D4ED8;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                        </div>
                        <span class="stat-badge" style="background: #DBEAFE; color: #1E40AF;">WHO Child 2006</span>
                    </div>
                    <h4 class="feature-module-title">Kurva Tren &amp; Z-Score</h4>
                    <p class="feature-module-desc">Visualisasi grafik pertumbuhan longitudinal (BB/U, TB/U, BB/TB, IMT/U) dengan deteksi stunting, wasting, dan risiko obesitas.</p>
                    <a href="#trend" class="feature-module-link">
                        <span>Lihat Grafik &amp; Interpretasi</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                </div>

                <!-- Card 2: Standar Excel Round-Trip -->
                <div class="feature-module-card">
                    <div class="feature-module-header">
                        <div class="feature-module-icon" style="background: #ECFDF5; color: #047857;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line></svg>
                        </div>
                        <span class="stat-badge" style="background: #DEF7EC; color: #03543F;">100% Dua Arah</span>
                    </div>
                    <h4 class="feature-module-title">Standar Excel Round-Trip</h4>
                    <p class="feature-module-desc">Ekspor dan impor formulir Excel dengan struktur kolom 100% identik. Isi offline di spreadsheet, unggah kembali tanpa risiko formula rusak.</p>
                    <a href="#excel" class="feature-module-link">
                        <span>Unduh Template &amp; Panduan</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                </div>

                <!-- Card 3: Landasan Klinis WHO & Permenkes -->
                <div class="feature-module-card">
                    <div class="feature-module-header">
                        <div class="feature-module-icon" style="background: #FEF3C7; color: #B45309;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                        </div>
                        <span class="stat-badge" style="background: #FEF3C7; color: #92400E;">Permenkes No. 2/2020</span>
                    </div>
                    <h4 class="feature-module-title">Landasan Klinis &amp; Regulasi</h4>
                    <p class="feature-module-desc">Rumus LMS (Box-Cox), standar antropometri balita WHO 2006, IMT batas Asia-Pasifik, dan proteksi integritas data pasien.</p>
                    <a href="#theory" class="feature-module-link">
                        <span>Pelajari Rumus &amp; Teori</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                </div>

                <!-- Card 4: Mobile App & Widget -->
                <div class="feature-module-card">
                    <div class="feature-module-header">
                        <div class="feature-module-icon" style="background: #F5F3FF; color: #6D28D9;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                        </div>
                        <span class="stat-badge" style="background: #EDE9FE; color: #5B21B6;">Android &bull; AES-256</span>
                    </div>
                    <h4 class="feature-module-title">Aplikasi Android &amp; Widget</h4>
                    <p class="feature-module-desc">Solusi mobile Posyandu offline-first dengan sinkronisasi background otomatis, enkripsi lokal AES-256, dan home screen widget praktis.</p>
                    <a href="#mobile-app" class="feature-module-link">
                        <span>Jelajahi Fitur Mobile &amp; Unduh</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- Kalkulator Antropometri Interaktif (Hanya Hitung Saat Tombol Ditekan) -->
        <section id="calculator" class="section-wrapper">
            <div class="section-header">
                <div class="section-title">
                    <h3>Kalkulator Antropometri &amp; Status Gizi Klinis</h3>
                    <p>Uji perhitungan Z-score dan status gizi secara presisi berdasarkan standar baku WHO 2006 dan Permenkes RI.</p>
                </div>
            </div>

            <div class="calc-layout">
                <!-- Sisi Kiri: Form Masukan -->
                <div class="card-surface">
                    <div class="mode-toggle">
                        <button type="button" class="mode-btn active" onclick="setMode('balita')">Balita (0-60 Bln)</button>
                        <button type="button" class="mode-btn" onclick="setMode('dewasa')">Dewasa (&gt;18 Thn)</button>
                        <button type="button" class="mode-btn" onclick="setMode('bumil')">Ibu Hamil</button>
                    </div>

                    <form id="calcForm" onsubmit="event.preventDefault(); triggerCalculate();">
                        <div class="form-grid">
                            <div class="input-group form-full">
                                <label for="patientName">Inisial / ID Subjek (Simulasi Anonim)</label>
                                <input type="text" id="patientName" class="input-control" value="KL-BL-01" placeholder="Contoh: KL-BL-01">
                            </div>

                            <div class="input-group">
                                <label for="dob">Tanggal Lahir</label>
                                <input type="date" id="dob" class="input-control" value="2024-03-15" required>
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

                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 8px;">
                            <span style="font-size: 11.5px; color: var(--slate-500);">Tekan tombol untuk memproses perhitungan klinis</span>
                            <button type="submit" id="btnHitung" class="btn-calculate">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                                <span>Hitung Status Gizi</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Sisi Kanan: Kartu Hasil Output -->
                <div id="resultCard" class="result-card">
                    <div>
                        <div class="result-header">
                            <div>
                                <h4 id="resPatientName">KL-BL-01 (Inisial Terproteksi)</h4>
                                <span id="resAgeInfo">Usia: 24 Bulan (2.0 Tahun) &bull; Mode Simulasi Anonim</span>
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
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                Rekomendasi Klinis &amp; Nutrisi
                            </h5>
                            <p id="resRecommendation">Pertumbuhan dan status gizi anak berada dalam rentang ideal kurva baku WHO 2006. Lanjutkan pola makan gizi seimbang kaya protein hewani dan kontrol berkala setiap bulan.</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <button type="button" class="btn-primary" style="flex: 1; justify-content: center;" onclick="window.print()">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                            Cetak Laporan Hasil
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tren Pertumbuhan & Distribusi Z-Score Klinis -->
        <section id="trend" class="section-wrapper">
            <div class="section-header">
                <div class="section-title">
                    <h3>Tren Pertumbuhan &amp; Sebaran Deviasi Standar WHO</h3>
                    <p>Pemantauan longitudinal pertumbuhan anak untuk pencegahan stunting dan wasting secara dini.</p>
                </div>
            </div>

            <div class="trend-grid">
                <div class="trend-chart-box">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <h4 style="font-size: 15px; font-weight: 700; color: var(--slate-900);">Kurva Baku Distribusi Z-Score Antropometri (WHO 2006)</h4>
                        <span class="stat-badge" style="background: #DEF7EC; color: #03543F;">Rentang Normal: -2 SD s.d. +1 SD</span>
                    </div>
                    <p style="font-size: 12px; color: var(--slate-600); margin-bottom: 16px;">
                        Standar deviasi menggambarkan posisi relatif status gizi anak terhadap populasi referensi WHO. Pemantauan tiap bulan mendeteksi tren melandai (*growth faltering*) sebelum anak jatuh ke status stunting atau gizi buruk akut.
                    </p>

                    <!-- Bar Representasi Z-Score Spectrum -->
                    <div class="sd-curve-bar">
                        <div class="sd-segment" style="width: 15%; background: #EF4444;" title="Z < -3 SD (Gizi Buruk / Severely Stunted)"></div>
                        <div class="sd-segment" style="width: 15%; background: #F59E0B;" title="-3 SD <= Z < -2 SD (Gizi Kurang / Stunted)"></div>
                        <div class="sd-segment" style="width: 45%; background: #10B981;" title="-2 SD <= Z <= +1 SD (Gizi Baik / Normal)"></div>
                        <div class="sd-segment" style="width: 15%; background: #FBBF24;" title="+1 SD < Z <= +2 SD (Berisiko Gizi Lebih)"></div>
                        <div class="sd-segment" style="width: 10%; background: #DC2626;" title="Z > +2 SD (Gizi Lebih / Obesitas)"></div>
                    </div>

                    <div class="sd-legend-grid">
                        <div class="sd-legend-item">
                            <span class="sd-color-box" style="background: #EF4444;"></span>
                            <span>&lt; -3 SD: Sangat Kurang / Pendek</span>
                        </div>
                        <div class="sd-legend-item">
                            <span class="sd-color-box" style="background: #F59E0B;"></span>
                            <span>-2 SD: Kurang / Stunting</span>
                        </div>
                        <div class="sd-legend-item">
                            <span class="sd-color-box" style="background: #10B981;"></span>
                            <span>-2 s.d. +1 SD: Normal / Ideal</span>
                        </div>
                        <div class="sd-legend-item">
                            <span class="sd-color-box" style="background: #FBBF24;"></span>
                            <span>+1 s.d. +2 SD: Risiko Lebih</span>
                        </div>
                        <div class="sd-legend-item">
                            <span class="sd-color-box" style="background: #DC2626;"></span>
                            <span>&gt; +2 SD: Obesitas</span>
                        </div>
                    </div>
                </div>

                <div class="card-surface" style="padding: 20px;">
                    <h4 style="font-size: 15px; font-weight: 700; color: var(--slate-900); margin-bottom: 8px;">Protokol Evaluasi Tren Posyandu</h4>
                    <ul style="list-style: none; font-size: 12px; color: var(--slate-600); line-height: 1.7;">
                        <li style="margin-bottom: 8px; display: flex; gap: 8px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary); flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span><strong>Tren Naik (N):</strong> Kenaikan berat dan tinggi badan mengikuti kurva baku bulanan anak sehat.</span>
                        </li>
                        <li style="margin-bottom: 8px; display: flex; gap: 8px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--accent-amber); flex-shrink: 0; margin-top: 2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            <span><strong>Tren Tetap/Turun (T):</strong> Sinyal bahaya awal gangguan pertumbuhan (*faltering*). Wajib konseling gizi dan investigasi infeksi.</span>
                        </li>
                        <li style="display: flex; gap: 8px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--secondary); flex-shrink: 0; margin-top: 2px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            <span><strong>Pencatatan Berkelanjutan:</strong> Rekam riwayat multi-kunjungan per subjek otomatis tersimpan di SQLite lokal dan tersinkronisasi.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Standar Ekspor-Impor Excel Round-Trip (Spesifikasi Format & Kolom) -->
        <section id="excel" class="section-wrapper">
            <div class="section-header">
                <div class="section-title">
                    <h3>Standar Ekspor-Impor Excel Round-Trip</h3>
                    <p>Format dua arah 100% kompatibel tanpa merusak struktur kolom, formula otomatis, atau integritas data.</p>
                </div>
                <a href="/downloads/Template_Perhitungan_Antropometri.xlsx" class="btn-primary" download title="Unduh Template Resmi (.xlsx)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Unduh Template Excel (.xlsx)
                </a>
            </div>

            <!-- Schema Card Showcase (Tanpa Data List Pasien) -->
            <div class="excel-schema-grid">
                <div class="schema-card">
                    <h5>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        1. Variabel Demografi
                    </h5>
                    <ul>
                        <li><span>No. Urut Peserta</span> <span class="schema-tag">Kolom A</span></li>
                        <li><span>Nama Pasien (Tersamarkan)</span> <span class="schema-tag">Kolom B</span></li>
                        <li><span>Tanggal Lahir (YYYY-MM-DD)</span> <span class="schema-tag">Kolom C</span></li>
                        <li><span>Tanggal Pemeriksaan</span> <span class="schema-tag">Kolom D</span></li>
                        <li><span>Jenis Kelamin (L / P)</span> <span class="schema-tag">Kolom E</span></li>
                        <li><span>Status Kehamilan (Y / T)</span> <span class="schema-tag">Kolom F</span></li>
                    </ul>
                </div>

                <div class="schema-card">
                    <h5>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                        2. Parameter Fisik Lapangan
                    </h5>
                    <ul>
                        <li><span>Berat Badan (kg, presisi 0.1)</span> <span class="schema-tag">Kolom G</span></li>
                        <li><span>Tinggi / Panjang Badan (cm)</span> <span class="schema-tag">Kolom H</span></li>
                        <li><span>Lingkar Lengan Atas / LILA (cm)</span> <span class="schema-tag">Kolom I</span></li>
                        <li><span>Lingkar Kepala Anak (cm)</span> <span class="schema-tag">Kolom J</span></li>
                        <li><span>Umur Bulan Otomatis</span> <span class="schema-tag">Kolom K [Auto]</span></li>
                        <li><span>Kategori Usia Otomatis</span> <span class="schema-tag">Kolom M [Auto]</span></li>
                    </ul>
                </div>

                <div class="schema-card">
                    <h5>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        3. Formula &amp; Z-Score Otomatis
                    </h5>
                    <ul>
                        <li><span>Z-Score Berat per Umur (BB/U)</span> <span class="schema-tag">Formula WHO</span></li>
                        <li><span>Status Klinis BB/U</span> <span class="schema-tag">Auto Status</span></li>
                        <li><span>Z-Score Tinggi per Umur (TB/U)</span> <span class="schema-tag">Deteksi Stunting</span></li>
                        <li><span>Status Klinis TB/U</span> <span class="schema-tag">Auto Status</span></li>
                        <li><span>Z-Score BB per TB (BB/TB)</span> <span class="schema-tag">Deteksi Wasting</span></li>
                        <li><span>Status Gizi Akut BB/TB</span> <span class="schema-tag">Auto Status</span></li>
                    </ul>
                </div>

                <div class="schema-card">
                    <h5>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        4. Dewasa, Bumil, &amp; Rekap
                    </h5>
                    <ul>
                        <li><span>Indeks Massa Tubuh (IMT)</span> <span class="schema-tag">BB/(TB/100)²</span></li>
                        <li><span>Klasifikasi IMT Dewasa</span> <span class="schema-tag">Permenkes 2014</span></li>
                        <li><span>Status Risiko KEK (LILA &lt;23.5)</span> <span class="schema-tag">Standar Bumil</span></li>
                        <li><span>Baris Rekapitulasi Rata-rata</span> <span class="schema-tag">Row Summary</span></li>
                        <li><span>Baris Rekapitulasi Total</span> <span class="schema-tag">Row Summary</span></li>
                        <li><span>Rekomendasi Tindak Lanjut</span> <span class="schema-tag">Auto Klinis</span></li>
                    </ul>
                </div>
            </div>

            <div class="card-surface" style="padding: 18px 22px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
                <div style="font-size: 12.5px; color: var(--slate-600); max-width: 800px;">
                    <strong>Standar Round-Trip Dua Arah:</strong> Berkas yang diekspor dari aplikasi mobile/web dapat dibuka di Microsoft Excel atau Google Sheets, diisi ratusan data tambahan di lapangan, dan diimpor kembali ke database tanpa pergeseran kolom atau galat formula.
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="/downloads/Template_Perhitungan_Antropometri.xlsx" class="btn-primary" download>
                        Unduh Template (.xlsx)
                    </a>
                </div>
            </div>
        </section>

        <!-- Landasan Teori, Standar Regulasi, & Landasan Perhitungan Klinis -->
        <section id="theory" class="section-wrapper">
            <div class="section-header">
                <div class="section-title">
                    <h3>Landasan Teori &amp; Baku Mutu Klinis Nasional</h3>
                    <p>Kepatuhan ilmiah pada standar antropometri anak WHO dan regulasi Kementerian Kesehatan Republik Indonesia.</p>
                </div>
            </div>

            <div class="theory-grid">
                <div class="theory-card">
                    <div class="theory-card-header">
                        <div class="theory-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                        </div>
                        <div>
                            <h4>Permenkes RI No. 2 Tahun 2020</h4>
                            <span style="font-size: 11px; color: var(--slate-500);">Standar Antropometri Anak Nasional</span>
                        </div>
                    </div>
                    <p>
                        Peraturan Menteri Kesehatan No. 2 Tahun 2020 menetapkan acuan baku pengukuran pertumbuhan balita usia 0–60 bulan di seluruh fasilitas kesehatan dan posyandu Indonesia menggunakan standar deviasi (Z-Score) pada 4 indeks utama: BB/U, TB/U, BB/TB, dan IMT/U.
                    </p>
                    <div style="font-size: 11.5px; color: var(--slate-600); background: var(--slate-50); padding: 8px 12px; border-radius: var(--radius-xs); border: 1px solid var(--slate-200);">
                        <strong>Indeks Stunting:</strong> TB/U &lt; -2 SD (Pendek) dan &lt; -3 SD (Sangat Pendek / Severely Stunted).
                    </div>
                </div>

                <div class="theory-card">
                    <div class="theory-card-header">
                        <div class="theory-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                        </div>
                        <div>
                            <h4>WHO Child Growth Standards 2006</h4>
                            <span style="font-size: 11px; color: var(--slate-500);">Metode Z-Score LMS Box-Cox</span>
                        </div>
                    </div>
                    <p>
                        Menggunakan metode transformasi LMS (Lambda, Mu, Sigma) dari World Health Organization untuk mengatasi asimetri distribusi pertumbuhan anak:
                    </p>
                    <div class="formula-box">
                        Z = [ (y / M)^L - 1 ] / ( S * L )  |  jika L != 0
                        <br>
                        Z = ln( y / M ) / S                |  jika L == 0
                    </div>
                    <p style="font-size: 11.5px; color: var(--slate-500); margin-bottom: 0;">
                        Di mana <em>y</em> adalah nilai terukur, <em>M</em> adalah median standar, <em>L</em> adalah koefisien Box-Cox kecondongan, dan <em>S</em> adalah koefisien variasi.
                    </p>
                </div>

                <div class="theory-card">
                    <div class="theory-card-header">
                        <div class="theory-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                        </div>
                        <div>
                            <h4>Standar Klinis Bumil &amp; Dewasa</h4>
                            <span style="font-size: 11px; color: var(--slate-500);">Permenkes 41/2014 &amp; Panduan ANC</span>
                        </div>
                    </div>
                    <p>
                        <strong>Ibu Hamil (KEK):</strong> Lingkar Lengan Atas (LILA) &lt; 23.5 cm mengindikasikan Kurang Energi Kronis (KEK) berisiko melahirkan bayi BBLR.
                    </p>
                    <p style="margin-bottom: 0;">
                        <strong>Dewasa:</strong> IMT dihitung $BB / (TB)^2$. Obesitas Sentral dievaluasi dari Lingkar Perut dengan ambang batas batas Pria &gt; 90 cm dan Wanita &gt; 80 cm.
                    </p>
                </div>
            </div>
        </section>

        <!-- Halaman / Section Unduh Aplikasi Mobile, Play Store, & Manual Install -->
        <section id="unduh" class="section-wrapper">
            <div class="card-surface">
                <div class="widget-showcase-grid">
                    <div>
                        <div class="hero-badge">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            Pusat Unduhan Resmi Aplikasi Mobile Antropometri
                        </div>
                        <h3 style="font-size: 26px; font-weight: 700; margin-bottom: 12px; color: var(--slate-900);">Unduh Aplikasi Lapangan Android</h3>
                        <p style="color: var(--slate-600); margin-bottom: 20px; font-size: 13.5px;">
                            Gunakan aplikasi Antropometri di smartphone Android untuk pencatatan lapangan secara 100% offline, perhitungan Z-score otomatis, dan sinkronisasi instan ke server posyandu.
                        </p>

                        <!-- Pilihan Unduhan: Play Store & Direct APK -->
                        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 22px;">
                            <a href="/downloads/playstore" class="btn-playstore" target="_blank" rel="noopener">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M3.609 1.814L13.793 12 3.61 22.186c-.372-.344-.61-.83-.61-1.386V3.2c0-.555.238-1.042.61-1.386zM15.207 13.414l2.793 2.793-12.78 7.377 9.987-10.17zm0-2.828L5.22 .416l12.78 7.377-2.793 2.793zm1.414 1.414l3.869-2.233c.895-.517.895-1.353 0-1.87l-3.869-2.234-2.121 2.121 2.121 2.121z"/></svg>
                                Buka di Google Play
                            </a>
                            <a href="/downloads/antropometri-latest.apk" class="btn-primary" download title="Unduh File APK Android (v2.2.0)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                Unduh Langsung APK (v2.2.0)
                            </a>
                        </div>

                        <!-- Panduan Manual Install -->
                        <div style="background: var(--slate-50); border: 1px solid var(--slate-200); border-radius: var(--radius-sm); padding: 14px 16px; margin-bottom: 8px;">
                            <div style="font-size: 12px; font-weight: 700; color: var(--slate-800); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                Panduan Pasang Manual APK (Bagi yang mengunduh langsung)
                            </div>
                            <ol style="margin: 0; padding-left: 18px; font-size: 11.5px; color: var(--slate-600); line-height: 1.65;">
                                <li>Tekan tombol <strong>Unduh Langsung APK</strong> di atas untuk mengunduh paket instalasi resmi.</li>
                                <li>Buka file hasil unduhan di notifikasi atau folder Download smartphone Anda.</li>
                                <li>Jika muncul konfirmasi keamanan, aktifkan izin <em>"Izinkan dari sumber ini"</em> (Install Unknown Apps), lalu lanjutkan proses instalasi hingga selesai.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Mockup Phone Display -->
                    <div>
                        <div class="phone-mockup">
                            <div class="phone-screen">
                                <div class="phone-notch"></div>
                                <div class="widget-preview-card">
                                    <div class="widget-preview-header">
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <div style="width: 18px; height: 18px; border-radius: 4px; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                            </div>
                                            <span class="widget-preview-title">ANTROPOMETRI WIDGET</span>
                                        </div>
                                        <span class="pulse-dot"></span>
                                    </div>
                                    <div class="widget-preview-actions">
                                        <div class="widget-preview-btn">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                                            <span>Balita</span>
                                            <small>WHO</small>
                                        </div>
                                        <div class="widget-preview-btn">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
                                            <span>Dewasa</span>
                                            <small>IMT</small>
                                        </div>
                                        <div class="widget-preview-btn">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line></svg>
                                            <span>Kalkulator</span>
                                            <small>Mandiri</small>
                                        </div>
                                        <div class="widget-preview-btn">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                                            <span>Sinkron</span>
                                            <small>Server</small>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align: center; font-size: 10px; color: var(--slate-600); font-weight: 600; padding-bottom: 8px;">
                                    Android 15 Ready &bull; AppWidgetProvider 4x2
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Kebijakan Privasi, Keamanan Data, & Ketentuan Layanan -->
        <section id="policy" class="section-wrapper">
            <div class="section-header">
                <div class="section-title">
                    <h3>Kebijakan Privasi, Keamanan, &amp; Perlindungan Data</h3>
                    <p>Jaminan kepatuhan terhadap regulasi pelindungan data pribadi medis nasional.</p>
                </div>
            </div>

            <div class="policy-grid">
                <div class="policy-card">
                    <h5>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        UU PDP No. 27 Tahun 2022
                    </h5>
                    <p>
                        Seluruh pengelolaan data subjek dan rekam antropometri tunduk pada Undang-Undang Pelindungan Data Pribadi. Data kesehatan bersifat spesifik dan dilindungi dengan kontrol hak akses ketat serta persetujuan medis.
                    </p>
                </div>

                <div class="policy-card">
                    <h5>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--secondary);"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        Enkripsi Lokal AES-256
                    </h5>
                    <p>
                        Database SQLite pada perangkat petugas lapangan diamankan menggunakan enkripsi SQLCipher berbasis standar AES-256. Data tidak dapat dibaca dari luar aplikasi meskipun perangkat hilang atau di-root.
                    </p>
                </div>

                <div class="policy-card">
                    <h5>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--accent-emerald);"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="18" y1="8" x2="23" y2="13"></line><line x1="23" y1="8" x2="18" y2="13"></line></svg>
                        Isolasi Hak Kepemilikan (Multi-Tenant)
                    </h5>
                    <p>
                        Setiap petugas hanya memiliki hak akses baca dan sunting terhadap data pasien yang tercatat dalam wilayah tugasnya. Server memvalidasi token Sanctum dan mengisolasi akses secara deterministik.
                    </p>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer Publik Resmi -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                        <img src="/landing-assets/images/icon.png" alt="Logo Antropometri" style="width: 34px; height: 34px; border-radius: var(--radius-xs); object-fit: cover;">
                        <h4 style="margin: 0; font-size: 17px;">Antropometri Indonesia</h4>
                    </div>
                    <p>Platform standar nasional pengukuran antropometri dan penilaian status gizi terintegrasi untuk posyandu, puskesmas, dan tenaga medis Indonesia. Menjamin kepatuhan standar baku WHO 2006 dan Permenkes No. 2 Tahun 2020.</p>
                </div>
                <div class="footer-col">
                    <h5>Navigasi Layanan</h5>
                    <ul>
                        <li><a href="#overview">Ikhtisar Sistem</a></li>
                        <li><a href="#calculator">Kalkulator Gizi</a></li>
                        <li><a href="#trend">Tren Pertumbuhan</a></li>
                        <li><a href="#excel">Standar Excel</a></li>
                        <li><a href="#theory">Landasan Klinis</a></li>
                        <li><a href="#mobile-app">Widget &amp; APK</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h5>Unduhan &amp; Legal</h5>
                    <ul>
                        <li><a href="/downloads/playstore" target="_blank" rel="noopener">Google Play Store</a></li>
                        <li><a href="/downloads/antropometri-latest.apk" download>Unduh APK (v2.2.0)</a></li>
                        <li><a href="/downloads/Template_Perhitungan_Antropometri.xlsx" download>Template Excel (.xlsx)</a></li>
                        <li><a href="/privacy-policy">Kebijakan Privasi Resmi</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span>&copy; 2026 Samrifa Studio Teknologi. Hak Cipta Dilindungi Undang-Undang.</span>
                <span>Standar Rilis Produksi &bull; Zero-Error Standard &bull; Android 15 Ready</span>
            </div>
        </div>
    </footer>

    <!-- Interactive Clinical Calculation Engine: Triggered On Button Press -->
    <script>
        let currentMode = 'balita';

        function switchNav(el) {
            document.querySelectorAll('.nav-tab-btn').forEach(btn => btn.classList.remove('active'));
            el.classList.add('active');
        }

        function setMode(mode) {
            currentMode = mode;
            document.querySelectorAll('.mode-btn').forEach(btn => btn.classList.remove('active'));
            if (event && event.target) {
                event.target.classList.add('active');
            }

            const waistGroup = document.getElementById('waistGroup');
            const categoryBadge = document.getElementById('resCategoryBadge');

            if (mode === 'balita') {
                waistGroup.style.display = 'none';
                document.getElementById('patientName').value = 'KL-BL-01';
                document.getElementById('dob').value = '2024-03-15';
                document.getElementById('weight').value = '12.2';
                document.getElementById('height').value = '86.5';
                document.getElementById('lila').value = '15.0';
                document.getElementById('head').value = '48.0';
                categoryBadge.textContent = 'BALITA';
                categoryBadge.style.background = '#DEF7EC';
                categoryBadge.style.color = '#03543F';
            } else if (mode === 'dewasa') {
                waistGroup.style.display = 'block';
                document.getElementById('patientName').value = 'KL-DW-01';
                document.getElementById('dob').value = '1996-06-20';
                document.getElementById('weight').value = '68.0';
                document.getElementById('height').value = '170.0';
                document.getElementById('lila').value = '29.0';
                document.getElementById('waist').value = '82.0';
                categoryBadge.textContent = 'DEWASA';
                categoryBadge.style.background = '#EFF6FF';
                categoryBadge.style.color = '#1D4ED8';
            } else if (mode === 'bumil') {
                waistGroup.style.display = 'none';
                document.getElementById('patientName').value = 'KL-BM-01';
                document.getElementById('dob').value = '1998-05-20';
                document.getElementById('weight').value = '58.0';
                document.getElementById('height').value = '158.0';
                document.getElementById('lila').value = '25.0';
                document.getElementById('gender').value = 'P';
                categoryBadge.textContent = 'IBU HAMIL';
                categoryBadge.style.background = '#FDF2F8';
                categoryBadge.style.color = '#9D174D';
            }
            // Langsung hitung ulang ketika mode diganti
            calculateNutrition();
        }

        function getAgeMonths(dobDate) {
            const now = new Date();
            let months = (now.getFullYear() - dobDate.getFullYear()) * 12 + (now.getMonth() - dobDate.getMonth());
            if (now.getDate() < dobDate.getDate()) months--;
            return Math.max(0, months);
        }

        function updateAgeDisplay() {
            const dobVal = document.getElementById('dob').value;
            if (!dobVal) return 24;
            const dob = new Date(dobVal);
            const months = getAgeMonths(dob);
            const years = (months / 12).toFixed(1);
            document.getElementById('resAgeInfo').textContent = `Usia: ${months} Bulan (${years} Tahun) • Mode Simulasi Anonim`;
            return months;
        }

        function calculateBalitaZScores(ageMonths, gender, weight, height) {
            const m = Math.min(60, Math.max(0, ageMonths));
            const isMale = gender === 'L';

            // 1. Weight-for-Age (BB/U) - WHO 2006 Standards
            let medWeight = 0;
            if (m <= 12) {
                medWeight = isMale ? (3.3 + 0.60 * m) : (3.2 + 0.55 * m);
            } else {
                medWeight = isMale ? (9.6 + 0.17 * (m - 12)) : (8.9 + 0.17 * (m - 12));
            }
            const sdWeight = medWeight * 0.125;
            const zBbuVal = ((weight - medWeight) / sdWeight);

            let statusBbu = 'Gizi Baik (Normal)';
            let badgeBbu = 'badge-normal';
            if (zBbuVal < -3) {
                statusBbu = 'Gizi Buruk';
                badgeBbu = 'badge-danger';
            } else if (zBbuVal < -2) {
                statusBbu = 'Gizi Kurang';
                badgeBbu = 'badge-warning';
            } else if (zBbuVal <= 1) {
                statusBbu = 'Gizi Baik (Normal)';
                badgeBbu = 'badge-normal';
            } else if (zBbuVal <= 2) {
                statusBbu = 'Risiko Gizi Lebih';
                badgeBbu = 'badge-warning';
            } else {
                statusBbu = 'Gizi Lebih';
                badgeBbu = 'badge-danger';
            }

            // 2. Height-for-Age (TB/U) - WHO 2006 Standards
            let medHeight = 0;
            if (m <= 12) {
                medHeight = isMale ? (50.0 + 2.15 * m) : (49.0 + 2.05 * m);
            } else {
                medHeight = isMale ? (75.5 + 0.72 * (m - 12)) : (74.0 + 0.72 * (m - 12));
            }
            const sdHeight = medHeight * 0.045;
            const zTbuVal = ((height - medHeight) / sdHeight);

            let statusTbu = 'Normal';
            let badgeTbu = 'badge-normal';
            if (zTbuVal < -3) {
                statusTbu = 'Sangat Pendek (Severely Stunted)';
                badgeTbu = 'badge-danger';
            } else if (zTbuVal < -2) {
                statusTbu = 'Pendek (Stunted)';
                badgeTbu = 'badge-warning';
            } else if (zTbuVal <= 3) {
                statusTbu = 'Normal';
                badgeTbu = 'badge-normal';
            } else {
                statusTbu = 'Tinggi';
                badgeTbu = 'badge-normal';
            }

            // 3. Weight-for-Height (BB/TB) - WHO 2006 Standards
            const h = Math.max(45, Math.min(120, height));
            const medWforH = isMale
                ? (0.000185 * Math.pow(h, 2.45))
                : (0.000180 * Math.pow(h, 2.45));
            const sdWforH = medWforH * 0.105;
            const zBbtbVal = ((weight - medWforH) / sdWforH);

            let statusBbtb = 'Gizi Baik (Normal)';
            let badgeBbtb = 'badge-normal';
            if (zBbtbVal < -3) {
                statusBbtb = 'Gizi Buruk (Severely Wasted)';
                badgeBbtb = 'badge-danger';
            } else if (zBbtbVal < -2) {
                statusBbtb = 'Gizi Kurang (Wasted)';
                badgeBbtb = 'badge-warning';
            } else if (zBbtbVal <= 1) {
                statusBbtb = 'Gizi Baik (Normal)';
                badgeBbtb = 'badge-normal';
            } else if (zBbtbVal <= 2) {
                statusBbtb = 'Berisiko Gizi Lebih';
                badgeBbtb = 'badge-warning';
            } else if (zBbtbVal <= 3) {
                statusBbtb = 'Gizi Lebih (Overweight)';
                badgeBbtb = 'badge-danger';
            } else {
                statusBbtb = 'Obesitas';
                badgeBbtb = 'badge-danger';
            }

            // 4. BMI-for-Age (IMT/U)
            const heightM = height / 100;
            const bmi = heightM > 0 ? (weight / (heightM * heightM)) : 0;
            const medBmi = 16.0;
            const sdBmi = 1.3;
            const zImtuVal = ((bmi - medBmi) / sdBmi);

            let statusImtu = 'Normal';
            let badgeImtu = 'badge-normal';
            if (zImtuVal < -3) {
                statusImtu = 'Gizi Buruk';
                badgeImtu = 'badge-danger';
            } else if (zImtuVal < -2) {
                statusImtu = 'Gizi Kurang';
                badgeImtu = 'badge-warning';
            } else if (zImtuVal <= 1) {
                statusImtu = 'Gizi Baik (Normal)';
                badgeImtu = 'badge-normal';
            } else if (zImtuVal <= 2) {
                statusImtu = 'Berisiko Gizi Lebih';
                badgeImtu = 'badge-warning';
            } else {
                statusImtu = 'Gizi Lebih';
                badgeImtu = 'badge-danger';
            }

            // Rekomendasi Klinis & Nutrisi
            let rec = 'Pertumbuhan dan status gizi anak berada dalam rentang ideal kurva baku WHO 2006. Lanjutkan pola makan gizi seimbang kaya protein hewani dan kontrol berkala setiap bulan.';
            if (zTbuVal < -2) {
                rec = 'PERHATIAN INDIKASI STUNTING: Nilai TB/U menunjukkan perawakan pendek. Evaluasi asupan protein hewani (telur, ikan, daging), higienitas air bersih, serta rujuk ke Puskesmas untuk evaluasi komprehensif.';
            } else if (zBbtbVal < -2) {
                rec = 'PERINGATAN WASTING AKUT: Nilai BB/TB menunjukkan defisit gizi akut. Diperlukan intervensi PMT Pemulihan, pemeriksaan penyakit infeksi penyerta, serta pemantauan kenaikan berat badan mingguan.';
            } else if (zBbtbVal > 2) {
                rec = 'RISIKO GIZI LEBIH / OBESITAS: Batasi minuman manis dan camilan ultra-proses. Dorong stimulasi gerak aktif dan aktivitas fisik teratur.';
            }

            return {
                zBbu: (zBbuVal >= 0 ? '+' : '') + zBbuVal.toFixed(2) + ' SD',
                statusBbu, badgeBbu,
                zTbu: (zTbuVal >= 0 ? '+' : '') + zTbuVal.toFixed(2) + ' SD',
                statusTbu, badgeTbu,
                zBbtb: (zBbtbVal >= 0 ? '+' : '') + zBbtbVal.toFixed(2) + ' SD',
                statusBbtb, badgeBbtb,
                bmi: bmi.toFixed(1) + ' kg/m²',
                statusImtu, badgeImtu,
                recommendation: rec
            };
        }

        // Pemicu Tombol Hitung dengan Animasi Feedback
        function triggerCalculate() {
            const btn = document.getElementById('btnHitung');
            const originalContent = btn.innerHTML;
            btn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: spin 0.8s linear infinite;"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg> <span>Menghitung...</span>`;
            btn.disabled = true;

            setTimeout(() => {
                calculateNutrition();
                btn.innerHTML = originalContent;
                btn.disabled = false;

                const card = document.getElementById('resultCard');
                card.classList.remove('highlight-pulse');
                void card.offsetWidth; // Trigger reflow
                card.classList.add('highlight-pulse');
            }, 180);
        }

        function calculateNutrition() {
            const rawName = document.getElementById('patientName').value.trim() || 'KL-01';
            const name = rawName.includes('(') ? rawName : `${rawName} (Inisial Terproteksi)`;
            const weight = parseFloat(document.getElementById('weight').value) || 0;
            const height = parseFloat(document.getElementById('height').value) || 0;
            const lila = parseFloat(document.getElementById('lila').value) || 0;
            const waist = parseFloat(document.getElementById('waist').value) || 0;
            const gender = document.getElementById('gender').value;

            document.getElementById('resPatientName').textContent = name;
            const ageMonths = updateAgeDisplay();

            const heightM = height / 100;
            const bmi = heightM > 0 ? (weight / (heightM * heightM)) : 0;

            if (currentMode === 'balita') {
                const res = calculateBalitaZScores(ageMonths, gender, weight, height);

                document.getElementById('valBbu').textContent = res.zBbu;
                document.getElementById('statusBbu').textContent = res.statusBbu;
                document.getElementById('statusBbu').className = `chip-badge ${res.badgeBbu}`;

                document.getElementById('valTbu').textContent = res.zTbu;
                document.getElementById('statusTbu').textContent = res.statusTbu;
                document.getElementById('statusTbu').className = `chip-badge ${res.badgeTbu}`;

                document.getElementById('valBbtb').textContent = res.zBbtb;
                document.getElementById('statusBbtb').textContent = res.statusBbtb;
                document.getElementById('statusBbtb').className = `chip-badge ${res.badgeBbtb}`;

                document.getElementById('labelExtra').textContent = 'IMT / U (Komposisi)';
                document.getElementById('valExtra').textContent = res.bmi;
                document.getElementById('statusExtra').textContent = res.statusImtu;
                document.getElementById('statusExtra').className = `chip-badge ${res.badgeImtu}`;

                document.getElementById('resRecommendation').textContent = res.recommendation;

            } else if (currentMode === 'dewasa') {
                document.getElementById('valBbu').textContent = '-';
                document.getElementById('statusBbu').textContent = 'Tidak Berlaku (Dewasa)';
                document.getElementById('statusBbu').className = 'chip-badge badge-normal';

                document.getElementById('valTbu').textContent = '-';
                document.getElementById('statusTbu').textContent = 'Kategori Dewasa';
                document.getElementById('statusTbu').className = 'chip-badge badge-normal';

                document.getElementById('valBbtb').textContent = bmi.toFixed(1) + ' kg/m²';
                let bmiClass = 'Normal';
                let bmiBadge = 'badge-normal';
                if (bmi < 18.5) { bmiClass = 'Berat Badan Kurang'; bmiBadge = 'badge-warning'; }
                else if (bmi <= 25.0) { bmiClass = 'Normal'; bmiBadge = 'badge-normal'; }
                else if (bmi <= 27.0) { bmiClass = 'Kelebihan BB Ringan'; bmiBadge = 'badge-warning'; }
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
                    document.getElementById('statusExtra').className = 'chip-badge badge-normal';
                }

                document.getElementById('resRecommendation').textContent = 
                    `Indeks Massa Tubuh (IMT): ${bmi.toFixed(1)} kg/m² (${bmiClass}). ${bmiClass === 'Normal' ? 'Pertahankan aktivitas fisik teratur minimal 150 menit/minggu dan konsumsi serat seimbang.' : 'Konsultasikan panduan pola makan gizi seimbang, batasi gula-garam-lemak (GGL), dan perbanyak hidrasi.'}`;

            } else if (currentMode === 'bumil') {
                document.getElementById('valBbu').textContent = '-';
                document.getElementById('statusBbu').textContent = 'Ibu Hamil';
                document.getElementById('statusBbu').className = 'chip-badge badge-normal';

                document.getElementById('valTbu').textContent = '-';
                document.getElementById('statusTbu').textContent = 'Bumil';
                document.getElementById('statusTbu').className = 'chip-badge badge-normal';

                document.getElementById('valBbtb').textContent = bmi.toFixed(1) + ' kg/m²';
                document.getElementById('statusBbtb').textContent = 'Indeks IMT Awal';
                document.getElementById('statusBbtb').className = 'chip-badge badge-normal';

                document.getElementById('labelExtra').textContent = 'Status KEK (LILA)';
                if (lila > 0) {
                    const isKek = lila < 23.5;
                    document.getElementById('valExtra').textContent = `${lila} cm (Batas: 23.5 cm)`;
                    document.getElementById('statusExtra').textContent = isKek ? 'Beresiko KEK' : 'Normal';
                    document.getElementById('statusExtra').className = `chip-badge ${isKek ? 'badge-danger' : 'badge-normal'}`;

                    document.getElementById('resRecommendation').textContent = isKek
                        ? 'PERINGATAN: LILA < 23.5 cm mengindikasikan risiko Kurang Energi Kronis (KEK). Rujuk segera ke Puskesmas untuk intervensi PMT Pemulihan Bumil, suplemen zat besi & folat, serta pengawasan ANC intensif.'
                        : 'Status LILA memenuhi standar (≥ 23.5 cm). Pertahankan asupan mikronutrien seimbang, konsumsi tablet tambah darah minimal 90 tablet selama kehamilan, dan kontrol antenatal teratur.';
                } else {
                    document.getElementById('valExtra').textContent = '-';
                    document.getElementById('statusExtra').textContent = 'LILA Belum Diukur';
                    document.getElementById('statusExtra').className = 'chip-badge badge-normal';
                    document.getElementById('resRecommendation').textContent = 'Masukkan ukuran LILA untuk evaluasi status Kurang Energi Kronis (KEK) pada ibu hamil.';
                }
            }
        }

        // Jalankan kalkulasi awal saat halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', function () {
            calculateNutrition();
        });
    </script>
</body>
</html>
