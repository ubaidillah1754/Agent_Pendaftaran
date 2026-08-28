@extends('layouts.app')
@section('title', 'Poin Saya — My Sakinah Agent')
@section('page-title', 'Poin Saya')
@section('page-subtitle', 'Pantau akumulasi perolehan poin dari input pasien dan reward yang tersedia.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Poin Saya</li>
@endsection

@section('content')

{{-- ══════════ HERO BANNER ══════════ --}}
<div class="pi-hero mb-4">
    <div class="pi-hero-shapes">
        <div class="pi-shape pi-s1"></div>
        <div class="pi-shape pi-s2"></div>
        <div class="pi-shape pi-s3"></div>
    </div>
    <div class="pi-hero-left">
        <div class="pi-hero-eyebrow">
            <i class="bi bi-shield-fill-check"></i> Program Apresiasi Petugas RSI Sakinah
        </div>
        <div class="pi-hero-greeting">Selamat datang, <strong>{{ auth()->user()->name }}</strong>!</div>
        <div class="pi-hero-pts">
            <span class="pi-pts-num">{{ number_format($saldoPoin) }}</span>
            <div class="pi-pts-meta">
                <span class="pi-pts-unit">Poin Aktif</span>
                <span class="pi-pts-eq">≈ Rp {{ number_format($saldoPoin * 1000, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="pi-hero-level">
            @php
                $lvl = $saldoPoin >= 500 ? ['Platinum','bi-gem','#E2D9F3','#6F42C1'] :
                      ($saldoPoin >= 200 ? ['Gold','bi-trophy-fill','#FEF3C7','#D97706'] :
                      ($saldoPoin >= 50  ? ['Silver','bi-award-fill','#F1F5F9','#64748B'] :
                                          ['Bronze','bi-star-fill','#FEF6EE','#C2410C']));
            @endphp
            <span class="pi-level-badge" style="background:{{ $lvl[2] }}; color:{{ $lvl[3] }};">
                <i class="bi {{ $lvl[1] }}"></i> Level {{ $lvl[0] }}
            </span>
            <span class="pi-hero-earned">Total diperoleh: <b>{{ number_format($totalEarned) }} poin</b></span>
        </div>
        <div class="pi-hero-actions">
            <a href="{{ route('points.katalog') }}" class="pi-btn-primary">
                <i class="bi bi-gift-fill"></i> Tukar Poin Sekarang
            </a>
            <a href="{{ route('points.riwayat') }}" class="pi-btn-ghost">
                <i class="bi bi-clock-history"></i> Lihat Riwayat
            </a>
        </div>
    </div>
    <div class="pi-hero-right">
        <div class="pi-medal-ring">
            <div class="pi-medal">
                <i class="bi bi-award-fill"></i>
            </div>
        </div>
        <div class="pi-medal-lbl">{{ $lvl[0] }} Member</div>
    </div>
</div>

{{-- ══════════ STAT CARDS ══════════ --}}
<div class="pi-stats mb-4">
    <div class="pi-stat-card pi-sc-green fade-in">
        <div class="pi-sc-header">
            <div class="pi-sc-icon"><i class="bi bi-star-fill"></i></div>
            <span class="pi-sc-trend up"><i class="bi bi-arrow-up-short"></i>Aktif</span>
        </div>
        <div class="pi-sc-label">Saldo Poin</div>
        <div class="pi-sc-value">{{ number_format($saldoPoin) }}</div>
        <div class="pi-sc-sub">Poin dapat ditukarkan</div>
    </div>
    <div class="pi-stat-card pi-sc-blue fade-in fade-in-delay-1">
        <div class="pi-sc-header">
            <div class="pi-sc-icon"><i class="bi bi-person-check-fill"></i></div>
            <span class="pi-sc-trend"><i class="bi bi-people"></i>Total</span>
        </div>
        <div class="pi-sc-label">Pasien Diinput</div>
        <div class="pi-sc-value">{{ number_format($totalPasien) }}</div>
        <div class="pi-sc-sub">Total pasien baru Anda</div>
    </div>
    <div class="pi-stat-card pi-sc-amber fade-in fade-in-delay-2">
        <div class="pi-sc-header">
            <div class="pi-sc-icon"><i class="bi bi-graph-up-arrow"></i></div>
            <span class="pi-sc-trend up"><i class="bi bi-arrow-up-short"></i>Kumulatif</span>
        </div>
        <div class="pi-sc-label">Total Diperoleh</div>
        <div class="pi-sc-value">{{ number_format($totalEarned) }}</div>
        <div class="pi-sc-sub">Akumulasi seumur hidup</div>
    </div>
    <div class="pi-stat-card pi-sc-slate fade-in fade-in-delay-3">
        <div class="pi-sc-header">
            <div class="pi-sc-icon"><i class="bi bi-gift-fill"></i></div>
            <span class="pi-sc-trend"><i class="bi bi-box-arrow-right"></i>Pakai</span>
        </div>
        <div class="pi-sc-label">Total Digunakan</div>
        <div class="pi-sc-value">{{ number_format($totalRedeemed) }}</div>
        <div class="pi-sc-sub">Telah ditukarkan reward</div>
    </div>
</div>

{{-- ══════════ MAIN CONTENT ══════════ --}}
<div class="row g-3">

    {{-- Reward Tersedia --}}
    <div class="col-lg-7 fade-in">
        <div class="pi-panel h-100">
            <div class="pi-panel-head">
                <div class="pi-panel-title">
                    <span class="pi-panel-icon gift"><i class="bi bi-gift-fill"></i></span>
                    Reward Tersedia
                </div>
                <a href="{{ route('points.katalog') }}" class="pi-panel-link">
                    Lihat Katalog Lengkap <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="pi-panel-body">
                @forelse($featuredRewards as $reward)
                <div class="pi-reward-item">
                    <div class="pi-reward-img">
                        <img src="{{ $reward->image_url }}" alt="{{ $reward->name }}">
                    </div>
                    <div class="pi-reward-info">
                        <div class="pi-reward-name">{{ $reward->name }}</div>
                        <div class="pi-reward-pts">
                            <i class="bi bi-star-fill"></i> {{ number_format($reward->points_required) }} Poin
                        </div>
                        <div class="pi-reward-stock">Stok: {{ $reward->stock }} unit</div>
                    </div>
                    <div class="pi-reward-action">
                        @if($saldoPoin >= $reward->points_required && $reward->stock > 0)
                        <a href="{{ route('points.katalog') }}" class="pi-btn-tukar">
                            Tukar <i class="bi bi-arrow-right"></i>
                        </a>
                        @elseif($reward->stock <= 0)
                        <span class="pi-btn-habis">Stok Habis</span>
                        @else
                        <span class="pi-btn-kurang" title="Butuh {{ number_format($reward->points_required - $saldoPoin) }} poin lagi">
                            <i class="bi bi-lock-fill"></i> Kurang
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="pi-empty">
                    <i class="bi bi-bag-x"></i>
                    <p>Belum ada reward aktif saat ini.</p>
                </div>
                @endforelse
                <div class="pi-reward-footer">
                    <a href="{{ route('points.katalog') }}" class="pi-see-all">
                        <i class="bi bi-grid-3x3-gap-fill me-1"></i>
                        Lihat Semua Katalog Reward
                        <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Mutasi Poin Terbaru --}}
    <div class="col-lg-5 fade-in fade-in-delay-1">
        <div class="pi-panel h-100">
            <div class="pi-panel-head">
                <div class="pi-panel-title">
                    <span class="pi-panel-icon"><i class="bi bi-clock-history"></i></span>
                    Mutasi Poin Terbaru
                </div>
                <a href="{{ route('points.riwayat') }}" class="pi-panel-link">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="pi-mutation-list">
                @forelse($recentTransactions as $tx)
                <div class="pi-mutation-item">
                    <div class="pi-mut-icon pi-mut-{{ $tx->type }}">
                        <i class="bi {{ $tx->type === 'earn' ? 'bi-plus-lg' : ($tx->type === 'redeem' ? 'bi-gift' : 'bi-arrow-left-right') }}"></i>
                    </div>
                    <div class="pi-mut-body">
                        <div class="pi-mut-desc">{{ Str::limit($tx->description, 42) }}</div>
                        <div class="pi-mut-meta">
                            {{ $tx->created_at->format('d M Y, H:i') }}
                            <span class="pi-mut-ref">• Ref: {{ $tx->reference }}</span>
                        </div>
                    </div>
                    <div class="pi-mut-right">
                        <div class="pi-mut-amount {{ $tx->amount > 0 ? 'plus' : 'minus' }}">
                            {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount) }}
                        </div>
                        <div class="pi-mut-sisa">Sisa: {{ number_format($tx->balance_after) }}</div>
                    </div>
                </div>
                @empty
                <div class="pi-empty p-4">
                    <i class="bi bi-inbox"></i>
                    <p>Belum ada mutasi poin. Daftarkan pasien baru untuk mendapatkan poin!</p>
                </div>
                @endforelse
            </div>
            @if(count($recentTransactions) > 0)
            <div class="pi-mut-footer">
                <a href="{{ route('points.riwayat') }}" class="pi-see-all">
                    <i class="bi bi-list-ul me-1"></i> Riwayat Lengkap
                    <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ══════════ INFO STRIP ══════════ --}}
<div class="pi-info-strip mt-4 fade-in">
    <div class="pi-info-item">
        <div class="pi-info-icon"><i class="bi bi-person-plus-fill"></i></div>
        <div>
            <div class="pi-info-title">Cara Mendapat Poin</div>
            <div class="pi-info-desc">Daftarkan pasien baru untuk mendapat poin otomatis</div>
        </div>
    </div>
    <div class="pi-info-divider"></div>
    <div class="pi-info-item">
        <div class="pi-info-icon gift"><i class="bi bi-gift-fill"></i></div>
        <div>
            <div class="pi-info-title">Tukar dengan Reward</div>
            <div class="pi-info-desc">Kunjungi katalog dan pilih hadiah favorit Anda</div>
        </div>
    </div>
    <div class="pi-info-divider"></div>
    <div class="pi-info-item">
        <div class="pi-info-icon gold"><i class="bi bi-cash-coin"></i></div>
        <div>
            <div class="pi-info-title">Nilai Poin</div>
            <div class="pi-info-desc">1 Poin = Rp 1.000 (ekuivalen tunai)</div>
        </div>
    </div>
</div>

<style>
/* ── HERO ── */
.pi-hero {
    position: relative; overflow: hidden;
    background: linear-gradient(135deg, #063D2C 0%, #0A5644 40%, #0F7B63 75%, #0E7490 100%);
    border-radius: 24px; padding: 36px 40px;
    display: flex; align-items: center; justify-content: space-between; gap: 28px;
    color: #fff; box-shadow: 0 24px 64px -20px rgba(6,61,44,.55);
}
.pi-hero-shapes { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
.pi-shape {
    position: absolute; border-radius: 50%;
    background: rgba(255,255,255,.055); pointer-events: none;
}
.pi-s1 { width: 380px; height: 380px; top: -120px; right: 160px; }
.pi-s2 { width: 220px; height: 220px; bottom: -70px; right: 360px; }
.pi-s3 { width: 140px; height: 140px; top: 20px; left: 220px; }

.pi-hero-left { position: relative; z-index: 1; flex: 1; min-width: 0; }
.pi-hero-eyebrow {
    display: inline-flex; align-items: center; gap: 7px;
    background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.22);
    backdrop-filter: blur(6px);
    border-radius: 999px; padding: 5px 14px;
    font-size: 11px; font-weight: 700; letter-spacing: .06em; color: #A7F3D0;
    margin-bottom: 10px;
}
.pi-hero-greeting { font-size: 13.5px; color: rgba(255,255,255,.7); margin-bottom: 8px; }
.pi-hero-pts {
    display: flex; align-items: baseline; gap: 14px; margin-bottom: 12px;
}
.pi-pts-num {
    font-family: 'Spectral', serif; font-size: 4rem;
    font-weight: 900; line-height: 1; letter-spacing: -3px; color: #fff;
}
.pi-pts-meta { display: flex; flex-direction: column; gap: 2px; }
.pi-pts-unit { font-size: 13px; font-weight: 700; color: rgba(255,255,255,.75); }
.pi-pts-eq { font-size: 12px; color: #A7F3D0; font-weight: 600; }

.pi-hero-level { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
.pi-level-badge {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 800; letter-spacing: .05em;
    padding: 5px 14px; border-radius: 999px; text-transform: uppercase;
}
.pi-hero-earned { font-size: 12.5px; color: rgba(255,255,255,.65); }

.pi-hero-actions { display: flex; gap: 10px; flex-wrap: wrap; }
.pi-btn-primary {
    display: inline-flex; align-items: center; gap: 7px;
    background: #fff; color: var(--primary-dark);
    font-size: 13px; font-weight: 800; padding: 10px 20px;
    border-radius: 11px; text-decoration: none;
    box-shadow: 0 6px 20px -6px rgba(0,0,0,.2);
    transition: all .2s;
}
.pi-btn-primary:hover { background: #F0FDF6; color: var(--primary-dark); transform: translateY(-1px); }
.pi-btn-ghost {
    display: inline-flex; align-items: center; gap: 7px;
    background: rgba(255,255,255,.12); color: #fff;
    border: 1px solid rgba(255,255,255,.25);
    font-size: 13px; font-weight: 700; padding: 10px 20px;
    border-radius: 11px; text-decoration: none;
    transition: background .2s;
}
.pi-btn-ghost:hover { background: rgba(255,255,255,.22); color: #fff; }

.pi-hero-right {
    position: relative; z-index: 1;
    display: flex; flex-direction: column; align-items: center; gap: 10px;
    flex-shrink: 0;
}
.pi-medal-ring {
    width: 110px; height: 110px; border-radius: 50%;
    background: rgba(255,255,255,.1);
    border: 2px solid rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    animation: piPulse 3s ease-in-out infinite;
}
.pi-medal {
    width: 80px; height: 80px; border-radius: 50%;
    background: rgba(255,255,255,.15); border: 2px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 2.6rem; color: #FCD34D;
}
.pi-medal-lbl {
    font-size: 11.5px; font-weight: 800; letter-spacing: .08em;
    color: #FCD34D; text-transform: uppercase;
}
@keyframes piPulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(252,211,77,.2); }
    50% { box-shadow: 0 0 0 12px rgba(252,211,77,.06); }
}

/* ── STAT CARDS ── */
.pi-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
}
.pi-stat-card {
    background: #fff; border: 1.5px solid #E2E8F0;
    border-radius: 20px; padding: 20px 22px; position: relative;
    overflow: hidden; transition: transform .2s, box-shadow .2s;
}
.pi-stat-card::after {
    content: ''; position: absolute; top: 0; left: 0; right: 0;
    height: 3px; border-radius: 20px 20px 0 0;
}
.pi-sc-green::after { background: linear-gradient(90deg,#059669,#34D399); }
.pi-sc-blue::after  { background: linear-gradient(90deg,#0284C7,#38BDF8); }
.pi-sc-amber::after { background: linear-gradient(90deg,#D97706,#FCD34D); }
.pi-sc-slate::after { background: linear-gradient(90deg,#475569,#94A3B8); }
.pi-stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px -10px rgba(0,0,0,.12); }

.pi-sc-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.pi-sc-icon {
    width: 42px; height: 42px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; font-size: 1rem;
}
.pi-sc-green .pi-sc-icon { background: #D1FAE5; color: #059669; }
.pi-sc-blue .pi-sc-icon  { background: #E0F2FE; color: #0284C7; }
.pi-sc-amber .pi-sc-icon { background: #FEF3C7; color: #D97706; }
.pi-sc-slate .pi-sc-icon { background: #F1F5F9; color: #475569; }
.pi-sc-trend {
    font-size: 11px; font-weight: 700; padding: 4px 9px;
    border-radius: 999px; display: flex; align-items: center; gap: 2px;
    background: #F1F5F9; color: #64748B;
}
.pi-sc-trend.up { background: #D1FAE5; color: #059669; }

.pi-sc-label { font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; color: #94A3B8; margin-bottom: 6px; }
.pi-sc-value { font-family: 'Spectral', serif; font-size: 2rem;
    font-weight: 900; color: #0B1D17; line-height: 1.1; margin-bottom: 4px; }
.pi-sc-sub { font-size: 12px; color: #94A3B8; }

/* ── PANELS ── */
.pi-panel {
    background: #fff; border: 1.5px solid #E2E8F0;
    border-radius: 22px; overflow: hidden;
    box-shadow: 0 2px 16px -8px rgba(0,0,0,.07);
    display: flex; flex-direction: column;
}
.pi-panel-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 22px; border-bottom: 1px solid #F1F5F9; flex-shrink: 0;
}
.pi-panel-title { display: flex; align-items: center; gap: 10px;
    font-weight: 800; font-size: 14.5px; color: #0B1D17; }
.pi-panel-icon {
    width: 34px; height: 34px; border-radius: 10px;
    background: var(--primary-soft); color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; flex-shrink: 0;
}
.pi-panel-icon.gift { background: #FEF3C7; color: #D97706; }
.pi-panel-link {
    font-size: 12.5px; font-weight: 700; color: var(--primary);
    text-decoration: none; display: flex; align-items: center; gap: 4px;
    white-space: nowrap;
}
.pi-panel-link:hover { color: var(--primary-dark); text-decoration: underline; }
.pi-panel-body { padding: 16px 22px; flex: 1; }

/* Reward items */
.pi-reward-item {
    display: flex; align-items: center; gap: 14px;
    padding: 12px 0; border-bottom: 1px solid #F1F5F9;
}
.pi-reward-item:last-of-type { border-bottom: none; }
.pi-reward-img {
    width: 56px; height: 56px; border-radius: 12px;
    overflow: hidden; flex-shrink: 0;
    background: #F0FDF6; border: 1px solid #E2E8F0;
}
.pi-reward-img img { width: 100%; height: 100%; object-fit: cover; }
.pi-reward-info { flex: 1; min-width: 0; }
.pi-reward-name { font-size: 13.5px; font-weight: 700; color: #0B1D17;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pi-reward-pts { font-size: 12.5px; font-weight: 800; color: #D97706; margin: 2px 0; }
.pi-reward-pts i { color: #F59E0B; }
.pi-reward-stock { font-size: 11.5px; color: #94A3B8; }
.pi-reward-action { flex-shrink: 0; }
.pi-btn-tukar {
    display: inline-flex; align-items: center; gap: 5px;
    background: var(--primary); color: #fff;
    font-size: 12px; font-weight: 700; padding: 7px 14px;
    border-radius: 9px; text-decoration: none;
    transition: background .2s;
}
.pi-btn-tukar:hover { background: var(--primary-dark); color: #fff; }
.pi-btn-habis, .pi-btn-kurang {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 700; padding: 7px 12px;
    border-radius: 9px; cursor: default;
}
.pi-btn-habis { background: #F1F5F9; color: #94A3B8; }
.pi-btn-kurang { background: #FEF2F2; color: #DC2626; }

.pi-reward-footer, .pi-mut-footer {
    border-top: 1px solid #F1F5F9; padding: 12px 22px;
    display: flex; justify-content: center; flex-shrink: 0;
}
.pi-see-all {
    display: inline-flex; align-items: center;
    font-size: 12.5px; font-weight: 700; color: var(--primary);
    text-decoration: none;
}
.pi-see-all:hover { text-decoration: underline; color: var(--primary-dark); }

/* Mutation list */
.pi-mutation-list { flex: 1; overflow: hidden; }
.pi-mutation-item {
    display: flex; align-items: center; gap: 12px;
    padding: 13px 22px; border-bottom: 1px solid #F8FAFC;
    transition: background .15s;
}
.pi-mutation-item:hover { background: #FAFCFF; }
.pi-mut-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
}
.pi-mut-earn   { background: #D1FAE5; color: #059669; }
.pi-mut-redeem { background: #FEF3C7; color: #D97706; }
.pi-mut-adjust { background: #EEF2FF; color: #4338CA; }

.pi-mut-body { flex: 1; min-width: 0; }
.pi-mut-desc { font-size: 13px; font-weight: 600; color: #0B1D17;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pi-mut-meta { font-size: 11px; color: #94A3B8; margin-top: 2px; }
.pi-mut-ref { opacity: .7; }

.pi-mut-right { text-align: right; flex-shrink: 0; }
.pi-mut-amount { font-size: 14px; font-weight: 800; line-height: 1.2; }
.pi-mut-amount.plus { color: #059669; }
.pi-mut-amount.minus { color: #DC2626; }
.pi-mut-sisa { font-size: 11px; color: #94A3B8; margin-top: 2px; }

/* Empty */
.pi-empty {
    display: flex; flex-direction: column; align-items: center;
    gap: 8px; padding: 28px 16px; text-align: center;
}
.pi-empty i { font-size: 2.2rem; color: #CBD5E1; }
.pi-empty p { font-size: 13px; color: #94A3B8; margin: 0; max-width: 260px; line-height: 1.5; }

/* Info strip */
.pi-info-strip {
    background: linear-gradient(135deg, #F0FDF6, #E0F2FE);
    border: 1.5px solid #D3F0E0; border-radius: 20px;
    padding: 20px 28px;
    display: flex; align-items: center; gap: 0; flex-wrap: wrap;
}
.pi-info-item { display: flex; align-items: center; gap: 14px; flex: 1; min-width: 200px; }
.pi-info-icon {
    width: 42px; height: 42px; border-radius: 12px;
    background: var(--primary-soft); color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.05rem; flex-shrink: 0;
}
.pi-info-icon.gift { background: #FEF3C7; color: #D97706; }
.pi-info-icon.gold { background: #FEF3C7; color: #D97706; }
.pi-info-title { font-size: 13px; font-weight: 700; color: #0B1D17; margin-bottom: 2px; }
.pi-info-desc { font-size: 12px; color: #64748B; }
.pi-info-divider {
    width: 1px; height: 40px; background: #D3F0E0; margin: 0 24px; flex-shrink: 0;
}

/* Responsive */
@media (max-width: 1100px) {
    .pi-stats { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .pi-hero { flex-direction: column; padding: 28px 22px; }
    .pi-hero-right { display: none; }
    .pi-pts-num { font-size: 2.8rem; }
    .pi-stats { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .pi-info-strip { flex-direction: column; gap: 16px; }
    .pi-info-divider { width: 100%; height: 1px; margin: 0; }
}
@media (max-width: 480px) {
    .pi-stats { grid-template-columns: 1fr 1fr; }
    .pi-sc-value { font-size: 1.5rem; }
}
</style>
@endsection
