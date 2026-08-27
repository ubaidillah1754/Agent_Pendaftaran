{{-- resources/views/point-requests/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Pengajuan Poin Saya')
@section('page-title', 'Pengajuan Poin Saya')
@section('page-subtitle', 'Riwayat pengajuan poin yang telah Anda buat.')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Pengajuan Poin</li>
@endsection

@section('content')
<div class="preq-page">

    {{-- ══════════ FLASH MESSAGES ══════════ --}}
    @if(session('success'))
    <div class="preq-alert preq-alert-success mb-4">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
        <button type="button" onclick="this.parentElement.remove()" class="preq-alert-close">&times;</button>
    </div>
    @endif
    @if(session('error'))
    <div class="preq-alert preq-alert-danger mb-4">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>{{ session('error') }}</span>
        <button type="button" onclick="this.parentElement.remove()" class="preq-alert-close">&times;</button>
    </div>
    @endif

    {{-- ══════════ PAGE HEADER ══════════ --}}
    <div class="preq-header mb-4">
        <div class="preq-header-left">
            <nav class="preq-bc" aria-label="breadcrumb">
                <a href="{{ route('dashboard') }}"><i class="bi bi-house-heart-fill"></i> Dashboard</a>
                <i class="bi bi-chevron-right"></i>
                <span>Pengajuan Poin</span>
            </nav>
            <h1 class="preq-title">Pengajuan Poin Saya</h1>
            <p class="preq-subtitle">Pantau status setiap pengajuan poin yang telah Anda kirimkan kepada admin.</p>
        </div>
        <a href="{{ route('point-requests.create') }}" class="preq-cta-btn">
            <i class="bi bi-plus-circle-fill"></i>
            Ajukan Poin Baru
        </a>
    </div>

    {{-- ══════════ STAT STRIP ══════════ --}}
    <div class="preq-stat-strip mb-4">
        {{-- Poin Saya --}}
        <div class="pss-card pss-balance">
            <div class="pss-icon"><i class="bi bi-star-fill"></i></div>
            <div class="pss-body">
                <div class="pss-label">Saldo Poin</div>
                <div class="pss-value">{{ number_format($totalPoin) }}</div>
                <div class="pss-sub">poin aktif</div>
            </div>
        </div>

        {{-- Total Pengajuan --}}
        <div class="pss-card">
            <div class="pss-icon pss-icon-blue"><i class="bi bi-list-check"></i></div>
            <div class="pss-body">
                <div class="pss-label">Total Pengajuan</div>
                <div class="pss-value">{{ $requests->total() }}</div>
                <div class="pss-sub">semua status</div>
            </div>
        </div>

        {{-- Disetujui --}}
        @php
            $approved = $requests->getCollection()->where('status', 'approved')->count();
            $pending  = $requests->getCollection()->where('status', 'pending')->count();
            $rejected = $requests->getCollection()->where('status', 'rejected')->count();
        @endphp
        <div class="pss-card">
            <div class="pss-icon pss-icon-green"><i class="bi bi-check-circle-fill"></i></div>
            <div class="pss-body">
                <div class="pss-label">Disetujui</div>
                <div class="pss-value">{{ $approved }}</div>
                <div class="pss-sub">pada halaman ini</div>
            </div>
        </div>

        {{-- Menunggu --}}
        <div class="pss-card">
            <div class="pss-icon pss-icon-amber"><i class="bi bi-hourglass-split"></i></div>
            <div class="pss-body">
                <div class="pss-label">Menunggu</div>
                <div class="pss-value">{{ $pending }}</div>
                <div class="pss-sub">sedang diproses</div>
            </div>
        </div>

        {{-- Shortcuts --}}
        <div class="pss-card pss-links">
            <a href="{{ route('points.index') }}" class="pss-link-btn">
                <i class="bi bi-award-fill"></i> Poin Saya
            </a>
            <a href="{{ route('points.katalog') }}" class="pss-link-btn">
                <i class="bi bi-gift-fill"></i> Katalog Hadiah
            </a>
        </div>
    </div>

    {{-- ══════════ TIMELINE (mobile) / TABLE (desktop) ══════════ --}}
    <div class="preq-panel">
        <div class="preq-panel-head">
            <div class="preq-panel-title">
                <span class="preq-panel-icon"><i class="bi bi-clock-history"></i></span>
                Riwayat Pengajuan Poin
            </div>
            <div class="preq-panel-actions">
                <span class="preq-count-badge">{{ $requests->total() }} pengajuan</span>
            </div>
        </div>

        @forelse($requests as $req)
        @php
            $sc = $req->status_color;
            $statusIcon = match($req->status) {
                'approved' => 'bi-check-circle-fill',
                'rejected' => 'bi-x-circle-fill',
                default    => 'bi-hourglass-split',
            };
            $statusClass = match($req->status) {
                'approved' => 'preq-status-approved',
                'rejected' => 'preq-status-rejected',
                default    => 'preq-status-pending',
            };
        @endphp

        <div class="preq-item" data-status="{{ $req->status }}">
            {{-- Left: Status indicator --}}
            <div class="preq-item-indicator {{ $statusClass }}">
                <i class="bi {{ $statusIcon }}"></i>
            </div>

            {{-- Center: Main info --}}
            <div class="preq-item-body">
                <div class="preq-item-top">
                    <div class="preq-item-date">
                        <span class="preq-date-main">{{ $req->created_at->translatedFormat('d M Y') }}</span>
                        <span class="preq-date-time">{{ $req->created_at->format('H:i') }} WIB</span>
                    </div>
                    <span class="preq-status-pill {{ $statusClass }}">
                        <i class="bi {{ $statusIcon }}"></i>
                        {{ $req->status_label }}
                    </span>
                </div>

                <div class="preq-item-reason">
                    @if(strlen($req->reason) > 80)
                        <span class="preq-reason-text">{{ Str::limit($req->reason, 80) }}</span>
                        <button type="button" class="preq-read-more"
                            data-bs-toggle="modal" data-bs-target="#reasonModal{{ $req->id }}">
                            Selengkapnya
                        </button>
                    @else
                        <span class="preq-reason-text">{{ $req->reason }}</span>
                    @endif
                </div>

                @if($req->admin_note)
                <div class="preq-admin-note">
                    <i class="bi bi-chat-left-dots-fill"></i>
                    <span>Catatan Admin: {{ $req->admin_note }}</span>
                </div>
                @endif
            </div>

            {{-- Right: Points + dates --}}
            <div class="preq-item-right">
                <div class="preq-pts-display">
                    <span class="preq-pts-num">+{{ number_format($req->points) }}</span>
                    <span class="preq-pts-label">poin</span>
                </div>
                @if($req->status === 'approved' && $req->approved_at)
                <div class="preq-process-date">
                    <i class="bi bi-check2-all"></i>
                    Disetujui {{ $req->approved_at->translatedFormat('d M Y') }}
                </div>
                @elseif($req->status === 'rejected' && $req->rejected_at)
                <div class="preq-process-date rejected">
                    <i class="bi bi-x-lg"></i>
                    Ditolak {{ $req->rejected_at->translatedFormat('d M Y') }}
                </div>
                @endif
            </div>
        </div>

        {{-- Modal: Full reason --}}
        @if(strlen($req->reason) > 80)
        <div class="modal fade" id="reasonModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content preq-modal">
                    <div class="preq-modal-header">
                        <span><i class="bi bi-chat-left-text-fill"></i> Alasan Pengajuan</span>
                        <button type="button" class="preq-modal-close" data-bs-dismiss="modal">&times;</button>
                    </div>
                    <div class="preq-modal-body">
                        <p>{{ $req->reason }}</p>
                        <div class="preq-modal-meta">
                            <span><i class="bi bi-calendar3"></i> {{ $req->created_at->translatedFormat('d M Y') }}</span>
                            <span><i class="bi bi-star-fill"></i> {{ number_format($req->points) }} poin</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @empty
        <div class="preq-empty">
            <div class="preq-empty-icon">
                <i class="bi bi-inbox"></i>
            </div>
            <div class="preq-empty-title">Belum Ada Pengajuan</div>
            <p class="preq-empty-sub">Anda belum pernah mengajukan poin. Mulai ajukan sekarang untuk mendapatkan apresiasi atas kerja keras Anda.</p>
            <a href="{{ route('point-requests.create') }}" class="preq-empty-btn">
                <i class="bi bi-plus-circle-fill"></i> Ajukan Poin Pertama Anda
            </a>
        </div>
        @endforelse

        {{-- Pagination --}}
        @if($requests->hasPages())
        <div class="preq-pagination">
            {{ $requests->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

    {{-- ══════════ INFORMASI PENGAJUAN ══════════ --}}
    <div class="preq-info-banner mt-4">
        <div class="pib-icon"><i class="bi bi-lightbulb-fill"></i></div>
        <div class="pib-body">
            <div class="pib-title">Cara Pengajuan Poin Manual</div>
            <div class="pib-steps">
                <div class="pib-step"><span class="pib-step-num">1</span> Klik tombol <strong>"Ajukan Poin Baru"</strong> di kanan atas</div>
                <div class="pib-step"><span class="pib-step-num">2</span> Isi formulir dengan jumlah poin dan alasan yang jelas</div>
                <div class="pib-step"><span class="pib-step-num">3</span> Admin akan meninjau dalam <strong>1–2 hari kerja</strong></div>
                <div class="pib-step"><span class="pib-step-num">4</span> Poin ditambahkan otomatis setelah disetujui</div>
            </div>
        </div>
    </div>

</div>

<style>
/* ── BASE ── */
.preq-page { font-family: 'Plus Jakarta Sans', sans-serif; }

/* ── ALERT ── */
.preq-alert {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 18px; border-radius: 14px; font-size: 13.5px;
    font-weight: 600; position: relative;
}
.preq-alert-success { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
.preq-alert-danger  { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
.preq-alert-close {
    position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
    background: none; border: none; font-size: 1.3rem; cursor: pointer;
    opacity: .5; line-height: 1;
}
.preq-alert-close:hover { opacity: 1; }

/* ── PAGE HEADER ── */
.preq-header {
    display: flex; align-items: flex-start;
    justify-content: space-between; flex-wrap: wrap; gap: 16px;
}
.preq-bc {
    display: flex; align-items: center; gap: 6px;
    font-size: 12.5px; color: #64748B; margin-bottom: 8px;
}
.preq-bc a { color: var(--primary); text-decoration: none; font-weight: 500; }
.preq-bc a:hover { text-decoration: underline; }
.preq-bc .bi-chevron-right { font-size: 10px; opacity: .5; }

.preq-title {
    font-size: 1.55rem; font-weight: 800; color: #0B1D17;
    letter-spacing: -.5px; margin: 0 0 4px;
}
.preq-subtitle { font-size: 13.5px; color: #64748B; margin: 0; }

.preq-cta-btn {
    display: inline-flex; align-items: center; gap: 8px;
    background: linear-gradient(135deg, #0F7B63, #0A5644);
    color: #fff; font-size: 13.5px; font-weight: 700;
    padding: 11px 22px; border-radius: 12px; text-decoration: none;
    box-shadow: 0 6px 20px -6px rgba(15,123,99,.5);
    transition: all .2s; white-space: nowrap; margin-top: 6px;
}
.preq-cta-btn:hover {
    color: #fff; transform: translateY(-2px);
    box-shadow: 0 10px 28px -8px rgba(15,123,99,.55);
}

/* ── STAT STRIP ── */
.preq-stat-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 14px;
}
.pss-card {
    background: #fff; border: 1.5px solid #E2E8F0;
    border-radius: 18px; padding: 18px 20px;
    display: flex; align-items: center; gap: 14px;
    transition: box-shadow .2s, transform .2s;
}
.pss-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px -8px rgba(0,0,0,.1); }
.pss-balance {
    background: linear-gradient(135deg, #083D2E, #0F7B63);
    border-color: transparent; color: #fff;
}
.pss-icon {
    width: 42px; height: 42px; border-radius: 12px;
    background: var(--primary-soft); color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.05rem; flex-shrink: 0;
}
.pss-balance .pss-icon { background: rgba(255,255,255,.2); color: #FCD34D; }
.pss-icon-blue  { background: #E0F2FE !important; color: #0284C7 !important; }
.pss-icon-green { background: #D1FAE5 !important; color: #059669 !important; }
.pss-icon-amber { background: #FEF3C7 !important; color: #D97706 !important; }

.pss-label { font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; color: #94A3B8; margin-bottom: 2px; }
.pss-balance .pss-label { color: rgba(255,255,255,.65); }
.pss-value { font-family: 'Spectral', serif; font-size: 1.6rem;
    font-weight: 900; color: #0B1D17; line-height: 1.1; }
.pss-balance .pss-value { color: #fff; }
.pss-sub { font-size: 11.5px; color: #94A3B8; }
.pss-balance .pss-sub { color: rgba(255,255,255,.55); }

.pss-links {
    flex-direction: column; align-items: stretch; gap: 10px;
}
.pss-link-btn {
    display: flex; align-items: center; gap: 8px;
    background: var(--primary-soft); color: var(--primary);
    font-size: 12.5px; font-weight: 700; padding: 9px 14px;
    border-radius: 10px; text-decoration: none;
    transition: background .2s, color .2s;
}
.pss-link-btn:hover { background: var(--primary); color: #fff; }

/* ── PANEL ── */
.preq-panel {
    background: #fff; border: 1.5px solid #E2E8F0;
    border-radius: 22px; overflow: hidden;
    box-shadow: 0 4px 20px -8px rgba(0,0,0,.08);
}
.preq-panel-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 24px; border-bottom: 2px solid #F1F5F9;
}
.preq-panel-title {
    display: flex; align-items: center; gap: 10px;
    font-weight: 800; font-size: 15.5px; color: #0B1D17;
}
.preq-panel-icon {
    width: 36px; height: 36px; border-radius: 10px;
    background: var(--primary-soft); color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
}
.preq-count-badge {
    background: #F1F5F9; color: #64748B;
    font-size: 12px; font-weight: 600;
    padding: 5px 14px; border-radius: 999px;
}

/* ── PREQ ITEM (card-style row) ── */
.preq-item {
    display: flex; align-items: flex-start; gap: 0;
    padding: 20px 24px;
    border-bottom: 1px solid #F1F5F9;
    transition: background .15s;
    position: relative;
}
.preq-item:last-of-type { border-bottom: none; }
.preq-item:hover { background: #FAFCFF; }

/* Left indicator */
.preq-item-indicator {
    width: 40px; height: 40px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px; flex-shrink: 0; margin-right: 16px; margin-top: 2px;
}
.preq-status-approved { background: #D1FAE5; color: #059669; }
.preq-status-rejected { background: #FEE2E2; color: #DC2626; }
.preq-status-pending  { background: #FEF3C7; color: #D97706; }

/* Body */
.preq-item-body { flex: 1; min-width: 0; }
.preq-item-top {
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; flex-wrap: wrap; margin-bottom: 8px;
}
.preq-item-date { display: flex; align-items: center; gap: 8px; }
.preq-date-main { font-size: 13.5px; font-weight: 700; color: #0B1D17; }
.preq-date-time { font-size: 12px; color: #94A3B8; }

.preq-status-pill {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .04em; padding: 4px 12px; border-radius: 999px;
}
.preq-status-pill.preq-status-approved { background: #D1FAE5; color: #065F46; }
.preq-status-pill.preq-status-rejected { background: #FEE2E2; color: #991B1B; }
.preq-status-pill.preq-status-pending  { background: #FEF3C7; color: #92400E; }

.preq-item-reason {
    font-size: 13.5px; color: #374151; line-height: 1.55;
    margin-bottom: 6px;
}
.preq-reason-text { color: #374151; }
.preq-read-more {
    background: none; border: none; padding: 0;
    color: var(--primary); font-size: 12.5px; font-weight: 700;
    cursor: pointer; text-decoration: underline; margin-left: 6px;
}
.preq-read-more:hover { color: var(--primary-dark); }

.preq-admin-note {
    display: inline-flex; align-items: center; gap: 7px;
    background: #F1F5F9; color: #475569;
    font-size: 12px; padding: 6px 12px; border-radius: 8px;
    border-left: 3px solid var(--primary);
    margin-top: 6px;
}
.preq-admin-note i { color: var(--primary); flex-shrink: 0; }

/* Right */
.preq-item-right {
    display: flex; flex-direction: column;
    align-items: flex-end; gap: 6px; flex-shrink: 0; margin-left: 16px;
}
.preq-pts-display { display: flex; align-items: baseline; gap: 4px; }
.preq-pts-num {
    font-family: 'Spectral', serif; font-size: 1.65rem;
    font-weight: 900; color: var(--primary); letter-spacing: -.5px;
}
.preq-pts-label { font-size: 12px; color: #94A3B8; font-weight: 600; }
.preq-process-date {
    display: flex; align-items: center; gap: 5px;
    font-size: 11.5px; color: #059669; font-weight: 600;
    background: #D1FAE5; padding: 3px 10px; border-radius: 999px;
}
.preq-process-date.rejected { color: #DC2626; background: #FEE2E2; }

/* ── MODAL ── */
.preq-modal {
    border-radius: 20px; border: none;
    box-shadow: 0 20px 60px -20px rgba(0,0,0,.2);
}
.preq-modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 22px; border-bottom: 1px solid #F1F5F9;
    font-weight: 700; font-size: 14.5px; color: #0B1D17;
}
.preq-modal-header i { color: var(--primary); margin-right: 8px; }
.preq-modal-close {
    background: none; border: none; font-size: 1.4rem; cursor: pointer;
    color: #94A3B8; line-height: 1;
}
.preq-modal-close:hover { color: #0B1D17; }
.preq-modal-body {
    padding: 20px 22px;
    font-size: 14px; color: #374151; line-height: 1.7;
}
.preq-modal-meta {
    display: flex; gap: 16px; margin-top: 16px; padding-top: 14px;
    border-top: 1px solid #F1F5F9; font-size: 12px;
    color: #94A3B8; font-weight: 600;
}
.preq-modal-meta i { color: var(--primary); }

/* ── EMPTY STATE ── */
.preq-empty {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; padding: 60px 24px; text-align: center;
}
.preq-empty-icon {
    width: 80px; height: 80px; border-radius: 50%;
    background: #F1F5F9; color: #CBD5E1;
    display: flex; align-items: center; justify-content: center;
    font-size: 2.4rem; margin-bottom: 18px;
}
.preq-empty-title { font-size: 1.15rem; font-weight: 800; color: #374151; margin-bottom: 8px; }
.preq-empty-sub { font-size: 13.5px; color: #94A3B8; max-width: 380px; line-height: 1.6; margin-bottom: 20px; }
.preq-empty-btn {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--primary); color: #fff;
    font-size: 13.5px; font-weight: 700;
    padding: 11px 24px; border-radius: 12px; text-decoration: none;
    box-shadow: 0 6px 20px -6px rgba(15,123,99,.45);
    transition: all .2s;
}
.preq-empty-btn:hover { background: var(--primary-dark); color: #fff; transform: translateY(-1px); }

/* ── PAGINATION ── */
.preq-pagination {
    padding: 16px 24px; border-top: 1px solid #F1F5F9;
    display: flex; justify-content: center;
}

/* ── INFO BANNER ── */
.preq-info-banner {
    background: linear-gradient(135deg, #F0FDF6, #E0F2FE);
    border: 1.5px solid #D3F0E0; border-radius: 20px;
    padding: 24px 28px;
    display: flex; align-items: flex-start; gap: 18px;
}
.pib-icon {
    width: 48px; height: 48px; border-radius: 14px;
    background: linear-gradient(135deg, #0F7B63, #34D399);
    color: #fff; display: flex; align-items: center;
    justify-content: center; font-size: 1.3rem; flex-shrink: 0;
}
.pib-title { font-size: 14px; font-weight: 800; color: #0B1D17; margin-bottom: 14px; }
.pib-steps { display: flex; flex-wrap: wrap; gap: 14px; }
.pib-step {
    display: flex; align-items: center; gap: 8px;
    font-size: 13px; color: #374151;
}
.pib-step-num {
    width: 22px; height: 22px; border-radius: 50%;
    background: var(--primary); color: #fff;
    font-size: 11px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .preq-item { padding: 16px; gap: 0; }
    .preq-item-right { display: none; }
    .preq-stat-strip { grid-template-columns: repeat(2, 1fr); }
    .pss-links { display: none; }
    .preq-header { flex-direction: column; }
    .pib-steps { flex-direction: column; }
    .preq-info-banner { flex-direction: column; }
}
@media (max-width: 480px) {
    .preq-stat-strip { grid-template-columns: 1fr 1fr; }
}
</style>
@endsection
