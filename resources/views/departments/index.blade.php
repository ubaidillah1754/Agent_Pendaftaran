@extends('layouts.app')
@section('title','Data Poli')
@section('page-title','Master Data Poli')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Data Poli</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
    <div>
        <h5 class="mb-1 fw-700" style="color:var(--primary)">Daftar Poli / Departemen</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Kelola data poli pelayanan rumah sakit</p>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="d-flex align-items-center gap-2 px-3 py-2 rounded" style="background:#fff;border:1px solid #e5e7eb;">
            <i class="bi bi-building text-primary"></i>
            <div class="lh-1">
                <div class="small text-muted" style="font-size:.7rem;">Total Poli</div>
                <div class="fw-700">{{ $totalPoli ?? $departments->count() }}</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 px-3 py-2 rounded" style="background:#fff;border:1px solid #e5e7eb;">
            <i class="bi bi-person-badge text-primary"></i>
            <div class="lh-1">
                <div class="small text-muted" style="font-size:.7rem;">Total Dokter</div>
                <div class="fw-700">{{ $totalDokter ?? 0 }}</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 px-3 py-2 rounded" style="background:#fff;border:1px solid #e5e7eb;">
            <i class="bi bi-check-circle text-success"></i>
            <div class="lh-1">
                <div class="small text-muted" style="font-size:.7rem;">Poli Aktif</div>
                <div class="fw-700">{{ $poliAktifCount ?? 0 }}</div>
            </div>
        </div>

        <a href="{{ route('departments.create') }}" class="btn btn-accent">
            <i class="bi bi-plus-circle me-1"></i> Tambah Poli
        </a>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 mb-3">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="small">{{ session('error') }}</div>
    </div>
@endif

<div class="card table-card fade-in">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th width="60">#</th>
                    <th width="90">Kode</th>
                    <th>Nama Poli</th>
                    <th>Deskripsi</th>
                    <th width="80" class="text-center">Dokter</th>
                    <th width="80" class="text-center">Status</th>
                    <th width="120" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $i => $dept)
                <tr>
                    <td class="text-muted" style="font-size:.8rem;">
                        {{ $departments instanceof \Illuminate\Pagination\AbstractPaginator ? $departments->firstItem() + $i : $i + 1 }}
                    </td>
                    <td>
                        <span class="badge fw-700" style="background:var(--primary);color:#fff;font-size:.8rem;letter-spacing:.05em;">
                            {{ $dept->kode_poli }}
                        </span>
                    </td>
                    <td class="fw-600">{{ $dept->nama_poli }}</td>
                    <td class="text-muted" style="font-size:.82rem;max-width:250px;">
                        {{ $dept->deskripsi ? Str::limit($dept->deskripsi, 60, '...') : '-' }}
                    </td>
                    <td class="text-center">
                        <span class="badge" style="background:#eff6ff;color:#1e40af;">{{ $dept->doctors_count }}</span>
                    </td>
                    <td class="text-center">
                        @if($dept->is_active)
                            <span class="badge" style="background:#d1fae5;color:#065f46;">Aktif</span>
                        @else
                            <span class="badge" style="background:#fee2e2;color:#991b1b;">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('departments.edit', $dept) }}" class="btn btn-sm btn-icon" style="background:#eff6ff;color:var(--primary);" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <form action="{{ route('departments.destroy', $dept) }}" method="POST"
                                  onsubmit="return confirm('Yakin hapus poli {{ $dept->nama_poli }}?{{ $dept->doctors_count > 0 ? ' Poli ini memiliki '.$dept->doctors_count.' dokter terdaftar.' : '' }}')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-icon" style="background:#fef2f2;color:#ef4444;" title="Hapus">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size:2.5rem;display:block;margin-bottom:8px;"></i>
                    Belum ada data poli. <a href="{{ route('departments.create') }}">Tambah sekarang</a>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($departments instanceof \Illuminate\Pagination\AbstractPaginator && $departments->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $departments->links() }}
        </div>
    @endif
</div>
@endsection