@php
    $appName = 'Antropometri Indonesia';
    $canonicalUrl = rtrim(config('app.url'), '/') . '/terms';
    $playStoreUrl = config('app.mobile_play_store_url');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat dan Ketentuan Layanan | {{ $appName }}</title>
    <meta name="description" content="Syarat dan ketentuan resmi penggunaan aplikasi dan platform Antropometri Indonesia untuk Posyandu, Puskesmas, dan Tenaga Kesehatan.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" href="{{ asset('landing-assets/images/icon.png') }}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="Syarat &amp; Ketentuan Layanan {{ $appName }}">
    <meta property="og:description" content="Ketentuan operasional, standar klinis WHO/Permenkes, penafian medis, dan perlindungan data Antropometri Indonesia.">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ asset('landing-assets/images/icon.png') }}">

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --green: #246b4a;
            --green-dark: #1b5238;
            --green-soft: #e9f5ef;
            --ink: #111827;
            --muted: #64748b;
            --line: #e5e7eb;
            --bg: #f8fafc;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: var(--ink);
            background: var(--bg);
            line-height: 1.75;
        }

        a { color: var(--green); font-weight: 700; text-decoration: none; }
        a:hover { text-decoration: underline; }

        .shell {
            width: min(860px, calc(100% - 32px));
            margin: 0 auto;
        }

        header {
            padding: 20px 0;
            border-bottom: 1px solid var(--line);
            background: rgba(248, 250, 252, 0.95);
            backdrop-filter: blur(16px);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand-main {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--ink);
            font-weight: 800;
            font-size: 16px;
        }

        .brand img {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(36, 107, 74, 0.18);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .btn-subtle {
            background: white;
            border: 1px solid var(--line);
            color: var(--ink);
        }

        .btn-primary {
            background: var(--green);
            color: white;
            border: 1px solid var(--green);
            box-shadow: 0 2px 8px rgba(36, 107, 74, 0.25);
        }

        .btn-primary:hover {
            background: var(--green-dark);
            color: white;
            text-decoration: none;
        }

        main { padding: 46px 0 70px; }

        .hero {
            background: white;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 32px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.04);
            margin-bottom: 22px;
        }

        .eyebrow {
            color: var(--green);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        h1 {
            margin: 8px 0 12px;
            font-size: clamp(28px, 5vw, 42px);
            line-height: 1.15;
            letter-spacing: -0.03em;
            color: var(--ink);
        }

        .updated {
            display: inline-flex;
            padding: 7px 12px;
            border-radius: 999px;
            background: var(--green-soft);
            color: var(--green);
            font-size: 13px;
            font-weight: 800;
        }

        section {
            background: white;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 28px;
            margin-bottom: 18px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.03);
        }

        h2 {
            margin: 0 0 12px;
            font-size: 20px;
            color: var(--ink);
            letter-spacing: -0.02em;
        }

        p {
            margin: 0 0 14px;
            color: #334155;
            font-size: 14px;
        }

        p:last-child { margin-bottom: 0; }

        ul, ol {
            margin: 0 0 14px 20px;
            padding: 0;
            color: #334155;
            font-size: 14px;
        }

        li { margin-bottom: 8px; }

        .callout {
            background: var(--green-soft);
            border-left: 4px solid var(--green);
            padding: 16px;
            border-radius: 8px;
            margin: 16px 0;
            font-size: 13.5px;
            color: var(--green-dark);
            font-weight: 500;
        }

        footer {
            border-top: 1px solid var(--line);
            padding: 26px 0 40px;
            color: var(--muted);
            font-size: 13px;
            background: white;
        }

        .footer-links {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
    </style>
</head>
<body>
    <header>
        <div class="shell brand">
            <a href="/" class="brand-main">
                <img src="{{ asset('landing-assets/images/icon.png') }}" alt="Logo Antropometri">
                <span>{{ $appName }}</span>
            </a>
            <div class="header-actions">
                <a href="/privacy-policy" class="btn btn-subtle">Kebijakan Privasi</a>
                <a href="/#unduh" class="btn btn-primary">Unduh Aplikasi</a>
            </div>
        </div>
    </header>

    <main class="shell">
        <div class="hero">
            <div class="eyebrow">KETENTUAN LAYANAN &amp; INTEGRITAS KLINIS</div>
            <h1>Syarat dan Ketentuan Penggunaan</h1>
            <p>Ketentuan operasional, standar baku pengukuran antropometri, batasan tanggung jawab medis, dan tata kelola perlindungan data pada ekosistem aplikasi Antropometri Indonesia.</p>
            <div class="updated">Terakhir diperbarui: 25 Oktober 2026</div>
        </div>

        <section>
            <h2>1. Ketentuan Umum &amp; Definisi</h2>
            <p>Selamat datang di <strong>{{ $appName }}</strong>. Dengan mengunduh, memasang, mengakses, atau menggunakan aplikasi mobile maupun kalkulator berbasis web ini, Anda menyatakan telah membaca, memahami, dan menyetujui seluruh ketentuan yang tercantum dalam dokumen ini.</p>
            <ul>
                <li><strong>Aplikasi:</strong> Merujuk pada aplikasi mobile Antropometri untuk platform Android dan seluruh layanan sinkronisasi backend terkait.</li>
                <li><strong>Pengguna:</strong> Tenaga kesehatan, dokter, bidan desa, nutrisionis, kader Posyandu, atau institusi kesehatan yang terdaftar secara sah pada sistem.</li>
                <li><strong>Subjek:</strong> Pasien yang diperiksa, meliputi kategori Balita (0–60 bulan), Anak/Remaja (5–18 tahun), Dewasa (&gt;18 tahun), dan Ibu Hamil.</li>
                <li><strong>Data Antropometri:</strong> Parameter fisik hasil pengukuran klinis mencakup Berat Badan (BB), Tinggi/Panjang Badan (TB/PB), Lingkar Kepala (LK), Lingkar Lengan Atas (LiLA), dan Lingkar Perut (LP).</li>
            </ul>
        </section>

        <section>
            <h2>2. Standar Baku Klinis &amp; Validitas Perhitungan</h2>
            <p>Aplikasi ini mengimplementasikan algoritma perhitungan dan kurva baku pertumbuhan berdasarkan rujukan resmi:</p>
            <ul>
                <li><strong>Kategori Balita (0–60 Bulan):</strong> Berdasarkan <em>WHO Child Growth Standards 2006</em> dan <em>Permenkes RI No. 2 Tahun 2020</em> tentang Standar Antropometri Anak, mencakup 4 indeks: BB/U, TB/U, BB/TB, dan IMT/U dengan penyesuaian koreksi posisi ukur terlentang/berdiri.</li>
                <li><strong>Kategori Anak &amp; Remaja (5–18 Tahun):</strong> Berdasarkan kurva baku <em>WHO Reference 2007</em> untuk indeks IMT/U.</li>
                <li><strong>Kategori Dewasa (&gt;18 Tahun):</strong> Klasifikasi Indeks Massa Tubuh (IMT) standar WHO Asia-Pasifik dan Kementerian Kesehatan RI, disertai indikator risiko obesitas sentral via lingkar perut.</li>
                <li><strong>Kategori Ibu Hamil:</strong> Pemantauan penambahan berat badan gestasional dan skrining risiko Kurang Energi Kronis (KEK) berbasis ambang batas Lingkar Lengan Atas (LiLA &lt; 23.5 cm).</li>
            </ul>
        </section>

        <section>
            <h2>3. Penafian Medis (Medical Disclaimer)</h2>
            <div class="callout">
                <strong>PENTING:</strong> Sistem Antropometri Indonesia dirancang sebagai instrumen bantu skrining awal, pencatatan digital lapangan, dan pemantauan epidemiologis. Hasil analisis status gizi, z-score, dan rekomendasi nutrisi yang disajikan oleh sistem BUKAN merupakan pengganti diagnosis medis definitif atau anjuran tindakan klinis dari dokter atau dokter spesialis anak/gizi klinis.
            </div>
            <p>Setiap tindakan medis atau intervensi terapi terhadap subjek yang terindikasi gizi buruk, stunting kronis, atau penyakit penyerta wajib dirujuk ke fasilitas pelayanan kesehatan tingkat pertama (Puskesmas) atau rumah sakit rujukan.</p>
        </section>

        <section>
            <h2>4. Kewajiban &amp; Tanggung Jawab Pengguna</h2>
            <p>Dalam menjalankan pencatatan data antropometri di lapangan, pengguna berkewajiban:</p>
            <ul>
                <li><strong>Kalibrasi Alat Ukur:</strong> Memastikan alat timbang (timbangan digital tertera) dan alat ukur panjang/tinggi badan (stadiometer/infantometer) terkalibrasi secara berkala sesuai standar Kemenkes RI.</li>
                <li><strong>Akurasi Penginputan:</strong> Menginputkan data pengukuran dengan cermat dan objektif tanpa rekayasa angka atau pemalsuan rekam pemeriksaan.</li>
                <li><strong>Kerahasiaan Akun:</strong> Menjaga kerahasiaan kata sandi, token autentikasi Sanctum, dan tidak memindahtangankan akun petugas kepada pihak yang tidak berwenang.</li>
                <li><strong>Etika Medis:</strong> Menghormati privasi subjek pemeriksaan dan memperoleh persetujuan lisan atau tertulis dari subjek atau orang tua/wali sebelum pemeriksaan dilakukan.</li>
            </ul>
        </section>

        <section>
            <h2>5. Perlindungan Data Pribadi &amp; Keamanan Rekam Medis</h2>
            <p>Selaras dengan amanat <strong>Undang-Undang Pelindungan Data Pribadi (UU No. 27 Tahun 2022)</strong> dan standar tata kelola rekam medis:</p>
            <ul>
                <li>Data identitas pasien disimpan dengan enkripsi lokal tingkat militer (AES-256) pada database SQLite perangkat mobile saat mode offline.</li>
                <li>Proses sinkronisasi data ke cloud server berjalan melalui saluran terenkripsi Transport Layer Security (TLS 1.3 / HTTPS).</li>
                <li>Tampilan publik dashboard dan kalkulator web menerapkan kebijakan <em>Strict Zero PII</em> (tanpa nama, NIK, alamat lengkap, atau nomor telepon pasien).</li>
                <li>Pengguna berhak mengajukan permohonan koreksi atau penghapusan data rekam medis sesuai regulasi retensi yang berlaku melalui administrator instansi.</li>
            </ul>
        </section>

        <section>
            <h2>6. Integritas Standar Ekspor &amp; Impor Excel (Round-Trip)</h2>
            <p>Format spreadsheet Excel (.xlsx) yang diunduh dari aplikasi atau web resmi merupakan format template baku round-trip dua arah. Pengguna dilarang mengubah penamaan kolom sistem, struktur metadata tersembunyi, atau formula validasi agar proses re-impor data ke basis data offline-online tetap berjalan dengan integritas 100%.</p>
        </section>

        <section>
            <h2>7. Hak Kekayaan Intelektual</h2>
            <p>Seluruh arsitektur perangkat lunak, algoritma z-score, desain antarmuka, tata letak kartu pasien digital, logo, dan dokumentasi sistem merupakan hak kekayaan intelektual resmi Samrifa Studio Teknologi. Pengguna dilarang merekayasa balik (*reverse engineer*), mendekompilasi kode sumber, atau mendistribusikan ulang aplikasi di luar saluran distribusi resmi.</p>
        </section>

        <section>
            <h2>8. Pembaruan Ketentuan</h2>
            <p>Kami berhak memperbarui dokumen Syarat dan Ketentuan ini sewaktu-waktu guna menyesuaikan dengan perkembangan regulasi Kementerian Kesehatan RI, pembaruan standar WHO, atau peningkatan fitur keamanan. Tanggal pembaruan terakhir akan selalu dicantumkan pada bagian atas dokumen ini.</p>
        </section>

        <section>
            <h2>9. Kontak &amp; Dukungan Pengguna</h2>
            <p>Apabila Anda memiliki pertanyaan, kendala teknis operasional, atau saran pengembangan terkait syarat dan ketentuan ini, silakan hubungi tim kami melalui:</p>
            <ul>
                <li>Email Resmi: <a href="mailto:antropometri@samrifa.com">antropometri@samrifa.com</a></li>
                <li>Portal Dukungan: <a href="https://samrifa.com" target="_blank" rel="noopener">samrifa.com</a></li>
            </ul>
        </section>
    </main>

    <footer>
        <div class="shell footer-links">
            <div>&copy; 2026 {{ $appName }}. Hak Cipta Dilindungi Undang-Undang.</div>
            <div>
                <a href="/">Beranda</a> &bull;
                <a href="/privacy-policy">Kebijakan Privasi</a> &bull;
                <a href="/#unduh">Pusat Unduhan</a>
            </div>
        </div>
    </footer>
</body>
</html>
