<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Pendaftaran Rawat Jalan RSI Sakinah — kelola pendaftaran pasien, antrian, dan jadwal praktik dokter.">
    <link rel="icon" type="image/png" sizes="225x225" href="{{ asset('favicon.png') }}?v=4">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}?v=4">

    <title>@yield('title', 'Dashboard') — RS Islam Sakinah</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Spectral:wght@700;800&display=swap"
        rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    

    <style>
        :root {
            --primary:      #0F7B63;
            --primary-dark: #0A5644;
            --primary-light:#15966F;
            --primary-soft: #E6F6F0;
            --accent:       #B8912E;
            --accent-soft:  #FBF6E9;
            --tile:         #0E7490;
            --tile-soft:    #E7F4F6;
            --bg:           #F5F8F7;
            --surface:      #FFFFFF;
            --ink:          #16211D;
            --muted:        #6C7A76;
            --border:       #E7EDEA;
            --sidebar-w: 272px;
            --card-radius: 16px;
            --card-shadow: 0 1px 2px rgba(16,24,32,.04), 0 8px 20px -12px rgba(16,24,32,.10);
            --focus-ring: 0 0 0 3px rgba(15, 123, 99, .35);
            /* bentuk "arch" — sudut atas membulat, bawah landai — dipakai konsisten
               di seluruh ikon berulang (stat, nav, topbar) sebagai identitas visual */
            --arch-lg: 23px 23px 6px 6px;
            --arch-sm: 13px 13px 4px 4px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--ink);
        }

        /* ── SKIP LINK (aksesibilitas keyboard) ─────────────── */
        .skip-link {
            position: absolute;
            left: -9999px;
            top: 0;
            z-index: 2000;
            background: var(--primary-dark);
            color: #fff;
            padding: 10px 18px;
            border-radius: 0 0 10px 0;
            font-weight: 600;
            font-size: .85rem;
        }

        .skip-link:focus {
            left: 0;
        }

        /* ── FOCUS VISIBLE (semua elemen interaktif) ────────── */
        a:focus-visible,
        button:focus-visible,
        .nav-link:focus-visible,
        .form-control:focus-visible,
        .form-select:focus-visible {
            outline: none;
            box-shadow: var(--focus-ring);
        }

        /* ── SIDEBAR ──────────────────────────────────────────── */
        #sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(190deg, var(--primary) 0%, var(--primary-dark) 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform .3s ease;
            overflow: hidden;
        }

        .sidebar-brand {
            margin: 16px 14px 8px;
            padding: 14px 14px;
            border-radius: 14px;
            background: var(--surface);
            box-shadow: 0 6px 16px -8px rgba(6, 30, 24, .35);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-brand .brand-icon {
            width: 40px;
            height: 40px;
            flex-shrink: 0;
        }

        .sidebar-brand h5 {
            color: var(--primary-dark);
            font-weight: 800;
            font-size: .92rem;
            margin: 0;
            line-height: 1.3;
        }

        .sidebar-brand small {
            color: var(--muted);
            font-size: .66rem;
            font-weight: 500;
        }

        .sidebar-nav {
            padding: 8px 14px;
            flex: 1;
            min-height : 0;
            overflow-y: auto;
        }

        .sidebar-label {
            font-size: .64rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255, 255, 255, .55);
            padding: 14px 10px 6px;
        }

        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, .82);
            border-radius: 11px;
            padding: 9px 12px;
            font-size: .84rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background .2s, color .2s;
            margin-bottom: 3px;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255, 255, 255, .1);
            color: #fff;
        }

        .sidebar-nav .nav-link.active {
            background: var(--surface);
            color: var(--primary-dark);
            font-weight: 700;
            box-shadow: 0 4px 10px -4px rgba(6, 30, 24, .3);
        }

        .sidebar-nav .nav-link .nav-icon-box {
            width: 26px;
            height: 26px;
            border-radius: var(--arch-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: rgba(255, 255, 255, .14);
            font-size: .9rem;
        }

        .sidebar-nav .nav-link.active .nav-icon-box {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .sidebar-illustration {
            padding: 6px 22px 4px;
            opacity: .9;
        }

        .sidebar-footer {
    padding: 8px 14px 16px;
    margin-top: auto;
    position: relative;
    z-index: 10;
}

        .sidebar-footer .user-card {
            background: var(--surface);
            border-radius: 12px;
            padding: 9px 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 6px 16px -8px rgba(6, 30, 24, .3);
        }

        .sidebar-footer .avatar {
            width: 32px;
            height: 32px;
            border-radius: var(--arch-sm);
            background: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: .78rem;
            flex-shrink: 0;
        }

        .sidebar-footer .user-name {
            color: var(--ink);
            font-size: .8rem;
            font-weight: 700;
        }

        .sidebar-footer .user-role {
            color: var(--muted);
            font-size: .68rem;
        }

        .sidebar-footer .logout-btn {
            background: var(--bg);
            border: none;
            color: var(--muted);
            cursor: pointer;
            width: 30px;
            height: 30px;
            flex-shrink: 0;
            font-size: .9rem;
            border-radius: 8px;
            transition: color .2s, background .2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-footer .logout-btn:hover {
            color: #fff;
            background: var(--primary);
        }

        /* ── MAIN CONTENT ────────────────────────────────────── */
        #main-content {
            margin-left: var(--sidebar-w);
            /* overflow-x:clip memotong konten yang melebihi batas #main-content
               tanpa membuat Block Formatting Context (aman untuk sticky elements).
               Ini mencegah horizontal scroll di level body akibat elemen di dalam
               (tabel lebar, datatable, dll) tanpa mengganggu halaman lain. */
            overflow-x: clip;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin .3s ease;
        }

        /* ── TOP NAVBAR ──────────────────────────────────────── */
        #topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .page-title {
            font-weight: 800;
            font-size: 1.15rem;
            color: var(--ink);
            margin: 0;
        }

        .page-subtitle {
            font-size: .8rem;
            color: var(--muted);
            margin: 2px 0 0;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Badge akreditasi — penanda kredibilitas institusi, konsisten dengan
           strip akreditasi di halaman login */
        .topbar-accred {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--accent-soft);
            border: 1px solid rgba(184, 145, 46, .3);
            border-radius: 999px;
            padding: 7px 14px;
            font-size: .72rem;
            font-weight: 700;
            color: #7A5E17;
        }

        .topbar-accred i {
            color: var(--accent);
        }

        .topbar-date {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 8px 16px;
            font-size: .8rem;
            color: var(--ink);
            font-weight: 500;
        }

        .topbar-date i {
            color: var(--primary);
        }

        .topbar-btn {
            width: 38px;
            height: 38px;
            border-radius: var(--arch-sm);
            background: var(--bg);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            cursor: pointer;
            transition: all .2s;
            font-size: .95rem;
        }

        .topbar-btn:hover {
            background: var(--primary);
            color: #fff;
        }

        .sidebar-toggle {
            display: none;
        }

        /* ── CARDS ───────────────────────────────────────────── */
        .card {
            border: 1px solid var(--border);
            border-radius: var(--card-radius);
            box-shadow: var(--card-shadow);
            background: var(--surface);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border);
            padding: 16px 20px;
            font-weight: 700;
            font-size: .95rem;
            color: var(--ink);
            border-radius: var(--card-radius) var(--card-radius) 0 0 !important;
        }

        .card-body {
            padding: 20px;
        }

        /* Stat Cards */
        .stat-card {
            border-radius: var(--card-radius);
            border: 1px solid var(--border);
            background: var(--surface);
            box-shadow: var(--card-shadow);
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--arch-lg);
            background: var(--primary-soft);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .stat-label {
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--muted);
        }

        .stat-value {
            font-family: 'Spectral', serif;
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1.2;
            color: var(--ink);
        }

        .stat-sub {
            font-size: .72rem;
            color: var(--muted);
            margin-top: 2px;
        }

        /* ── BUTTONS ─────────────────────────────────────────── */
        .btn {
            border-radius: 10px;
            font-weight: 600;
            font-size: .85rem;
            padding: 8px 18px;
            transition: all .2s;
        }

        .btn-primary,
        .btn-brand {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .btn-primary:hover,
        .btn-brand:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            color: #fff;
        }

        .btn-accent,
        .btn-warning {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .btn-accent:hover {
            background: #9C7A1A;
            color: #fff;
        }

        .btn-sm {
            padding: 5px 12px;
            font-size: .78rem;
            border-radius: 8px;
        }

        .btn-icon {
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* ── TABLES ──────────────────────────────────────────── */
        .table-card {
            border-radius: var(--card-radius);
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: var(--card-shadow);
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background: var(--primary-soft);
            color: var(--primary-dark);
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: 14px 16px;
            border: none;
        }

        .table tbody td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            font-size: .875rem;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background: var(--bg);
        }

        /* ── BADGES ──────────────────────────────────────────── */
        .badge {
            border-radius: 8px;
            font-weight: 600;
            font-size: .72rem;
            padding: 4px 10px;
        }

        /* ── FORMS ───────────────────────────────────────────── */
        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1.5px solid var(--border);
            padding: 10px 14px;
            font-size: .875rem;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(15, 123, 99, .12);
        }

        .form-label {
            font-weight: 600;
            font-size: .82rem;
            color: var(--ink);
            margin-bottom: 6px;
        }

        .form-section {
            background: var(--bg);
            border-radius: var(--card-radius);
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid var(--border);
        }

        .form-section-title {
            font-weight: 700;
            color: var(--primary-dark);
            font-size: .9rem;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent);
            display: inline-block;
        }

        /* ── ALERTS ──────────────────────────────────────────── */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: .875rem;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .alert-warning {
            background: #fffbeb;
            color: #92400e;
            border-left: 4px solid #f59e0b;
        }

        .alert-info {
            background: var(--tile-soft);
            color: #0c5c73;
            border-left: 4px solid var(--tile);
        }

        /* ── MISC ────────────────────────────────────────────── */
        .breadcrumb {
            background: none;
            padding: 0;
            font-size: .8rem;
        }

        .breadcrumb-item a {
            color: var(--primary-light);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--muted);
        }

        .content-area {
            padding: 28px;
            flex: 1;
        }

        /* ── STATUS BADGES ──────────────────────────────────── */
        .badge-menunggu {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-diperiksa {
            background: var(--tile-soft);
            color: #0c5c73;
        }

        .badge-selesai {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-batal {
            background: #fee2e2;
            color: #991b1b;
        }

        /* ── ANTRIAN NUMBER ─────────────────────────────────── */
        .nomor-antrian {
            font-family: 'Spectral', serif;
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--primary);
            letter-spacing: .05em;
            line-height: 1;
        }

        /* ── RESPONSIVE ─────────────────────────────────────── */
        @media (max-width: 991px) {
            #sidebar {
                transform: translateX(-100%);
            }

            #sidebar.show {
                transform: translateX(0);
            }

            #main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: flex !important;
            }

            .content-area {
                padding: 16px;
            }

            #topbar {
                padding: 0 16px;
            }

            .topbar-accred {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .stat-card {
                padding: 16px;
            }

            .stat-value {
                font-size: 1.4rem;
            }

            .card-body {
                padding: 16px;
            }
        }

        /* ── OVERLAY ─────────────────────────────────────────── */
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(6, 61, 44, .45);
            z-index: 999;
        }

        #sidebar-overlay.show {
            display: block;
        }

        /* ── ANIMATIONS ──────────────────────────────────────── */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .fade-in {
            animation: fadeInUp .3s ease both;
        }

        .fade-in-delay-1 {
            animation-delay: .05s;
        }

        .fade-in-delay-2 {
            animation-delay: .10s;
        }

        .fade-in-delay-3 {
            animation-delay: .15s;
        }

        .fade-in-delay-4 {
            animation-delay: .20s;
        }

        /* Hormati preferensi pengguna yang sensitif terhadap animasi */
        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: .001ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .001ms !important;
            }
        }

        /* ── SCROLLBAR ───────────────────────────────────────── */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #C7CDD6;
            border-radius: 10px;
        }

        /* ── CUSTOM SIMPLE-DATATABLES STYLE OVERRIDES ────────── */
        .datatable-wrapper {
            padding: 10px 0;
            /* Jaga agar wrapper tidak memperlebar container saat kolom banyak */
            width: 100%;
            min-width: 0;
        }
        .datatable-top {
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid var(--border);
        }
        .datatable-bottom {
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--border);
            font-size: 0.8rem;
            color: var(--muted);
        }
        .datatable-search {
            float: none !important;
            margin-left: auto;
        }
        .datatable-search .datatable-input {
            border-radius: 10px;
            border: 1.5px solid var(--border);
            padding: 8px 14px;
            font-size: 0.85rem;
            font-family: inherit;
            width: 250px;
            transition: all 0.2s;
        }
        .datatable-search .datatable-input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(15, 123, 99, 0.12);
        }
        .datatable-dropdown {
            float: none !important;
            font-size: 0.85rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .datatable-selector {
            border-radius: 8px;
            border: 1.5px solid var(--border);
            padding: 6px 28px 6px 12px;
            font-size: 0.85rem;
            font-family: inherit;
            margin: 0 4px;
            background-color: #fff;
        }
        .datatable-selector:focus {
            outline: none;
            border-color: var(--primary-light);
        }
        .datatable-table {
            border-collapse: collapse;
        }
        /* Pastikan scroll tabel terjadi DI DALAM .table-card, bukan di level halaman */
        .datatable-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .datatable-pagination ul {
            margin: 0;
            padding-left: 0;
            display: flex;
            gap: 4px;
        }
        .datatable-pagination li {
            list-style: none;
        }
        .datatable-pagination a {
            display: block;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid var(--border);
            color: var(--primary);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s;
            background-color: #fff;
        }
        .datatable-pagination a:hover {
            background: var(--primary-soft);
            border-color: var(--primary-light);
            color: var(--primary-dark);
        }
        .datatable-pagination .active a,
        .datatable-pagination .active a:focus,
        .datatable-pagination .active a:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }
        .datatable-pagination .disabled a,
        .datatable-pagination .disabled a:hover {
            color: #c7cdd6;
            border-color: var(--border);
            background: transparent;
            cursor: not-allowed;
        }
        .datatable-info {
            margin: 0;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    @stack('styles')
</head>

<body>

    <!-- Skip link untuk pengguna keyboard/screen reader -->
    <a href="#main" class="skip-link">Langsung ke konten utama</a>

    <!-- Sidebar Overlay (mobile) -->
    <div id="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- SIDEBAR -->
    <nav id="sidebar" aria-label="Navigasi utama">
        <div class="sidebar-brand">
            <svg class="brand-icon" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <circle cx="20" cy="10" r="6" fill="#0F7B63" />
                <circle cx="20" cy="30" r="6" fill="#0E7490" />
                <circle cx="10" cy="20" r="6" fill="#B8912E" />
                <circle cx="30" cy="20" r="6" fill="#15966F" />
                <circle cx="20" cy="20" r="6.5" fill="#FFFFFF" stroke="#0A5644" stroke-width="1" />
                <path d="M20 16.5v7M16.5 20h7" stroke="#0F7B63" stroke-width="1.6" stroke-linecap="round" />
            </svg>
            <div>
                <h5>My Sakinah Agent</h5>
                <small>Sistem Pendaftaran Rawat Jalan</small>
            </div>
        </div>

        <div class="sidebar-nav">
            <a href="{{ route('dashboard') }}"
                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                <span class="nav-icon-box"><i class="bi bi-speedometer2" aria-hidden="true"></i></span> Dashboard
            </a>

            <a href="{{ route('info.pendaftaran') }}" target="_blank"
                class="nav-link {{ request()->routeIs('info.pendaftaran') ? 'active' : '' }}">
                <span class="nav-icon-box"><i class="bi bi-window-sidebar" aria-hidden="true"></i></span> Portal Publik
            </a>

            <div class="sidebar-label">Pendaftaran</div>
            @php
                $pendaftaranRoute = auth()->user()->isAdmin() ? route('registrations.index') : route('registrations.create');
            @endphp
            <a href="{{ $pendaftaranRoute }}"
                class="nav-link {{ request()->routeIs('registrations.*') && !request()->routeIs('registrations.riwayat') ? 'active' : '' }}"
                @if(request()->routeIs('registrations.*') && !request()->routeIs('registrations.riwayat')) aria-current="page" @endif>
                <span class="nav-icon-box"><i class="bi bi-clipboard2-plus" aria-hidden="true"></i></span> Pendaftaran
            </a>


            @if(auth()->user()->isPetugas())
            <a href="{{ route('registrations.riwayat') }}"
                class="nav-link {{ request()->routeIs('registrations.riwayat') ? 'active' : '' }}"
                @if(request()->routeIs('registrations.riwayat')) aria-current="page" @endif>
                <span class="nav-icon-box"><i class="bi bi-clock-history" aria-hidden="true"></i></span> Riwayat Saya
            </a>
            @endif

            {{-- Menu Data Pasien & Monitor Antrian disembunyikan (di luar scope utama aplikasi) --}}
           <!--
<a href="#"
    class="nav-link disabled-link"
    style="pointer-events: none; opacity: 0.6; cursor: not-allowed;">
    <span class="nav-icon-box"><i class="bi bi-people" aria-hidden="true"></i></span> Data Pasien
</a>

<a href="#"
    class="nav-link disabled-link"
    style="pointer-events: none; opacity: 0.6; cursor: not-allowed;">
    <span class="nav-icon-box"><i class="bi bi-list-ol" aria-hidden="true"></i></span> Monitor Antrian
</a>
-->

            @if(auth()->user()->isPetugas())
                <div class="sidebar-label">Poin & Reward</div>
                <a href="{{ route('points.index') }}"
                    class="nav-link {{ request()->routeIs('points.index', 'points.riwayat') ? 'active' : '' }}"
                    @if(request()->routeIs('points.index', 'points.riwayat')) aria-current="page" @endif>
                    <span class="nav-icon-box"><i class="bi bi-star-fill" aria-hidden="true"></i></span> Poin Saya
                </a>
                <a href="{{ route('points.katalog') }}"
                    class="nav-link {{ request()->routeIs('points.katalog') ? 'active' : '' }}"
                    @if(request()->routeIs('points.katalog')) aria-current="page" @endif>
                    <span class="nav-icon-box"><i class="bi bi-gift-fill" aria-hidden="true"></i></span> Katalog Reward
                </a>
                <a href="{{ route('points.redemptions.index') }}"
                    class="nav-link {{ request()->routeIs('points.redemptions.*') ? 'active' : '' }}"
                    @if(request()->routeIs('points.redemptions.*')) aria-current="page" @endif>
                    <span class="nav-icon-box"><i class="bi bi-clock-history" aria-hidden="true"></i></span> Riwayat Penukaran
                </a>
            @endif

            @if(auth()->user()->isAdmin())
                <div class="sidebar-label">Poin & Reward</div>
                <a href="{{ route('admin.redemptions.index') }}"
                    class="nav-link {{ request()->routeIs('admin.redemptions.*') ? 'active' : '' }}"
                    @if(request()->routeIs('admin.redemptions.*')) aria-current="page" @endif>
                    <span class="nav-icon-box"><i class="bi bi-gift" aria-hidden="true"></i></span> Penukaran Reward
                    @php $pendingCount = \App\Models\PointRedemption::where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="ms-auto badge rounded-pill" style="background:#EF4444; color:#fff; font-size:.65rem; padding:3px 7px;">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.points.adjustment.index') }}"
                    class="nav-link {{ request()->routeIs('admin.points.adjustment.*') ? 'active' : '' }}"
                    @if(request()->routeIs('admin.points.adjustment.*')) aria-current="page" @endif>
                    <span class="nav-icon-box"><i class="bi bi-sliders2" aria-hidden="true"></i></span> Penyesuaian Poin
                </a>
                <a href="{{ route('admin.reports.index') }}"
                    class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                    @if(request()->routeIs('admin.reports.*')) aria-current="page" @endif>
                    <span class="nav-icon-box"><i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i></span> Laporan Poin
                </a>

                <div class="sidebar-label">Master Data</div>
                <a href="{{ route('admin.merchandises.index') }}"
                    class="nav-link {{ request()->routeIs('admin.merchandises.*') ? 'active' : '' }}"
                    @if(request()->routeIs('admin.merchandises.*')) aria-current="page" @endif>
                    <span class="nav-icon-box"><i class="bi bi-box-seam" aria-hidden="true"></i></span> Master Hadiah
                </a>
                <a href="{{ route('departments.index') }}"
                    class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}"
                    @if(request()->routeIs('departments.*')) aria-current="page" @endif>
                    <span class="nav-icon-box"><i class="bi bi-person-badge" aria-hidden="true"></i></span> Data Poli
                </a>

                <a href="{{ route('doctors.index') }}"
                    class="nav-link {{ request()->routeIs('doctors.*') ? 'active' : '' }}"
                    @if(request()->routeIs('doctors.*')) aria-current="page" @endif>
                    <span class="nav-icon-box"><i class="bi bi-person-badge" aria-hidden="true"></i></span> Data Dokter
                </a>
                <a href="{{ route('doctor-schedules.index') }}"
                    class="nav-link {{ request()->routeIs('doctor-schedules.*') ? 'active' : '' }}"
                    @if(request()->routeIs('doctor-schedules.*')) aria-current="page" @endif>
                    <span class="nav-icon-box"><i class="bi bi-calendar-week" aria-hidden="true"></i></span> Jadwal Praktik
                </a>
            @endif
        </div>

        <div class="sidebar-illustration" aria-hidden="true">
            <svg viewBox="0 0 220 110" xmlns="http://www.w3.org/2000/svg" width="100%">
                <ellipse cx="60" cy="102" rx="34" ry="6" fill="rgba(255,255,255,.10)" />
                <rect x="18" y="70" width="10" height="32" rx="2" fill="rgba(255,255,255,.22)" />
                <circle cx="23" cy="62" r="16" fill="rgba(255,255,255,.16)" />
                <rect x="70" y="30" width="80" height="72" rx="4" fill="rgba(255,255,255,.22)" />
                <rect x="82" y="42" width="10" height="10" rx="1.5" fill="rgba(255,255,255,.55)" />
                <rect x="100" y="42" width="10" height="10" rx="1.5" fill="rgba(255,255,255,.55)" />
                <rect x="118" y="42" width="10" height="10" rx="1.5" fill="rgba(255,255,255,.55)" />
                <rect x="136" y="42" width="10" height="10" rx="1.5" fill="rgba(255,255,255,.55)" />
                <rect x="82" y="60" width="10" height="10" rx="1.5" fill="rgba(255,255,255,.55)" />
                <rect x="100" y="60" width="10" height="10" rx="1.5" fill="rgba(255,255,255,.55)" />
                <rect x="118" y="60" width="10" height="10" rx="1.5" fill="rgba(255,255,255,.55)" />
                <rect x="136" y="60" width="10" height="10" rx="1.5" fill="rgba(255,255,255,.55)" />
                <rect x="102" y="78" width="16" height="24" rx="1.5" fill="rgba(255,255,255,.4)" />
                <rect x="108" y="16" width="4" height="18" fill="rgba(255,255,255,.5)" />
                <rect x="102" y="20" width="16" height="4" fill="rgba(255,255,255,.5)" />
                <ellipse cx="180" cy="98" rx="26" ry="6" fill="rgba(255,255,255,.10)" />
                <rect x="172" y="66" width="9" height="32" rx="2" fill="rgba(255,255,255,.2)" />
                <circle cx="176" cy="58" r="14" fill="rgba(255,255,255,.14)" />
            </svg>
        </div>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="avatar" aria-hidden="true">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="flex-1 overflow-hidden">
                    <div class="user-name text-truncate">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="ms-auto">
                    @csrf
                    <button type="submit" class="logout-btn" aria-label="Keluar dari akun">
                        <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
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
                <button class="topbar-btn sidebar-toggle" onclick="toggleSidebar()" aria-label="Buka/tutup menu navigasi">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
                <div>
                    <p class="page-title mb-0">@yield('page-title', 'Dashboard')</p>
                    @hasSection('page-subtitle')
                        <p class="page-subtitle">@yield('page-subtitle')</p>
                    @endif
                    @hasSection('breadcrumb')
                        <nav aria-label="Breadcrumb" class="mt-1">
                            <ol class="breadcrumb">@yield('breadcrumb')</ol>
                        </nav>
                    @endif
                </div>
            </div>
            <div class="topbar-right">
                <div class="topbar-accred d-none d-lg-flex">
                    <i class="bi bi-patch-check-fill" aria-hidden="true"></i>
                    <span>Akreditasi Paripurna KARS</span>
                </div>
                <div class="topbar-date d-none d-md-flex">
                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                    <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
                @if(auth()->user()->isAdmin() || auth()->user()->isPetugas())
                    <a href="{{ route('registrations.create') }}"
                        class="btn btn-brand btn-sm d-none d-md-flex align-items-center gap-1">
                        <i class="bi bi-plus-circle" aria-hidden="true"></i> Daftar Pasien
                    </a>
                @endif
            </div>
        </header>

        <!-- FLASH MESSAGES -->
        <div class="px-4 pt-3" role="status" aria-live="polite">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" data-autohide="true">
                    <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup notifikasi"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup notifikasi"></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert" data-autohide="true">
                    <i class="bi bi-exclamation-circle-fill me-2" aria-hidden="true"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup notifikasi"></button>
                </div>
            @endif
        </div>

        <!-- PAGE CONTENT -->
        <main id="main" class="content-area">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Simple-DataTables JS -->
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/umd/simple-datatables.js" type="text/javascript"></script>
    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebar-overlay').classList.toggle('show');
        }

        // Auto-dismiss HANYA alert success/warning setelah 6 detik.
        // Alert error (danger) sengaja TIDAK auto-dismiss agar pesan penting
        // (misal: gagal simpan data pasien) tidak hilang sebelum sempat dibaca.
        document.querySelectorAll('.alert[data-autohide="true"]').forEach((el) => {
            let timer = setTimeout(() => closeAlert(el), 6000);

            // Jeda timer saat kursor di atas alert, lanjut saat kursor keluar
            el.addEventListener('mouseenter', () => clearTimeout(timer));
            el.addEventListener('mouseleave', () => {
                timer = setTimeout(() => closeAlert(el), 3000);
            });
        });

        function closeAlert(el) {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        }

        // Auto-initialize components
        document.addEventListener('DOMContentLoaded', () => {
            // simple-datatables
            document.querySelectorAll('.datatable').forEach(table => {
                new simpleDatatables.DataTable(table, {
                    labels: {
                        placeholder: "Cari...",
                        searchTitle: "Cari dalam tabel",
                        perPage: "data per halaman",
                        noRows: "Tidak ada data ditemukan",
                        info: "Menampilkan {start} sampai {end} dari {rows} data",
                    }
                });
            });

            // Tom Select
            document.querySelectorAll('select.searchable').forEach(select => {
                new TomSelect(select, {
                    create: false,
                    placeholder: select.getAttribute('placeholder') || '— Pilih —'
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>