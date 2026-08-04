@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --rs-green:       #0B6B4F;
        --rs-green-dark:  #063D2C;
        --rs-green-light: #12885F;
        --rs-gold:        #C9A227;
        --rs-gold-light:  #E8C766;
        --rs-gold-dark:   #9C7A1A;
        --rs-tile:        #0E7490;
        --rs-tile-light:  #0891B2;
        --rs-cream:       #FBF9F3;
        --rs-ink:         #1B2B24;
        --rs-muted:       #64766D;
    }

    /* ---------- Ambient Islamic geometric watermark ---------- */
    .rs-star-field {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64'%3E%3Cg fill='none' stroke='%23C9A227' stroke-width='1' opacity='0.16'%3E%3Crect x='16' y='16' width='32' height='32' transform='rotate(45 32 32)'/%3E%3Crect x='16' y='16' width='32' height='32'/%3E%3C/g%3E%3C/svg%3E");
        background-repeat: repeat;
    }

    /* ---------- Welcome ribbon ---------- */
    .rs-hero {
        position: relative;
        overflow: hidden;
        border-radius: 18px;
        padding: 22px 26px;
        margin-bottom: 20px;
        background: linear-gradient(120deg, var(--rs-green-dark) 0%, var(--rs-green) 55%, var(--rs-green-light) 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-shadow: 0 12px 28px rgba(11,107,79,.25);
    }
    .rs-hero::before {
        content: "";
        position: absolute; inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64'%3E%3Cg fill='none' stroke='%23ffffff' stroke-width='1' opacity='0.10'%3E%3Crect x='16' y='16' width='32' height='32' transform='rotate(45 32 32)'/%3E%3Crect x='16' y='16' width='32' height='32'/%3E%3C/g%3E%3C/svg%3E");
    }
    .rs-hero-text { position: relative; z-index: 1; }
    .rs-hero-eyebrow {
        font-size: .72rem; letter-spacing: .12em; text-transform: uppercase;
        color: var(--rs-gold-light); font-weight: 700; margin-bottom: 4px;
        display: flex; align-items: center; gap: 6px;
    }
    .rs-hero-title {
        font-family: 'Amiri', serif; font-weight: 700; font-size: 1.5rem; margin: 0;
    }
    .rs-hero-sub { font-size: .85rem; opacity: .85; margin-top: 4px; }
    .rs-hero-badge {
        position: relative; z-index: 1;
        width: 58px; height: 58px;
        border-radius: 50% 50% 12px 12px;
        background: rgba(255,255,255,.16);
        border: 1px solid rgba(232,199,102,.6);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; color: var(--rs-gold-light); flex-shrink: 0;
    }

    /* ---------- Stat cards — Islamic architecture palette ---------- */
    .stat-card { border-radius: 16px; position: relative; overflow: hidden; }
    .stat-card-green  { background: linear-gradient(135deg,var(--rs-green-dark),var(--rs-green-light)); color:#fff; }
    .stat-card-gold   { background: linear-gradient(135deg,var(--rs-gold-dark),var(--rs-gold)); color:#fff; }
    .stat-card-emerald{ background: linear-gradient(135deg,#065F46,#059669); color:#fff; }
    .stat-card-tile   { background: linear-gradient(135deg,var(--rs-tile),var(--rs-tile-light)); color:#fff; }
    .stat-card::after {
        content: "";
        position: absolute; top: -18px; right: -18px;
        width: 70px; height: 70px; opacity: .18;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64'%3E%3Cg fill='none' stroke='%23ffffff' stroke-width='2'%3E%3Crect x='16' y='16' width='32' height='32' transform='rotate(45 32 32)'/%3E%3Crect x='16' y='16' width='32' height='32'/%3E%3C/g%3E%3C/svg%3E");
    }
    /* dome-shaped icon badge instead of plain circle */
    .stat-icon-white {
        background: rgba(255,255,255,.22); color:#fff;
        border-radius: 50% 50% 8px 8px;
        border: 1px solid rgba(255,255,255,.35);
    }

    /* ---------- Section headers with gold ornamental divider ---------- */
    .rs-card-title {
        display: flex; align-items: center; gap: 8px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .rs-star-bullet {
        color: var(--rs-gold); font-size: .7rem; line-height: 1;
    }

    /* ---------- Quick actions ---------- */
    .quick-action-btn {
        background: var(--rs-cream); border: 1.5px solid #e7e1d0; border-radius:14px;
        padding:18px; text-align:center; text-decoration:none; color: var(--rs-ink);
        transition: all .25s ease; display:flex; flex-direction:column;
        align-items:center; gap:8px; position: relative;
    }
    .quick-action-btn:hover {
        border-color: var(--rs-gold); color: var(--rs-green-dark);
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(201,162,39,.22);
        background: #fff;
    }
    .quick-action-btn .icon { font-size:1.5rem; }
    .quick-action-btn span  { font-size:.8rem; font-weight:700; }

    .chart-container { position:relative; height:220px; }

    /* ---------- Antrian list — arch queue numbers ---------- */
    .antrian-item {
        display:flex; align-items:center; gap:12px;
        padding:11px 4px; border-bottom:1px dashed #e7e1d0;
    }
    .antrian-item:last-child { border-bottom:none; }
    .antrian-no {
        width:44px; height:44px;
        border-radius: 50% 50% 10px 10px;
        background: linear-gradient(160deg, var(--rs-green), var(--rs-green-dark));
        color:#fff; border: 1px solid var(--rs-gold-light);
        display:flex; align-items:center; justify-content:center;
        font-weight:800; font-size:.85rem; flex-shrink:0;
        box-shadow: 0 3px 8px rgba(6,61,44,.25);
    }

    /* ---------- Info ringkasan panel ---------- */
    .rs-info-panel {
        background: linear-gradient(135deg, #F4F9F6, #FBF9F3);
        border: 1px solid #d7ead9;
        position: relative; overflow: hidden;
    }
    .rs-info-panel::before {
        content: "";
        position: absolute; inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64'%3E%3Cg fill='none' stroke='%230B6B4F' stroke-width='1' opacity='0.06'%3E%3Crect x='16' y='16' width='32' height='32' transform='rotate(45 32 32)'/%3E%3Crect x='16' y='16' width='32' height='32'/%3E%3C/g%3E%3C/svg%3E");
    }
    .rs-info-panel * { position: relative; z-index: 1; }
    .rs-info-title { color: var(--rs-green-dark) !important; }
    .rs-info-row { color: var(--rs-green-dark) !important; }
</style>
@endpush

@section('content')

<!-- Welcome Ribbon -->
<div class="rs-hero fade-in">
    <div class="rs-hero-text">
        <div class="rs-hero-eyebrow"><i class="bi bi-moon-stars-fill"></i> Assalamu'alaikum Warahmatullah</div>
        <h2 class="rs-hero-title">Dashboard Pelayanan RS Islam</h2>
        <div class="rs-hero-sub">Ringkasan aktivitas pendaftaran &amp; antrian pasien hari ini, {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
    </div>
    <div class="rs-hero-badge"><i class="bi bi-hospital"></i></div>
</div>

<div class="row g-3 mb-4">
    <!-- Stat Cards Row 1 -->
    <div class="col-6 col-lg-3 fade-in">
        <div class="stat-card stat-card-green card">
            <div class="stat-icon stat-icon-white"><i class="bi bi-clipboard2-check"></i></div>
            <div>
                <div class="stat-label">Pendaftaran Hari Ini</div>
                <div class="stat-value">{{ $stats['total_pendaftaran_hari_ini'] }}</div>
                <div class="stat-sub">Total pendaftar</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 fade-in fade-in-delay-1">
        <div class="stat-card stat-card-gold card">
            <div class="stat-icon stat-icon-white"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="stat-label">Menunggu</div>
                <div class="stat-value">{{ $stats['menunggu'] }}</div>
                <div class="stat-sub">Antrian aktif</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 fade-in fade-in-delay-2">
        <div class="stat-card stat-card-emerald card">
            <div class="stat-icon stat-icon-white"><i class="bi bi-check2-circle"></i></div>
            <div>
                <div class="stat-label">Selesai</div>
                <div class="stat-value">{{ $stats['selesai'] }}</div>
                <div class="stat-sub">Dilayani hari ini</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 fade-in fade-in-delay-3">
        <div class="stat-card stat-card-tile card">
            <div class="stat-icon stat-icon-white"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-label">Total Pasien</div>
                <div class="stat-value">{{ $stats['total_pasien'] }}</div>
                <div class="stat-sub">Terdaftar sistem</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Grafik 7 Hari -->
    <div class="col-lg-7 fade-in">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="rs-card-title"><i class="bi bi-graph-up text-primary"></i>Tren Pendaftaran 7 Hari <span class="rs-star-bullet">✦</span></span>
                <span class="badge" style="background:#eaf4ef;color:var(--rs-green-dark);font-size:.72rem;">Terakhir 7 Hari</span>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="chartPendaftaran"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendaftaran Per Poli -->
    <div class="col-lg-5 fade-in fade-in-delay-1">
        <div class="card h-100">
            <div class="card-header"><span class="rs-card-title"><i class="bi bi-pie-chart text-primary"></i>Per Poli Hari Ini <span class="rs-star-bullet">✦</span></span></div>
            <div class="card-body">
                @forelse($pendaftaranPerPoli as $p)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="fw-600" style="font-size:.85rem;">{{ $p['nama_poli'] }}</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress" style="width:100px;height:8px;border-radius:4px;background:#f1ede1;">
                            <div class="progress-bar" role="progressbar"
                                 style="width:{{ $stats['total_pendaftaran_hari_ini'] > 0 ? round($p['total']/$stats['total_pendaftaran_hari_ini']*100) : 0 }}%;background:linear-gradient(90deg,var(--rs-green),var(--rs-gold));">
                            </div>
                        </div>
                        <span class="badge" style="background:var(--rs-green-dark);color:#fff;min-width:28px;">{{ $p['total'] }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox" style="font-size:2rem;"></i>
                    <p class="mt-2 mb-0" style="font-size:.85rem;">Belum ada pendaftaran hari ini</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Antrian Terbaru -->
    <div class="col-lg-8 fade-in">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="rs-card-title"><i class="bi bi-list-ol text-primary"></i>Antrian Aktif Hari Ini <span class="rs-star-bullet">✦</span></span>
                <a href="{{ route('antrian.index') }}" class="btn btn-sm" style="background:#eaf4ef;color:var(--rs-green-dark);font-size:.75rem;font-weight:700;">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body py-2">
                @forelse($antrianTerbaru as $reg)
                <div class="antrian-item">
                    <div class="antrian-no">{{ $reg->nomor_antrian }}</div>
                    <div class="flex-1">
                        <div class="fw-600" style="font-size:.875rem;">{{ $reg->patient->nama_pasien }}</div>
                        <div style="font-size:.75rem;color:var(--rs-muted);">
                            {{ $reg->department->nama_poli }} · dr. {{ $reg->doctor->nama_dokter }}
                        </div>
                    </div>
                    <span class="badge badge-{{ $reg->status }}">{{ $reg->status_label }}</span>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-calendar-x" style="font-size:2rem;"></i>
                    <p class="mt-2 mb-0" style="font-size:.85rem;">Belum ada antrian aktif hari ini</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 fade-in fade-in-delay-1">
        <div class="card">
            <div class="card-header"><span class="rs-card-title"><i class="bi bi-lightning text-primary"></i>Aksi Cepat <span class="rs-star-bullet">✦</span></span></div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('registrations.create') }}" class="quick-action-btn">
                            <span class="icon" style="color:var(--rs-gold-dark)"><i class="bi bi-clipboard2-plus-fill"></i></span>
                            <span>Daftar Pasien</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('patients.create') }}" class="quick-action-btn">
                            <span class="icon" style="color:var(--rs-green)"><i class="bi bi-person-plus-fill"></i></span>
                            <span>Pasien Baru</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('antrian.index') }}" class="quick-action-btn">
                            <span class="icon" style="color:var(--rs-tile)"><i class="bi bi-display"></i></span>
                            <span>Monitor Antrian</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('registrations.index') }}" class="quick-action-btn">
                            <span class="icon" style="color:var(--rs-green-dark)"><i class="bi bi-table"></i></span>
                            <span>Semua Pendaftaran</span>
                        </a>
                    </div>
                </div>

                <!-- Info Hari Ini -->
                <div class="mt-3 p-3 rounded-3 rs-info-panel">
                    <div class="rs-info-title" style="font-size:.75rem;font-weight:700;margin-bottom:8px;">
                        <i class="bi bi-info-circle me-1"></i>Ringkasan Hari Ini
                    </div>
                    <div class="d-flex justify-content-between rs-info-row" style="font-size:.78rem;">
                        <span>Dokter Aktif</span><strong>{{ $stats['total_dokter'] }}</strong>
                    </div>
                    <div class="d-flex justify-content-between rs-info-row" style="font-size:.78rem;">
                        <span>Poli Aktif</span><strong>{{ $stats['total_poli'] }}</strong>
                    </div>
                    <div class="d-flex justify-content-between rs-info-row" style="font-size:.78rem;">
                        <span>Dipanggil</span><strong>{{ $stats['dipanggil'] }}</strong>
                    </div>
                    <div class="d-flex justify-content-between rs-info-row" style="font-size:.78rem;">
                        <span>Batal</span><strong>{{ $stats['batal'] }}</strong>
                    </div>
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
gradientFill.addColorStop(0, 'rgba(11,107,79,.28)');
gradientFill.addColorStop(1, 'rgba(11,107,79,.02)');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($labels7Hari) !!},
        datasets: [{
            label: 'Pendaftaran',
            data: {!! json_encode($data7Hari) !!},
            backgroundColor: gradientFill,
            borderColor: '#0B6B4F',
            borderWidth: 2,
            borderRadius: 8,
            fill: true,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 11, family: 'Plus Jakarta Sans' } } },
            y: { beginAtZero: true, grid: { color: '#f1ede1' }, ticks: { precision: 0, font: { size: 11 } } }
        }
    }
});
</script>
@endpush