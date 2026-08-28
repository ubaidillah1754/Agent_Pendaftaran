@extends('layouts.app')
@section('title', 'Poin Saya — My Sakinah Agent')
@section('page-title', 'Poin Saya')
@section('page-subtitle', 'Pantau akumulasi perolehan poin dari input pasien dan reward yang tersedia.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Poin Saya</li>
@endsection

@section('content')
<div class="row g-3 mb-4">
    <!-- Stat Saldo Poin Utama -->
    <div class="col-12 col-md-6 col-xl-3 fade-in">
        <div class="stat-card" style="border-left: 4px solid var(--rs-primary);">
            <div class="stat-icon" style="background:var(--rs-primary-soft); color:var(--rs-primary);">
                <i class="bi bi-star-fill"></i>
            </div>
            <div>
                <div class="stat-label">Saldo Poin Saat Ini</div>
                <div class="stat-value" style="color:var(--rs-primary);">{{ number_format($saldoPoin) }}</div>
                <div class="stat-sub">Poin dapat ditukarkan</div>
            </div>
        </div>
    </div>

    <!-- Stat Total Pasien Diinput -->
    <div class="col-12 col-md-6 col-xl-3 fade-in fade-in-delay-1">
        <div class="stat-card" style="border-left: 4px solid var(--rs-info);">
            <div class="stat-icon" style="background:var(--rs-info-soft); color:var(--rs-info);">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <div>
                <div class="stat-label">Pasien Baru Diinput</div>
                <div class="stat-value" style="color:var(--rs-info);">{{ number_format($totalPasien) }}</div>
                <div class="stat-sub">Total pasien baru Anda</div>
            </div>
        </div>
    </div>

    <!-- Stat Total Poin Diperoleh -->
    <div class="col-12 col-md-6 col-xl-3 fade-in fade-in-delay-2">
        <div class="stat-card" style="border-left: 4px solid var(--rs-accent);">
            <div class="stat-icon" style="background:var(--rs-accent-soft); color:var(--rs-accent);">
                <i class="bi bi-arrow-up-right-circle-fill"></i>
            </div>
            <div>
                <div class="stat-label">Total Poin Diperoleh</div>
                <div class="stat-value" style="color:var(--rs-accent);">{{ number_format($totalEarned) }}</div>
                <div class="stat-sub">Akumulasi seumur hidup</div>
            </div>
        </div>
    </div>

    <!-- Stat Total Poin Digunakan -->
    <div class="col-12 col-md-6 col-xl-3 fade-in fade-in-delay-3">
        <div class="stat-card" style="border-left: 4px solid #64748B;">
            <div class="stat-icon" style="background:#F1F5F9; color:#475569;">
                <i class="bi bi-gift-fill"></i>
            </div>
            <div>
                <div class="stat-label">Total Poin Digunakan</div>
                <div class="stat-value" style="color:#475569;">{{ number_format($totalRedeemed) }}</div>
                <div class="stat-sub">Telah ditukarkan</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Reward Tersedia -->
    <div class="col-lg-7 fade-in">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="rs-card-title"><i class="bi bi-gift-fill"></i>Reward Tersedia</span>
                <a href="{{ route('points.katalog') }}" class="rs-card-link">Lihat Katalog Lengkap <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @forelse($featuredRewards as $reward)
                    <div class="col-sm-6">
                        <div class="p-3 border rounded-3 h-100 d-flex flex-column justify-content-between" style="background:#FAFDFB; border-color:var(--rs-border) !important;">
                            <div>
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <img src="{{ $reward->image_url }}" alt="{{ $reward->name }}" style="width:52px; height:52px; object-fit:cover; border-radius:10px; border:1px solid #E2E8F0;">
                                    <div>
                                        <div class="fw-bold" style="font-size:.92rem; color:var(--rs-ink);">{{ $reward->name }}</div>
                                        <div style="font-size:.78rem; color:var(--rs-accent); font-weight:700;">
                                            <i class="bi bi-star-fill me-1"></i>{{ number_format($reward->points_required) }} Poin
                                        </div>
                                    </div>
                                </div>
                                <div style="font-size:.75rem; color:var(--rs-muted); margin-bottom:12px;">
                                    Sisa stok: <strong>{{ $reward->stock }}</strong> unit
                                </div>
                            </div>
                            <div>
                                @if($saldoPoin >= $reward->points_required && $reward->stock > 0)
                                    <a href="{{ route('points.katalog') }}" class="btn btn-sm w-100 text-white" style="background:var(--rs-primary); border-radius:8px; font-weight:600; font-size:.8rem;">
                                        Tukar Sekarang
                                    </a>
                                @elseif($reward->stock <= 0)
                                    <button class="btn btn-sm w-100 btn-light text-muted" disabled style="border-radius:8px; font-size:.8rem;">Stok Habis</button>
                                @else
                                    <button class="btn btn-sm w-100 btn-light text-muted" disabled style="border-radius:8px; font-size:.8rem;">Poin Kurang</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="text-center py-4 text-muted">Belum ada reward aktif saat ini.</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Poin Terbaru -->
    <div class="col-lg-5 fade-in fade-in-delay-1">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="rs-card-title"><i class="bi bi-clock-history"></i>Mutasi Poin Terbaru</span>
                <a href="{{ route('points.riwayat') }}" class="rs-card-link">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recentTransactions as $tx)
                    <div class="list-group-item px-3 py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;
                                        background:{{ $tx->type === 'earn' ? '#E6F6F0' : ($tx->type === 'redeem' ? '#FEF3C7' : '#EFF6FF') }};
                                        color:{{ $tx->type === 'earn' ? '#0F7B63' : ($tx->type === 'redeem' ? '#B45309' : '#1D4ED8') }};">
                                <i class="bi {{ $tx->type === 'earn' ? 'bi-plus-lg' : ($tx->type === 'redeem' ? 'bi-gift' : 'bi-arrow-left-right') }}"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-truncate" style="font-size:.84rem; max-width:220px; color:var(--rs-ink);">{{ $tx->description }}</div>
                                <div style="font-size:.72rem; color:var(--rs-muted);">{{ $tx->created_at->format('d M Y, H:i') }} • Ref: {{ $tx->reference }}</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold" style="font-size:.9rem; color:{{ $tx->amount > 0 ? '#0F7B63' : '#B54545' }};">
                                {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount) }}
                            </span>
                            <div style="font-size:.68rem; color:var(--rs-muted);">Sisa: {{ number_format($tx->balance_after) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted" style="font-size:.85rem;">
                        Belum ada mutasi poin. Daftarkan pasien baru untuk mendapatkan poin!
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
