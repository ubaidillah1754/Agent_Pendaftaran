@extends('layouts.app')
@section('title', 'Daftar Pendaftaran')
@section('page-title', 'Daftar Pendaftaran')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Pendaftaran</li>
@endsection

@push('styles')
<style>
    .stat-card-custom {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stat-icon-custom {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .stat-icon-green { background: #d1fae5; color: #059669; }
    .stat-icon-yellow { background: #fef3c7; color: #d97706; }

    .stat-info .stat-val {
        font-size: 1.35rem;
        font-weight: 700;
        color: #111827;
        line-height: 1.2;
    }

    .stat-info .stat-label {
        font-size: 0.75rem;
        color: #4b5563;
        font-weight: 600;
    }

    .filter-wrapper {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        gap: 10px;
        flex: 1;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-group .form-control,
    .filter-group .form-select,
    .filter-group .btn {
        font-size: 0.8rem;
        padding: 6px 12px;
    }

    .search-box {
        position: relative;
        width: 260px;
    }

    .search-box input {
        width: 100%;
        padding-left: 32px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding-top: 6px;
        padding-bottom: 6px;
        font-size: 0.8rem;
    }

    .search-box i {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 0.8rem;
    }

    .custom-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .custom-card-header {
        padding: 14px 20px;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 700;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 8px;
        background: #fdfdfd;
    }

    .table-container { padding: 0; overflow-x: auto; }

    .table-custom {
        width: 100%;
        min-width: 750px;
        border-collapse: collapse;
    }

    .table-custom th {
        background: #f9fafb;
        font-size: 0.72rem;
        text-transform: uppercase;
        color: #6b7280;
        padding: 12px 16px;
        text-align: left;
        font-weight: 700;
        border-bottom: 1px solid #e5e7eb;
    }

    .table-custom td {
        padding: 12px 16px;
        font-size: 0.85rem;
        color: #374151;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }

    .table-custom tr:hover { background: #f0fdf4; }

    .btn-action-sm {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid transparent;
        text-decoration: none;
        transition: all 0.2s;
        background: none;
        cursor: pointer;
    }

    .btn-action-view   { background: #eff6ff; color: #3b82f6; border-color: #bfdbfe; }
    .btn-action-print  { background: #f0fdf4; color: #059669; border-color: #a7f3d0; }
    .btn-action-delete { background: #fef2f2; color: #ef4444; border-color: #fecaca; }
    .btn-action-sm:hover { filter: brightness(0.92); }
</style>
@endpush

@section('content')

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4 fade-in">
        <div class="col-md-6">
            <div class="stat-card-custom">
                <div class="stat-icon-custom stat-icon-green">
                    <i class="bi bi-clipboard2-check-fill"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-val">{{ $totalPendaftaran }}</div>
                    <div class="stat-label">Pendaftaran Terdaftar Hari Ini</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card-custom">
                <div class="stat-icon-custom stat-icon-yellow">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-val">{{ $departments->count() }}</div>
                    <div class="stat-label">Poli Tersedia</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Form --}}
    <form action="{{ route('registrations.index') }}" method="GET" class="fade-in">
        <div class="filter-wrapper">
            <div class="filter-group">
                <input type="date" name="tanggal" class="form-control" style="width: 160px;"
                    title="Filter berdasarkan Tanggal Kunjungan"
                    value="{{ request('tanggal', date('Y-m-d')) }}">

                <select name="department_id" class="form-select" style="width: 200px;">
                    <option value="">Semua Poli</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->nama_poli }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-success" style="background-color: #059669; border-color: #059669;">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('registrations.index') }}" class="btn btn-light" style="border: 1px solid #d1d5db;">Reset</a>
            </div>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" name="search" placeholder="Cari nama, RM, booking..." value="{{ request('search') }}">
            </div>
        </div>
    </form>

    {{-- Tabel Pendaftaran --}}
    <div class="custom-card fade-in">
        <div class="custom-card-header text-success" style="background-color: #f0fdf4;">
            <i class="bi bi-card-list"></i> DATA PENDAFTARAN RAWAT JALAN
        </div>
        <div class="table-container">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nomor Antrean</th>
                        <th>Kode Booking</th>
                        <th>Nama Pasien</th>
                        <th>No. RM</th>
                        <th>Poli Tujuan</th>
                        <th>Dokter</th>
                        <th>Tgl Kunjungan</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrations as $i => $reg)
                        <tr>
                            <td>{{ $i + 1 + ($registrations->currentPage() - 1) * $registrations->perPage() }}</td>
                            <td>
                                <span class="badge bg-success px-3 py-2" style="border-radius: 8px; font-size: 0.85rem; letter-spacing: 0.5px;">
                                    {{ $reg->nomor_antrian }}
                                </span>
                            </td>
                            <td><code class="fw-bold text-dark">{{ $reg->kode_booking }}</code></td>
                            <td class="fw-bold">{{ $reg->patient->nama_pasien }}</td>
                            <td><span style="font-family: monospace; font-size: 0.8rem;">{{ $reg->patient->no_rm }}</span></td>
                            <td>{{ $reg->department->nama_poli }}</td>
                            <td>{{ $reg->doctor->nama_dokter }}</td>
                            <td>{{ $reg->tanggal_kunjungan ? $reg->tanggal_kunjungan->format('d/m/Y') : '-' }}</td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('registrations.show', $reg) }}" class="btn-action-sm btn-action-view" title="Detail">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('registrations.cetak', $reg) }}" target="_blank" class="btn-action-sm btn-action-print" title="Cetak Tracer">
                                        <i class="bi bi-printer-fill"></i>
                                    </a>
                                    <form action="{{ route('registrations.destroy', $reg) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data pendaftaran ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action-sm btn-action-delete" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                Data pendaftaran tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top">
            {{ $registrations->links('pagination::bootstrap-5') }}
        </div>
    </div>

@endsection