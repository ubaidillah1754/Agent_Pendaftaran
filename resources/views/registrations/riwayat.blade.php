@extends('layouts.app')
@section('title', 'Riwayat Pendaftaran Saya')
@section('page-title', 'Riwayat Pendaftaran')
@section('page-subtitle', 'Daftar pendaftaran pasien yang Anda proses')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Riwayat Pendaftaran Saya</li>
@endsection

@push('styles')
<style>
    .stat-mini {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .stat-mini-icon {
        width: 40px; height: 40px;
        border-radius: var(--arch-lg);
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
    }
    .stat-mini-value {
        font-family: 'Spectral', serif;
        font-size: 1.5rem; font-weight: 800;
        color: var(--ink); line-height: 1.1;
    }
    .stat-mini-label {
        font-size: .7rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .04em;
        color: var(--muted);
    }
    .filter-bar {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--card-radius);
        padding: 14px 18px;
    }
</style>
@endpush

@section('content')

{{-- ═══════════ STAT MINI ═══════════ --}}
<div class="row g-3 mb-4">
    <div class="col-md-4 col-6">
        <div class="stat-mini fade-in">
            <div class="stat-mini-icon" style="background:var(--primary-soft); color:var(--primary);">
                <i class="bi bi-clipboard2-check" aria-hidden="true"></i>
            </div>
            <div>
                <div class="stat-mini-value">{{ $totalPendaftaran }}</div>
                <div class="stat-mini-label">Total Pendaftaran</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="stat-mini fade-in fade-in-delay-1">
            <div class="stat-mini-icon" style="background:var(--accent-soft); color:var(--accent);">
                <i class="bi bi-star-fill" aria-hidden="true"></i>
            </div>
            <div>
                <div class="stat-mini-value">{{ $totalPoin }}</div>
                <div class="stat-mini-label">Saldo Poin Saya</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="stat-mini fade-in fade-in-delay-2">
            <div class="stat-mini-icon" style="background:var(--tile-soft); color:var(--tile);">
                <i class="bi bi-person-check" aria-hidden="true"></i>
            </div>
            <div>
                <div class="stat-mini-value">{{ $registrations->total() }}</div>
                <div class="stat-mini-label">Hasil Filter Ini</div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════ FILTER BAR ═══════════ --}}
<div class="filter-bar mb-4 fade-in">
    <form method="GET" action="{{ route('registrations.riwayat') }}" class="row g-3 align-items-end">
        <div class="col-md-4 col-sm-6">
            <label class="form-label text-muted mb-2" style="font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Bulan</label>
            <input type="month" name="bulan" class="form-control"
                   value="{{ request('bulan', today()->format('Y-m')) }}"
                   style="height: 44px; border-radius: 10px; border-color: var(--border);">
        </div>
        <div class="col-md-4 col-sm-6">
            <label class="form-label text-muted mb-2" style="font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Poli</label>
            <select name="department_id" class="form-select" style="height: 44px; border-radius: 10px; border-color: var(--border);">
                <option value="">Semua Poli</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->nama_poli }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 col-sm-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill fw-bold" style="height: 44px; border-radius: 10px; font-size: .85rem;">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <a href="{{ route('registrations.riwayat') }}" class="btn flex-fill fw-bold d-flex align-items-center justify-content-center"
               style="height: 44px; background:var(--bg); color:var(--muted); border:1px solid var(--border); border-radius: 10px; font-size: .85rem; text-decoration: none;">
                Reset
            </a>
        </div>
    </form>
</div>

{{-- ═══════════ TABEL RIWAYAT ═══════════ --}}
<div class="table-card fade-in">
    <div class="card-header d-flex align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-clock-history" style="color:var(--primary);" aria-hidden="true"></i>
            <span class="fw-700">Riwayat Pendaftaran Saya</span>
        </div>
        <span class="badge" style="background:var(--primary-soft); color:var(--primary-dark); font-size:.75rem;">
            {{ $registrations->total() }} data
        </span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width:48px;">#</th>
                    <th>Nomor Antrean</th>
                    <th>Kode Booking</th>
                    <th>Pasien</th>
                    <th>Poli / Dokter</th>
                    <th>Tanggal Daftar</th>
                    <th class="text-center">Poin</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registrations as $reg)
                <tr>
                    <td class="ps-4" style="color:var(--muted); font-size:.82rem;">
                        {{ $registrations->firstItem() + $loop->index }}
                    </td>
                    <td>
                        <span class="badge bg-success fs-6" style="border-radius: 6px;">
                            {{ $reg->nomor_antrian }}
                        </span>
                    </td>
                    <td><code class="fw-bold fs-6 text-dark">{{ $reg->kode_booking }}</code></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:30px; height:30px; border-radius:var(--arch-sm);
                                        background:{{ $reg->patient->jenis_kelamin === 'L' ? 'var(--primary-soft)' : '#FCE7F3' }};
                                        color:{{ $reg->patient->jenis_kelamin === 'L' ? 'var(--primary)' : '#be185d' }};
                                        display:flex; align-items:center; justify-content:center;
                                        font-weight:700; font-size:.75rem; flex-shrink:0;">
                                {{ strtoupper(substr($reg->patient->nama_pasien ?? '-', 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:.875rem; color:var(--ink);">
                                    {{ $reg->patient->nama_pasien ?? '-' }}
                                </div>
                                <div style="font-size:.72rem; color:var(--muted);">
                                    {{ $reg->patient->no_rm ?? '-' }} · {{ $reg->patient->nik ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:.875rem; font-weight:600; color:var(--ink);">
                            {{ $reg->department->nama_poli ?? '-' }}
                        </div>
                        <div style="font-size:.75rem; color:var(--muted);">
                            dr. {{ $reg->doctor->nama_dokter ?? '-' }}
                        </div>
                    </td>
                    <td style="font-size:.85rem; color:var(--ink); white-space:nowrap;">
                        {{ $reg->tanggal_daftar ? $reg->tanggal_daftar->translatedFormat('d M Y') : '-' }}
                        <div style="font-size:.72rem; color:var(--muted);">
                            {{ $reg->created_at->format('H:i') }} WIB
                        </div>
                    </td>
                    <td class="text-center">
                        @if($reg->pointTransaction)
                            <span class="badge"
                                  style="background:var(--primary-soft); color:var(--primary); font-size:.75rem;">
                                +{{ $reg->pointTransaction->amount }} poin
                            </span>
                        @else
                            <span style="color:var(--muted); font-size:.8rem;">—</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('registrations.show', $reg) }}"
                           class="btn btn-sm"
                           style="background:var(--primary-soft); color:var(--primary); border-radius:8px;"
                           title="Lihat detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('registrations.cetak', $reg) }}"
                           target="_blank"
                           class="btn btn-sm"
                           style="background:#f0fdf4; color:#059669; border-radius:8px;"
                           title="Cetak Tracer">
                            <i class="bi bi-printer"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5" style="color:var(--muted);">
                        <i class="bi bi-inbox d-block mb-2" style="font-size:2rem; opacity:.35;" aria-hidden="true"></i>
                        <span style="font-size:.875rem;">
                            Belum ada pendaftaran yang Anda proses.
                        </span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($registrations->hasPages())
    <div class="px-4 py-3 border-top" style="border-color:var(--border) !important;">
        {{ $registrations->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
