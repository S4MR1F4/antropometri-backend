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
            --green-soft: #e9f5ef;
            --ink: #111827;
            --muted: #64748b;
            --line: #e5e7eb;
            --bg: #f8fafc;
            --white: #ffffff;
            --amber: #f59e0b;
            --blue: #2563eb;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background: var(--bg);
            line-height: 1.6;
        }

        a { color: inherit; text-decoration: none; }

        .shell {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
        }

        .nav {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(248, 250, 252, 0.92);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.8);
        }

        .nav-inner {
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
        }

        .brand img {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(36, 107, 74, 0.18);
        }

        .brand small {
            display: block;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-top: -2px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 18px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 10px;
            min-height: 44px;
            padding: 0 18px;
            font-weight: 800;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: var(--green);
            color: white;
            box-shadow: 0 12px 28px rgba(36, 107, 74, 0.22);
        }

        .btn-secondary {
            background: white;
            color: var(--green);
            border-color: var(--line);
        }

        .hero {
            position: relative;
            min-height: calc(100vh - 72px);
            display: flex;
            align-items: center;
            overflow: hidden;
            background:
                linear-gradient(90deg, rgba(248, 250, 252, 0.98) 0%, rgba(248, 250, 252, 0.94) 42%, rgba(248, 250, 252, 0.72) 100%),
                url('{{ asset('landing-assets/images/hero.png') }}') right center / min(46vw, 520px) auto no-repeat;
        }

        .hero-content {
            max-width: 680px;
            padding: 72px 0;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--green-soft);
            color: var(--green);
            font-size: 12px;
            font-weight: 800;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
        }

        h1 {
            font-size: clamp(40px, 7vw, 76px);
            line-height: 0.98;
            margin: 22px 0 18px;
            letter-spacing: -0.03em;
        }

        .lead {
            max-width: 620px;
            font-size: clamp(16px, 2vw, 20px);
            color: #475569;
            margin: 0 0 28px;
        }

        .cta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 26px;
        }

        .trust {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 10px;
            background: white;
            border: 1px solid var(--line);
            color: #475569;
            font-size: 13px;
            font-weight: 700;
        }

        section { padding: 78px 0; }

        .section-title {
            max-width: 720px;
            margin-bottom: 28px;
        }

        .kicker {
            color: var(--green);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        h2 {
            margin: 8px 0 10px;
            font-size: clamp(28px, 4vw, 44px);
            line-height: 1.08;
            letter-spacing: -0.02em;
        }

        .muted { color: var(--muted); }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .card {
            background: white;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.04);
        }

        .card strong {
            display: block;
            margin-bottom: 6px;
            font-size: 16px;
        }

        .icon {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: var(--green-soft);
            color: var(--green);
            font-weight: 900;
            margin-bottom: 14px;
        }

        .workflow {
            background: white;
            border-block: 1px solid var(--line);
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .step-number {
            color: var(--green);
            font-weight: 900;
            font-size: 13px;
            letter-spacing: 0.12em;
        }

        .download {
            background: #0f172a;
            color: white;
            border-radius: 18px;
            padding: 28px;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 22px;
            align-items: center;
        }

        .download p { color: #cbd5e1; }

        .download img {
            width: 86px;
            height: 86px;
            border-radius: 18px;
            justify-self: end;
        }

        .faq details {
            background: white;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 16px 18px;
            margin-bottom: 10px;
        }

        .faq summary {
            cursor: pointer;
            font-weight: 800;
        }

        footer {
            padding: 34px 0;
            border-top: 1px solid var(--line);
            color: var(--muted);
            font-size: 14px;
        }

        .footer-inner {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        @@media (max-width: 860px) {
            .nav-links a:not(.btn) { display: none; }
            .hero {
                min-height: auto;
                background:
                    linear-gradient(180deg, rgba(248, 250, 252, 0.96), rgba(248, 250, 252, 0.96)),
                    url('{{ asset('landing-assets/images/hero.png') }}') right bottom / 300px auto no-repeat;
            }
            .grid, .steps, .download { grid-template-columns: 1fr; }
            .download img { justify-self: start; }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <div class="shell nav-inner">
            <a class="brand" href="{{ $canonicalUrl }}" aria-label="{{ $appName }}">
                <img src="{{ asset('landing-assets/images/icon.png') }}" alt="Logo {{ $appName }}">
                <span>{{ $appName }}<small>Pemantauan Gizi</small></span>
            </a>
            <div class="nav-links" aria-label="Navigasi utama">
                <a href="#fitur">Fitur</a>
                <a href="#alur">Alur</a>
                <a href="/privacy-policy">Privasi</a>
                <a class="btn btn-primary" href="{{ $playStoreUrl }}" rel="noopener">Download</a>
            </div>
        </div>
    </nav>

    <main>
        <section class="hero">
            <div class="shell hero-content">
                <span class="eyebrow"><span class="dot"></span> Offline-first untuk pendataan lapangan</span>
                <h1>Aplikasi Antropometri Indonesia</h1>
                <p class="lead">
                    Catat data pasien, hitung status gizi, simpan saat offline, lalu sinkronkan otomatis ketika internet kembali. Dibuat ringan untuk petugas lapangan, posyandu, puskesmas, dan tim kesehatan.
                </p>
                <div class="cta-row">
                    <a class="btn btn-primary" href="{{ $playStoreUrl }}" rel="noopener">Download di Google Play</a>
                    <a class="btn btn-secondary" href="#fitur">Lihat Fitur</a>
                </div>
                <div class="trust" aria-label="Keunggulan aplikasi">
                    <span class="chip">Offline & online</span>
                    <span class="chip">PDF dan Excel</span>
                    <span class="chip">Role admin/petugas</span>
                    <span class="chip">Log aktivitas</span>
                </div>
            </div>
        </section>

        <section id="fitur">
            <div class="shell">
                <div class="section-title">
                    <div class="kicker">Fokus Masalah</div>
                    <h2>Pendataan gizi tetap jalan walau jaringan tidak stabil.</h2>
                    <p class="muted">Aplikasi dirancang untuk pekerjaan nyata di lapangan: input cepat, data tidak mudah hilang, dan laporan tetap rapi saat dibutuhkan.</p>
                </div>
                <div class="grid">
                    <article class="card">
                        <div class="icon">01</div>
                        <strong>Input pemeriksaan</strong>
                        <p class="muted">Simpan berat, tinggi, LiLA, lingkar kepala, lingkar perut, kategori usia, dan hasil perhitungan.</p>
                    </article>
                    <article class="card">
                        <div class="icon">02</div>
                        <strong>Sinkronisasi aman</strong>
                        <p class="muted">Data offline masuk antrean lokal dan dikirim ke server saat koneksi tersedia.</p>
                    </article>
                    <article class="card">
                        <div class="icon">03</div>
                        <strong>Dashboard ringkas</strong>
                        <p class="muted">Pantau total pemeriksaan, status gizi, jenis kelamin, distribusi usia, dan aktivitas terbaru.</p>
                    </article>
                    <article class="card">
                        <div class="icon">04</div>
                        <strong>Export siap pakai</strong>
                        <p class="muted">Cetak laporan PDF atau Excel berdasarkan range, bulan, tahun, dan kategori pasien.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="alur" class="workflow">
            <div class="shell">
                <div class="section-title">
                    <div class="kicker">Cara Kerja</div>
                    <h2>Sederhana untuk petugas, tetap terkontrol untuk admin.</h2>
                </div>
                <div class="steps">
                    <article class="card">
                        <div class="step-number">LANGKAH 1</div>
                        <h3>Login dan pilih pasien</h3>
                        <p class="muted">Petugas dapat bekerja dari data yang sudah ada di perangkat atau membuat pasien baru.</p>
                    </article>
                    <article class="card">
                        <div class="step-number">LANGKAH 2</div>
                        <h3>Input pengukuran</h3>
                        <p class="muted">Hasil perhitungan dan rekomendasi tampil langsung setelah data disimpan.</p>
                    </article>
                    <article class="card">
                        <div class="step-number">LANGKAH 3</div>
                        <h3>Sync dan laporkan</h3>
                        <p class="muted">Admin dapat melihat data sesuai role, memantau log, dan membuat export saat dibutuhkan.</p>
                    </article>
                </div>
            </div>
        </section>

        <section>
            <div class="shell">
                <div class="download">
                    <div>
                        <div class="kicker" style="color:#86efac">Download Android</div>
                        <h2>Mulai pakai dari Google Play.</h2>
                        <p>Aplikasi akan memakai halaman Play Store resmi untuk update, instalasi, dan distribusi versi production.</p>
                        <div class="cta-row" style="margin-bottom:0">
                            <a class="btn btn-primary" href="{{ $playStoreUrl }}" rel="noopener">Buka Google Play</a>
                            <a class="btn btn-secondary" href="/privacy-policy">Kebijakan Privasi</a>
                        </div>
                    </div>
                    <img src="{{ asset('landing-assets/images/icon.png') }}" alt="Ikon aplikasi Antropometri">
                </div>
            </div>
        </section>

        <section class="faq">
            <div class="shell">
                <div class="section-title">
                    <div class="kicker">FAQ</div>
                    <h2>Pertanyaan singkat sebelum instal.</h2>
                </div>
                <details>
                    <summary>Apakah aplikasi bisa dipakai saat offline?</summary>
                    <p class="muted">Bisa. Data tersimpan di perangkat dan masuk antrean sinkronisasi sampai koneksi tersedia.</p>
                </details>
                <details>
                    <summary>Apakah data petugas biasa terlihat oleh petugas lain?</summary>
                    <p class="muted">Data dibatasi berdasarkan role. Admin dapat memantau seluruh data, sedangkan petugas hanya melihat data sesuai hak aksesnya.</p>
                </details>
                <details>
                    <summary>Apakah laporan bisa diekspor?</summary>
                    <p class="muted">Bisa. Laporan PDF dan Excel dapat dibuat berdasarkan filter periode dan kategori yang dipilih.</p>
                </details>
            </div>
        </section>
    </main>

    <footer>
        <div class="shell footer-inner">
            <span>&copy; 2026 {{ $appName }}. Dikembangkan oleh Samrifa Studio Teknologi.</span>
            <span><a href="/privacy-policy">Kebijakan Privasi</a> · <a href="{{ $playStoreUrl }}">Google Play</a></span>
        </div>
    </footer>
</body>
</html>
