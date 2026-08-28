@extends('layouts.app')
@section('title', 'Manajemen Penukaran Reward — Admin')
@section('page-title', 'Persetujuan Penukaran Reward')
@section('page-subtitle', 'Kelola seluruh permohonan penukaran reward, persetujuan, penyerahan hadiah, dan pembatalan.')

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
                        <th>Kode Ref</th>
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
                            <code style="font-weight:800; color:var(--rs-ink); font-size:.82rem;">{{ $item->reference_code }}</code>
                        </td>
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
                        <td colspan="8" class="text-center py-4 text-muted">
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
