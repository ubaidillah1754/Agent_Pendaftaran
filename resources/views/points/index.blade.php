{{-- resources/views/points/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Poin Saya')

@section('content')
<div class="points-page">

    {{-- ══════════ PAGE HEADER ══════════ --}}
    <div class="ph-wrap mb-4">
        <div class="ph-left">
            <nav class="bc-nav" aria-label="breadcrumb">
                <a href="{{ route('dashboard') }}"><i class="bi bi-house-heart-fill"></i> Dashboard</a>
                <i class="bi bi-chevron-right"></i>
                <span>Poin Saya</span>
            </nav>
            <h1 class="ph-title">Poin &amp; Apresiasi Saya</h1>
            <p class="ph-sub">Pantau kontribusi dan raih penghargaan atas dedikasi Anda dalam melayani pasien.</p>
        </div>
        <div class="ph-actions">
            <a href="{{ route('points.katalog') }}" class="btn-glass">
                <i class="bi bi-gift-fill"></i> Tukar Poin
            </a>
            <span class="date-chip"><i class="bi bi-calendar3"></i> {{ now()->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    {{-- ══════════ HERO BANNER ══════════ --}}
    <div class="hero-banner mb-4">
        <div class="hero-bg-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
        <div class="hero-content">
            <div class="hero-badge"><i class="bi bi-shield-check"></i> Program Apresiasi Petugas RSI Sakinah</div>
            <div class="hero-points-label">Total Poin Terkumpul</div>
            <div class="hero-points-value">
                <span class="points-number">{{ number_format($totalPoin) }}</span>
                <span class="points-unit">Poin</span>
            </div>
            <div class="hero-sub">dari <strong>{{ $riwayat->count() }}</strong> pendaftaran yang berhasil Anda proses</div>
            <div class="hero-progress-wrap mt-3">
                @php $pct = min(100, ($totalPoin % 500) / 500 * 100); @endphp
                <div class="hero-prog-label">
                    <span>Menuju Level Berikutnya</span>
                    <span>{{ $totalPoin % 500 }} / 500 Poin</span>
                </div>
                <div class="hero-progress">
                    <div class="hero-progress-bar" style="width:{{ $pct }}%"></div>
                </div>
            </div>
        </div>
        <div class="hero-medal-wrap">
            <div class="hero-medal">
                <i class="bi bi-award-fill"></i>
            </div>
            <div class="hero-medal-label">
                @if($totalPoin >= 500) Platinum @elseif($totalPoin >= 200) Gold @elseif($totalPoin >= 50) Silver @else Bronze @endif
            </div>
        </div>
    </div>

    {{-- ══════════ STAT CARDS ══════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="kpi-card kpi-green fade-in">
                <div class="kpi-icon-wrap">
                    <div class="kpi-icon"><i class="bi bi-star-fill"></i></div>
                    <div class="kpi-trend up"><i class="bi bi-arrow-up-short"></i> Aktif</div>
                </div>
                <div class="kpi-label">Total Poin</div>
                <div class="kpi-value">{{ number_format($totalPoin) }}</div>
                <div class="kpi-foot">Akumulasi poin Anda saat ini</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="kpi-card kpi-blue fade-in fade-in-delay-1">
                <div class="kpi-icon-wrap">
                    <div class="kpi-icon"><i class="bi bi-hospital-fill"></i></div>
                    <div class="kpi-trend up"><i class="bi bi-bar-chart-fill"></i> Top</div>
                </div>
                <div class="kpi-label">Poli Teraktif</div>
                @php $topPoli = $rekapPerPoli->sortByDesc('total')->first(); @endphp
                <div class="kpi-value-sm">{{ $topPoli->department->name ?? '—' }}</div>
                <div class="kpi-foot">{{ $topPoli->total ?? 0 }} poin dari poli ini</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="kpi-card kpi-amber fade-in fade-in-delay-2">
                <div class="kpi-icon-wrap">
                    <div class="kpi-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                    <div class="kpi-trend"><i class="bi bi-layers"></i> Semua</div>
                </div>
                <div class="kpi-label">Total Poli Dilayani</div>
                <div class="kpi-value">{{ $rekapPerPoli->count() }}</div>
                <div class="kpi-foot">Poli yang pernah Anda tangani</div>
            </div>
        </div>
    </div>

    {{-- ══════════ REKAP PER POLI ══════════ --}}
    <div class="rs-panel mb-4 fade-in fade-in-delay-3">
        <div class="rs-panel-head">
            <div class="rs-panel-title">
                <span class="rs-panel-icon"><i class="bi bi-clipboard2-pulse-fill"></i></span>
                Rekap Poin per Poli
            </div>
            <span class="rs-panel-count">{{ $rekapPerPoli->count() }} poli</span>
        </div>
        <div class="rs-panel-body">
            @if($rekapPerPoli->count() > 0)
            <div class="poli-grid">
                @foreach($rekapPerPoli->sortByDesc('total') as $rekap)
                <div class="poli-item">
                    <div class="poli-icon"><i class="bi bi-activity"></i></div>
                    <div class="poli-info">
                        <div class="poli-name">{{ $rekap->department->name ?? '—' }}</div>
                        <div class="poli-bar-wrap">
                            @php $maxTotal = $rekapPerPoli->max('total'); $barPct = $maxTotal > 0 ? ($rekap->total / $maxTotal * 100) : 0; @endphp
                            <div class="poli-bar"><div class="poli-bar-fill" style="width:{{ $barPct }}%"></div></div>
                        </div>
                    </div>
                    <div class="poli-pts">{{ number_format($rekap->total) }} <span>poin</span></div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p>Belum ada data rekap poli.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ══════════ RIWAYAT POIN ══════════ --}}
    <div class="rs-panel mb-4 fade-in fade-in-delay-4">
        <div class="rs-panel-head">
            <div class="rs-panel-title">
                <span class="rs-panel-icon"><i class="bi bi-clock-history"></i></span>
                Riwayat Perolehan Poin
            </div>
            <span class="rs-panel-count">{{ $riwayat->count() }} entri</span>
        </div>
        <div class="table-responsive">
            <table class="rs-table datatable">
                <thead>
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Pasien</th>
                        <th>Poli</th>
                        <th class="text-end pe-4">Poin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)
                    @php $nama = $item->registration?->patient?->nama_pasien ?? '-'; @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="date-cell">
                                <div class="date-main">{{ $item->created_at->translatedFormat('d M Y') }}</div>
                                <div class="date-time">{{ $item->created_at->format('H:i') }} WIB</div>
                            </div>
                        </td>
                        <td>
                            <div class="patient-cell">
                                <div class="patient-avatar">{{ strtoupper(substr($nama, 0, 1)) }}</div>
                                <span class="patient-name">{{ $nama }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="dept-chip">{{ $item->department?->nama_poli ?? '-' }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <span class="point-badge">+{{ $item->points }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-5">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>Belum ada riwayat poin.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════ RIWAYAT PENUKARAN ══════════ --}}
    <div class="rs-panel fade-in">
        <div class="rs-panel-head">
            <div class="rs-panel-title">
                <span class="rs-panel-icon gold"><i class="bi bi-arrow-left-right"></i></span>
                Riwayat Penukaran Poin
            </div>
            <span class="rs-panel-count gold-text">{{ $riwayatTukar->count() }} transaksi</span>
        </div>
        <div class="table-responsive">
            <table class="rs-table">
                <thead>
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th class="text-center">Poin</th>
                        <th class="text-center">Jenis</th>
                        <th class="text-center">Status</th>
                        <th>Catatan Admin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatTukar as $item)
                    <tr>
                        <td class="ps-4">
                            <div class="date-cell">
                                <div class="date-main">{{ $item->created_at->translatedFormat('d M Y') }}</div>
                                <div class="date-time">{{ $item->created_at->format('H:i') }} WIB</div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="pts-redeem">−{{ number_format($item->points) }}</span>
                        </td>
                        <td class="text-center">
                            <span class="type-chip">{{ $item->type_label }}</span>
                        </td>
                        <td class="text-center">
                            @php $sc = $item->status_color; @endphp
                            <span class="status-pill" style="background:{{ $sc['bg'] }}; color:{{ $sc['color'] }};">
                                {{ $item->status_label }}
                            </span>
                        </td>
                        <td class="note-cell">{{ $item->catatan ?: '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-5">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>Belum ada riwayat penukaran poin.</p>
                                <a href="{{ route('points.katalog') }}" class="btn-empty">Lihat Katalog</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
/* ── BASE ── */
.points-page { font-family: 'Plus Jakarta Sans', sans-serif; }

/* ── PAGE HEADER ── */
.ph-wrap {
    display: flex; align-items: flex-start;
    justify-content: space-between; flex-wrap: wrap; gap: 16px;
}
.bc-nav {
    display: flex; align-items: center; gap: 6px;
    font-size: 12.5px; color: #64748B; margin-bottom: 8px;
}
.bc-nav a { color: var(--primary); text-decoration: none; font-weight: 500; }
.bc-nav a:hover { text-decoration: underline; }
.bc-nav i.bi-chevron-right { font-size: 10px; opacity: .5; }
.ph-title {
    font-size: 1.55rem; font-weight: 800; color: #0B1D17;
    letter-spacing: -.5px; margin: 0 0 4px;
}
.ph-sub { font-size: 13.5px; color: #64748B; margin: 0; max-width: 520px; }
.ph-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 6px; }

.btn-glass {
    display: inline-flex; align-items: center; gap: 7px;
    background: var(--primary); color: #fff;
    font-size: 13px; font-weight: 700;
    padding: 9px 18px; border-radius: 10px;
    text-decoration: none; transition: background .2s, transform .15s;
    box-shadow: 0 4px 14px -4px rgba(15,123,99,.45);
}
.btn-glass:hover { background: var(--primary-dark); color: #fff; transform: translateY(-1px); }

.date-chip {
    display: inline-flex; align-items: center; gap: 6px;
    background: #fff; border: 1px solid #E2E8F0;
    color: #374151; font-size: 12.5px; font-weight: 500;
    padding: 9px 16px; border-radius: 999px;
}
.date-chip i { color: var(--primary); }

/* ── HERO BANNER ── */
.hero-banner {
    position: relative; overflow: hidden;
    background: linear-gradient(135deg, #083D2E 0%, #0F7B63 50%, #0E7490 100%);
    border-radius: 22px; padding: 36px 40px;
    display: flex; align-items: center;
    justify-content: space-between; gap: 24px;
    color: #fff; box-shadow: 0 20px 60px -20px rgba(8,61,46,.5);
}
.hero-bg-shapes { position: absolute; inset: 0; pointer-events: none; }
.shape {
    position: absolute; border-radius: 50%;
    background: rgba(255,255,255,.05);
}
.shape-1 { width: 300px; height: 300px; top: -80px; right: 200px; }
.shape-2 { width: 180px; height: 180px; bottom: -50px; right: 350px; }
.shape-3 { width: 120px; height: 120px; top: 20px; left: 200px; }

.hero-content { position: relative; z-index: 1; flex: 1; }
.hero-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,.15); backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 999px; padding: 5px 14px;
    font-size: 11px; font-weight: 700; letter-spacing: .04em;
    color: #A7F3D0; margin-bottom: 14px;
}
.hero-points-label { font-size: 11.5px; font-weight: 600; text-transform: uppercase;
    letter-spacing: .08em; color: rgba(255,255,255,.6); margin-bottom: 4px; }
.hero-points-value { display: flex; align-items: baseline; gap: 10px; margin-bottom: 6px; }
.points-number {
    font-family: 'Spectral', serif; font-size: 3.8rem;
    font-weight: 900; line-height: 1; letter-spacing: -2px;
}
.points-unit { font-size: 1.1rem; font-weight: 600; opacity: .7; }
.hero-sub { font-size: 13px; color: rgba(255,255,255,.72); }

.hero-prog-label {
    display: flex; justify-content: space-between;
    font-size: 11.5px; color: rgba(255,255,255,.65); margin-bottom: 6px;
}
.hero-progress {
    height: 6px; background: rgba(255,255,255,.2);
    border-radius: 999px; overflow: hidden;
}
.hero-progress-bar {
    height: 100%; background: #A7F3D0;
    border-radius: 999px; transition: width .6s ease;
}

.hero-medal-wrap {
    position: relative; z-index: 1;
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    flex-shrink: 0;
}
.hero-medal {
    width: 96px; height: 96px; border-radius: 50%;
    background: rgba(255,255,255,.12);
    border: 3px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 3rem; color: #FCD34D;
    box-shadow: 0 0 40px rgba(252,211,77,.25);
    animation: medalPulse 3s ease-in-out infinite;
}
.hero-medal-label {
    font-size: 12px; font-weight: 800; letter-spacing: .08em;
    color: #FCD34D; text-transform: uppercase;
}

@keyframes medalPulse {
    0%,100% { box-shadow: 0 0 30px rgba(252,211,77,.2); }
    50% { box-shadow: 0 0 50px rgba(252,211,77,.4); }
}

/* ── KPI CARDS ── */
.kpi-card {
    background: #fff; border: 1px solid #E2E8F0;
    border-radius: 18px; padding: 22px 24px;
    height: 100%; position: relative; overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}
.kpi-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0;
    height: 3px; border-radius: 18px 18px 0 0;
}
.kpi-green::before { background: linear-gradient(90deg, #059669, #34D399); }
.kpi-blue::before  { background: linear-gradient(90deg, #0284C7, #38BDF8); }
.kpi-amber::before { background: linear-gradient(90deg, #D97706, #FCD34D); }
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px -10px rgba(0,0,0,.12); }

.kpi-icon-wrap { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.kpi-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
}
.kpi-green .kpi-icon { background: #D1FAE5; color: #059669; }
.kpi-blue .kpi-icon  { background: #E0F2FE; color: #0284C7; }
.kpi-amber .kpi-icon { background: #FEF3C7; color: #D97706; }

.kpi-trend {
    font-size: 11px; font-weight: 700; padding: 4px 9px;
    border-radius: 999px; display: flex; align-items: center; gap: 3px;
}
.kpi-trend.up { background: #D1FAE5; color: #059669; }
.kpi-trend { background: #F1F5F9; color: #64748B; }

.kpi-label { font-size: 11.5px; font-weight: 600; text-transform: uppercase;
    letter-spacing: .06em; color: #94A3B8; margin-bottom: 6px; }
.kpi-value { font-family: 'Spectral', serif; font-size: 2.2rem;
    font-weight: 900; color: #0B1D17; line-height: 1.1; }
.kpi-value-sm { font-size: 1.2rem; font-weight: 700; color: #0B1D17; line-height: 1.3; }
.kpi-foot { font-size: 12px; color: #94A3B8; margin-top: 8px; }

/* ── RS PANEL ── */
.rs-panel {
    background: #fff; border: 1px solid #E2E8F0;
    border-radius: 20px; overflow: hidden;
    box-shadow: 0 2px 16px -8px rgba(0,0,0,.07);
}
.rs-panel-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 24px; border-bottom: 1px solid #F1F5F9;
}
.rs-panel-title {
    display: flex; align-items: center; gap: 10px;
    font-weight: 700; font-size: 15px; color: #0B1D17;
}
.rs-panel-icon {
    width: 34px; height: 34px; border-radius: 10px;
    background: var(--primary-soft); color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
}
.rs-panel-icon.gold { background: #FEF3C7; color: #D97706; }
.rs-panel-count {
    font-size: 12px; font-weight: 600; padding: 4px 12px;
    background: #F1F5F9; color: #64748B; border-radius: 999px;
}
.rs-panel-count.gold-text { background: #FEF3C7; color: #92400E; }
.rs-panel-body { padding: 20px 24px; }

/* ── POLI GRID ── */
.poli-grid { display: flex; flex-direction: column; gap: 12px; }
.poli-item {
    display: flex; align-items: center; gap: 14px;
    padding: 12px 14px; background: #F8FAFC;
    border-radius: 12px; border: 1px solid #F1F5F9;
    transition: background .2s;
}
.poli-item:hover { background: #F0FDF6; border-color: #D3F0E0; }
.poli-icon {
    width: 36px; height: 36px; border-radius: 10px;
    background: var(--primary-soft); color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
}
.poli-info { flex: 1; min-width: 0; }
.poli-name { font-size: 13.5px; font-weight: 600; color: #0B1D17; margin-bottom: 6px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.poli-bar-wrap { width: 100%; }
.poli-bar { height: 5px; background: #E2E8F0; border-radius: 999px; overflow: hidden; }
.poli-bar-fill { height: 100%; background: linear-gradient(90deg, #0F7B63, #34D399);
    border-radius: 999px; transition: width .5s ease; }
.poli-pts { font-size: 13px; font-weight: 800; color: var(--primary);
    white-space: nowrap; text-align: right; min-width: 60px; }
.poli-pts span { font-size: 11px; font-weight: 500; color: #94A3B8; }

/* ── TABLE ── */
.rs-table { width: 100%; border-collapse: collapse; }
.rs-table thead tr { background: #F8FAFC; }
.rs-table thead th {
    font-size: 11.5px; font-weight: 700; color: #64748B;
    text-transform: uppercase; letter-spacing: .05em;
    padding: 14px 14px; border-bottom: 2px solid #E2E8F0;
}
.rs-table tbody td {
    padding: 14px 14px; font-size: 13.5px;
    border-bottom: 1px solid #F1F5F9; vertical-align: middle;
    color: #374151;
}
.rs-table tbody tr:last-child td { border-bottom: none; }
.rs-table tbody tr:hover { background: #F8FAFC; }

.date-cell .date-main { font-size: 13.5px; font-weight: 600; color: #0B1D17; }
.date-cell .date-time { font-size: 11.5px; color: #94A3B8; margin-top: 2px; }

.patient-cell { display: flex; align-items: center; gap: 10px; }
.patient-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    background: linear-gradient(135deg, #0F7B63, #34D399);
    color: #fff; font-weight: 800; font-size: 13px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.patient-name { font-weight: 600; color: #0B1D17; }

.dept-chip {
    display: inline-block; background: #E0F2FE; color: #0369A1;
    font-size: 11.5px; font-weight: 700; padding: 4px 10px;
    border-radius: 8px; white-space: nowrap;
}

.point-badge {
    display: inline-flex; align-items: center;
    background: linear-gradient(135deg, #D1FAE5, #A7F3D0);
    color: #065F46; font-weight: 800; font-size: 13px;
    padding: 5px 13px; border-radius: 999px;
    border: 1px solid #6EE7B7;
}

.pts-redeem {
    font-weight: 800; color: #DC2626; font-size: 1rem;
}

.type-chip {
    display: inline-block; background: #FEF3C7; color: #92400E;
    font-size: 11.5px; font-weight: 700; padding: 4px 10px;
    border-radius: 8px;
}

.status-pill {
    display: inline-block; font-size: 11.5px; font-weight: 700;
    padding: 5px 12px; border-radius: 999px; text-transform: uppercase;
    letter-spacing: .04em;
}

.note-cell { font-size: 12.5px; color: #64748B; max-width: 200px; }

/* ── EMPTY STATE ── */
.empty-state {
    display: flex; flex-direction: column; align-items: center;
    gap: 8px; padding: 20px; text-align: center;
}
.empty-state i { font-size: 2.5rem; color: #CBD5E1; }
.empty-state p { font-size: 13.5px; color: #94A3B8; margin: 0; }
.btn-empty {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--primary); color: #fff;
    font-size: 12.5px; font-weight: 700;
    padding: 7px 16px; border-radius: 8px;
    text-decoration: none; margin-top: 6px;
    transition: background .2s;
}
.btn-empty:hover { background: var(--primary-dark); color: #fff; }

@media (max-width: 768px) {
    .hero-banner { flex-direction: column; padding: 28px 24px; }
    .hero-medal { width: 72px; height: 72px; font-size: 2.2rem; }
    .points-number { font-size: 2.8rem; }
    .ph-wrap { flex-direction: column; }
}
</style>
@endsection