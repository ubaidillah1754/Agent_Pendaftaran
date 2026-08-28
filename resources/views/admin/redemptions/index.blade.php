@extends('layouts.app')
@section('title', 'Manajemen Penukaran Reward — Admin')
@section('page-title', 'Penukaran Reward')
@section('page-subtitle', 'Kelola permohonan, persetujuan, dan penyerahan hadiah karyawan.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Penukaran Reward</li>
@endsection

@push('styles')
<style>
    /* ══ Hero Banner ══════════════════════════════════════════════ */
    .rw-hero {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 55%, #12A07A 100%);
        border-radius: 20px;
        padding: 26px 30px;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 8px 32px -8px rgba(15,123,99,.4);
    }
    .rw-hero::before {
        content: ''; position: absolute;
        top: -40px; right: -40px;
        width: 220px; height: 220px;
        border-radius: 50%; background: rgba(255,255,255,.06);
    }
    .rw-hero::after {
        content: ''; position: absolute;
        bottom: -60px; right: 100px;
        width: 160px; height: 160px;
        border-radius: 50%; background: rgba(255,255,255,.04);
    }
    .rw-hero-text h2 {
        font-size: 1.3rem; font-weight: 800; color: #fff;
        margin: 0 0 6px; line-height: 1.2;
    }
    .rw-hero-text p {
        color: rgba(255,255,255,.78); font-size: .83rem;
        margin: 0; line-height: 1.5;
    }
    .rw-hero-icon {
        width: 68px; height: 68px; border-radius: 20px;
        background: rgba(255,255,255,.15);
        backdrop-filter: blur(8px);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.9rem; color: #fff; flex-shrink: 0;
        border: 1px solid rgba(255,255,255,.2);
        position: relative; z-index: 1;
    }

    /* ══ Stat Cards ═══════════════════════════════════════════════ */
    .rw-stats { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-bottom: 22px; }
    .rw-stat {
        background: var(--surface); border-radius: 16px; padding: 18px 16px;
        border: 1px solid var(--border); position: relative; overflow: hidden;
        transition: transform .22s, box-shadow .22s; cursor: default;
    }
    .rw-stat:hover { transform: translateY(-3px); box-shadow: 0 12px 28px -10px rgba(0,0,0,.12); }
    .rw-stat::after {
        content: ''; position: absolute; right: -14px; bottom: -14px;
        width: 72px; height: 72px; border-radius: 50%; opacity: .08;
    }
    .rw-icon {
        width: 42px; height: 42px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; margin-bottom: 12px;
    }
    .rw-count {
        font-family: 'Spectral', serif, sans-serif;
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1.1;
        background: transparent !important;
        display: block;
        margin-bottom: 2px;
    }
    .rw-label {
        font-size: .7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--muted) !important;
        background: transparent !important;
        display: block;
    }

    /* colour tokens */
    .rw-stat.pending  .rw-icon  { background:#FEF3C7 !important; color:#D97706 !important; }
    .rw-stat.pending  .rw-count { color:#B45309 !important; }
    .rw-stat.pending::after     { background:#F59E0B !important; }
    .rw-stat.approved .rw-icon  { background:#DBEAFE !important; color:#1D4ED8 !important; }
    .rw-stat.approved .rw-count { color:#1D4ED8 !important; }
    .rw-stat.approved::after    { background:#3B82F6 !important; }
    .rw-stat.completed .rw-icon { background:#D1FAE5 !important; color:#065F46 !important; }
    .rw-stat.completed .rw-count{ color:#065F46 !important; }
    .rw-stat.completed::after   { background:#10B981 !important; }
    .rw-stat.rejected .rw-icon  { background:#FEE2E2 !important; color:#991B1B !important; }
    .rw-stat.rejected .rw-count { color:#B91C1C !important; }
    .rw-stat.rejected::after    { background:#EF4444 !important; }
    .rw-stat.cancelled .rw-icon { background:#F1F5F9 !important; color:#475569 !important; }
    .rw-stat.cancelled .rw-count{ color:#475569 !important; }
    .rw-stat.cancelled::after   { background:#64748B !important; }

    /* ══ Filter Bar ═══════════════════════════════════════════════ */
    .rw-filter {
        background: var(--surface); border: 1px solid var(--border); border-radius: 16px;
        padding: 18px 20px; margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .rw-filter .filter-title { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--primary); margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
    .rw-filter label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: var(--muted); margin-bottom: 4px; display: block; }
    .rw-filter .form-select, .rw-filter .form-control { border-radius: 10px; border-color: var(--border); font-size: .82rem; }
    .rw-filter .form-select:focus, .rw-filter .form-control:focus { border-color: var(--primary-light); box-shadow: 0 0 0 3px rgba(15,123,99,.15); }
    .btn-filter {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #fff; border: none; border-radius: 10px;
        font-weight: 700; font-size: .82rem; padding: 9px 18px;
        transition: all .2s; box-shadow: 0 3px 10px rgba(15,123,99,.25);
        display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-filter:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(15,123,99,.3); }
    .btn-reset {
        background: var(--bg); border: 1px solid var(--border);
        border-radius: 10px; font-size: .82rem; padding: 9px 16px;
        color: var(--muted); font-weight: 600; transition: all .15s;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-reset:hover { background: var(--surface); color: var(--ink); }

    /* ══ Table Card ═══════════════════════════════════════════════ */
    .rw-table-card {
        border-radius: 18px; border: 1px solid var(--border);
        overflow: hidden; box-shadow: 0 2px 16px -6px rgba(0,0,0,.08);
        background: var(--surface);
    }
    .rw-table-header {
        background: linear-gradient(to right, #F8FDFB, var(--surface));
        padding: 18px 22px; border-bottom: 1px solid var(--border);
        display: flex; justify-content: space-between; align-items: center;
    }
    .rw-table-title { font-weight: 800; font-size: .96rem; color: var(--ink); display: flex; align-items: center; gap: 8px; }
    .rw-table-title i { color: var(--primary); }
    .rw-table-count {
        background: var(--primary-soft); color: var(--primary); font-size: .72rem;
        font-weight: 700; padding: 4px 12px; border-radius: 999px;
        border: 1px solid rgba(15,123,99,.15);
    }
    .rw-tbl { width: 100%; border-collapse: collapse; }
    .rw-tbl thead th {
        background: #F4F9F7; padding: 12px 16px;
        font-size: .69rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em;
        color: var(--muted); border-bottom: 2px solid var(--border);
        white-space: nowrap;
    }
    .rw-tbl tbody tr { border-bottom: 1px solid #EEF4F1; transition: background .15s; }
    .rw-tbl tbody tr:hover { background: #F8FDFB; }
    .rw-tbl tbody tr:last-child { border-bottom: none; }
    .rw-tbl tbody td { padding: 14px 16px; vertical-align: middle; font-size: .84rem; }

    /* Avatar */
    .rw-avatar {
        width: 36px; height: 36px; border-radius: 11px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: .75rem; color: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,.15);
    }

    /* Poin & Qty chips */
    .poin-chip {
        display: inline-flex; align-items: center; gap: 4px;
        background: var(--accent-soft); color: var(--accent);
        font-weight: 800; font-size: .82rem;
        padding: 4px 10px; border-radius: 8px;
        border: 1px solid rgba(184,145,46,.2);
    }
    .poin-chip i { font-size: .7rem; }
    .qty-chip {
        background: var(--primary-soft); color: var(--primary-dark);
        font-weight: 800; font-size: .82rem;
        padding: 3px 11px; border-radius: 8px; display: inline-block;
    }

    /* Badges */
    .rw-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 11px; border-radius: 9px;
        font-size: .7rem; font-weight: 700; line-height: 1; white-space: nowrap;
    }
    .rw-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .rw-badge.pending   { background:#FEF3C7; color:#92400E; }
    .rw-badge.pending::before   { background:#D97706; }
    .rw-badge.approved  { background:#DBEAFE; color:#1E40AF; }
    .rw-badge.approved::before  { background:#3B82F6; }
    .rw-badge.completed { background:#D1FAE5; color:#065F46; }
    .rw-badge.completed::before { background:#10B981; }
    .rw-badge.rejected  { background:#FEE2E2; color:#991B1B; }
    .rw-badge.rejected::before  { background:#EF4444; }
    .rw-badge.cancelled { background:#F1F5F9; color:#475569; }
    .rw-badge.cancelled::before { background:#94A3B8; }

    /* Action Buttons */
    .rw-btn {
        padding: 6px 13px; border-radius: 9px; font-size: .74rem;
        font-weight: 700; border: none; cursor: pointer;
        display: inline-flex; align-items: center; gap: 5px;
        transition: all .2s; white-space: nowrap;
    }
    .rw-btn.approve  { background:#D1FAE5; color:#065F46; }
    .rw-btn.approve:hover  { background:#059669; color:#fff; transform:translateY(-1px); }
    .rw-btn.reject   { background:#FEE2E2; color:#991B1B; }
    .rw-btn.reject:hover   { background:#DC2626; color:#fff; transform:translateY(-1px); }
    .rw-btn.deliver  { background:var(--primary-soft); color:var(--primary-dark); }
    .rw-btn.deliver:hover  { background:var(--primary); color:#fff; transform:translateY(-1px); }
    .rw-btn.cancel-btn { background:#F1F5F9; color:#64748B; }
    .rw-btn.cancel-btn:hover { background:#E2E8F0; color:#334155; }
    .rw-btn.print-btn { background:var(--bg); color:var(--muted); border:1px solid var(--border); }
    .rw-btn.print-btn:hover { background:var(--surface); color:var(--ink); }

    /* Empty State */
    .rw-empty { text-align: center; padding: 60px 20px; }
    .rw-empty-icon {
        width: 72px; height: 72px; border-radius: 20px;
        background: var(--primary-soft); margin: 0 auto 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: var(--primary);
    }
    .rw-empty h6 { font-weight: 700; color: var(--ink); margin-bottom: 6px; }
    .rw-empty p  { font-size: .84rem; color: var(--muted); margin: 0; }

    /* Modals */
    .rw-modal .modal-content { border-radius: 18px; border: none; overflow: hidden; box-shadow: 0 24px 64px -16px rgba(0,0,0,.28); }
    .rw-modal .modal-header { padding: 20px 24px; border-bottom: 1px solid var(--border); }
    .rw-modal .modal-body   { padding: 24px; }
    .rw-modal .modal-footer { padding: 16px 24px; border-top: 1px solid var(--border); background: #FAFBFA; }
    .rw-modal .modal-icon {
        width: 48px; height: 48px; border-radius: 13px;
        display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
    }
    .rw-modal .info-box {
        border-radius: 12px; padding: 13px 16px;
        font-size: .8rem; line-height: 1.55;
        display: flex; gap: 8px; align-items: flex-start;
    }

    /* Pagination */
    .rw-pagination { padding: 14px 22px; border-top: 1px solid var(--border); background: #FAFBFA; }

    @media (max-width: 1100px) { .rw-stats { grid-template-columns: repeat(3,1fr); } }
    @media (max-width: 640px)  { .rw-stats { grid-template-columns: repeat(2,1fr); } }
</style>
@endpush

@section('content')

{{-- ══ Hero Banner ══════════════════════════════════════════════════════════ --}}
<div class="rw-hero fade-in">
    <div class="rw-hero-text">
        <h2><i class="bi bi-gift-fill me-2" style="font-size:1.05rem;opacity:.85;"></i>Pengelolaan Penukaran Reward</h2>
        <p>Kelola seluruh permohonan penukaran, persetujuan, penyerahan hadiah, dan pembatalan karyawan.</p>
    </div>
    <div class="rw-hero-icon">
        <i class="bi bi-box2-heart-fill"></i>
    </div>
</div>

{{-- ══ Stat Cards ══════════════════════════════════════════════════════════ --}}
<div class="rw-stats">
    <div class="rw-stat pending fade-in">
        <div class="rw-icon"><i class="bi bi-hourglass-split"></i></div>
        <div class="rw-count">{{ number_format($stats['pending']) }}</div>
        <div class="rw-label">Menunggu</div>
    </div>
    <div class="rw-stat approved fade-in fade-in-delay-1">
        <div class="rw-icon"><i class="bi bi-check2-circle"></i></div>
        <div class="rw-count">{{ number_format($stats['approved']) }}</div>
        <div class="rw-label">Disetujui</div>
    </div>
    <div class="rw-stat completed fade-in fade-in-delay-2">
        <div class="rw-icon"><i class="bi bi-box2-heart-fill"></i></div>
        <div class="rw-count">{{ number_format($stats['completed']) }}</div>
        <div class="rw-label">Selesai</div>
    </div>
    <div class="rw-stat rejected fade-in fade-in-delay-3">
        <div class="rw-icon"><i class="bi bi-x-circle-fill"></i></div>
        <div class="rw-count">{{ number_format($stats['rejected']) }}</div>
        <div class="rw-label">Ditolak</div>
    </div>
    <div class="rw-stat cancelled fade-in fade-in-delay-4">
        <div class="rw-icon"><i class="bi bi-slash-circle-fill"></i></div>
        <div class="rw-count">{{ number_format($stats['cancelled']) }}</div>
        <div class="rw-label">Dibatalkan</div>
    </div>
</div>

{{-- ══ Filter Bar ══════════════════════════════════════════════════════════ --}}
<div class="rw-filter fade-in">
    <div class="filter-title"><i class="bi bi-funnel-fill"></i> Filter Data</div>
    <form method="GET" action="{{ route('admin.redemptions.index') }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label>Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui (Siap Ambil)</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai / Diterima</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Karyawan / Petugas</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Semua Karyawan</option>
                    @foreach($petugasList as $p)
                        <option value="{{ $p->id }}" {{ request('user_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Dari Tanggal</label>
                <input type="date" name="dari" value="{{ request('dari') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label>Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn-filter flex-fill">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                <a href="{{ route('admin.redemptions.index') }}" class="btn-reset flex-fill text-center">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

{{-- ── Data Table ── --}}
<div class="rw-table-card fade-in">
    <div class="rw-table-header">
        <div class="rw-table-title"><i class="bi bi-gift-fill"></i> Daftar Pengajuan Penukaran Reward</div>
        <span class="rw-table-count">{{ $redemptions->total() }} pengajuan</span>
    </div>
    <div class="table-responsive">
        <table class="rw-tbl">
            <thead>
                <tr>
                    <th class="ps-4" style="width:46px">#</th>
                    <th>Karyawan</th>
                    <th>Item Reward</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Total Poin</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($redemptions as $i => $item)
                @php
                    $colors = ['#0F7B63','#B8912E','#0E7490','#7C3AED','#C2410C','#0369A1','#4338CA','#B91C1C'];
                    $bgColor = $colors[crc32($item->user->name ?? '') % count($colors)];
                @endphp
                <tr>
                    <td class="ps-4" style="color:var(--muted); font-size:.78rem; font-weight:700;">{{ $redemptions->firstItem() + $i }}</td>

                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rw-avatar" style="background:{{ $bgColor }}">
                                {{ strtoupper(substr($item->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:700; color:var(--ink); font-size:.86rem; line-height:1.2;">{{ $item->user->name }}</div>
                                <div style="font-size:.72rem; color:var(--muted); margin-top:2px;">{{ $item->user->email }}</div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <div style="font-weight:700; color:var(--ink); font-size:.86rem; line-height:1.2;">{{ $item->merchandise_name }}</div>
                        <div style="font-size:.7rem; color:var(--muted); margin-top:2px;">
                            <i class="bi bi-star-fill" style="color:var(--accent); font-size:.6rem;"></i>
                            {{ number_format($item->points_required) }} poin/item
                        </div>
                    </td>

                    <td class="text-center">
                        <span class="qty-chip">{{ $item->quantity }}</span>
                    </td>

                    <td class="text-center">
                        <span class="poin-chip">
                            <i class="bi bi-star-fill"></i>{{ number_format($item->total_points) }}
                        </span>
                    </td>

                    <td>
                        <div style="font-size:.82rem; font-weight:600; color:var(--ink);">{{ $item->created_at->format('d M Y') }}</div>
                        <div style="font-size:.7rem; color:var(--muted);">{{ $item->created_at->format('H:i') }} WIB</div>
                    </td>

                    <td>
                        @php $sc = $item->status ?? 'pending'; @endphp
                        <span class="rw-badge {{ $sc }}">{{ $item->status_label }}</span>
                    </td>

                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-1 flex-nowrap">
                            @if($item->isPending())
                                <button type="button" class="rw-btn approve"
                                        data-bs-toggle="modal" data-bs-target="#modalApprove{{ $item->id }}">
                                    <i class="bi bi-check-lg"></i> Setujui
                                </button>
                                <button type="button" class="rw-btn reject"
                                        data-bs-toggle="modal" data-bs-target="#modalReject{{ $item->id }}">
                                    <i class="bi bi-x-lg"></i> Tolak
                                </button>
                            @elseif($item->isApproved())
                                <form action="{{ route('admin.redemptions.complete', $item) }}" method="POST"
                                      onsubmit="return confirm('Tandai penukaran ini telah selesai dan reward telah diserahkan?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="rw-btn deliver">
                                        <i class="bi bi-box-seam"></i> Serahkan
                                    </button>
                                </form>
                                <button type="button" class="rw-btn cancel-btn"
                                        data-bs-toggle="modal" data-bs-target="#modalCancel{{ $item->id }}">
                                    Batal
                                </button>
                            @endif
                            <a href="{{ route('points.redemptions.cetak', $item) }}" target="_blank"
                               class="rw-btn print-btn" title="Cetak Resi">
                                <i class="bi bi-printer"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="rw-empty">
                            <div class="rw-empty-icon"><i class="bi bi-inbox"></i></div>
                            <h6>Tidak Ada Data</h6>
                            <p>Tidak ada data penukaran reward yang ditemukan sesuai filter.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($redemptions->hasPages())
    <div class="rw-pagination">
        {{ $redemptions->links() }}
    </div>
    @endif
</div>

{{-- ── Modals ── --}}
@foreach($redemptions as $item)
@if($item->isPending())
{{-- Modal Approve --}}
<div class="modal fade rw-modal" id="modalApprove{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.redemptions.approve', $item) }}" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <span class="modal-icon" style="background:#D1FAE5; color:#065F46;">
                            <i class="bi bi-check-lg"></i>
                        </span>
                        Persetujuan Penukaran
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size:.87rem; line-height:1.6; margin-bottom:14px;">
                        Setujui penukaran <strong>{{ $item->merchandise_name }} ({{ $item->quantity }}x)</strong>
                        untuk karyawan <strong>{{ $item->user->name }}</strong> senilai
                        <strong>{{ number_format($item->total_points) }} poin</strong>?
                    </p>
                    <div class="info-box mb-3" style="background:#F0FDF4; border:1px solid #BBF7D0; color:#065F46;">
                        <i class="bi bi-info-circle-fill" style="margin-top:1px; flex-shrink:0;"></i>
                        <span>Poin akan dipotong otomatis dari saldo karyawan setelah persetujuan.</span>
                    </div>
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Catatan Admin <span style="color:var(--muted); font-weight:400;">(Opsional)</span></label>
                    <input type="text" name="notes" class="form-control form-control-sm"
                           placeholder="Contoh: Barang siap diambil di bagian HRD..."
                           style="border-radius:10px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm px-4"
                            style="background:var(--bg); border:1px solid var(--border); border-radius:10px; font-weight:600;"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm text-white px-4"
                            style="background:#059669; border-radius:10px; font-weight:700;">
                        <i class="bi bi-check-lg me-1"></i>Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade rw-modal" id="modalReject{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <form action="{{ route('admin.redemptions.reject', $item) }}" method="POST">
            @csrf @method('PATCH')
            <div class="modal-header"><h6 class="modal-title fw-bold d-flex align-items-center gap-2"><span class="modal-icon" style="background:#FEE2E2; color:#991B1B;"><i class="bi bi-x-lg"></i></span>Tolak Penukaran</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <p style="font-size:.87rem; line-height:1.6; margin-bottom:14px;">Tolak penukaran <strong>{{ $item->merchandise_name }} ({{ $item->quantity }}x)</strong> dari <strong>{{ $item->user->name }}</strong>?</p>
                <div class="info-box mb-3" style="background:#FEF2F2; border:1px solid #FECACA; color:#991B1B;"><i class="bi bi-info-circle-fill" style="margin-top:1px; flex-shrink:0;"></i><span>Poin <strong>+{{ number_format($item->total_points) }}</strong> dan stok <strong>+{{ $item->quantity }}</strong> akan dikembalikan.</span></div>
                <label class="form-label" style="font-size:.75rem; font-weight:700;">Alasan Penolakan <span class="text-danger">*</span></label>
                <textarea name="reason" class="form-control form-control-sm" rows="2" placeholder="Contoh: Stok barang cacat fisik / salah pengajuan..." required style="border-radius:10px;"></textarea>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-sm px-4" style="background:var(--bg); border:1px solid var(--border); border-radius:10px; font-weight:600;" data-bs-dismiss="modal">Tutup</button><button type="submit" class="btn btn-sm text-white px-4" style="background:#DC2626; border-radius:10px; font-weight:700;"><i class="bi bi-x-lg me-1"></i>Tolak & Kembalikan</button></div>
        </form>
    </div></div>
</div>
@endif

@if($item->isApproved())
<div class="modal fade rw-modal" id="modalCancel{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.redemptions.cancel', $item) }}" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <span class="modal-icon" style="background:#FEF3C7; color:#92400E;">
                            <i class="bi bi-exclamation-triangle"></i>
                        </span>
                        Batalkan Penukaran
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size:.87rem; line-height:1.6; margin-bottom:14px;">
                        Batalkan penukaran <strong>{{ $item->merchandise_name }}</strong> yang sebelumnya sudah disetujui?
                    </p>
                    <div class="info-box mb-3" style="background:#FFFBEB; border:1px solid #FDE68A; color:#92400E;">
                        <i class="bi bi-info-circle-fill" style="margin-top:1px; flex-shrink:0;"></i>
                        <span>Poin <strong>+{{ number_format($item->total_points) }}</strong> dan stok barang akan dikembalikan.</span>
                    </div>
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Alasan Pembatalan <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control form-control-sm" rows="2"
                              placeholder="Alasan pembatalan..." required style="border-radius:10px;"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm px-4"
                            style="background:var(--bg); border:1px solid var(--border); border-radius:10px; font-weight:600;"
                            data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-sm text-white px-4"
                            style="background:#D97706; border-radius:10px; font-weight:700;">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Batalkan &amp; Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection

