@extends('layouts.app')
@section('title','Daftar Pendaftaran')
@section('page-title','Daftar Pendaftaran')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Pendaftaran</li>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="mb-1 fw-700" style="color:var(--primary)"><i class="bi bi-clipboard2-data me-1"></i>Daftar Pendaftaran Rawat Jalan</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Total {{ $registrations->total() }} pendaftaran ditemukan</p>
    </div>
    <a href="{{ route('registrations.create') }}" class="btn btn-accent">
        <i class="bi bi-clipboard2-plus me-1"></i> Pendaftaran Baru
    </a>
</div>

<!-- Filter -->
<div class="card mb-3 fade-in">
    <div class="card-body py-3">
        <form action="{{ route('registrations.index') }}" method="GET">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal', date('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <select name="department_id" class="form-select">
                        <option value="">Semua Poli</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_poli }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach(['menunggu','dipanggil','selesai','batal'] as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-auto">
                    <button class="btn btn-primary" type="submit">Filter</button>
                    <a href="{{ route('registrations.index') }}" class="btn ms-1" style="background:var(--bg);color:#64766D;">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card table-card fade-in">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No. Antrian</th>
                    <th>Pasien</th>
                    <th>Poli</th>
                    <th>Dokter</th>
                    <th>Tgl Kunjungan</th>
                    <th>Pembayaran</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registrations as $reg)
                <tr>
                    <td>
                        <span class="fw-900" style="font-size:1.1rem;color:var(--primary);letter-spacing:.05em;">{{ $reg->nomor_antrian }}</span>
                    </td>
                    <td>
                        <div class="fw-600" style="font-size:.875rem;">{{ $reg->patient->nama_pasien }}</div>
                        <div style="font-size:.72rem;color:#64766D;">{{ $reg->patient->no_rm }}</div>
                    </td>
                    <td style="font-size:.82rem;">{{ $reg->department->nama_poli }}</td>
                    <td style="font-size:.82rem;">{{ $reg->doctor->nama_dokter }}</td>
                    <td style="font-size:.82rem;">{{ $reg->tanggal_daftar->format('d M Y') }}</td>
                    <td>
                        <span class="badge" style="background:#f1efe4;color:#4a4335;">{{ strtoupper($reg->patient->jenis_pembayaran) }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-{{ $reg->status }}">{{ $reg->status_label }}</span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('registrations.show', $reg) }}" class="btn btn-sm btn-icon" style="background:#eaf6f8;color:var(--tile,#0E7490);" title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            @if($reg->status === 'menunggu')
                            <form action="{{ route('registrations.batal', $reg) }}" method="POST" onsubmit="return confirm('Batalkan pendaftaran ini?')">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-icon" style="background:#fef2f2;color:#ef4444;" title="Batal">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-5 text-muted">
                    <i class="bi bi-clipboard-x" style="font-size:2.5rem;display:block;margin-bottom:8px;"></i>
                    Tidak ada pendaftaran untuk filter ini.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($registrations->hasPages())
    <div class="card-body border-top py-3 d-flex justify-content-between align-items-center">
        <small class="text-muted">{{ $registrations->firstItem() }}–{{ $registrations->lastItem() }} dari {{ $registrations->total() }}</small>
        {{ $registrations->links() }}
    </div>
    @endif
</div>
@endsection