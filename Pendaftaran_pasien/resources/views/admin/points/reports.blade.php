@extends('layouts.app')
@section('title', 'Laporan Rekapitulasi Poin & Reward — Admin')
@section('page-title', 'Laporan Poin & Reward')
@section('page-subtitle', 'Rekapitulasi performa input pasien karyawan, mutasi buku besar poin, dan analitik penukaran hadiah.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Laporan Poin</li>
@endsection

@section('content')
<!-- Filter Utama Laporan -->
<div class="card mb-4 fade-in">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.reports.index') }}">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Filter Karyawan</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Semua Karyawan</option>
                        @foreach($petugasList as $p)
                            <option value="{{ $p->id }}" {{ $userId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ $dari }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ $sampai }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm text-white flex-fill" style="background:var(--rs-primary); border-radius:8px; font-weight:600;">
                        <i class="bi bi-filter me-1"></i>Terapkan Filter
                    </button>
                    <a href="{{ route('admin.reports.index', ['tab' => $tab]) }}" class="btn btn-sm btn-light border flex-fill" style="border-radius:8px;">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabs Navigasi Laporan -->
<div class="fade-in mb-3">
    <ul class="nav nav-pills gap-2 p-1 rounded-3" style="background:#E2E8F0; width:fit-content;">
        <li class="nav-item">
            <a class="nav-link py-2 px-3 fw-bold {{ $tab === 'karyawan' ? 'active text-white' : 'text-secondary' }}"
               style="{{ $tab === 'karyawan' ? 'background:var(--rs-primary); border-radius:8px;' : '' }}"
               href="{{ route('admin.reports.index', array_merge(request()->all(), ['tab' => 'karyawan'])) }}">
                <i class="bi bi-people-fill me-1"></i>Laporan Karyawan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link py-2 px-3 fw-bold {{ $tab === 'ledger' ? 'active text-white' : 'text-secondary' }}"
               style="{{ $tab === 'ledger' ? 'background:var(--rs-primary); border-radius:8px;' : '' }}"
               href="{{ route('admin.reports.index', array_merge(request()->all(), ['tab' => 'ledger'])) }}">
                <i class="bi bi-journal-check me-1"></i>Mutasi Buku Besar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link py-2 px-3 fw-bold {{ $tab === 'redemption' ? 'active text-white' : 'text-secondary' }}"
               style="{{ $tab === 'redemption' ? 'background:var(--rs-primary); border-radius:8px;' : '' }}"
               href="{{ route('admin.reports.index', array_merge(request()->all(), ['tab' => 'redemption'])) }}">
                <i class="bi bi-gift-fill me-1"></i>Laporan Penukaran
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link py-2 px-3 fw-bold {{ $tab === 'merchandise' ? 'active text-white' : 'text-secondary' }}"
               style="{{ $tab === 'merchandise' ? 'background:var(--rs-primary); border-radius:8px;' : '' }}"
               href="{{ route('admin.reports.index', array_merge(request()->all(), ['tab' => 'merchandise'])) }}">
                <i class="bi bi-box-seam me-1"></i>Stok &amp; Penggunaan
            </a>
        </li>
    </ul>
</div>

<!-- Tab Content -->
<div class="fade-in">
    @if($tab === 'karyawan')
    <!-- 1. TAB LAPORAN KARYAWAN -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="rs-card-title"><i class="bi bi-trophy-fill text-warning"></i>Peringkat &amp; Ringkasan Poin Karyawan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table rs-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Nama Karyawan</th>
                            <th>Email</th>
                            <th class="text-center">Pasien Baru Diinput</th>
                            <th class="text-end">Total Poin Diperoleh</th>
                            <th class="text-end">Total Poin Digunakan</th>
                            <th class="text-end pe-4">Saldo Poin Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($karyawanReport as $i => $k)
                        <tr>
                            <td class="ps-4">
                                @if($i === 0) 🥇
                                @elseif($i === 1) 🥈
                                @elseif($i === 2) 🥉
                                @else {{ $i + 1 }}
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold" style="color:var(--rs-ink);">{{ $k->name }}</div>
                            </td>
                            <td style="color:var(--rs-muted); font-size:.84rem;">{{ $k->email }}</td>
                            <td class="text-center fw-bold" style="color:var(--rs-info);">
                                {{ number_format($k->total_pasien_diinput) }} Pasien
                            </td>
                            <td class="text-end fw-bold" style="color:#0F7B63;">
                                +{{ number_format($k->total_earned ?? 0) }} Poin
                            </td>
                            <td class="text-end fw-bold" style="color:#B54545;">
                                -{{ number_format(abs($k->total_redeemed ?? 0)) }} Poin
                            </td>
                            <td class="text-end pe-4">
                                <span class="badge" style="background:var(--rs-primary-soft); color:var(--rs-primary-dark); font-size:.85rem; padding:6px 12px;">
                                    {{ number_format($k->point_balance) }} Poin
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Tidak ada data karyawan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @elseif($tab === 'ledger')
    <!-- 2. TAB MUTASI BUKU BESAR -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card" style="border-left: 4px solid #10B981;">
                <div>
                    <div class="stat-label">Total Poin Masuk (Earn)</div>
                    <div class="stat-value" style="color:#0F7B63;">+{{ number_format($ledgerStats['total_earn']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="border-left: 4px solid #F59E0B;">
                <div>
                    <div class="stat-label">Total Poin Ditukar (Redeem)</div>
                    <div class="stat-value" style="color:#B45309;">-{{ number_format($ledgerStats['total_redeem']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="border-left: 4px solid #3B82F6;">
                <div>
                    <div class="stat-label">Total Penyesuaian (Adj)</div>
                    <div class="stat-value" style="color:#1D4ED8;">{{ number_format($ledgerStats['total_adjustment']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="border-left: 4px solid #64748B;">
                <div>
                    <div class="stat-label">Total Pengembalian (Rev)</div>
                    <div class="stat-value" style="color:#475569;">+{{ number_format($ledgerStats['total_reversal']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="rs-card-title"><i class="bi bi-journal-text"></i>Riwayat Transaksi Poin Lengkap</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table rs-table mb-0">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Kode Ref</th>
                            <th>Karyawan</th>
                            <th>Tipe</th>
                            <th>Keterangan</th>
                            <th class="text-end">Sebelum</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-end pe-4">Sesudah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ledgerTransactions as $tx)
                        <tr>
                            <td style="white-space:nowrap; font-size:.82rem;">{{ $tx->created_at->format('d M Y, H:i') }}</td>
                            <td><code>{{ $tx->reference }}</code></td>
                            <td class="fw-bold">{{ $tx->user->name }}</td>
                            <td><span class="badge bg-{{ $tx->type_badge }}">{{ $tx->type_label }}</span></td>
                            <td style="font-size:.84rem; max-width:260px;">{{ $tx->description }}</td>
                            <td class="text-end text-muted">{{ number_format($tx->balance_before) }}</td>
                            <td class="text-end fw-bold" style="color:{{ $tx->amount > 0 ? '#0F7B63' : '#B54545' }};">
                                {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount) }}
                            </td>
                            <td class="text-end pe-4 fw-bold">{{ number_format($tx->balance_after) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Tidak ada transaksi ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($ledgerTransactions->hasPages())
        <div class="card-footer bg-transparent border-top">
            {{ $ledgerTransactions->links() }}
        </div>
        @endif
    </div>

    @elseif($tab === 'redemption')
    <!-- 3. TAB LAPORAN PENUKARAN -->
    <div class="card">
        <div class="card-header">
            <span class="rs-card-title"><i class="bi bi-gift-fill"></i>Rekapitulasi Penukaran Reward</span>
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
                            <th class="text-end">Poin Dipotong</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Status</th>
                            <th class="pe-4">Diproses Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($redemptionList as $red)
                        <tr>
                            <td><code>{{ $red->reference_code }}</code></td>
                            <td class="fw-bold">{{ $red->user->name }}</td>
                            <td>{{ $red->merchandise_name }}</td>
                            <td class="text-center">{{ $red->quantity }}</td>
                            <td class="text-end fw-bold" style="color:var(--rs-accent);">{{ number_format($red->total_points) }} Poin</td>
                            <td style="white-space:nowrap; font-size:.82rem;">{{ $red->created_at->format('d M Y, H:i') }}</td>
                            <td><span class="badge bg-{{ $red->status_badge }}">{{ $red->status_label }}</span></td>
                            <td class="pe-4 font-size:.82rem;">{{ $red->approver?->name ?: '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Tidak ada data penukaran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($redemptionList->hasPages())
        <div class="card-footer bg-transparent border-top">
            {{ $redemptionList->links() }}
        </div>
        @endif
    </div>

    @elseif($tab === 'merchandise')
    <!-- 4. TAB LAPORAN STOK & PENGGUNAAN -->
    <div class="card">
        <div class="card-header">
            <span class="rs-card-title"><i class="bi bi-box-seam"></i>Rekapitulasi Stok Merchandise &amp; Poin Terpakai</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table rs-table mb-0">
                    <thead>
                        <tr>
                            <th>Nama Merchandise</th>
                            <th class="text-end">Harga Poin</th>
                            <th class="text-center">Sisa Stok</th>
                            <th class="text-center">Total Ditukar</th>
                            <th class="text-end">Total Poin Digunakan</th>
                            <th class="pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($merchandiseReport as $item)
                        <tr>
                            <td>
                                <div class="fw-bold" style="color:var(--rs-ink);">{{ $item->name }}</div>
                            </td>
                            <td class="text-end fw-bold" style="color:var(--rs-accent);">{{ number_format($item->points_required) }} Poin</td>
                            <td class="text-center">
                                <span class="badge {{ $item->stock > 5 ? 'bg-success' : 'bg-warning text-dark' }}">{{ $item->stock }} unit</span>
                            </td>
                            <td class="text-center fw-bold">{{ number_format($item->total_redeemed_count) }} unit</td>
                            <td class="text-end fw-bold" style="color:var(--rs-primary);">{{ number_format($item->total_points_used ?? 0) }} Poin</td>
                            <td class="pe-4">
                                @if($item->trashed())
                                    <span class="badge bg-danger">Dihapus</span>
                                @elseif($item->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada data merchandise.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
