<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - Antropometri Indonesia</title>
    <link rel="icon" type="image/png" href="{{ asset('landing-assets/images/icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('landing-assets/images/icon.png') }}">

    <!-- Google Fonts: Poppins (Identik dengan Aplikasi Mobile) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0D9488;
            --primary-dark: #0F766E;
            --primary-light: #F0FDFA;
            --slate-800: #1E293B;
            --slate-600: #475569;
            --slate-500: #64748B;
            --slate-200: #E2E8F0;
            --bg-subtle: #F8FAFC;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: var(--bg-subtle);
            color: var(--slate-800);
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            padding: 20px;
        }

        main {
            width: min(440px, 100%);
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 20px;
            padding: 30px 26px;
            box-shadow: 0 16px 36px rgba(13, 148, 136, 0.08), 0 4px 12px rgba(0, 0, 0, 0.03);
            text-align: left;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 22px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--slate-200);
        }

        .header-brand img {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.2);
        }

        .header-brand .brand-title {
            font-size: 17px;
            font-weight: 800;
            color: var(--primary-dark);
            line-height: 1.2;
        }

        .header-brand .brand-sub {
            font-size: 11.5px;
            color: var(--slate-500);
            font-weight: 500;
        }

        h1 {
            margin: 0 0 10px;
            font-size: 21px;
            font-weight: 800;
            color: var(--slate-800);
            line-height: 1.3;
        }

        p {
            margin: 0 0 24px;
            color: var(--slate-600);
            font-size: 13px;
            line-height: 1.6;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        a.btn {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(13, 148, 136, 0.28);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(13, 148, 136, 0.36);
        }

        .btn-secondary {
            color: var(--primary-dark);
            background: var(--primary-light);
            border: 1px solid rgba(13, 148, 136, 0.18);
        }

        .btn-secondary:hover {
            background: #E6FAF6;
        }
    </style>
</head>
<body>
    <main>
        <div class="header-brand">
            <img src="{{ asset('landing-assets/images/icon.png') }}" alt="Logo Antropometri">
            <div>
                <div class="brand-title">Antropometri</div>
                <div class="brand-sub">Sistem Pemantauan Status Gizi</div>
            </div>
        </div>
        <h1>{{ $title }}</h1>
        <p>{{ $message }} Jika aplikasi tidak terbuka otomatis, gunakan tombol di bawah untuk melanjutkan.</p>
        <div class="btn-group">
            <a class="btn btn-primary" href="{{ $appUrl }}">Buka Aplikasi</a>
            <a class="btn btn-secondary" href="{{ $playStoreUrl }}">Buka di Play Store</a>
        </div>
    </main>
    <script>
        window.location.href = @json($appUrl);
        setTimeout(function () {
            window.location.href = @json($playStoreUrl);
        }, 1800);
    </script>
</body>
</html>
