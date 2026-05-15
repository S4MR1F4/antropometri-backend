@php
    $appName = 'Antropometri Indonesia';
    $canonicalUrl = rtrim(config('app.url'), '/') . '/privacy-policy';
    $playStoreUrl = config('app.mobile_play_store_url');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi | {{ $appName }}</title>
    <meta name="description" content="Kebijakan privasi aplikasi Antropometri Indonesia terkait data pasien, pengukuran, akun petugas, sinkronisasi offline-online, log aktivitas, dan hak pengguna.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" href="{{ asset('landing-assets/images/icon.png') }}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="Kebijakan Privasi {{ $appName }}">
    <meta property="og:description" content="Penjelasan ringkas penggunaan dan perlindungan data pada aplikasi Antropometri Indonesia.">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ asset('landing-assets/images/icon.png') }}">
    <style>
        :root {
            --green: #246b4a;
            --green-soft: #e9f5ef;
            --ink: #111827;
            --muted: #64748b;
            --line: #e5e7eb;
            --bg: #f8fafc;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background: var(--bg);
            line-height: 1.7;
        }

        a { color: var(--green); font-weight: 800; text-decoration: none; }

        .shell {
            width: min(860px, calc(100% - 32px));
            margin: 0 auto;
        }

        header {
            padding: 28px 0;
            border-bottom: 1px solid var(--line);
            background: rgba(248, 250, 252, 0.92);
            backdrop-filter: blur(16px);
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
            gap: 10px;
            color: var(--ink);
            font-weight: 900;
        }

        .brand img {
            width: 42px;
            height: 42px;
            border-radius: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 14px;
            border-radius: 10px;
            background: white;
            border: 1px solid var(--line);
            font-size: 14px;
        }

        main { padding: 46px 0 70px; }

        .hero {
            background: white;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.04);
            margin-bottom: 18px;
        }

        .eyebrow {
            color: var(--green);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        h1 {
            margin: 8px 0 10px;
            font-size: clamp(32px, 6vw, 48px);
            line-height: 1.05;
            letter-spacing: -0.03em;
        }

        .updated {
            display: inline-flex;
            padding: 7px 10px;
            border-radius: 999px;
            background: var(--green-soft);
            color: var(--green);
            font-size: 13px;
            font-weight: 800;
        }

        section {
            background: white;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 22px;
            margin-top: 12px;
        }

        h2 {
            margin: 0 0 10px;
            font-size: 22px;
            line-height: 1.2;
        }

        p, li { color: #475569; }
        ul { padding-left: 20px; }
        li { margin-bottom: 8px; }

        footer {
            padding: 28px 0;
            border-top: 1px solid var(--line);
            color: var(--muted);
            font-size: 14px;
        }

        @@media (max-width: 640px) {
            .brand { align-items: flex-start; flex-direction: column; }
            .hero, section { padding: 18px; }
        }
    </style>
</head>
<body>
    <header>
        <div class="shell brand">
            <a class="brand-main" href="/">
                <img src="{{ asset('landing-assets/images/icon.png') }}" alt="Logo {{ $appName }}">
                <span>{{ $appName }}</span>
            </a>
            <a class="btn" href="{{ $playStoreUrl }}" rel="noopener">Google Play</a>
        </div>
    </header>

    <main class="shell">
        <div class="hero">
            <div class="eyebrow">Kebijakan Privasi</div>
            <h1>Data kesehatan harus jelas cara pakainya.</h1>
            <p>Dokumen ini menjelaskan data yang dikumpulkan, alasan penggunaan, cara penyimpanan, dan pilihan pengguna pada aplikasi {{ $appName }}.</p>
            <span class="updated">Terakhir diperbarui: 15 Mei 2026</span>
        </div>

        <section>
            <h2>1. Data yang Dikumpulkan</h2>
            <ul>
                <li><strong>Data pasien/subjek:</strong> nama, tanggal lahir, jenis kelamin, alamat, nama orang tua, dan nomor telepon jika diisi.</li>
                <li><strong>Data pengukuran:</strong> berat badan, tinggi badan, lingkar kepala, lingkar perut, LiLA, status kehamilan, hasil hitung, rekomendasi, dan tanggal pemeriksaan.</li>
                <li><strong>Data akun petugas:</strong> nama, email, role, dan informasi akun yang diperlukan untuk login.</li>
                <li><strong>Data teknis:</strong> perangkat, versi aplikasi, status sinkronisasi, log login, log aktivitas, dan log error untuk membantu investigasi masalah.</li>
            </ul>
        </section>

        <section>
            <h2>2. Tujuan Penggunaan Data</h2>
            <p>Data digunakan untuk menghitung status gizi, menyimpan riwayat pemeriksaan, menampilkan dashboard, membuat laporan PDF/Excel, menjalankan sinkronisasi offline-online, menjaga keamanan akses, serta memperbaiki bug aplikasi.</p>
        </section>

        <section>
            <h2>3. Penyimpanan Offline dan Sinkronisasi</h2>
            <p>Aplikasi dapat menyimpan data sementara di perangkat saat offline. Ketika koneksi tersedia, data dikirim ke server melalui HTTPS. Data lokal digunakan agar petugas tetap dapat bekerja di area dengan jaringan tidak stabil.</p>
        </section>

        <section>
            <h2>4. Hak Akses dan Privasi Role</h2>
            <p>Petugas hanya dapat mengakses data sesuai hak akses yang berlaku. Admin dapat memantau data, pengguna, log, dan laporan untuk kebutuhan operasional. Aplikasi tidak menjual data pribadi atau data kesehatan kepada pihak ketiga.</p>
        </section>

        <section>
            <h2>5. Izin Perangkat</h2>
            <ul>
                <li><strong>Internet:</strong> untuk login, sinkronisasi, reset password, cek pembaruan, dan export online.</li>
                <li><strong>Penyimpanan/file:</strong> untuk menyimpan dan membagikan laporan PDF atau Excel.</li>
            </ul>
        </section>

        <section>
            <h2>6. Retensi dan Penghapusan Data</h2>
            <p>Data yang dihapus masuk proses soft delete terlebih dahulu. Jika tidak dipulihkan dalam masa retensi yang berlaku, data dapat dihapus permanen setelah backup database dibuat sesuai prosedur server.</p>
        </section>

        <section>
            <h2>7. Hak Pengguna</h2>
            <p>Pengguna dapat meminta akses, koreksi, salinan, atau penghapusan data melalui administrator instansi atau melalui email resmi aplikasi setelah identitas diverifikasi.</p>
        </section>

        <section>
            <h2>8. Kontak</h2>
            <p>Untuk pertanyaan privasi, penghapusan data, atau pelaporan masalah, hubungi <a href="mailto:antropometri@samrifa.com">antropometri@samrifa.com</a>.</p>
        </section>
    </main>

    <footer>
        <div class="shell">
            &copy; 2026 {{ $appName }}. <a href="/">Kembali ke beranda</a>.
        </div>
    </footer>
</body>
</html>
