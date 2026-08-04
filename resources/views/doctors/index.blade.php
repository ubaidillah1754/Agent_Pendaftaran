@extends('layouts.app')
@section('title','Data Dokter')
@section('page-title','Master Data Dokter')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Data Dokter</li>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 fw-700" style="color:var(--primary)">Daftar Dokter</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Kelola data dokter dan spesialisasi</p>
    </div>
    <a href="{{ route('doctors.create') }}" class="btn btn-accent">
        <i class="bi bi-person-plus me-1"></i> Tambah Dokter
    </a>
</div>
<div class="card table-card fade-in">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Dokter</th>
                    <th>NIP</th>
                    <th>Poli Utama</th>
                    <th>Spesialisasi</th>
                    <th>No. Telepon</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($doctors as $i => $doctor)
                <tr>
                    <td class="text-muted" style="font-size:.8rem;">{{ $i+1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;border-radius:10px;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0;">
                                {{ strtoupper(substr($doctor->nama_dokter,0,1)) }}
                            </div>
                            <span class="fw-600" style="font-size:.875rem;">{{ $doctor->nama_dokter }}</span>
                        </div>
                    </td>
                    <td style="font-size:.82rem;color:#64748b;">{{ $doctor->nip ?? '-' }}</td>
                    <td>
                        <span class="badge" style="background:var(--primary);color:#fff;">{{ $doctor->department->kode_poli }}</span>
                        <span style="font-size:.82rem;"> {{ $doctor->department->nama_poli }}</span>
                    </td>
                    <td style="font-size:.82rem;">{{ $doctor->spesialisasi ?? '-' }}</td>
                    <td style="font-size:.82rem;">{{ $doctor->no_telepon ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge" style="background:{{ $doctor->is_active ? '#d1fae5' : '#fee2e2' }};color:{{ $doctor->is_active ? '#065f46' : '#991b1b' }};">
                            {{ $doctor->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-sm btn-icon" style="background:#eff6ff;color:var(--primary);" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <form action="{{ route('doctors.destroy', $doctor) }}" method="POST" onsubmit="return confirm('Hapus dokter ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-icon" style="background:#fef2f2;color:#ef4444;" title="Hapus">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-5 text-muted">
                    <i class="bi bi-person-x" style="font-size:2.5rem;display:block;margin-bottom:8px;"></i>
                    Belum ada data dokter. <a href="{{ route('doctors.create') }}">Tambah sekarang</a>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
