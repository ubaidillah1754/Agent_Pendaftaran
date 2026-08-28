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
<div class="card mb-4 fade-in">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('points.redemptions.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Status Penukaran</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai / Diterima</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm text-white flex-fill" style="background:var(--rs-primary); border-radius:8px; font-weight:600;">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('points.redemptions.index') }}" class="btn btn-sm btn-light border flex-fill" style="border-radius:8px;">Reset</a>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('points.katalog') }}" class="btn btn-sm text-white" style="background:var(--rs-primary); border-radius:8px; font-weight:700;">
                        <i class="bi bi-plus-circle me-1"></i>Tukar Reward Baru
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card fade-in">
    <div class="card-header">
        <span class="rs-card-title"><i class="bi bi-list-check"></i>Daftar Pengajuan Penukaran</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table rs-table mb-0">
                <thead>
                    <tr>
                        <th>Kode Referensi</th>
                        <th>Item Reward</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end">Total Poin</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Catatan / Keterangan</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($redemptions as $item)
                    <tr>
                        <td>
                            <code style="font-weight:800; color:var(--rs-ink); font-size:.84rem;">{{ $item->reference_code }}</code>
                        </td>
                        <td>
                            <div class="fw-bold" style="color:var(--rs-ink); font-size:.88rem;">{{ $item->merchandise_name }}</div>
                            <small class="text-muted">{{ number_format($item->points_required) }} poin / item</small>
                        </td>
                        <td class="text-center fw-semibold">
                            {{ $item->quantity }}
                        </td>
                        <td class="text-end fw-bold" style="color:var(--rs-accent); font-size:.92rem;">
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
                        <td style="font-size:.82rem; max-width:200px; color:var(--rs-muted);">
                            {{ $item->notes ?: '-' }}
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('points.redemptions.cetak', $item) }}" target="_blank" class="btn btn-sm btn-light border" style="border-radius:6px; font-size:.78rem; font-weight:600;" title="Cetak Resi Pengajuan">
                                <i class="bi bi-printer me-1"></i>Resi
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            Belum ada riwayat penukaran reward. Silakan kunjungi <a href="{{ route('points.katalog') }}" style="color:var(--rs-primary);">Katalog Reward</a> untuk menukarkan poin Anda.
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
@endsection
