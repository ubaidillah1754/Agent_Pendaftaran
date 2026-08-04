@extends('layouts.app')
@section('title','Data Poli')
@section('page-title','Master Data Poli')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Data Poli</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 fw-700" style="color:var(--primary)">Daftar Poli / Departemen</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Kelola data poli pelayanan rumah sakit</p>
    </div>
    <a href="{{ route('departments.create') }}" class="btn btn-accent">
        <i class="bi bi-plus-circle me-1"></i> Tambah Poli
    </a>
</div>

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
                    <td class="text-muted" style="font-size:.8rem;">{{ $i + 1 }}</td>
                    <td>
                        <span class="badge fw-700" style="background:var(--primary);color:#fff;font-size:.8rem;letter-spacing:.05em;">
                            {{ $dept->kode_poli }}
                        </span>
                    </td>
                    <td class="fw-600">{{ $dept->nama_poli }}</td>
                    <td class="text-muted" style="font-size:.82rem;max-width:250px;">
                        {{ Str::limit($dept->deskripsi, 60, '...') ?? '-' }}
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
                            <form action="{{ route('departments.destroy', $dept) }}" method="POST" onsubmit="return confirm('Yakin hapus poli {{ $dept->nama_poli }}?')">
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
</div>
@endsection
