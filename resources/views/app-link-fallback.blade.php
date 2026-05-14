<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - Antropometri</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f7faf8;
            color: #17201b;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        main {
            width: min(420px, calc(100vw - 32px));
            background: #fff;
            border: 1px solid #e5ece8;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 18px 40px rgba(25, 68, 47, .08);
        }
        h1 {
            margin: 0 0 8px;
            font-size: 22px;
            line-height: 1.2;
        }
        p {
            margin: 0 0 18px;
            color: #66756d;
            line-height: 1.55;
        }
        a {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
        }
        .primary {
            background: #176b4d;
            color: #fff;
        }
        .secondary {
            margin-top: 10px;
            color: #176b4d;
            background: #eaf4ef;
        }
    </style>
</head>
<body>
    <main>
        <h1>{{ $title }}</h1>
        <p>{{ $message }} Jika aplikasi tidak terbuka otomatis, gunakan tombol di bawah.</p>
        <a class="primary" href="{{ $appUrl }}">Buka Aplikasi</a>
        <a class="secondary" href="{{ $playStoreUrl }}">Buka di Play Store</a>
    </main>
    <script>
        window.location.href = @json($appUrl);
        setTimeout(function () {
            window.location.href = @json($playStoreUrl);
        }, 1800);
    </script>
</body>
</html>
