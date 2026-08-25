<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Terjadi Kesalahan') - Sistem Informasi RT 09</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #F0F4F8 0%, #E2E8F0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #1A1A2E;
        }
        .error-card {
            background: #FFFFFF;
            border: 1px solid #CBD2D6;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 48, 135, 0.08), 0 8px 10px -6px rgba(0, 48, 135, 0.04);
            max-width: 32rem;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #003087, #009CDE, #019C34);
        }
        .code-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.35rem 1rem;
            border-radius: 9999px;
            font-weight: 800;
            font-size: 0.875rem;
            letter-spacing: 0.05em;
            background: #EBF3FF;
            color: #003087;
            border: 1px solid rgba(0, 156, 222, 0.3);
            margin-bottom: 1.25rem;
        }
        .error-code-large {
            font-size: 5rem;
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, #001B52 0%, #003087 50%, #009CDE 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.75rem;
            letter-spacing: -0.04em;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #001B52;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }
        .error-desc {
            font-size: 0.95rem;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        @media (min-width: 480px) {
            .btn-group {
                flex-direction: row;
                justify-content: center;
            }
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            gap: 0.5rem;
        }
        .btn-primary {
            background: #003087;
            color: #FFFFFF;
            border: 1px solid #003087;
            box-shadow: 0 4px 6px -1px rgba(0, 48, 135, 0.2);
        }
        .btn-primary:hover {
            background: #00246B;
            border-color: #00246B;
        }
        .btn-outline {
            background: #FFFFFF;
            color: #003087;
            border: 1px solid #CBD2D6;
        }
        .btn-outline:hover {
            background: #F0F4F8;
            border-color: #003087;
        }
        .footer-brand {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: #687173;
            font-weight: 600;
        }
        .brand-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #019C34;
        }
    </style>
</head>
<body>
    <main class="error-card">
        <div class="code-badge">
            ERROR @yield('code', '500')
        </div>

        <div class="error-code-large">
            @yield('code', '500')
        </div>

        <h1 class="error-title">
            @yield('title', 'Terjadi Kendala')
        </h1>

        <p class="error-desc">
            @yield('message', 'Halaman yang Anda cari tidak dapat diakses atau terjadi kendala pada sistem.')
        </p>

        <div class="btn-group">
            <a href="/" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Kembali ke Beranda
            </a>
            <button onclick="history.back()" class="btn btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
                Halaman Sebelumnya
            </button>
        </div>

        <div class="footer-brand">
            <span class="brand-dot"></span>
            <span>Sistem Informasi RT 09 & Karang Taruna</span>
        </div>
    </main>
</body>
</html>
