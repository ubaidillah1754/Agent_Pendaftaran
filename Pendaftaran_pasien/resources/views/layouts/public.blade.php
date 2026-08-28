<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - RS Islam Sakinah</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0F7B63;
            --primary-dark: #0A5644;
            --primary-light: #24977D;
            --primary-soft: #E8F4F0;
            --accent: #D4AF37;
            --bg: #F8FAF9;
            --surface: #FFFFFF;
            --ink: #1F2D27;
            --muted: #64766D;
            --border: #E2E8E5;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-public {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 16px 0;
            box-shadow: 0 4px 20px rgba(10, 86, 68, 0.15);
        }
        .navbar-brand {
            color: white !important;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
        }
        .nav-link:hover, .nav-link.active {
            color: white !important;
        }
        .main-container {
            flex: 1;
            padding: 40px 20px;
        }

        .footer-public {
            background: var(--surface);
            padding: 24px 0;
            text-align: center;
            border-top: 1px solid var(--border);
            color: var(--muted);
            font-size: 0.9rem;
        }
    </style>
    @stack('styles')
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-public navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('info.pendaftaran') }}">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="28" height="28">
                    <path d="M12 2L4 6V12C4 15.31 7.58 18.8 12 20C16.42 18.8 20 15.31 20 12V6L12 2Z" fill="rgba(255,255,255,.3)" stroke="#fff" stroke-width="1.5"/>
                    <path d="M11 8H13V11H16V13H13V16H11V13H8V11H11V8Z" fill="#fff"/>
                </svg>
                My Sakinah Agent
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('info.pendaftaran') ? 'active' : '' }}" href="{{ route('info.pendaftaran') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.jadwal') ? 'active' : '' }}" href="{{ route('public.jadwal') }}">Jadwal Dokter</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.cek') ? 'active' : '' }}" href="{{ route('public.cek') }}">Cek Pendaftaran</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <div class="main-container">
        @yield('content')
    </div>

    <footer class="footer-public">
        <div class="container">
            &copy; {{ date('Y') }} RS Islam Sakinah. Melayani dengan Sepenuh Hati.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
