@extends('layouts.app')
@section('title', 'Riwayat Penukaran — My Sakinah Agent')
@section('page-title', 'Riwayat Penukaran Reward')
@section('page-subtitle', 'Daftar seluruh permohonan penukaran reward dan status persetujuannya.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('points.index') }}">Poin Saya</a></li>
    <li class="breadcrumb-item active">Riwayat Penukaran</li>
@endsection

@section('content')

{{-- Flash Messages --}}
@if(session('success'))
<div class="rp-alert rp-ok mb-4">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button onclick="this.parentElement.remove()" class="rp-alert-x">&times;</button>
</div>
@endif
@if(session('error'))
<div class="rp-alert rp-err mb-4">
    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
    <button onclick="this.parentElement.remove()" class="rp-alert-x">&times;</button>
</div>
@endif

{{-- ══════════ PAGE HEADER ══════════ --}}
<div class="rp-header mb-4">
    <div class="rp-header-left">
        <div class="rp-icon-wrap"><i class="bi bi-arrow-left-right"></i></div>
        <div>
            <h1 class="rp-page-title">Riwayat Penukaran Reward</h1>
            <p class="rp-page-sub">Pantau status setiap permohonan penukaran yang telah Anda ajukan.</p>
        </div>
    </div>
    <a href="{{ route('points.katalog') }}" class="rp-cta-btn">
        <i class="bi bi-plus-circle-fill"></i> Tukar Reward Baru
    </a>
</div>

{{-- ══════════ STAT STRIP ══════════ --}}
<div class="rp-stat-row mb-4">
    @php
        $all       = $redemptions->getCollection();
        $cPending  = $all->where('status','pending')->count();
        $cApproved = $all->whereIn('status',['approved','completed'])->count();
        $cRejected = $all->where('status','rejected')->count();
    @endphp
    <div class="rp-stat-item">
        <div class="rp-stat-icon rp-si-all"><i class="bi bi-list-check"></i></div>
        <div>
            <div class="rp-stat-lbl">Semua Pengajuan</div>
            <div class="rp-stat-val">{{ $redemptions->total() }}</div>
        </div>
    </div>
    <div class="rp-stat-item">
        <div class="rp-stat-icon rp-si-pending"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="rp-stat-lbl">Menunggu</div>
            <div class="rp-stat-val">{{ $cPending }}</div>
        </div>
    </div>
    <div class="rp-stat-item">
        <div class="rp-stat-icon rp-si-ok"><i class="bi bi-check-circle-fill"></i></div>
        <div>
            <div class="rp-stat-lbl">Disetujui</div>
            <div class="rp-stat-val">{{ $cApproved }}</div>
        </div>
    </div>
    <div class="rp-stat-item">
        <div class="rp-stat-icon rp-si-rej"><i class="bi bi-x-circle-fill"></i></div>
        <div>
            <div class="rp-stat-lbl">Ditolak</div>
            <div class="rp-stat-val">{{ $cRejected }}</div>
        </div>
    </div>
    <div class="rp-stat-item rp-stat-links">
        <a href="{{ route('points.index') }}" class="rp-link-chip">
            <i class="bi bi-star-fill"></i> Poin Saya
        </a>
        <a href="{{ route('points.katalog') }}" class="rp-link-chip">
            <i class="bi bi-gift-fill"></i> Katalog
        </a>
    </div>
</div>

{{-- ══════════ FILTER BAR ══════════ --}}
<div class="rp-filter-bar mb-4 fade-in">
    <form method="GET" action="{{ route('points.redemptions.index') }}" class="rp-filter-form">
        <div class="rp-filter-group">
            <label class="rp-filter-label">Status Penukaran</label>
            <select name="status" class="rp-filter-select">
                <option value="">Semua Status</option>
                <option value="pending"   {{ request('status')==='pending'    ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="approved"  {{ request('status')==='approved'   ? 'selected' : '' }}>Disetujui</option>
                <option value="completed" {{ request('status')==='completed'  ? 'selected' : '' }}>Selesai / Diterima</option>
                <option value="rejected"  {{ request('status')==='rejected'   ? 'selected' : '' }}>Ditolak</option>
                <option value="cancelled" {{ request('status')==='cancelled'  ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>
        <div class="rp-filter-actions">
            <button type="submit" class="rp-btn-filter">
                <i class="bi bi-funnel-fill me-1"></i> Terapkan Filter
            </button>
            <a href="{{ route('points.redemptions.index') }}" class="rp-btn-reset">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </div>
    </form>
    @if(request('status'))
    <div class="rp-filter-active">
        Filter aktif: <span>{{ ucfirst(request('status')) }}</span>
        <a href="{{ route('points.redemptions.index') }}">× Hapus</a>
    </div>
    @endif
</div>

{{-- ══════════ REDEMPTION LIST ══════════ --}}
<div class="rp-panel fade-in">
    <div class="rp-panel-head">
        <div class="rp-panel-title">
            <span class="rp-panel-icon"><i class="bi bi-list-check"></i></span>
            Daftar Pengajuan Penukaran
        </div>
        <span class="rp-count-badge">{{ $redemptions->total() }} pengajuan</span>
    </div>

    @forelse($redemptions as $item)
    @php
        $stMap = [
            'pending'   => ['label'=>'Menunggu',  'class'=>'rp-sts-pending',   'icon'=>'bi-hourglass-split'],
            'approved'  => ['label'=>'Disetujui', 'class'=>'rp-sts-approved',  'icon'=>'bi-check-circle-fill'],
            'completed' => ['label'=>'Selesai',   'class'=>'rp-sts-completed', 'icon'=>'bi-bag-check-fill'],
            'rejected'  => ['label'=>'Ditolak',   'class'=>'rp-sts-rejected',  'icon'=>'bi-x-circle-fill'],
            'cancelled' => ['label'=>'Batal',     'class'=>'rp-sts-cancelled', 'icon'=>'bi-slash-circle-fill'],
        ];
        $st = $stMap[$item->status] ?? ['label'=>$item->status_label, 'class'=>'rp-sts-pending', 'icon'=>'bi-circle'];
    @endphp
    <div class="rp-item">
        {{-- Status indicator --}}
        <div class="rp-item-indicator {{ $st['class'] }}">
            <i class="bi {{ $st['icon'] }}"></i>
        </div>

        {{-- Main info --}}
        <div class="rp-item-body">
            <div class="rp-item-top">
                <div class="rp-item-ref">
                    <code class="rp-ref-code">{{ $item->reference_code }}</code>
                    <span class="rp-status-badge {{ $st['class'] }}">
                        <i class="bi {{ $st['icon'] }}"></i> {{ $st['label'] }}
                    </span>
                </div>
                <div class="rp-item-date">{{ $item->created_at->format('d M Y, H:i') }}</div>
            </div>

            <div class="rp-item-product">
                <div class="rp-product-img">
                    @if($item->merchandise)
                    <img src="{{ $item->merchandise->image_url }}" alt="{{ $item->merchandise_name }}">
                    @else
                    <div class="rp-product-placeholder"><i class="bi bi-bag"></i></div>
                    @endif
                </div>
                <div class="rp-product-info">
                    <div class="rp-product-name">{{ $item->merchandise_name }}</div>
                    <div class="rp-product-qty">
                        <i class="bi bi-box-seam"></i> {{ $item->quantity }}× item ·
                        <span>{{ number_format($item->points_required) }} poin/item</span>
                    </div>
                </div>
            </div>

            @if($item->notes)
            <div class="rp-item-note">
                <i class="bi bi-chat-left-text-fill"></i>
                <span>{{ $item->notes }}</span>
            </div>
            @endif
        </div>

        {{-- Right: Points + actions --}}
        <div class="rp-item-right">
            <div class="rp-pts-display">
                <span class="rp-pts-num">{{ number_format($item->total_points) }}</span>
                <span class="rp-pts-unit">poin</span>
            </div>
            <div class="rp-qty-badge">{{ $item->quantity }}× item</div>
            <a href="{{ route('points.redemptions.cetak', $item) }}" target="_blank"
               class="rp-btn-resi" title="Cetak Resi">
                <i class="bi bi-printer-fill me-1"></i> Resi
            </a>
        </div>
    </div>
    @empty
    <div class="rp-empty">
        <div class="rp-empty-icon"><i class="bi bi-inbox"></i></div>
        <div class="rp-empty-title">Belum Ada Riwayat Penukaran</div>
        <p class="rp-empty-sub">
            Anda belum pernah menukar poin dengan reward.
            Kunjungi katalog untuk melihat hadiah yang tersedia.
        </p>
        <a href="{{ route('points.katalog') }}" class="rp-empty-btn">
            <i class="bi bi-gift-fill me-1"></i> Lihat Katalog Reward
        </a>
    </div>
    @endforelse

    {{-- Pagination --}}
    @if($redemptions->hasPages())
    <div class="rp-pagination">
        {{ $redemptions->links() }}
    </div>
    @endif
</div>

{{-- ══════════ INFO PANEL ══════════ --}}
<div class="rp-info-panel mt-4 fade-in">
    <div class="rp-info-icon"><i class="bi bi-info-circle-fill"></i></div>
    <div class="rp-info-body">
        <div class="rp-info-title">Status Penukaran Reward</div>
        <div class="rp-legend-grid">
            <div class="rp-legend-item">
                <span class="rp-legend-dot rp-sts-pending"></span>
                <div>
                    <div class="rp-legend-name">Menunggu Persetujuan</div>
                    <div class="rp-legend-desc">Pengajuan telah diterima, sedang ditinjau admin</div>
                </div>
            </div>
            <div class="rp-legend-item">
                <span class="rp-legend-dot rp-sts-approved"></span>
                <div>
                    <div class="rp-legend-name">Disetujui</div>
                    <div class="rp-legend-desc">Admin telah menyetujui, reward sedang disiapkan</div>
                </div>
            </div>
            <div class="rp-legend-item">
                <span class="rp-legend-dot rp-sts-completed"></span>
                <div>
                    <div class="rp-legend-name">Selesai / Diterima</div>
                    <div class="rp-legend-desc">Reward telah Anda terima</div>
                </div>
            </div>
            <div class="rp-legend-item">
                <span class="rp-legend-dot rp-sts-rejected"></span>
                <div>
                    <div class="rp-legend-name">Ditolak</div>
                    <div class="rp-legend-desc">Pengajuan tidak dapat diproses</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ── ALERTS ── */
.rp-alert {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 18px; border-radius: 14px;
    font-size: 13.5px; font-weight: 600; position: relative;
}
.rp-ok  { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
.rp-err { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
.rp-alert-x {
    position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
    background: none; border: none; font-size: 1.2rem; cursor: pointer; opacity: .5;
}
.rp-alert-x:hover { opacity: 1; }

/* ── PAGE HEADER ── */
.rp-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 16px;
}
.rp-header-left { display: flex; align-items: center; gap: 16px; }
.rp-icon-wrap {
    width: 52px; height: 52px; border-radius: 16px;
    background: linear-gradient(135deg, #0F7B63, #34D399);
    color: #fff; display: flex; align-items: center;
    justify-content: center; font-size: 1.3rem; flex-shrink: 0;
}
.rp-page-title { font-size: 1.45rem; font-weight: 800; color: #0B1D17;
    letter-spacing: -.4px; margin: 0 0 3px; }
.rp-page-sub { font-size: 13px; color: #64748B; margin: 0; }
.rp-cta-btn {
    display: inline-flex; align-items: center; gap: 7px;
    background: linear-gradient(135deg, #0F7B63, #059669);
    color: #fff; font-size: 13.5px; font-weight: 800;
    padding: 11px 22px; border-radius: 12px; text-decoration: none;
    box-shadow: 0 6px 20px -6px rgba(15,123,99,.5); transition: all .2s;
    white-space: nowrap;
}
.rp-cta-btn:hover { color: #fff; transform: translateY(-2px);
    box-shadow: 0 10px 28px -8px rgba(15,123,99,.55); }

/* ── STAT ROW ── */
.rp-stat-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 12px;
}
.rp-stat-item {
    background: #fff; border: 1.5px solid #E2E8F0; border-radius: 18px;
    padding: 16px 18px; display: flex; align-items: center; gap: 12px;
    transition: transform .2s, box-shadow .2s;
}
.rp-stat-item:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -8px rgba(0,0,0,.1); }
.rp-stat-icon {
    width: 40px; height: 40px; border-radius: 11px;
    display: flex; align-items: center; justify-content: center; font-size: 1rem;
    flex-shrink: 0;
}
.rp-si-all     { background: #F1F5F9; color: #475569; }
.rp-si-pending { background: #FEF3C7; color: #D97706; }
.rp-si-ok      { background: #D1FAE5; color: #059669; }
.rp-si-rej     { background: #FEE2E2; color: #DC2626; }
.rp-stat-lbl { font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; color: #94A3B8; }
.rp-stat-val { font-family: 'Spectral', serif; font-size: 1.6rem;
    font-weight: 900; color: #0B1D17; line-height: 1.1; }

.rp-stat-links { flex-direction: column; align-items: stretch; gap: 8px; }
.rp-link-chip {
    display: flex; align-items: center; gap: 7px;
    background: var(--primary-soft); color: var(--primary);
    font-size: 12.5px; font-weight: 700; padding: 8px 14px;
    border-radius: 9px; text-decoration: none; transition: all .2s;
}
.rp-link-chip:hover { background: var(--primary); color: #fff; }

/* ── FILTER BAR ── */
.rp-filter-bar {
    background: #fff; border: 1.5px solid #E2E8F0;
    border-radius: 18px; padding: 18px 22px;
}
.rp-filter-form { display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap; }
.rp-filter-group { display: flex; flex-direction: column; gap: 6px; }
.rp-filter-label { font-size: 11.5px; font-weight: 700; color: #64748B;
    text-transform: uppercase; letter-spacing: .05em; }
.rp-filter-select {
    border: 2px solid #E2E8F0; border-radius: 10px;
    padding: 10px 14px; font-size: 13.5px; outline: none;
    min-width: 220px; font-family: 'Plus Jakarta Sans', sans-serif;
    color: #0B1D17; transition: border-color .2s;
}
.rp-filter-select:focus { border-color: var(--primary); }
.rp-filter-actions { display: flex; gap: 8px; }
.rp-btn-filter {
    background: var(--primary); border: none; color: #fff;
    font-size: 13px; font-weight: 700; padding: 10px 18px;
    border-radius: 10px; cursor: pointer; transition: background .2s;
    display: flex; align-items: center;
}
.rp-btn-filter:hover { background: var(--primary-dark); }
.rp-btn-reset {
    background: #F1F5F9; border: none; color: #374151;
    font-size: 13px; font-weight: 600; padding: 10px 14px;
    border-radius: 10px; text-decoration: none; display: flex;
    align-items: center; gap: 5px; transition: background .2s;
}
.rp-btn-reset:hover { background: #E2E8F0; color: #0B1D17; }
.rp-filter-active {
    margin-top: 10px; font-size: 12.5px; color: #64748B;
    display: flex; align-items: center; gap: 6px;
}
.rp-filter-active span { font-weight: 700; color: #0B1D17; }
.rp-filter-active a {
    color: #DC2626; text-decoration: none; font-weight: 700;
    margin-left: 4px;
}

/* ── PANEL ── */
.rp-panel {
    background: #fff; border: 1.5px solid #E2E8F0;
    border-radius: 22px; overflow: hidden;
    box-shadow: 0 2px 16px -8px rgba(0,0,0,.07);
}
.rp-panel-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 24px; border-bottom: 1px solid #F1F5F9;
}
.rp-panel-title { display: flex; align-items: center; gap: 10px;
    font-weight: 800; font-size: 15px; color: #0B1D17; }
.rp-panel-icon {
    width: 34px; height: 34px; border-radius: 10px;
    background: var(--primary-soft); color: var(--primary);
    display: flex; align-items: center; justify-content: center; font-size: 15px;
}
.rp-count-badge {
    background: #F1F5F9; color: #64748B;
    font-size: 12px; font-weight: 600; padding: 5px 14px; border-radius: 999px;
}

/* ── ITEM ── */
.rp-item {
    display: flex; align-items: flex-start; gap: 0;
    padding: 20px 24px; border-bottom: 1px solid #F8FAFC;
    transition: background .15s;
}
.rp-item:last-of-type { border-bottom: none; }
.rp-item:hover { background: #FAFCFF; }

.rp-item-indicator {
    width: 38px; height: 38px; border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0; margin-right: 16px; margin-top: 2px;
}

.rp-item-body { flex: 1; min-width: 0; }
.rp-item-top {
    display: flex; align-items: center; flex-wrap: wrap;
    gap: 10px; margin-bottom: 12px;
}
.rp-item-ref { display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0; }
.rp-ref-code {
    font-size: 13px; font-weight: 800; color: #0B1D17;
    background: #F1F5F9; padding: 4px 10px; border-radius: 7px;
    font-family: monospace; white-space: nowrap;
}
.rp-item-date { font-size: 12px; color: #94A3B8; white-space: nowrap; }

.rp-status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .04em; padding: 4px 12px; border-radius: 999px;
}

/* Status colors */
.rp-sts-pending   { background: #FEF3C7; color: #92400E; }
.rp-sts-approved  { background: #D1FAE5; color: #065F46; }
.rp-sts-completed { background: #D1FAE5; color: #065F46; }
.rp-sts-rejected  { background: #FEE2E2; color: #991B1B; }
.rp-sts-cancelled { background: #F1F5F9; color: #64748B; }

.rp-item-product {
    display: flex; align-items: center; gap: 12px; margin-bottom: 8px;
}
.rp-product-img {
    width: 52px; height: 52px; border-radius: 11px;
    overflow: hidden; flex-shrink: 0;
    background: #F0FDF6; border: 1px solid #E2E8F0;
}
.rp-product-img img { width: 100%; height: 100%; object-fit: cover; }
.rp-product-placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    color: #94A3B8; font-size: 1.3rem;
}
.rp-product-name { font-size: 14px; font-weight: 700; color: #0B1D17; margin-bottom: 3px; }
.rp-product-qty { font-size: 12px; color: #64748B; display: flex; align-items: center; gap: 5px; }
.rp-product-qty i { color: var(--primary); }

.rp-item-note {
    display: inline-flex; align-items: center; gap: 7px;
    background: #F1F5F9; border-left: 3px solid var(--primary);
    font-size: 12px; color: #475569; padding: 6px 12px;
    border-radius: 0 8px 8px 0;
}
.rp-item-note i { color: var(--primary); flex-shrink: 0; }

/* Right col */
.rp-item-right {
    display: flex; flex-direction: column; align-items: flex-end;
    gap: 6px; flex-shrink: 0; margin-left: 16px;
}
.rp-pts-display { display: flex; align-items: baseline; gap: 4px; }
.rp-pts-num {
    font-family: 'Spectral', serif; font-size: 1.7rem;
    font-weight: 900; color: #D97706; letter-spacing: -.5px;
}
.rp-pts-unit { font-size: 12px; color: #94A3B8; font-weight: 600; }
.rp-qty-badge {
    font-size: 11.5px; font-weight: 700; background: #F1F5F9;
    color: #475569; padding: 3px 10px; border-radius: 999px;
}
.rp-btn-resi {
    display: inline-flex; align-items: center;
    background: #F8FAFC; border: 1.5px solid #E2E8F0;
    color: #374151; font-size: 12px; font-weight: 700;
    padding: 6px 14px; border-radius: 8px; text-decoration: none;
    transition: all .2s; white-space: nowrap;
}
.rp-btn-resi:hover {
    background: var(--primary); border-color: var(--primary); color: #fff;
}

/* ── EMPTY ── */
.rp-empty {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; padding: 64px 24px; text-align: center;
}
.rp-empty-icon {
    width: 80px; height: 80px; border-radius: 50%;
    background: #F1F5F9; color: #CBD5E1;
    display: flex; align-items: center; justify-content: center;
    font-size: 2.3rem; margin-bottom: 18px;
}
.rp-empty-title { font-size: 1.1rem; font-weight: 800; color: #374151; margin-bottom: 8px; }
.rp-empty-sub { font-size: 13px; color: #94A3B8; max-width: 380px;
    line-height: 1.6; margin-bottom: 20px; }
.rp-empty-btn {
    display: inline-flex; align-items: center;
    background: var(--primary); color: #fff;
    font-size: 13.5px; font-weight: 700; padding: 11px 24px;
    border-radius: 12px; text-decoration: none;
    box-shadow: 0 6px 20px -6px rgba(15,123,99,.45); transition: all .2s;
}
.rp-empty-btn:hover { background: var(--primary-dark); color: #fff; transform: translateY(-1px); }

/* ── PAGINATION ── */
.rp-pagination {
    padding: 16px 24px; border-top: 1px solid #F1F5F9;
    display: flex; justify-content: center;
}

/* ── INFO PANEL ── */
.rp-info-panel {
    background: linear-gradient(135deg, #F0FDF6, #E0F2FE);
    border: 1.5px solid #D3F0E0; border-radius: 20px;
    padding: 22px 26px;
    display: flex; align-items: flex-start; gap: 16px;
}
.rp-info-icon {
    width: 44px; height: 44px; border-radius: 13px;
    background: var(--primary); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.rp-info-title { font-size: 14px; font-weight: 800; color: #0B1D17; margin-bottom: 14px; }
.rp-legend-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
}
.rp-legend-item { display: flex; align-items: flex-start; gap: 10px; }
.rp-legend-dot {
    width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; margin-top: 3px;
}
.rp-sts-pending.rp-legend-dot   { background: #D97706; }
.rp-sts-approved.rp-legend-dot,
.rp-sts-completed.rp-legend-dot { background: #059669; }
.rp-sts-rejected.rp-legend-dot  { background: #DC2626; }
.rp-sts-cancelled.rp-legend-dot { background: #94A3B8; }
.rp-legend-name { font-size: 12.5px; font-weight: 700; color: #0B1D17; margin-bottom: 2px; }
.rp-legend-desc { font-size: 11.5px; color: #64748B; }

@media (max-width: 768px) {
    .rp-header { flex-direction: column; align-items: flex-start; }
    .rp-item-right { display: none; }
    .rp-stat-row { grid-template-columns: repeat(2,1fr); }
    .rp-stat-links { display: none; }
    .rp-item { padding: 16px; }
    .rp-filter-form { flex-direction: column; align-items: stretch; }
    .rp-info-panel { flex-direction: column; }
}
</style>
@endsection
