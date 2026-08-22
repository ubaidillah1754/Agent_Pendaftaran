@extends('layouts.app')
@section('title', 'Riwayat Poin — My Sakinah Agent')
@section('page-title', 'Riwayat Mutasi Poin')
@section('page-subtitle', 'Buku besar lengkap seluruh perolehan, penukaran, dan penyesuaian poin Anda.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('points.index') }}">Poin Saya</a></li>
    <li class="breadcrumb-item active">Riwayat Mutasi</li>
@endsection

@section('content')
<div class="card mb-4 fade-in">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('points.riwayat') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Tipe Mutasi</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Semua Tipe</option>
                        <option value="earn" {{ request('type') === 'earn' ? 'selected' : '' }}>Poin Masuk (Earn)</option>
                        <option value="redeem" {{ request('type') === 'redeem' ? 'selected' : '' }}>Tukar Reward (Redeem)</option>
                        <option value="reversal" {{ request('type') === 'reversal' ? 'selected' : '' }}>Pengembalian (Reversal)</option>
                        <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Penyesuaian (Adjustment)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ request('dari') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm text-white flex-fill" style="background:var(--rs-primary); border-radius:8px; font-weight:600;">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('points.riwayat') }}" class="btn btn-sm btn-light border flex-fill" style="border-radius:8px;">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="rs-card-title"><i class="bi bi-journal-text"></i>Buku Besar Transaksi Poin</span>
        <span class="badge" style="background:var(--rs-primary-soft); color:var(--rs-primary-dark); font-size:.8rem; padding:6px 12px;">
            Saldo Saat Ini: <strong>{{ number_format($user->point_balance) }} Poin</strong>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table rs-table mb-0">
                <thead>
                    <tr>
                        <th>Tanggal &amp; Waktu</th>
                        <th>Kode Referensi</th>
                        <th>Tipe</th>
                        <th>Keterangan</th>
                        <th class="text-end">Saldo Sebelum</th>
                        <th class="text-end">Jumlah</th>
                        <th class="text-end pe-4">Saldo Sesudah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr>
                        <td style="white-space:nowrap; font-size:.82rem;">
                            {{ $tx->created_at->format('d M Y, H:i') }}
                        </td>
                        <td>
                            <code style="font-weight:700; color:var(--rs-ink); font-size:.8rem;">{{ $tx->reference }}</code>
                        </td>
                        <td>
                            <span class="badge bg-{{ $tx->type_badge }}">{{ $tx->type_label }}</span>
                        </td>
                        <td style="font-size:.84rem; max-width:280px;">
                            {{ $tx->description }}
                        </td>
                        <td class="text-end" style="color:var(--rs-muted); font-size:.84rem;">
                            {{ number_format($tx->balance_before) }}
                        </td>
                        <td class="text-end fw-bold" style="color:{{ $tx->amount > 0 ? '#0F7B63' : '#B54545' }}; font-size:.9rem;">
                            {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount) }}
                        </td>
                        <td class="text-end pe-4 fw-bold" style="color:var(--rs-ink); font-size:.9rem;">
                            {{ number_format($tx->balance_after) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Tidak ada data transaksi poin ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transactions->hasPages())
    <div class="card-footer bg-transparent border-top">
        {{ $transactions->links() }}
    </div>
    @endif
</div>
@endsection
