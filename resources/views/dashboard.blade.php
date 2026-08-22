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
            /* bentuk arch yang sama dipakai di layout & halaman login — identitas
                   visual berulang, bukan sekadar radius acak */
            --rs-arch-lg: 22px 22px 6px 6px;
            --rs-arch-sm: 13px 13px 4px 4px;
        }

        body {
            background: var(--rs-bg);
            color: var(--rs-ink);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ---------- Cards: satu bahasa desain yang konsisten ---------- */
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

        .rs-card-link:hover {
            color: var(--rs-primary-dark);
        }

        /* ---------- Hero: banner sapaan dengan ilustrasi gedung ---------- */
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

        /* ---------- Stat cards: flat, jelas, tidak ramai ---------- */
        .stat-card {
            border-radius: var(--rs-radius);
            border: 1px solid var(--rs-border);
            background: var(--rs-surface);
            box-shadow: var(--rs-shadow);
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
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

        .stat-card-a .stat-icon {
            background: var(--rs-primary-soft);
            color: var(--rs-primary);
        }

        .stat-card-b .stat-icon {
            background: var(--rs-accent-soft);
            color: var(--rs-accent);
        }

        .stat-card-c .stat-icon {
            background: #EAF7EF;
            color: #0F9D58;
        }

        .stat-card-d .stat-icon {
            background: var(--rs-info-soft);
            color: var(--rs-info);
        }

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

        /* ---------- Quick actions: bersih, konsisten ---------- */
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

        .quick-action-btn.qa-1 .icon {
            background: var(--rs-primary-soft);
            color: var(--rs-primary);
        }

        .quick-action-btn.qa-2 .icon {
            background: var(--rs-info-soft);
            color: var(--rs-info);
        }

        .quick-action-btn.qa-3 .icon {
            background: var(--rs-accent-soft);
            color: var(--rs-accent);
        }

        .quick-action-btn.qa-4 .icon {
            background: #EAF7EF;
            color: #0F9D58;
        }

        .quick-action-btn span.label {
            font-size: .8rem;
            font-weight: 700;
        }

        .quick-action-btn span.sub {
            font-size: .68rem;
            color: var(--rs-muted);
            font-weight: 500;
            margin-top: -6px;
        }

        .chart-container {
            position: relative;
            height: 220px;
        }

        /* ---------- Donut per poli ---------- */
        .rs-donut-wrap {
            position: relative;
            width: 148px;
            height: 148px;
            flex-shrink: 0;
        }

        .rs-donut-empty {
            width: 148px;
            height: 148px;
            border-radius: 50%;
            border: 14px solid var(--rs-primary-soft);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 10px;
            gap: 4px;
        }

        .rs-donut-empty i {
            font-size: 1.3rem;
            color: var(--rs-primary);
        }

        .rs-donut-empty span {
            font-size: .7rem;
            color: var(--rs-muted);
            font-weight: 600;
            line-height: 1.3;
        }

        .rs-legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            font-size: .8rem;
        }

        .rs-legend-item:last-child {
            margin-bottom: 0;
        }

        .rs-legend-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .rs-legend-name {
            flex: 1;
            color: var(--rs-ink);
            font-weight: 500;
        }

        .rs-legend-pct {
            color: var(--rs-muted);
            font-weight: 700;
            font-size: .75rem;
        }

        /* ---------- Antrian table ---------- */
        .rs-table thead th {
            background: var(--rs-primary-soft);
            color: var(--rs-primary-dark);
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: 12px 14px;
            border: none;
        }

        .rs-table tbody td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--rs-border);
            vertical-align: middle;
            font-size: .84rem;
        }

        .rs-table tbody tr:last-child td {
            border-bottom: none;
        }

        .rs-antrian-no {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 30px;
            height: 26px;
            padding: 0 6px;
            border-radius: 7px;
            background: var(--rs-primary-soft);
            color: var(--rs-primary-dark);
            font-weight: 800;
            font-size: .78rem;
        }

        .rs-empty-state {
            text-align: center;
            padding: 34px 10px;
            color: var(--rs-muted);
        }

        .rs-empty-state i {
            font-size: 1.8rem;
            color: #C9D6D1;
        }

        .rs-empty-state p {
            margin: 10px 0 2px;
            font-size: .85rem;
            font-weight: 600;
            color: var(--rs-ink);
        }

        .rs-empty-state small {
            font-size: .74rem;
        }

        /* ---------- Info panel ---------- */
        .rs-info-panel {
            background: var(--rs-bg);
            border: 1px solid var(--rs-border);
            border-radius: var(--rs-radius-sm);
        }

        .rs-info-title {
            color: var(--rs-ink) !important;
        }

        .rs-info-row {
            color: var(--rs-ink) !important;
        }

        .rs-info-row+.rs-info-row {
            margin-top: 6px;
        }
    </style>
@endpush

@section('content')

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
            @if(request()->hasAny(['dari','sampai','department_id','doctor_id','user_id']))
            <div class="mt-2 pt-2" style="border-top:1px solid var(--rs-border);">
                <span style="font-size:.75rem; color:var(--rs-muted);">
                    <i class="bi bi-info-circle me-1"></i>
                    Filter aktif — statistik menampilkan data sesuai filter.
                    @if(request('dari') || request('sampai'))
                        Periode: <strong>{{ request('dari') ? \Carbon\Carbon::parse(request('dari'))->translatedFormat('d M Y') : '…' }}</strong>
                        s/d <strong>{{ request('sampai') ? \Carbon\Carbon::parse(request('sampai'))->translatedFormat('d M Y') : '…' }}</strong>.
                    @endif
                </span>
            </div>
            @endif
        </form>
    </div>
    @endif

    <!-- Welcome -->
    <div class="rs-hero fade-in">
        <div>
            <div class="rs-hero-eyebrow"><i class="bi bi-moon-stars-fill" aria-hidden="true"></i> Assalamu'alaikum
                Warahmatullah</div>
            <h2 class="rs-hero-title">My Sakinah Agent</h2>
            <div class="rs-hero-sub">Ringkasan aktivitas pendaftaran &amp; antrian pasien hari ini,
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </div>
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

    <div class="row g-3 mb-4">
        <!-- Stat Cards -->
        <div class="col-12 col-md-6 fade-in">
            <div class="stat-card stat-card-a">
                <div class="stat-icon" aria-hidden="true"><i class="bi bi-clipboard2-check"></i></div>
                <div>
                    <div class="stat-label">Pendaftaran Hari Ini</div>
                    <div class="stat-value">{{ $stats['total_pendaftaran_hari_ini'] }}</div>
                    <div class="stat-sub">Total pendaftar</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 fade-in fade-in-delay-1">
            <div class="stat-card stat-card-b">
                <div class="stat-icon" aria-hidden="true"><i class="bi bi-calendar-event"></i></div>
                <div>
                    <div class="stat-label">Pasien Terjadwal</div>
                    <div class="stat-value">{{ $stats['terjadwal'] }}</div>
                    <div class="stat-sub">Jadwal mendatang</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Grafik 7 Hari -->
        <div class="col-lg-7 fade-in">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="rs-card-title"><i class="bi bi-graph-up" aria-hidden="true"></i>Tren Pendaftaran 7
                        Hari</span>
                    <span class="badge"
                        style="background:var(--rs-primary-soft);color:var(--rs-primary-dark);font-weight:600;font-size:.72rem;">7
                        Hari Terakhir</span>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartPendaftaran" role="img"
                            aria-label="Grafik jumlah pendaftaran pasien selama 7 hari terakhir"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendaftaran Per Poli -->
        <div class="col-lg-5 fade-in fade-in-delay-1">
            <div class="card h-100">
                <div class="card-header"><span class="rs-card-title"><i class="bi bi-pie-chart" aria-hidden="true"></i>Per
                        Poli Hari Ini</span></div>
                <div class="card-body d-flex align-items-center gap-4">
                    @if($stats['total_pendaftaran_hari_ini'] > 0)
                        <div class="rs-donut-wrap">
                            <canvas id="chartPoli" role="img"
                                aria-label="Diagram distribusi pendaftaran per poli hari ini"></canvas>
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

    <div class="row g-3">
        {{-- Antrian Terbaru — disembunyikan
        <div class="col-lg-8 fade-in">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="rs-card-title"><i class="bi bi-list-ol" aria-hidden="true"></i>Antrian Aktif Hari
                        Ini</span>
                    <a href="{{ route('antrian.index') }}" class="rs-card-link">
                        Lihat Semua <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table rs-table mb-0">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Antrian</th>
                                    <th>Nama Pasien</th>
                                    <th>Poli Tujuan</th>
                                    <th>Waktu Daftar</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($antrianTerbaru as $reg)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><span class="rs-antrian-no">{{ $reg->nomor_antrian }}</span></td>
                                        <td class="fw-600">{{ $reg->patient->nama_pasien }}</td>
                                        <td>{{ $reg->department->nama_poli }}</td>
                                        <td>{{ $reg->created_at->format('H:i') }}</td>
                                        <td><span class="badge badge-{{ $reg->status }}">{{ $reg->status_label }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="rs-empty-state">
                                                <i class="bi bi-inbox" aria-hidden="true"></i>
                                                <p>Belum ada antrian aktif</p>
                                                <small>Antrian akan muncul di sini ketika ada pendaftaran baru.</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <!-- Antrian Terbaru -->
        <!--
<div class="col-lg-8 fade-in">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="rs-card-title">
                <i class="bi bi-list-ol" aria-hidden="true"></i>
                Antrian Aktif Hari Ini
            </span>

            <a href="{{ route('antrian.index') }}" class="rs-card-link">
                Lihat Semua
                <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
            </a>

        </div>
        --}}


        <!-- Quick Actions -->
        <div class="col-lg-12 fade-in fade-in-delay-1">
            <div class="card">
                <div class="card-header"><span class="rs-card-title"><i class="bi bi-lightning" aria-hidden="true"></i>Aksi
                        Cepat</span></div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('registrations.create') }}" class="quick-action-btn qa-1">
                                <span class="icon" aria-hidden="true"><i class="bi bi-clipboard2-plus-fill"></i></span>
                                <span class="label">Daftar Pasien</span>
                                <span class="sub">Pendaftaran baru</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('registrations.index') }}" class="quick-action-btn qa-4">
                                <span class="icon" aria-hidden="true"><i class="bi bi-table"></i></span>
                                <span class="label">Pendaftaran</span>
                                <span class="sub">Riwayat lengkap</span>
                            </a>
                        </div>
                    </div>
                </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table rs-table mb-0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>No. Antrian</th>
                            <th>Nama Pasien</th>
                            <th>Poli Tujuan</th>
                            <th>Waktu Daftar</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($antrianTerbaru as $reg)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="rs-antrian-no">
                                        {{ $reg->nomor_antrian }}
                                    </span>
                                </td>
                                <td class="fw-600">
                                    {{ $reg->patient->nama_pasien }}
                                </td>
                                <td>
                                    {{ $reg->department->nama_poli }}
                                </td>
                                <td>
                                    {{ $reg->created_at->format('H:i') }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ $reg->status }}">
                                        {{ $reg->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="rs-empty-state">
                                        <i class="bi bi-inbox" aria-hidden="true"></i>
                                        <p>Belum ada antrian aktif</p>
                                        <small>
                                            Antrian akan muncul di sini ketika ada pendaftaran baru.
                                        </small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Ranking Poin -->
<div class="col-lg-8 fade-in">

    <div class="card h-100">

        <div class="card-header d-flex justify-content-between align-items-center">

            <span class="rs-card-title">
                <i class="bi bi-trophy-fill" aria-hidden="true"></i>
                Peringkat Poin Petugas
            </span>

            @if(auth()->user()->role === 'admin')
                <a href="{{ route('points.admin') }}" class="rs-card-link">
                    Lihat Semua
                    <i class="bi bi-arrow-right ms-1"></i>
                </a>
            @endif

        </div>

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <div>
                    <div class="fw-bold" style="font-size:.85rem;">
                        Ranking Bulan Ini
                    </div>

                    <div style="font-size:.7rem;color:var(--rs-muted);">
                        {{ $namaBulanPoin }}
                    </div>
                </div>

                <span class="point-month-badge">
                    {{ $totalPendaftaranPoin }} pendaftaran
                </span>

            </div>

            @if($rankingPoin->count() > 0)

                <div class="table-responsive">

                    <table class="point-ranking-table">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Petugas</th>
                                <th>Pendaftaran</th>
                                <th class="text-end">Poin</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($rankingPoin->take(5) as $petugas)

                                <tr>

                                    <td>
                                        <span class="point-rank">
                                            @if($loop->iteration === 1)
                                                🥇
                                            @elseif($loop->iteration === 2)
                                                🥈
                                            @elseif($loop->iteration === 3)
                                                🥉
                                            @else
                                                {{ $loop->iteration }}
                                            @endif
                                        </span>
                                    </td>

                                    <td>
                                        <div style="font-weight:700;">
                                            {{ $petugas->name }}
                                        </div>

                                        <div style="font-size:.65rem;color:var(--rs-muted);">
                                            Petugas
                                        </div>
                                    </td>

                                    <td>
                                        {{ $petugas->total_pendaftaran ?? 0 }}
                                    </td>

                                    <td class="text-end">
                                        <span class="point-score">
                                            {{ $petugas->total_poin ?? 0 }} poin
                                        </span>
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="rs-empty-state">

                    <i class="bi bi-trophy"></i>

                    <p>Belum ada poin bulan ini</p>

                    <small>
                        Poin akan muncul setelah ada pendaftaran.
                    </small>

                </div>

            @endif

        </div>

    </div>

</div>


<!-- Aksi Cepat -->
<div class="col-lg-4 fade-in fade-in-delay-1">

    <div class="card h-100">

        <div class="card-header">

            <span class="rs-card-title">
                <i class="bi bi-lightning" aria-hidden="true"></i>
                Aksi Cepat
            </span>

        </div>

        <div class="card-body">

            <div class="row g-2">

                <div class="col-6">

                    <a href="{{ route('registrations.create') }}"
                       class="quick-action-btn qa-1">

                        <span class="icon">
                            <i class="bi bi-clipboard2-plus-fill"></i>
                        </span>

                        <span class="label">
                            Daftar Pasien
                        </span>

                        <span class="sub">
                            Pendaftaran baru
                        </span>

                    </a>

                </div>

                <div class="col-6">

                    <a href="{{ route('registrations.index') }}"
                       class="quick-action-btn qa-4">

                        <span class="icon">
                            <i class="bi bi-table"></i>
                        </span>

                        <span class="label">
                            Pendaftaran
                        </span>

                        <span class="sub">
                            Riwayat lengkap
                        </span>

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>
    </div>
    {{-- ═══════════ RANKING POIN PETUGAS (Admin only) ═══════════ --}}
    @if(auth()->user()->isAdmin() && $rankingPetugas && $rankingPetugas->isNotEmpty())
    <div class="mt-4 fade-in">
        <div class="table-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="d-flex align-items-center gap-2" style="font-weight:700; font-size:.95rem; color:var(--rs-ink);">
                    <i class="bi bi-trophy" style="color:var(--rs-accent);" aria-hidden="true"></i>
                    Ranking Poin Petugas
                </span>
                <a href="{{ route('points.admin') }}"
                   style="font-size:.78rem; font-weight:700; color:var(--rs-primary); text-decoration:none;">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width:56px;">#</th>
                            <th>Petugas</th>
                            <th>Total Pendaftaran</th>
                            <th class="text-end">Poin Bulan Ini</th>
                            <th class="text-end pe-4">Saldo Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rankingPetugas as $i => $petugas)
                        <tr>
                            <td class="ps-4">
                                @if($i === 0 && $petugas->total_poin_earned > 0)
                                    <i class="bi bi-trophy-fill" style="color:#D4AF37; font-size:1.05rem;" aria-label="Peringkat 1"></i>
                                @elseif($i === 1 && $petugas->total_poin_earned > 0)
                                    <i class="bi bi-trophy-fill" style="color:#A8A8A8; font-size:.98rem;" aria-label="Peringkat 2"></i>
                                @elseif($i === 2 && $petugas->total_poin_earned > 0)
                                    <i class="bi bi-trophy-fill" style="color:#C08552; font-size:.92rem;" aria-label="Peringkat 3"></i>
                                @else
                                    <span style="color:var(--rs-muted); font-weight:700; font-size:.85rem;">{{ $i + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:32px; height:32px; border-radius:10px;
                                                background:{{ ['#E6F6F0','#E7F4F6','#FBF6E9','#F3E8FF','#FEE2E2'][$i % 5] }};
                                                color:{{ ['#0F7B63','#0E7490','#B8912E','#7C3AED','#B54545'][$i % 5] }};
                                                display:flex; align-items:center; justify-content:center;
                                                font-weight:700; font-size:.76rem; flex-shrink:0;">
                                        {{ strtoupper(substr($petugas->name, 0, 1)) }}
                                    </div>
                                    <span style="font-weight:600; font-size:.87rem; color:var(--rs-ink);">{{ $petugas->name }}</span>
                                </div>
                            </td>
                            <td style="color:var(--rs-muted); font-size:.85rem;">
                                {{ number_format($petugas->total_pendaftaran_all ?? 0) }} pendaftaran
                            </td>
                            <td class="text-end">
                                <span class="badge" style="background:var(--rs-primary-soft); color:var(--rs-primary-dark); font-size:.76rem;">
                                    {{ number_format($petugas->total_poin_earned ?? 0) }} poin
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                @php $saldo = $petugas->saldo_poin ?? 0; @endphp
                                <span class="badge" style="background:{{ $saldo > 0 ? 'var(--rs-info-soft)' : '#F3F4F6' }}; color:{{ $saldo > 0 ? 'var(--rs-info)' : '#9CA3AF' }}; font-size:.76rem;">
                                    {{ number_format($saldo) }} poin
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