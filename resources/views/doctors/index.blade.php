@extends('layouts.app')
@section('title','Data Dokter')
@section('page-title','Master Data Dokter')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Data Dokter</li>
@endsection
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h5 class="mb-1 fw-700" style="color:var(--primary)">Daftar Dokter</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Kelola data dokter dan spesialisasi</p>
    </div>

    <div class="d-flex align-items-center gap-3 flex-wrap">
        {{-- Stat: Total Dokter --}}
        <div class="stat-pill d-flex align-items-center gap-2">
            <div class="stat-icon" style="background:#eff6ff;color:var(--primary);">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.72rem;">Total Dokter</div>
                <div class="fw-700" style="font-size:1rem;">{{ $doctors->count() }} <span class="fw-400 text-muted" style="font-size:.72rem;">Dokter</span></div>
            </div>
        </div>

        {{-- Stat: Dokter Aktif --}}
        <div class="stat-pill d-flex align-items-center gap-2">
            <div class="stat-icon" style="background:#e0f2fe;color:#0284c7;">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.72rem;">Dokter Aktif</div>
                <div class="fw-700" style="font-size:1rem;">{{ $activeCount ?? $doctors->where('is_active', true)->count() }} <span class="fw-400 text-muted" style="font-size:.72rem;">Dokter</span></div>
            </div>
        </div>

        {{-- Stat: Spesialisasi --}}
        <div class="stat-pill d-flex align-items-center gap-2">
            <div class="stat-icon" style="background:#f3e8ff;color:#9333ea;">
                <i class="bi bi-heart-pulse-fill"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.72rem;">Spesialisasi</div>
                <div class="fw-700" style="font-size:1rem;">{{ $specialtyCount ?? $doctors->pluck('spesialisasi')->filter()->unique()->count() }} <span class="fw-400 text-muted" style="font-size:.72rem;">Jenis</span></div>
            </div>
        </div>

        <a href="{{ route('doctors.create') }}" class="btn btn-accent">
            <i class="bi bi-person-plus me-1"></i> Tambah Dokter
        </a>
    </div>
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
                @forelse($doctors as $doctor)
                <tr>
                    <td class="text-muted" style="font-size:.8rem;">
                        {{ $doctors instanceof \Illuminate\Pagination\AbstractPaginator ? $loop->iteration + ($doctors->currentPage()-1)*$doctors->perPage() : $loop->iteration }}
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if(!empty($doctor->foto))
                                <img src="{{ asset('storage/'.$doctor->foto) }}" alt="{{ $doctor->nama_dokter }}"
                                     style="width:36px;height:36px;border-radius:10px;object-fit:cover;flex-shrink:0;">
                            @else
                                <div style="width:36px;height:36px;border-radius:10px;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0;">
                                    {{ strtoupper(substr($doctor->nama_dokter,0,1)) }}
                                </div>
                            @endif
                            <span class="fw-600" style="font-size:.875rem;">{{ $doctor->nama_dokter }}</span>
                        </div>
                    </td>
                    <td style="font-size:.82rem;color:#64748b;">{{ $doctor->nip ?? '-' }}</td>
                    <td>
                        <span class="badge" style="background:var(--primary);color:#fff;">{{ $doctor->department->kode_poli ?? '-' }}</span>
                        <span style="font-size:.82rem;"> {{ $doctor->department->nama_poli ?? '-' }}</span>
                    </td>
                    <td style="font-size:.82rem;">{{ $doctor->spesialisasi ?? '-' }}</td>
                    <td style="font-size:.82rem;">{{ $doctor->no_telepon ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge" style="background:{{ $doctor->is_active ? '#d1fae5' : '#fee2e2' }};color:{{ $doctor->is_active ? '#065f46' : '#991b1b' }};">
                            <i class="bi {{ $doctor->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
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

    @if($doctors instanceof \Illuminate\Pagination\AbstractPaginator && $doctors->hasPages())
    <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top">
        <span class="text-muted" style="font-size:.8rem;">
            Menampilkan {{ $doctors->firstItem() }} - {{ $doctors->lastItem() }} dari {{ $doctors->total() }} dokter
        </span>
        <div>
            {{ $doctors->onEachSide(1)->links() }}
        </div>
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
.stat-pill{
    background:#fff;
    border:1px solid #e2e8f0;
    border-radius:12px;
    padding:8px 14px;
}
.stat-icon{
    width:34px;height:34px;border-radius:9px;
    display:flex;align-items:center;justify-content:center;
    font-size:1rem;
}
</style>
@endpush