<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — RS Islam</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary:      #0B6B4F;   /* hijau utama */
            --primary-dark: #063D2C;
            --primary-light:#12885F;
            --accent:       #C9A227;   /* emas */
            --accent-light: #E8C766;
            --accent-dark:  #9C7A1A;
            --tile:         #0E7490;   /* biru tosca — aksen sekunder */
            --tile-light:   #0891B2;
            --bg:           #F6F5EF;
            --sidebar-w:    260px;
            --card-radius:  18px;
            --card-shadow:  0 4px 24px rgba(11,107,79,.08);
        }

        * { box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: #2A332E; }

        .rs-star-pattern {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64'%3E%3Cg fill='none' stroke='%23ffffff' stroke-width='1' opacity='0.08'%3E%3Crect x='16' y='16' width='32' height='32' transform='rotate(45 32 32)'/%3E%3Crect x='16' y='16' width='32' height='32'/%3E%3C/g%3E%3C/svg%3E");
        }

        /* ── SIDEBAR ──────────────────────────────────────────── */
        #sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary) 100%);
            position: fixed; top: 0; left: 0; z-index: 1000;
            display: flex; flex-direction: column;
            transition: transform .3s ease;
            box-shadow: 4px 0 20px rgba(0,0,0,.15);
        }
        .sidebar-brand {
            position: relative; overflow: hidden;
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(232,199,102,.25);
        }
        .sidebar-brand::before {
            content: "";
            position: absolute; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64'%3E%3Cg fill='none' stroke='%23E8C766' stroke-width='1' opacity='0.12'%3E%3Crect x='16' y='16' width='32' height='32' transform='rotate(45 32 32)'/%3E%3Crect x='16' y='16' width='32' height='32'/%3E%3C/g%3E%3C/svg%3E");
        }
        .sidebar-brand > * { position: relative; z-index: 1; }
        .sidebar-brand h5 { color: #fff; font-family: 'Amiri', serif; font-weight: 700; font-size: 1.05rem; margin: 0; line-height: 1.4; }
        .sidebar-brand small { color: rgba(255,255,255,.55); font-size: .72rem; }
        .sidebar-brand .brand-icon {
            width: 42px; height: 42px;
            border-radius: 50% 50% 10px 10px;
            background: var(--accent);
            border: 1px solid rgba(255,255,255,.4);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 12px; font-size: 1.3rem; color: #fff;
        }

        .sidebar-nav { padding: 16px 12px; flex: 1; overflow-y: auto; }
        .sidebar-label {
            font-size: .65rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .1em; color: rgba(255,255,255,.4);
            padding: 12px 8px 6px; margin-top: 4px;
        }
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,.72); border-radius: 12px;
            padding: 10px 14px; font-size: .85rem; font-weight: 500;
            display: flex; align-items: center; gap: 10px;
            transition: all .2s; margin-bottom: 2px;
        }
        .sidebar-nav .nav-link:hover { background: rgba(255,255,255,.1); color: #fff; transform: translateX(3px); }
        .sidebar-nav .nav-link.active { background: var(--accent); color: #fff; box-shadow: 0 4px 12px rgba(201,162,39,.4); }
        .sidebar-nav .nav-link i { font-size: 1rem; width: 20px; text-align: center; }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(232,199,102,.25);
        }
        .sidebar-footer .user-card {
            background: rgba(255,255,255,.08); border-radius: 12px; padding: 12px;
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-footer .avatar {
            width: 36px; height: 36px;
            border-radius: 50% 50% 8px 8px;
            background: var(--accent); display: flex; align-items: center;
            justify-content: center; color: #fff; font-weight: 700; font-size: .85rem; flex-shrink: 0;
        }
        .sidebar-footer .user-name { color: #fff; font-size: .82rem; font-weight: 600; }
        .sidebar-footer .user-role { color: rgba(255,255,255,.5); font-size: .7rem; }

        /* ── MAIN CONTENT ────────────────────────────────────── */
        #main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex; flex-direction: column;
            transition: margin .3s ease;
        }

        /* ── TOP NAVBAR ──────────────────────────────────────── */
        #topbar {
            background: #fff;
            border-bottom: 1px solid #ece6d6;
            padding: 0 28px;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 900;
            box-shadow: 0 2px 12px rgba(11,107,79,.06);
        }
        .page-title { font-weight: 700; font-size: 1.05rem; color: var(--primary); margin: 0; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .topbar-btn {
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--bg); border: none;
            display: flex; align-items: center; justify-content: center;
            color: #64766D; cursor: pointer; transition: all .2s; font-size: .95rem;
        }
        .topbar-btn:hover { background: var(--primary); color: #fff; }
        .sidebar-toggle { display: none; }

        /* ── CARDS ───────────────────────────────────────────── */
        .card {
            border: none; border-radius: var(--card-radius);
            box-shadow: var(--card-shadow); background: #fff;
            transition: transform .2s, box-shadow .2s;
        }
        .card:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(11,107,79,.12); }
        .card-header {
            background: transparent; border-bottom: 1px solid #f1efe4;
            padding: 18px 24px; font-weight: 700; color: var(--primary);
            border-radius: var(--card-radius) var(--card-radius) 0 0 !important;
        }
        .card-body { padding: 24px; }

        /* Stat Cards */
        .stat-card {
            border-radius: var(--card-radius); padding: 22px;
            display: flex; align-items: center; gap: 18px;
            transition: transform .2s, box-shadow .2s; cursor: default;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,.15); }
        .stat-icon {
            width: 56px; height: 56px;
            border-radius: 50% 50% 14px 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; flex-shrink: 0;
        }
        .stat-label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; opacity: .8; }
        .stat-value { font-size: 2rem; font-weight: 800; line-height: 1; }
        .stat-sub { font-size: .78rem; opacity: .7; margin-top: 2px; }

        /* ── BUTTONS ─────────────────────────────────────────── */
        .btn { border-radius: 10px; font-weight: 600; font-size: .85rem; padding: 8px 18px; transition: all .2s; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); transform: translateY(-1px); }
        .btn-accent, .btn-warning { background: var(--accent); border-color: var(--accent); color: #fff; }
        .btn-accent:hover { background: var(--accent-dark); color: #fff; transform: translateY(-1px); }
        .btn-sm { padding: 5px 12px; font-size: .78rem; border-radius: 8px; }
        .btn-icon { width: 34px; height: 34px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }

        /* ── TABLES ──────────────────────────────────────────── */
        .table-card { border-radius: var(--card-radius); overflow: hidden; box-shadow: var(--card-shadow); }
        .table { margin: 0; }
        .table thead th {
            background: var(--primary); color: #fff; font-size: .78rem;
            font-weight: 600; text-transform: uppercase; letter-spacing: .04em;
            padding: 14px 16px; border: none;
        }
        .table tbody td { padding: 13px 16px; border-bottom: 1px solid #f1efe4; vertical-align: middle; font-size: .875rem; }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr:hover { background: #f6faf7; }

        /* ── BADGES ──────────────────────────────────────────── */
        .badge { border-radius: 8px; font-weight: 600; font-size: .72rem; padding: 4px 10px; }

        /* ── FORMS ───────────────────────────────────────────── */
        .form-control, .form-select {
            border-radius: 10px; border: 1.5px solid #e5e0d0;
            padding: 10px 14px; font-size: .875rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light); box-shadow: 0 0 0 3px rgba(11,107,79,.12);
        }
        .form-label { font-weight: 600; font-size: .82rem; color: #475d52; margin-bottom: 6px; }
        .form-section { background: #f6faf7; border-radius: 14px; padding: 20px; margin-bottom: 20px; border: 1px solid #e2ede4; }
        .form-section-title { font-weight: 700; color: var(--primary); font-size: .9rem; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid var(--accent); display: inline-block; }

        /* ── ALERTS ──────────────────────────────────────────── */
        .alert { border: none; border-radius: 12px; padding: 14px 18px; font-size: .875rem; }
        .alert-success { background: #ecfdf5; color: #065f46; border-left: 4px solid #10b981; }
        .alert-danger  { background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; }
        .alert-warning { background: #fffbeb; color: #92400e; border-left: 4px solid #f59e0b; }
        .alert-info    { background: #eff9fb; color: #0c5c73; border-left: 4px solid var(--tile); }

        /* ── MISC ────────────────────────────────────────────── */
        .breadcrumb { background: none; padding: 0; font-size: .8rem; }
        .breadcrumb-item a { color: var(--primary-light); text-decoration: none; }
        .breadcrumb-item.active { color: #64766D; }
        .content-area { padding: 28px; flex: 1; }

        /* ── STATUS BADGES ──────────────────────────────────── */
        .badge-menunggu  { background: #fef3c7; color: #92400e; }
        .badge-dipanggil { background: #e0f2f6; color: #0c5c73; }
        .badge-selesai   { background: #d1fae5; color: #065f46; }
        .badge-batal     { background: #fee2e2; color: #991b1b; }

        /* ── ANTRIAN NUMBER ─────────────────────────────────── */
        .nomor-antrian {
            font-size: 2.5rem; font-weight: 900; color: var(--primary);
            letter-spacing: .05em; line-height: 1;
        }

        /* ── RESPONSIVE ─────────────────────────────────────── */
        @media (max-width: 991px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main-content { margin-left: 0; }
            .sidebar-toggle { display: flex !important; }
            .content-area { padding: 16px; }
            #topbar { padding: 0 16px; }
        }
        @media (max-width: 576px) {
            .stat-card { padding: 16px; }
            .stat-value { font-size: 1.6rem; }
            .card-body { padding: 16px; }
        }

        /* ── OVERLAY ─────────────────────────────────────────── */
        #sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(6,61,44,.45); z-index: 999;
        }
        #sidebar-overlay.show { display: block; }

        /* ── ANIMATIONS ──────────────────────────────────────── */
        @keyframes fadeInUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:none; } }
        .fade-in { animation: fadeInUp .35s ease both; }
        .fade-in-delay-1 { animation-delay: .05s; }
        .fade-in-delay-2 { animation-delay: .10s; }
        .fade-in-delay-3 { animation-delay: .15s; }
        .fade-in-delay-4 { animation-delay: .20s; }

        /* ── SCROLLBAR ───────────────────────────────────────── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c9c2a8; border-radius: 10px; }
    </style>
    @stack('styles')
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div id="sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- SIDEBAR -->
<nav id="sidebar" class="rs-star-pattern">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-moon-stars-fill"></i></div>
        <h5>RS Islam</h5>
        <small>Sistem Pendaftaran Rawat Jalan</small>
    </div>

    <div class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="sidebar-label">Pendaftaran</div>
        <a href="{{ route('registrations.index') }}" class="nav-link {{ request()->routeIs('registrations.*') ? 'active' : '' }}">
            <i class="bi bi-clipboard2-plus"></i> Pendaftaran
        </a>
        <a href="{{ route('patients.index') }}" class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Data Pasien
        </a>
        <a href="{{ route('antrian.index') }}" class="nav-link {{ request()->routeIs('antrian.*') ? 'active' : '' }}">
            <i class="bi bi-list-ol"></i> Monitor Antrian
        </a>

        @if(auth()->user()->isAdmin())
        <div class="sidebar-label">Master Data</div>
        <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
            <i class="bi bi-building-fill-cross"></i> Data Poli
        </a>
        <a href="{{ route('doctors.index') }}" class="nav-link {{ request()->routeIs('doctors.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i> Data Dokter
        </a>
        <a href="{{ route('doctor-schedules.index') }}" class="nav-link {{ request()->routeIs('doctor-schedules.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-week"></i> Jadwal Praktik
        </a>
        @endif
    </div>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="flex-1 overflow-hidden">
                <div class="user-name text-truncate">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="ms-auto">
                @csrf
                <button type="submit" title="Logout" style="background:none;border:none;color:rgba(255,255,255,.5);cursor:pointer;padding:4px;font-size:1rem;">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div id="main-content">
    <!-- TOPBAR -->
    <header id="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="topbar-btn sidebar-toggle" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <p class="page-title mb-0">@yield('page-title', 'Dashboard')</p>
                @hasSection('breadcrumb')
                <nav aria-label="breadcrumb" class="mt-1">
                    <ol class="breadcrumb">@yield('breadcrumb')</ol>
                </nav>
                @endif
            </div>
        </div>
        <div class="topbar-right">
            <div class="d-none d-md-flex align-items-center gap-2 px-3 py-1 rounded-3" style="background:var(--bg);font-size:.8rem;">
                <i class="bi bi-calendar3" style="color:var(--primary);"></i>
                <span class="text-secondary">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
            @if(auth()->user()->isAdmin() || auth()->user()->isPetugas())
            <a href="{{ route('registrations.create') }}" class="btn btn-accent btn-sm d-none d-md-flex align-items-center gap-1">
                <i class="bi bi-plus-circle"></i> Daftar Pasien
            </a>
            @endif
        </div>
    </header>

    <!-- FLASH MESSAGES -->
    <div class="px-4 pt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- PAGE CONTENT -->
    <main class="content-area">
        @yield('content')
    </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
        document.getElementById('sidebar-overlay').classList.toggle('show');
    }
    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        });
    }, 5000);
</script>
@stack('scripts')
</body>
</html>