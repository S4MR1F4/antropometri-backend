@php
    $appName = 'Antropometri Indonesia';
    $playStoreUrl = config('app.mobile_play_store_url');
    $canonicalUrl = rtrim(config('app.url'), '/');
    $description = 'Aplikasi Android offline-online untuk pencatatan pengukuran antropometri, status gizi, laporan PDF/Excel, dan sinkronisasi data lapangan.';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appName }} | Aplikasi Pencatatan Gizi Offline-Online</title>
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="antropometri, aplikasi gizi, pemantauan gizi, z-score, posyandu, puskesmas, aplikasi antropometri android, laporan antropometri">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="author" content="Samrifa Studio Teknologi">
    <meta name="geo.region" content="ID">
    <meta name="geo.placename" content="Indonesia">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" href="{{ asset('landing-assets/images/icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $appName }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ asset('landing-assets/images/hero.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $appName }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ asset('landing-assets/images/hero.png') }}">

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "SoftwareApplication",
        "name": "{{ $appName }}",
        "applicationCategory": "HealthApplication",
        "operatingSystem": "Android",
        "description": "{{ $description }}",
        "offers": {
            "@@type": "Offer",
            "price": "0",
            "priceCurrency": "IDR"
        },
        "downloadUrl": "{{ $playStoreUrl }}",
        "publisher": {
            "@@type": "Organization",
            "name": "Samrifa Studio Teknologi",
            "url": "https://samrifa.com"
        }
    }
    </script>
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "FAQPage",
        "mainEntity": [
            {
                "@@type": "Question",
                "name": "Apakah aplikasi bisa dipakai tanpa internet?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Bisa. Data dapat dicatat di perangkat saat offline, lalu disinkronkan ke server ketika koneksi tersedia."
                }
            },
            {
                "@@type": "Question",
                "name": "Data apa yang dapat diekspor?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Aplikasi mendukung laporan PDF dan Excel untuk data pengukuran sesuai periode dan kategori yang dipilih."
                }
            }
        ]
    }
    </script>

    <style>
        :root {
            --green: #246b4a;
            --green-strong: #185438;
            --mint: #e8f4ee;
            --sky: #eaf2ff;
            --amber: #fff5df;
            --ink: #121826;
            --body: #475569;
            --muted: #728095;
            --line: #e4e8ef;
            --paper: #ffffff;
            --page: #f7faf8;
            --dark: #0f172a;
            --radius: 14px;
            --shadow: 0 18px 44px rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: var(--page);
            color: var(--ink);
            font-family: Manrope, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            line-height: 1.65;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .shell {
            width: min(1140px, calc(100% - 40px));
            margin: 0 auto;
        }

        .nav {
            position: fixed;
            inset: 0 0 auto 0;
            z-index: 30;
            background: rgba(247, 250, 248, 0.86);
            border-bottom: 1px solid rgba(228, 232, 239, 0.8);
            backdrop-filter: blur(18px);
        }

        .nav-inner {
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .brand img {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            box-shadow: 0 12px 28px rgba(36, 107, 74, 0.22);
        }

        .brand-name {
            font-family: Poppins, Manrope, sans-serif;
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -0.02em;
            white-space: nowrap;
        }

        .brand-sub {
            display: block;
            margin-top: -2px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 24px;
            color: #536174;
            font-size: 14px;
            font-weight: 800;
        }

        .btn {
            display: inline-flex;
            min-height: 46px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            border-radius: 12px;
            padding: 0 18px;
            border: 1px solid transparent;
            font-size: 14px;
            font-weight: 900;
            transition: transform 180ms ease, box-shadow 180ms ease, background 180ms ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            color: #ffffff;
            background: var(--green);
            box-shadow: 0 16px 32px rgba(36, 107, 74, 0.26);
        }

        .btn-primary:hover {
            background: var(--green-strong);
        }

        .btn-light {
            color: var(--green);
            background: rgba(255, 255, 255, 0.92);
            border-color: rgba(255, 255, 255, 0.48);
        }

        .hero {
            min-height: 92svh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            isolation: isolate;
            background:
                linear-gradient(90deg, rgba(9, 25, 18, 0.92) 0%, rgba(13, 42, 29, 0.86) 45%, rgba(13, 42, 29, 0.45) 100%),
                url('{{ asset('landing-assets/images/hero.png') }}') right center / min(54vw, 680px) auto no-repeat,
                #0d2a1d;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: auto 0 0;
            height: 26%;
            background: linear-gradient(180deg, rgba(247, 250, 248, 0), var(--page));
            z-index: -1;
        }

        .hero-content {
            width: min(700px, 100%);
            padding: 138px 0 86px;
            color: #ffffff;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            color: #dff9ea;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #64eaa0;
            box-shadow: 0 0 0 6px rgba(100, 234, 160, 0.16);
        }

        h1, h2, h3 {
            font-family: Poppins, Manrope, sans-serif;
            letter-spacing: -0.035em;
        }

        h1 {
            margin: 22px 0 18px;
            font-size: clamp(42px, 7vw, 82px);
            line-height: 0.98;
            max-width: 780px;
        }

        .lead {
            max-width: 650px;
            margin: 0 0 30px;
            color: rgba(255, 255, 255, 0.82);
            font-size: clamp(16px, 2vw, 20px);
            font-weight: 600;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 28px;
        }

        .proof-row {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            max-width: 650px;
        }

        .proof {
            padding: 14px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.11);
            border: 1px solid rgba(255, 255, 255, 0.16);
        }

        .proof strong {
            display: block;
            color: #ffffff;
            font-size: 18px;
            line-height: 1;
        }

        .proof span {
            display: block;
            margin-top: 6px;
            color: rgba(255, 255, 255, 0.68);
            font-size: 12px;
            font-weight: 800;
        }

        section {
            padding: 86px 0;
        }

        .intro-strip {
            margin-top: -38px;
            position: relative;
            z-index: 5;
            padding: 0;
        }

        .strip-panel {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr 0.9fr;
            gap: 1px;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: var(--line);
            box-shadow: var(--shadow);
        }

        .strip-item {
            background: var(--paper);
            padding: 22px;
        }

        .strip-item span {
            color: var(--green);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .strip-item strong {
            display: block;
            margin-top: 6px;
            font-size: 19px;
            line-height: 1.25;
        }

        .section-head {
            display: grid;
            grid-template-columns: minmax(0, 0.95fr) minmax(260px, 0.55fr);
            align-items: end;
            gap: 28px;
            margin-bottom: 30px;
        }

        .kicker {
            color: var(--green);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }

        h2 {
            margin: 8px 0 0;
            font-size: clamp(30px, 4.7vw, 52px);
            line-height: 1.06;
        }

        .section-head p,
        .muted {
            color: var(--body);
            margin: 0;
            font-weight: 600;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .card {
            min-height: 100%;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            background: var(--paper);
            padding: 20px;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.045);
        }

        .card h3 {
            margin: 0 0 8px;
            font-size: 20px;
            line-height: 1.18;
        }

        .card p {
            color: var(--body);
            margin: 0;
            font-weight: 600;
        }

        .mark {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            margin-bottom: 18px;
            border-radius: 12px;
            background: var(--mint);
            color: var(--green);
            font-size: 13px;
            font-weight: 900;
        }

        .mark.blue {
            background: var(--sky);
            color: #2563eb;
        }

        .mark.amber {
            background: var(--amber);
            color: #b7791f;
        }

        .workflow {
            background: #ffffff;
            border-block: 1px solid var(--line);
        }

        .flow-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .flow-card {
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 22px;
            background: #fbfdfb;
        }

        .flow-card small {
            color: var(--green);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.12em;
        }

        .flow-card h3 {
            margin: 12px 0 8px;
            font-size: 22px;
            line-height: 1.18;
        }

        .download-panel {
            display: grid;
            grid-template-columns: 1fr 280px;
            align-items: center;
            gap: 28px;
            padding: 34px;
            border-radius: 22px;
            color: white;
            background:
                linear-gradient(90deg, rgba(15, 23, 42, 0.97), rgba(24, 84, 56, 0.94)),
                url('{{ asset('landing-assets/images/security.png') }}') right center / 420px auto no-repeat;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
        }

        .download-panel h2 {
            margin-top: 6px;
        }

        .download-panel p {
            color: rgba(255, 255, 255, 0.76);
            font-size: 16px;
            font-weight: 600;
            max-width: 660px;
        }

        .app-badge {
            justify-self: end;
            display: grid;
            place-items: center;
            width: 150px;
            height: 150px;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .app-badge img {
            width: 92px;
            height: 92px;
            border-radius: 22px;
        }

        .faq-grid {
            display: grid;
            grid-template-columns: 0.75fr 1.25fr;
            gap: 24px;
        }

        .faq-list {
            display: grid;
            gap: 10px;
        }

        details {
            border: 1px solid var(--line);
            border-radius: var(--radius);
            background: var(--paper);
            padding: 18px 20px;
        }

        summary {
            cursor: pointer;
            font-family: Poppins, Manrope, sans-serif;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        details p {
            color: var(--body);
            margin: 12px 0 0;
            font-weight: 600;
        }

        footer {
            padding: 34px 0;
            color: var(--muted);
            border-top: 1px solid var(--line);
            font-size: 14px;
            font-weight: 700;
        }

        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .footer-inner a {
            color: var(--green);
            font-weight: 900;
        }

        @@media (max-width: 980px) {
            .hero {
                background:
                    linear-gradient(90deg, rgba(9, 25, 18, 0.94), rgba(13, 42, 29, 0.82)),
                    url('{{ asset('landing-assets/images/hero.png') }}') right bottom / 430px auto no-repeat,
                    #0d2a1d;
            }

            .proof-row,
            .strip-panel,
            .section-head,
            .feature-grid,
            .flow-grid,
            .download-panel,
            .faq-grid {
                grid-template-columns: 1fr;
            }

            .app-badge {
                justify-self: start;
                width: 118px;
                height: 118px;
                border-radius: 22px;
            }

            .app-badge img {
                width: 76px;
                height: 76px;
            }
        }

        @@media (max-width: 700px) {
            .shell {
                width: min(100% - 28px, 1140px);
            }

            .nav-inner {
                min-height: 66px;
            }

            .brand img {
                width: 38px;
                height: 38px;
                border-radius: 10px;
            }

            .brand-name {
                font-size: 14px;
            }

            .brand-sub {
                font-size: 9px;
            }

            .nav-links {
                gap: 10px;
            }

            .nav-links a:not(.btn) {
                display: none;
            }

            .nav-links .btn {
                min-height: 40px;
                padding: 0 12px;
                font-size: 12px;
                border-radius: 10px;
            }

            .hero {
                min-height: 88svh;
                background:
                    linear-gradient(180deg, rgba(9, 25, 18, 0.94), rgba(13, 42, 29, 0.88)),
                    url('{{ asset('landing-assets/images/hero.png') }}') center bottom / 330px auto no-repeat,
                    #0d2a1d;
            }

            .hero-content {
                padding: 112px 0 228px;
            }

            h1 {
                font-size: clamp(38px, 12vw, 54px);
            }

            .lead {
                font-size: 15px;
            }

            .hero-actions .btn {
                width: 100%;
            }

            section {
                padding: 64px 0;
            }

            .intro-strip {
                margin-top: -28px;
            }

            .strip-item,
            .card,
            .flow-card,
            details {
                padding: 18px;
            }

            h2 {
                font-size: clamp(28px, 9vw, 38px);
            }

            .download-panel {
                padding: 24px;
                border-radius: 18px;
                background:
                    linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(24, 84, 56, 0.95)),
                    url('{{ asset('landing-assets/images/security.png') }}') right bottom / 280px auto no-repeat;
            }
        }
    </style>
    <script defer src="https://umami.samrifa.com/script.js" data-website-id="7883a719-cea1-4b99-ab74-df78c2775aef"></script>
</head>
<body>
    <nav class="nav" aria-label="Navigasi utama">
        <div class="shell nav-inner">
            <a class="brand" href="{{ $canonicalUrl }}" aria-label="{{ $appName }}">
                <img src="{{ asset('landing-assets/images/icon.png') }}" alt="Logo {{ $appName }}">
                <span class="brand-name">{{ $appName }}<span class="brand-sub">Pemantauan Gizi</span></span>
            </a>
            <div class="nav-links">
                <a href="#fitur">Fitur</a>
                <a href="#alur">Alur</a>
                <a href="/privacy-policy">Privasi</a>
                <a class="btn btn-primary" href="{{ $playStoreUrl }}" rel="noopener">Download</a>
            </div>
        </div>
    </nav>

    <main>
        <section class="hero">
            <div class="shell">
                <div class="hero-content">
                    <span class="eyebrow"><span class="pulse"></span> Offline-first untuk petugas lapangan</span>
                    <h1>Catat antropometri tanpa takut data hilang.</h1>
                    <p class="lead">
                        Aplikasi Android untuk input pemeriksaan, hitung status gizi, export laporan, dan sinkronisasi otomatis saat koneksi kembali tersedia.
                    </p>
                    <div class="hero-actions">
                        <a class="btn btn-primary" href="{{ $playStoreUrl }}" rel="noopener">Download di Google Play</a>
                        <a class="btn btn-light" href="#fitur">Lihat Solusi</a>
                    </div>
                    <div class="proof-row" aria-label="Ringkasan kemampuan aplikasi">
                        <div class="proof"><strong>Offline</strong><span>Tetap input saat sinyal buruk</span></div>
                        <div class="proof"><strong>PDF/XLSX</strong><span>Laporan siap pakai</span></div>
                        <div class="proof"><strong>Admin</strong><span>Role, log, dan monitoring</span></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="intro-strip" aria-label="Masalah utama yang diselesaikan">
            <div class="shell">
                <div class="strip-panel">
                    <div class="strip-item">
                        <span>Masalah</span>
                        <strong>Input lapangan sering terhambat jaringan.</strong>
                    </div>
                    <div class="strip-item">
                        <span>Solusi</span>
                        <strong>Data masuk lokal dulu, sync berjalan di belakang.</strong>
                    </div>
                    <div class="strip-item">
                        <span>Output</span>
                        <strong>Dashboard, riwayat, PDF, dan Excel tetap rapi.</strong>
                    </div>
                </div>
            </div>
        </section>

        <section id="fitur">
            <div class="shell">
                <div class="section-head">
                    <div>
                        <div class="kicker">Fitur Utama</div>
                        <h2>Dibuat untuk kerja cepat, bukan sekadar tampil bagus.</h2>
                    </div>
                    <p>Aplikasi memprioritaskan data yang aman, input yang ringan, dan tampilan yang mudah dibaca saat dipakai di perangkat Android lapangan.</p>
                </div>

                <div class="feature-grid">
                    <article class="card">
                        <div class="mark">01</div>
                        <h3>Input pemeriksaan</h3>
                        <p>Catat berat, tinggi, LiLA, lingkar kepala, lingkar perut, status hamil, dan data pasien.</p>
                    </article>
                    <article class="card">
                        <div class="mark blue">02</div>
                        <h3>Sync offline-online</h3>
                        <p>Data yang dibuat tanpa internet masuk antrean dan dikirim saat koneksi sudah stabil.</p>
                    </article>
                    <article class="card">
                        <div class="mark amber">03</div>
                        <h3>Dashboard ringkas</h3>
                        <p>Admin dan petugas dapat melihat total, status gizi, gender, usia, dan aktivitas terbaru.</p>
                    </article>
                    <article class="card">
                        <div class="mark">04</div>
                        <h3>Laporan siap kirim</h3>
                        <p>Export PDF dan Excel berdasarkan range, bulan, tahun, kategori, dan hak akses pengguna.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="alur" class="workflow">
            <div class="shell">
                <div class="section-head">
                    <div>
                        <div class="kicker">Alur Pemakaian</div>
                        <h2>Dari input sampai laporan dalam alur yang pendek.</h2>
                    </div>
                    <p>Dirancang agar petugas tidak perlu berpindah-pindah alat saat melakukan pemeriksaan dan membuat rekap.</p>
                </div>

                <div class="flow-grid">
                    <article class="flow-card">
                        <small>LANGKAH 1</small>
                        <h3>Pilih atau buat pasien</h3>
                        <p class="muted">Data pasien dapat dibuka dari cache perangkat atau dibuat baru saat offline.</p>
                    </article>
                    <article class="flow-card">
                        <small>LANGKAH 2</small>
                        <h3>Isi pengukuran</h3>
                        <p class="muted">Hasil status gizi tampil langsung setelah pemeriksaan dihitung dan disimpan.</p>
                    </article>
                    <article class="flow-card">
                        <small>LANGKAH 3</small>
                        <h3>Sync dan export</h3>
                        <p class="muted">Data terkirim ke server, admin memantau log, lalu laporan dapat diekspor.</p>
                    </article>
                </div>
            </div>
        </section>

        <section>
            <div class="shell">
                <div class="download-panel">
                    <div>
                        <div class="kicker" style="color:#9df3bf">Download Android</div>
                        <h2>Mulai pakai dari Google Play.</h2>
                        <p>Aplikasi memakai halaman Play Store resmi untuk instalasi, pembaruan versi, dan distribusi production. Jika update tersedia, pengguna diarahkan ke kanal resmi.</p>
                        <div class="hero-actions" style="margin-bottom:0">
                            <a class="btn btn-primary" href="{{ $playStoreUrl }}" rel="noopener">Buka Google Play</a>
                            <a class="btn btn-light" href="/privacy-policy">Kebijakan Privasi</a>
                        </div>
                    </div>
                    <div class="app-badge">
                        <img src="{{ asset('landing-assets/images/icon.png') }}" alt="Ikon aplikasi {{ $appName }}">
                    </div>
                </div>
            </div>
        </section>

        <section class="faq">
            <div class="shell faq-grid">
                <div>
                    <div class="kicker">FAQ</div>
                    <h2>Pertanyaan sebelum instal.</h2>
                    <p class="muted">Ringkas, langsung ke hal yang sering ditanyakan petugas dan admin.</p>
                </div>
                <div class="faq-list">
                    <details>
                        <summary>Apakah aplikasi bisa dipakai saat offline?</summary>
                        <p>Bisa. Data tersimpan di perangkat dan masuk antrean sinkronisasi sampai koneksi tersedia.</p>
                    </details>
                    <details>
                        <summary>Apakah petugas bisa melihat data petugas lain?</summary>
                        <p>Petugas dibatasi sesuai hak aksesnya. Admin dapat melihat seluruh data untuk kebutuhan monitoring dan operasional.</p>
                    </details>
                    <details>
                        <summary>Apakah laporan bisa langsung diekspor?</summary>
                        <p>Bisa. Export tersedia dalam PDF dan Excel dengan filter periode, kategori, bulan, tahun, dan range tanggal.</p>
                    </details>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="shell footer-inner">
            <span>&copy; 2026 {{ $appName }}. Dikembangkan oleh Samrifa Studio Teknologi.</span>
            <span><a href="/privacy-policy">Kebijakan Privasi</a> / <a href="{{ $playStoreUrl }}">Google Play</a></span>
        </div>
    </footer>
</body>
</html>
