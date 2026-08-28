@extends('layouts.app')
@section('title', 'Manajemen Penukaran Reward — Admin')
@section('page-title', 'Persetujuan Penukaran Reward')
@section('page-subtitle', 'Kelola seluruh permohonan penukaran reward, persetujuan, penyerahan hadiah, dan pembatalan.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Penukaran Reward</li>
@endsection

@push('styles')
<style>
    /* ── Premium Stat Cards ── */
    .rw-stats { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; margin-bottom: 24px; }
    .rw-stat {
        background: var(--surface); border-radius: 14px; padding: 18px 20px;
        border: 1px solid var(--border); position: relative; overflow: hidden;
        transition: all .25s; cursor: default;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .rw-stat:hover { transform: translateY(-3px); box-shadow: 0 8px 24px -8px rgba(0,0,0,.1); }
    .rw-stat::after {
        content: ''; position: absolute; right: -12px; bottom: -12px;
        width: 64px; height: 64px; border-radius: 50%;
        opacity: .08;
    }
    .rw-stat .rw-icon {
        width: 40px; height: 40px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.05rem; margin-bottom: 10px;
    }
    .rw-stat .rw-count { font-family: 'Spectral', serif; font-size: 1.8rem; font-weight: 800; line-height: 1; }
    .rw-stat .rw-label { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); margin-top: 4px; }

    /* stat colors */
    .rw-stat.pending .rw-icon  { background:#FEF3C7; color:#D97706; }
    .rw-stat.pending .rw-count { color:#B45309; }
    .rw-stat.pending::after    { background:#F59E0B; }
    .rw-stat.approved .rw-icon { background:#DBEAFE; color:#1D4ED8; }
    .rw-stat.approved .rw-count{ color:#1D4ED8; }
    .rw-stat.approved::after   { background:#3B82F6; }
    .rw-stat.completed .rw-icon{ background:#D1FAE5; color:#065F46; }
    .rw-stat.completed .rw-count{color:#065F46; }
    .rw-stat.completed::after  { background:#10B981; }
    .rw-stat.rejected .rw-icon { background:#FEE2E2; color:#991B1B; }
    .rw-stat.rejected .rw-count{ color:#B91C1C; }
    .rw-stat.rejected::after   { background:#EF4444; }
    .rw-stat.cancelled .rw-icon{ background:#F1F5F9; color:#475569; }
    .rw-stat.cancelled .rw-count{color:#475569; }
    .rw-stat.cancelled::after  { background:#64748B; }

    /* ── Filter Bar ── */
    .rw-filter {
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
        padding: 16px 20px; margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .rw-filter label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: var(--muted); margin-bottom: 4px; }
    .rw-filter .form-select, .rw-filter .form-control { border-radius: 10px; border-color: var(--border); font-size: .82rem; }
    .rw-filter .btn-filter {
        background: var(--primary); color: #fff; border: none; border-radius: 10px;
        font-weight: 700; font-size: .82rem; padding: 8px 18px;
        transition: all .2s; box-shadow: 0 2px 8px rgba(15,123,99,.2);
    }
    .rw-filter .btn-filter:hover { background: var(--primary-dark); transform: translateY(-1px); }
    .rw-filter .btn-reset { background: var(--bg); border: 1px solid var(--border); border-radius: 10px; font-size: .82rem; padding: 8px 16px; color: var(--muted); font-weight: 600; }

    /* ── Table Card ── */
    .rw-table-card { border-radius: 16px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 2px 12px -4px rgba(0,0,0,.06); }
    .rw-table-header {
        background: var(--surface); padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        display: flex; justify-content: space-between; align-items: center;
    }
    .rw-table-title { font-weight: 800; font-size: .95rem; color: var(--ink); display: flex; align-items: center; gap: 8px; }
    .rw-table-title i { color: var(--primary); }
    .rw-table-count {
        background: var(--primary-soft); color: var(--primary); font-size: .72rem;
        font-weight: 700; padding: 3px 10px; border-radius: 999px;
    }

    .rw-tbl { width: 100%; border-collapse: collapse; }
    .rw-tbl thead th {
        background: #F8FAF9; padding: 11px 16px; font-size: .7rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .05em; color: var(--muted); border-bottom: 1px solid var(--border);
    }
    .rw-tbl tbody tr { border-bottom: 1px solid #f0f3f2; transition: background .15s; }
    .rw-tbl tbody tr:hover { background: #f8faf9; }
    .rw-tbl tbody tr:last-child { border-bottom: none; }
    .rw-tbl tbody td { padding: 14px 16px; vertical-align: middle; font-size: .84rem; }

    /* Avatar */
    .rw-avatar {
        width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: .72rem; color: #fff;
    }

    /* Status Badges */
    .rw-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: 8px;
        font-size: .7rem; font-weight: 700; line-height: 1;
    }
    .rw-badge.pending  { background:#FEF3C7; color:#92400E; }
    .rw-badge.approved { background:#DBEAFE; color:#1E40AF; }
    .rw-badge.completed{ background:#D1FAE5; color:#065F46; }
    .rw-badge.rejected { background:#FEE2E2; color:#991B1B; }
    .rw-badge.cancelled{ background:#F1F5F9; color:#475569; }
    .rw-badge::before {
        content: ''; width: 6px; height: 6px; border-radius: 50%;
    }
    .rw-badge.pending::before  { background:#D97706; }
    .rw-badge.approved::before { background:#3B82F6; }
    .rw-badge.completed::before{ background:#10B981; }
    .rw-badge.rejected::before { background:#EF4444; }
    .rw-badge.cancelled::before{ background:#94A3B8; }

    /* Action Buttons */
    .rw-btn {
        padding: 5px 12px; border-radius: 8px; font-size: .74rem;
        font-weight: 700; border: none; cursor: pointer;
        display: inline-flex; align-items: center; gap: 4px;
        transition: all .2s;
    }
    .rw-btn.approve { background: #D1FAE5; color: #065F46; }
    .rw-btn.approve:hover { background: #059669; color: #fff; }
    .rw-btn.reject { background: #FEE2E2; color: #991B1B; }
    .rw-btn.reject:hover { background: #DC2626; color: #fff; }
    .rw-btn.deliver { background: var(--primary-soft); color: var(--primary-dark); }
    .rw-btn.deliver:hover { background: var(--primary); color: #fff; }
    .rw-btn.cancel-btn { background: #F1F5F9; color: #64748B; }
    .rw-btn.cancel-btn:hover { background: #E2E8F0; color: #334155; }
    .rw-btn.print-btn { background: #F8FAF9; color: var(--muted); border: 1px solid var(--border); }
    .rw-btn.print-btn:hover { background: var(--bg); }

    /* Empty State */
    .rw-empty { text-align: center; padding: 48px 20px; }
    .rw-empty i { font-size: 2.5rem; color: var(--border); margin-bottom: 12px; }
    .rw-empty p { font-size: .85rem; color: var(--muted); }

    /* Modals */
    .rw-modal .modal-content { border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 20px 60px -15px rgba(0,0,0,.25); }
    .rw-modal .modal-header { padding: 20px 24px; border-bottom: 1px solid var(--border); }
    .rw-modal .modal-body { padding: 24px; }
    .rw-modal .modal-footer { padding: 16px 24px; border-top: 1px solid var(--border); }
    .rw-modal .modal-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 12px; }
    .rw-modal .btn-modal { border-radius: 10px; font-weight: 700; font-size: .82rem; padding: 8px 20px; border: none; }

    /* Pagination */
    .rw-pagination { padding: 14px 20px; border-top: 1px solid var(--border); background: #FAFBFA; }

    @media (max-width: 1024px) { .rw-stats { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 600px) { .rw-stats { grid-template-columns: repeat(2, 1fr); } }
</style>
@endpush

@section('content')
{{-- ── Stat Cards ── --}}
<div class="rw-stats fade-in">
    <div class="rw-stat pending">
        <div class="rw-icon"><i class="bi bi-hourglass-split"></i></div>
        <div class="rw-count">{{ number_format($stats['pending']) }}</div>
        <div class="rw-label">Menunggu</div>
    </div>
    <div class="rw-stat approved">
        <div class="rw-icon"><i class="bi bi-check2-circle"></i></div>
        <div class="rw-count">{{ number_format($stats['approved']) }}</div>
        <div class="rw-label">Disetujui</div>
    </div>
    <div class="rw-stat completed">
        <div class="rw-icon"><i class="bi bi-box2-heart-fill"></i></div>
        <div class="rw-count">{{ number_format($stats['completed']) }}</div>
        <div class="rw-label">Selesai</div>
    </div>
    <div class="rw-stat rejected">
        <div class="rw-icon"><i class="bi bi-x-circle-fill"></i></div>
        <div class="rw-count">{{ number_format($stats['rejected']) }}</div>
        <div class="rw-label">Ditolak</div>
    </div>
    <div class="rw-stat cancelled">
        <div class="rw-icon"><i class="bi bi-slash-circle-fill"></i></div>
        <div class="rw-count">{{ number_format($stats['cancelled']) }}</div>
        <div class="rw-label">Dibatalkan</div>
    </div>
</div>

{{-- ── Filter Bar ── --}}
<div class="rw-filter fade-in">
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
                <button type="submit" class="btn-filter flex-fill"><i class="bi bi-funnel-fill me-1"></i>Filter</button>
                <a href="{{ route('admin.redemptions.index') }}" class="btn-reset flex-fill text-center text-decoration-none">Reset</a>
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
                    <th style="width:40px" class="ps-4">#</th>
                    <th>Karyawan</th>
                    <th>Item Reward</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-end">Total Poin</th>
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
                    <td class="ps-4" style="color:var(--muted); font-size:.78rem; font-weight:600;">{{ $redemptions->firstItem() + $i }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rw-avatar" style="background:{{ $bgColor }}">
                                {{ strtoupper(substr($item->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:700; color:var(--ink); font-size:.85rem;">{{ $item->user->name }}</div>
                                <div style="font-size:.72rem; color:var(--muted);">{{ $item->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:600; color:var(--ink); font-size:.84rem;">{{ $item->merchandise_name }}</div>
                        <div style="font-size:.7rem; color:var(--muted);">{{ number_format($item->points_required) }} poin/item</div>
                    </td>
                    <td class="text-center">
                        <span style="background:var(--bg); padding:3px 10px; border-radius:8px; font-weight:700; font-size:.82rem;">{{ $item->quantity }}</span>
                    </td>
                    <td class="text-end">
                        <span style="font-weight:800; color:var(--accent); font-size:.88rem;">{{ number_format($item->total_points) }}</span>
                        <span style="font-size:.7rem; color:var(--muted);"> Poin</span>
                    </td>
                    <td>
                        <div style="font-size:.8rem; font-weight:500; color:var(--ink);">{{ $item->created_at->format('d M Y') }}</div>
                        <div style="font-size:.68rem; color:var(--muted);">{{ $item->created_at->format('H:i') }} WIB</div>
                    </td>
                    <td>
                        @php
                            $statusMap = [
                                'pending' => 'pending', 'approved' => 'approved',
                                'completed' => 'completed', 'rejected' => 'rejected',
                                'cancelled' => 'cancelled'
                            ];
                            $sc = $statusMap[$item->status] ?? 'pending';
                        @endphp
                        <span class="rw-badge {{ $sc }}">{{ $item->status_label }}</span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-1 flex-nowrap">
                            @if($item->isPending())
                                <button type="button" class="rw-btn approve" data-bs-toggle="modal" data-bs-target="#modalApprove{{ $item->id }}">
                                    <i class="bi bi-check-lg"></i> Setujui
                                </button>
                                <button type="button" class="rw-btn reject" data-bs-toggle="modal" data-bs-target="#modalReject{{ $item->id }}">
                                    <i class="bi bi-x-lg"></i> Tolak
                                </button>
                            @elseif($item->isApproved())
                                <form action="{{ route('admin.redemptions.complete', $item) }}" method="POST" onsubmit="return confirm('Tandai penukaran ini telah selesai dan reward telah diserahkan?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="rw-btn deliver"><i class="bi bi-box-seam"></i> Serahkan</button>
                                </form>
                                <button type="button" class="rw-btn cancel-btn" data-bs-toggle="modal" data-bs-target="#modalCancel{{ $item->id }}">Batal</button>
                            @endif
                            <a href="{{ route('points.redemptions.cetak', $item) }}" target="_blank" class="rw-btn print-btn" title="Cetak Resi">
                                <i class="bi bi-printer"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="rw-empty">
                            <i class="bi bi-inbox d-block"></i>
                            <p>Tidak ada data penukaran reward yang ditemukan.</p>
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
                        <span style="width:28px;height:28px;border-radius:8px;background:#D1FAE5;color:#065F46;display:flex;align-items:center;justify-content:center;font-size:.85rem;">
                            <i class="bi bi-check-lg"></i>
                        </span>
                        Persetujuan Penukaran
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size:.85rem; line-height:1.6;">Setujui penukaran <strong>{{ $item->merchandise_name }} ({{ $item->quantity }}x)</strong> untuk karyawan <strong>{{ $item->user->name }}</strong> senilai <strong>{{ number_format($item->total_points) }} poin</strong>?</p>
                    <div class="p-3 rounded-3 mb-3" style="background:#F0FDF4; border:1px solid #BBF7D0;">
                        <div style="font-size:.78rem; color:#065F46;"><i class="bi bi-info-circle me-1"></i> Poin akan dipotong dari saldo karyawan setelah Anda menyetujui.</div>
                    </div>
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Catatan Admin (Opsional)</label>
                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="Contoh: Barang siap diambil di bagian HRD..." style="border-radius:10px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm" style="background:var(--bg); border:1px solid var(--border); border-radius:10px; font-weight:600;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm text-white" style="background:#059669; border-radius:10px; font-weight:700;"><i class="bi bi-check-lg me-1"></i>Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Reject --}}
<div class="modal fade rw-modal" id="modalReject{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.redemptions.reject', $item) }}" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <span style="width:28px;height:28px;border-radius:8px;background:#FEE2E2;color:#991B1B;display:flex;align-items:center;justify-content:center;font-size:.85rem;">
                            <i class="bi bi-x-lg"></i>
                        </span>
                        Tolak Penukaran
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size:.85rem; line-height:1.6;">Tolak penukaran <strong>{{ $item->merchandise_name }} ({{ $item->quantity }}x)</strong> dari <strong>{{ $item->user->name }}</strong>?</p>
                    <div class="p-3 rounded-3 mb-3" style="background:#FEF2F2; border:1px solid #FECACA;">
                        <div style="font-size:.78rem; color:#991B1B;"><i class="bi bi-info-circle me-1"></i> Poin <strong>+{{ number_format($item->total_points) }}</strong> dan stok <strong>+{{ $item->quantity }}</strong> akan dikembalikan otomatis.</div>
                    </div>
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control form-control-sm" rows="2" placeholder="Contoh: Stok barang cacat fisik / salah pengajuan..." required style="border-radius:10px;"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm" style="background:var(--bg); border:1px solid var(--border); border-radius:10px; font-weight:600;" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-sm text-white" style="background:#DC2626; border-radius:10px; font-weight:700;"><i class="bi bi-x-lg me-1"></i>Tolak & Kembalikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($item->isApproved())
{{-- Modal Cancel --}}
<div class="modal fade rw-modal" id="modalCancel{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.redemptions.cancel', $item) }}" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <span style="width:28px;height:28px;border-radius:8px;background:#FEF3C7;color:#92400E;display:flex;align-items:center;justify-content:center;font-size:.85rem;">
                            <i class="bi bi-exclamation-triangle"></i>
                        </span>
                        Batalkan Penukaran
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size:.85rem; line-height:1.6;">Batalkan penukaran <strong>{{ $item->merchandise_name }}</strong> yang sebelumnya sudah disetujui?</p>
                    <div class="p-3 rounded-3 mb-3" style="background:#FFFBEB; border:1px solid #FDE68A;">
                        <div style="font-size:.78rem; color:#92400E;"><i class="bi bi-info-circle me-1"></i> Poin <strong>+{{ number_format($item->total_points) }}</strong> dan stok barang akan dikembalikan.</div>
                    </div>
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Alasan Pembatalan <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control form-control-sm" rows="2" placeholder="Alasan pembatalan..." required style="border-radius:10px;"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm" style="background:var(--bg); border:1px solid var(--border); border-radius:10px; font-weight:600;" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-sm text-white" style="background:#D97706; border-radius:10px; font-weight:700;"><i class="bi bi-arrow-counterclockwise me-1"></i>Batalkan & Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection




@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Penukaran Reward</li>
@endsection

@section('content')
<!-- Ringkasan Status Cards -->
<div class="row g-3 mb-4 fade-in">
    <div class="col-6 col-md-4 col-xl">
        <div class="stat-card" style="border-left: 4px solid #F59E0B;">
            <div class="stat-icon" style="background:#FEF3C7; color:#D97706;">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="stat-label">Menunggu</div>
                <div class="stat-value" style="color:#D97706;">{{ number_format($stats['pending']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="stat-card" style="border-left: 4px solid #3B82F6;">
            <div class="stat-icon" style="background:#DBEAFE; color:#1D4ED8;">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <div class="stat-label">Disetujui</div>
                <div class="stat-value" style="color:#1D4ED8;">{{ number_format($stats['approved']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="stat-card" style="border-left: 4px solid #10B981;">
            <div class="stat-icon" style="background:#D1FAE5; color:#065F46;">
                <i class="bi bi-box2-heart-fill"></i>
            </div>
            <div>
                <div class="stat-label">Selesai</div>
                <div class="stat-value" style="color:#065F46;">{{ number_format($stats['completed']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="stat-card" style="border-left: 4px solid #EF4444;">
            <div class="stat-icon" style="background:#FEE2E2; color:#991B1B;">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div>
                <div class="stat-label">Ditolak</div>
                <div class="stat-value" style="color:#991B1B;">{{ number_format($stats['rejected']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="stat-card" style="border-left: 4px solid #64748B;">
            <div class="stat-icon" style="background:#F1F5F9; color:#475569;">
                <i class="bi bi-slash-circle-fill"></i>
            </div>
            <div>
                <div class="stat-label">Dibatalkan</div>
                <div class="stat-value" style="color:#475569;">{{ number_format($stats['cancelled']) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Box -->
<div class="card mb-4 fade-in">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.redemptions.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Status</label>
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
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Karyawan / Petugas</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Semua Karyawan</option>
                        @foreach($petugasList as $p)
                            <option value="{{ $p->id }}" {{ request('user_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ request('dari') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm text-white flex-fill" style="background:var(--rs-primary); border-radius:8px; font-weight:600;">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.redemptions.index') }}" class="btn btn-sm btn-light border flex-fill" style="border-radius:8px;">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Penukaran -->
<div class="card fade-in">
    <div class="card-header">
        <span class="rs-card-title"><i class="bi bi-list-stars"></i>Daftar Pengajuan Penukaran Reward</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table rs-table mb-0">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Item Reward</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end">Total Poin</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($redemptions as $item)
                    <tr>
                        <td>
                            <div class="fw-bold" style="color:var(--rs-ink); font-size:.88rem;">{{ $item->user->name }}</div>
                            <small class="text-muted">{{ $item->user->email }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold" style="font-size:.88rem;">{{ $item->merchandise_name }}</div>
                            <small class="text-muted">{{ number_format($item->points_required) }} poin/item</small>
                        </td>
                        <td class="text-center fw-bold">
                            {{ $item->quantity }}
                        </td>
                        <td class="text-end fw-bold" style="color:var(--rs-accent); font-size:.9rem;">
                            {{ number_format($item->total_points) }} Poin
                        </td>
                        <td style="font-size:.82rem; white-space:nowrap;">
                            {{ $item->created_at->format('d M Y, H:i') }}
                        </td>
                        <td>
                            <span class="badge bg-{{ $item->status_badge }}">
                                {{ $item->status_label }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                @if($item->isPending())
                                    <button type="button" class="btn btn-sm btn-success" style="border-radius:6px; font-size:.78rem; font-weight:600;"
                                            data-bs-toggle="modal" data-bs-target="#modalApprove{{ $item->id }}" title="Setujui Penukaran">
                                        <i class="bi bi-check-lg me-1"></i>Setujui
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" style="border-radius:6px; font-size:.78rem; font-weight:600;"
                                            data-bs-toggle="modal" data-bs-target="#modalReject{{ $item->id }}" title="Tolak Penukaran">
                                        <i class="bi bi-x-lg me-1"></i>Tolak
                                    </button>
                                @elseif($item->isApproved())
                                    <form action="{{ route('admin.redemptions.complete', $item) }}" method="POST" onsubmit="return confirm('Tandai penukaran ini telah selesai dan reward telah diserahkan?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm text-white" style="background:#0F7B63; border-radius:6px; font-size:.78rem; font-weight:600;">
                                            <i class="bi bi-box-seam me-1"></i>Serahkan
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" style="border-radius:6px; font-size:.78rem; font-weight:600;"
                                            data-bs-toggle="modal" data-bs-target="#modalCancel{{ $item->id }}" title="Batalkan Penukaran">
                                        Batal
                                    </button>
                                @endif
                                <a href="{{ route('points.redemptions.cetak', $item) }}" target="_blank" class="btn btn-sm btn-light border" style="border-radius:6px; font-size:.78rem;" title="Cetak Resi">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>



                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Tidak ada data penukaran reward yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($redemptions->hasPages())
    <div class="card-footer bg-transparent border-top">
        {{ $redemptions->links() }}
    </div>
    @endif
</div>

@foreach($redemptions as $item)
<!-- Modal Approve -->
@if($item->isPending())
<div class="modal fade" id="modalApprove{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;">
            <form action="{{ route('admin.redemptions.approve', $item) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Persetujuan Penukaran Reward</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <p style="font-size:.85rem;">Setujui penukaran reward <strong>{{ $item->merchandise_name }} ({{ $item->quantity }}x)</strong> untuk karyawan <strong>{{ $item->user->name }}</strong> senilai <strong>{{ number_format($item->total_points) }} poin</strong>?</p>
                    <div class="mb-2">
                        <label class="form-label" style="font-size:.75rem; font-weight:700;">Catatan Admin (Opsional)</label>
                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="Contoh: Barang siap diambil di bagian HRD...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success fw-bold">Setujui Penukaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="modalReject{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;">
            <form action="{{ route('admin.redemptions.reject', $item) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h6 class="modal-title fw-bold text-danger">Tolak Penukaran Reward</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <p style="font-size:.85rem;">Anda akan menolak penukaran <strong>{{ $item->reference_code }}</strong>. Poin <strong>+{{ number_format($item->total_points) }}</strong> dan stok <strong>+{{ $item->quantity }}</strong> akan dikembalikan secara otomatis.</p>
                    <div class="mb-2">
                        <label class="form-label" style="font-size:.75rem; font-weight:700;">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control form-control-sm" rows="2" placeholder="Contoh: Stok barang cacat fisik / salah pengajuan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold">Tolak &amp; Kembalikan Poin</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal Cancel -->
@if($item->isApproved())
<div class="modal fade" id="modalCancel{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;">
            <form action="{{ route('admin.redemptions.cancel', $item) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h6 class="modal-title fw-bold text-warning">Batalkan Penukaran</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <p style="font-size:.85rem;">Batalkan penukaran <strong>{{ $item->reference_code }}</strong> yang sebelumnya sudah disetujui? Poin <strong>+{{ number_format($item->total_points) }}</strong> dan stok barang akan dikembalikan.</p>
                    <div class="mb-2">
                        <label class="form-label" style="font-size:.75rem; font-weight:700;">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control form-control-sm" rows="2" placeholder="Alasan pembatalan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-sm btn-warning fw-bold">Batalkan &amp; Refund Poin</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection
