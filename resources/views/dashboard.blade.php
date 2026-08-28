@extends('layouts.app')
@section('title', 'My Sakinah Agent')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang kembali, ' . auth()->user()->name . '! 👋')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Amiri:wght@400;600;700&family=Spectral:wght@700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --rs-primary: #0F7B63;
            --rs-primary-dark: #0A5644;
            --rs-primary-soft: #E6F6F0;
            --rs-accent: #B8912E;
            --rs-accent-soft: #FBF6E9;
            --rs-info: #0E7490;
            --rs-info-soft: #E7F4F6;
            --rs-danger: #B54545;
            --rs-bg: #F5F8F7;
            --rs-surface: #FFFFFF;
            --rs-ink: #16211D;
            --rs-muted: #6C7A76;
            --rs-border: #E7EDEA;
            --rs-radius: 14px;
            --rs-radius-sm: 10px;
            --rs-shadow: 0 1px 2px rgba(16, 24, 32, .04), 0 8px 20px -12px rgba(16, 24, 32, .10);
            --rs-arch-lg: 22px 22px 6px 6px;
            --rs-arch-sm: 13px 13px 4px 4px;
        }

        body {
            background: var(--rs-bg);
            color: var(--rs-ink);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .card {
            border: 1px solid var(--rs-border);
            border-radius: var(--rs-radius);
            box-shadow: var(--rs-shadow);
            background: var(--rs-surface);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--rs-border);
            padding: 16px 20px;
        }

        .card-body {
            padding: 20px;
        }

        .rs-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: .95rem;
            color: var(--rs-ink);
        }

        .rs-card-title i {
            font-size: 1rem;
            color: var(--rs-primary);
        }

        .rs-card-link {
            font-size: .78rem;
            font-weight: 700;
            color: var(--rs-primary);
            text-decoration: none;
        }

        .rs-hero {
            position: relative;
            overflow: hidden;
            border-radius: var(--rs-radius);
            padding: 26px 32px;
            margin-bottom: 24px;
            background: linear-gradient(120deg, var(--rs-primary-dark) 0%, var(--rs-primary) 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            box-shadow: 0 12px 30px -14px rgba(6, 61, 44, .45);
        }

        .rs-hero-eyebrow {
            font-size: .72rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .75);
            font-weight: 600;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .rs-hero-eyebrow i {
            color: #F3D98B;
        }

        .rs-hero-title {
            font-family: 'Amiri', serif;
            font-weight: 700;
            font-size: 1.6rem;
            margin: 0;
            letter-spacing: 0;
        }

        .rs-hero-sub {
            font-size: .82rem;
            color: rgba(255, 255, 255, .8);
            margin-top: 6px;
            max-width: 420px;
        }

        .rs-hero-illustration {
            flex-shrink: 0;
            opacity: .95;
        }

        .rs-hero-illustration svg {
            width: 190px;
            height: auto;
        }

        .stat-card {
            border-radius: var(--rs-radius);
            border: 1px solid var(--rs-border);
            background: var(--rs-surface);
            box-shadow: var(--rs-shadow);
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            height: 100%;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--rs-arch-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .stat-card-a .stat-icon { background: var(--rs-primary-soft); color: var(--rs-primary); }
        .stat-card-b .stat-icon { background: var(--rs-accent-soft); color: var(--rs-accent); }
        .stat-card-c .stat-icon { background: #EAF7EF; color: #0F9D58; }
        .stat-card-d .stat-icon { background: var(--rs-info-soft); color: var(--rs-info); }

        .stat-label {
            font-size: .72rem;
            color: var(--rs-muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .stat-value {
            font-family: 'Spectral', serif;
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--rs-ink);
            line-height: 1.2;
        }

        .stat-sub {
            font-size: .72rem;
            color: var(--rs-muted);
        }

        .quick-action-btn {
            background: var(--rs-surface);
            border: 1px solid var(--rs-border);
            border-radius: var(--rs-radius-sm);
            padding: 18px 14px;
            text-align: center;
            text-decoration: none;
            color: var(--rs-ink);
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            height: 100%;
        }

        .quick-action-btn:hover {
            border-color: var(--rs-primary);
            box-shadow: var(--rs-shadow);
            transform: translateY(-2px);
            color: var(--rs-primary-dark);
        }

        .quick-action-btn .icon {
            width: 40px;
            height: 40px;
            border-radius: var(--rs-arch-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }

        .quick-action-btn.qa-1 .icon { background: var(--rs-primary-soft); color: var(--rs-primary); }
        .quick-action-btn.qa-2 .icon { background: var(--rs-info-soft); color: var(--rs-info); }
        .quick-action-btn.qa-3 .icon { background: var(--rs-accent-soft); color: var(--rs-accent); }
        .quick-action-btn.qa-4 .icon { background: #EAF7EF; color: #0F9D58; }

        .quick-action-btn span.label { font-size: .8rem; font-weight: 700; }
        .quick-action-btn span.sub { font-size: .68rem; color: var(--rs-muted); font-weight: 500; margin-top: -6px; }

        .chart-container { position: relative; height: 220px; }

        .rs-donut-wrap { position: relative; width: 148px; height: 148px; flex-shrink: 0; }
        .rs-donut-empty {
            width: 148px; height: 148px; border-radius: 50%;
            border: 14px solid var(--rs-primary-soft);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-align: center; padding: 10px; gap: 4px;
        }
        .rs-donut-empty i { font-size: 1.3rem; color: var(--rs-primary); }
        .rs-donut-empty span { font-size: .7rem; color: var(--rs-muted); font-weight: 600; line-height: 1.3; }

        .rs-legend-item { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: .8rem; }
        .rs-legend-item:last-child { margin-bottom: 0; }
        .rs-legend-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
        .rs-legend-name { flex: 1; color: var(--rs-ink); font-weight: 500; }
        .rs-legend-pct { color: var(--rs-muted); font-weight: 700; font-size: .75rem; }
    </style>
@endpush

@section('content')

    <!-- Welcome Hero -->
    <div class="rs-hero fade-in mb-4">
        <div>
            <div class="rs-hero-eyebrow"><i class="bi bi-moon-stars-fill" aria-hidden="true"></i> Assalamu'alaikum Warahmatullah</div>
            <h2 class="rs-hero-title">My Sakinah Agent</h2>
            <div class="rs-hero-sub">Ringkasan aktivitas pendaftaran, antrian, &amp; perolehan poin pasien hari ini,
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
        </div>
        <div class="rs-hero-illustration" aria-hidden="true">
            <svg viewBox="0 0 220 130" xmlns="http://www.w3.org/2000/svg">
                <circle cx="185" cy="30" r="16" fill="rgba(255,255,255,.10)" />
                <circle cx="205" cy="55" r="9" fill="rgba(255,255,255,.10)" />
                <ellipse cx="110" cy="122" rx="95" ry="7" fill="rgba(255,255,255,.08)" />
                <rect x="12" y="82" width="11" height="38" rx="2" fill="rgba(255,255,255,.22)" />
                <circle cx="17.5" cy="72" r="18" fill="rgba(255,255,255,.16)" />
                <rect x="60" y="34" width="100" height="86" rx="5" fill="rgba(255,255,255,.24)" />
                <g fill="rgba(255,255,255,.6)">
                    <rect x="74" y="48" width="12" height="12" rx="2" />
                    <rect x="94" y="48" width="12" height="12" rx="2" />
                    <rect x="114" y="48" width="12" height="12" rx="2" />
                    <rect x="134" y="48" width="12" height="12" rx="2" />
                    <rect x="74" y="70" width="12" height="12" rx="2" />
                    <rect x="94" y="70" width="12" height="12" rx="2" />
                    <rect x="114" y="70" width="12" height="12" rx="2" />
                    <rect x="134" y="70" width="12" height="12" rx="2" />
                </g>
                <rect x="98" y="92" width="24" height="28" rx="2" fill="rgba(255,255,255,.45)" />
                <rect x="106" y="18" width="6" height="20" fill="rgba(255,255,255,.55)" />
                <rect x="98" y="24" width="22" height="6" fill="rgba(255,255,255,.55)" />
                <rect x="192" y="86" width="10" height="34" rx="2" fill="rgba(255,255,255,.2)" />
                <circle cx="197" cy="76" r="15" fill="rgba(255,255,255,.14)" />
            </svg>
        </div>
    </div>

    <!-- Alert Pending Redemptions untuk Admin -->
    @if(auth()->user()->isAdmin() && $pendingRedemptionsCount > 0)
    <div class="alert alert-warning d-flex align-items-center justify-content-between mb-4 fade-in" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>
                <strong>Ada {{ $pendingRedemptionsCount }} permohonan penukaran reward</strong> yang menunggu persetujuan Anda.
            </div>
        </div>
        <a href="{{ route('admin.redemptions.index') }}" class="btn btn-sm btn-warning text-dark fw-bold" style="border-radius:8px;">
            Proses Sekarang <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    @endif

    <!-- Widget Ringkasan Poin untuk Petugas -->
    @if(auth()->user()->isPetugas())
    <div class="card mb-4 fade-in" style="background:linear-gradient(135deg, #FAFDFB 0%, #E6F6F0 100%); border:1px solid #C4E9DD;">
        <div class="card-body p-3 p-md-4">
            <div class="row align-items-center g-3">
                <div class="col-md-6 col-lg-7 d-flex align-items-center gap-3">
                    <div style="width:54px; height:54px; border-radius:14px; background:var(--rs-primary); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0;">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div>
                        <div style="font-size:.75rem; text-transform:uppercase; font-weight:700; color:var(--rs-primary-dark); letter-spacing:.05em;">Saldo Poin Anda</div>
                        <div style="font-size:1.65rem; font-weight:800; color:var(--rs-primary-dark); line-height:1.1;">
                            {{ number_format($myPointStats['point_balance'] ?? 0) }} <span style="font-size:.9rem; font-weight:600;">Poin</span>
                        </div>
                        <div style="font-size:.75rem; color:var(--rs-muted); margin-top:2px;">
                            Input pasien baru bernilai <strong>+10 poin</strong> setiap pendaftaran.
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-5 d-flex justify-content-md-end gap-2">
                    <a href="{{ route('points.katalog') }}" class="btn btn-sm text-white px-3" style="background:var(--rs-primary); border-radius:8px; font-weight:700; font-size:.82rem;">
                        <i class="bi bi-gift-fill me-1"></i>Tukar Reward
                    </a>
                    <a href="{{ route('points.index') }}" class="btn btn-sm btn-light border px-3" style="border-radius:8px; font-weight:600; font-size:.82rem;">
                        <i class="bi bi-speedometer2 me-1"></i>Detail Poin
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════ FILTER DASHBOARD (Admin only) ═══════════ --}}
    @if(auth()->user()->isAdmin())
    <div class="fade-in mb-4" style="background:var(--rs-surface); border:1px solid var(--rs-border); border-radius:var(--rs-radius); padding:16px 20px;">
        <form method="GET" action="{{ route('dashboard') }}" id="dashboard-filter-form">
            <div class="row g-2 align-items-end">
                <div class="col-12">
                    <div style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--rs-muted); margin-bottom:8px;">
                        <i class="bi bi-funnel me-1"></i>Filter Dashboard
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="form-label" style="font-size:.75rem; font-weight:700; color:var(--rs-ink);">Dari Tanggal</label>
                    <input type="date" name="dari" id="filter-dari"
                           class="form-control form-control-sm"
                           value="{{ request('dari') }}"
                           style="border-radius:8px; font-size:.82rem;">
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="form-label" style="font-size:.75rem; font-weight:700; color:var(--rs-ink);">Sampai Tanggal</label>
                    <input type="date" name="sampai" id="filter-sampai"
                           class="form-control form-control-sm"
                           value="{{ request('sampai') }}"
                           style="border-radius:8px; font-size:.82rem;">
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="form-label" style="font-size:.75rem; font-weight:700; color:var(--rs-ink);">Poli</label>
                    <select name="department_id" class="form-select form-select-sm" style="border-radius:8px; font-size:.82rem;">
                        <option value="">Semua Poli</option>
                        @foreach($filterDepts as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->nama_poli }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="form-label" style="font-size:.75rem; font-weight:700; color:var(--rs-ink);">Dokter</label>
                    <select name="doctor_id" class="form-select form-select-sm" style="border-radius:8px; font-size:.82rem;">
                        <option value="">Semua Dokter</option>
                        @foreach($filterDoctors as $doc)
                            <option value="{{ $doc->id }}" {{ request('doctor_id') == $doc->id ? 'selected' : '' }}>
                                {{ $doc->nama_dokter }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label class="form-label" style="font-size:.75rem; font-weight:700; color:var(--rs-ink);">Petugas</label>
                    <select name="user_id" class="form-select form-select-sm" style="border-radius:8px; font-size:.82rem;">
                        <option value="">Semua Petugas</option>
                        @foreach($filterPetugas as $p)
                            <option value="{{ $p->id }}" {{ request('user_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-12 d-flex gap-2">
                    <button type="submit" class="btn btn-sm flex-fill"
                            style="background:var(--rs-primary); color:#fff; border-radius:8px; font-weight:700; font-size:.8rem;">
                        <i class="bi bi-check2 me-1"></i>Terapkan
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-sm flex-fill"
                       style="background:var(--rs-bg); color:var(--rs-muted); border:1px solid var(--rs-border); border-radius:8px; font-size:.8rem; font-weight:600; text-decoration:none; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
    @endif

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3 fade-in">
            <div class="stat-card stat-card-a">
                <div class="stat-icon" aria-hidden="true"><i class="bi bi-clipboard2-check"></i></div>
                <div>
                    <div class="stat-label">Pendaftaran Hari Ini</div>
                    <div class="stat-value">{{ $stats['total_pendaftaran_hari_ini'] }}</div>
                    <div class="stat-sub">Total kunjungan hari ini</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 fade-in fade-in-delay-1">
            <div class="stat-card stat-card-b">
                <div class="stat-icon" aria-hidden="true"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="stat-label">Total Pasien</div>
                    <div class="stat-value">{{ $stats['total_pasien'] }}</div>
                    <div class="stat-sub">Pasien terdaftar</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 fade-in fade-in-delay-2">
            <div class="stat-card stat-card-c">
                <div class="stat-icon" aria-hidden="true"><i class="bi bi-person-badge-fill"></i></div>
                <div>
                    <div class="stat-label">Dokter Aktif</div>
                    <div class="stat-value">{{ $stats['total_dokter'] }}</div>
                    <div class="stat-sub">Dokter di sistem</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 fade-in fade-in-delay-3">
            <div class="stat-card stat-card-d">
                <div class="stat-icon" aria-hidden="true"><i class="bi bi-building"></i></div>
                <div>
                    <div class="stat-label">Poli Tersedia</div>
                    <div class="stat-value">{{ $stats['total_poli'] }}</div>
                    <div class="stat-sub">Departemen aktif</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Grafik 7 Hari -->
        <div class="col-lg-7 fade-in">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="rs-card-title"><i class="bi bi-graph-up" aria-hidden="true"></i>Tren Pendaftaran 7 Hari</span>
                    <span class="badge" style="background:var(--rs-primary-soft);color:var(--rs-primary-dark);font-weight:600;font-size:.72rem;">7 Hari Terakhir</span>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartPendaftaran" role="img" aria-label="Grafik jumlah pendaftaran pasien selama 7 hari terakhir"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendaftaran Per Poli -->
        <div class="col-lg-5 fade-in fade-in-delay-1">
            <div class="card h-100">
                <div class="card-header"><span class="rs-card-title"><i class="bi bi-pie-chart" aria-hidden="true"></i>Per Poli Hari Ini</span></div>
                <div class="card-body d-flex align-items-center gap-4">
                    @if($stats['total_pendaftaran_hari_ini'] > 0)
                        <div class="rs-donut-wrap">
                            <canvas id="chartPoli" role="img" aria-label="Diagram distribusi pendaftaran per poli hari ini"></canvas>
                        </div>
                        <div class="flex-1">
                            @foreach($pendaftaranPerPoli as $i => $p)
                                @php $persenPoli = round($p['total'] / $stats['total_pendaftaran_hari_ini'] * 100); @endphp
                                <div class="rs-legend-item">
                                    <span class="rs-legend-dot"
                                        style="background: {{ ['#0F7B63', '#0E7490', '#B8912E', '#B54545', '#7C3AED', '#6C7A76'][$i % 6] }};"></span>
                                    <span class="rs-legend-name">{{ $p['nama_poli'] }}</span>
                                    <span class="rs-legend-pct">{{ $p['total'] }} ({{ $persenPoli }}%)</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rs-donut-empty mx-auto">
                            <i class="bi bi-clipboard2-pulse" aria-hidden="true"></i>
                            <span>Belum ada pendaftaran hari ini</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Top Petugas Poin untuk Admin -->
    @if(auth()->user()->isAdmin() && $topPetugas->count() > 0)
    <div class="card mb-4 fade-in">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="rs-card-title"><i class="bi bi-trophy-fill text-warning"></i>Top Petugas Poin</span>
            <a href="{{ route('admin.reports.index') }}" class="rs-card-link">Lihat Laporan Lengkap <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table rs-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Peringkat</th>
                            <th>Nama Petugas</th>
                            <th class="text-center">Pasien Baru Diinput</th>
                            <th class="text-end pe-4">Saldo Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topPetugas as $i => $petugas)
                        <tr>
                            <td class="ps-4">
                                @if($i === 0) 🥇
                                @elseif($i === 1) 🥈
                                @elseif($i === 2) 🥉
                                @else {{ $i + 1 }}
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $petugas->name }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $petugas->created_patients_count }} Pasien</span>
                            </td>
                            <td class="text-end pe-4">
                                <span class="badge" style="background:var(--rs-accent-soft); color:var(--rs-accent); font-weight:800; font-size:.84rem;">
                                    {{ number_format($petugas->point_balance) }} Poin
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions Row -->
    <div class="row g-3">
        <div class="col-12 fade-in">
            <div class="card">
                <div class="card-header">
                    <span class="rs-card-title"><i class="bi bi-lightning" aria-hidden="true"></i>Aksi Cepat</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <a href="{{ route('registrations.create') }}" class="quick-action-btn qa-1">
                                <span class="icon" aria-hidden="true"><i class="bi bi-clipboard2-plus-fill"></i></span>
                                <span class="label">Daftar Pasien</span>
                                <span class="sub">Pendaftaran baru</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('registrations.index') }}" class="quick-action-btn qa-2">
                                <span class="icon" aria-hidden="true"><i class="bi bi-table"></i></span>
                                <span class="label">Pendaftaran</span>
                                <span class="sub">Data pendaftaran</span>
                            </a>
                        </div>
                        @if(auth()->user()->isPetugas())
                        <div class="col-6 col-md-3">
                            <a href="{{ route('points.katalog') }}" class="quick-action-btn qa-3">
                                <span class="icon" aria-hidden="true"><i class="bi bi-gift-fill"></i></span>
                                <span class="label">Tukar Reward</span>
                                <span class="sub">Katalog merchandise</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('registrations.riwayat') }}" class="quick-action-btn qa-4">
                                <span class="icon" aria-hidden="true"><i class="bi bi-clock-history"></i></span>
                                <span class="label">Riwayat Saya</span>
                                <span class="sub">Pendaftaran oleh saya</span>
                            </a>
                        </div>
                        @endif
                        @if(auth()->user()->isAdmin())
                        <div class="col-6 col-md-3">
                            <a href="{{ route('admin.redemptions.index') }}" class="quick-action-btn qa-3">
                                <span class="icon" aria-hidden="true"><i class="bi bi-gift"></i></span>
                                <span class="label">Penukaran Reward</span>
                                <span class="sub">Persetujuan hadiah</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('admin.reports.index') }}" class="quick-action-btn qa-4">
                                <span class="icon" aria-hidden="true"><i class="bi bi-file-earmark-bar-graph"></i></span>
                                <span class="label">Laporan Poin</span>
                                <span class="sub">Rekapitulasi sistem</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('chartPendaftaran').getContext('2d');
        const gradientFill = ctx.createLinearGradient(0, 0, 0, 220);
        gradientFill.addColorStop(0, 'rgba(15,123,99,.22)');
        gradientFill.addColorStop(1, 'rgba(15,123,99,.02)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels7Hari) !!},
                datasets: [{
                    label: 'Pendaftaran',
                    data: {!! json_encode($data7Hari) !!},
                    backgroundColor: gradientFill,
                    borderColor: '#0F7B63',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#0F7B63',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11, family: 'Plus Jakarta Sans' } } },
                    y: { beginAtZero: true, grid: { color: '#F1F3F5' }, ticks: { precision: 0, font: { size: 11 } } }
                }
            }
        });

        @if($stats['total_pendaftaran_hari_ini'] > 0)
            const ctxPoli = document.getElementById('chartPoli').getContext('2d');
            new Chart(ctxPoli, {
                type: 'doughnut',
                data: {
                    labels: {!! $pendaftaranPerPoli->pluck('nama_poli')->toJson() !!},
                    datasets: [{
                        data: {!! $pendaftaranPerPoli->pluck('total')->toJson() !!},
                        backgroundColor: ['#0F7B63', '#0E7490', '#B8912E', '#B54545', '#7C3AED', '#6C7A76'],
                        borderWidth: 0,
                        hoverOffset: 4,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: { legend: { display: false } }
                }
            });
        @endif
    </script>
@endpush