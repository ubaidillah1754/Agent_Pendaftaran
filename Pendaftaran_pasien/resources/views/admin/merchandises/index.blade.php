@extends('layouts.app')
@section('title', 'Master Hadiah / Merchandise — Admin')
@section('page-title', 'Master Hadiah / Merchandise')
@section('page-subtitle', 'Kelola daftar katalog hadiah, stok, poin yang dibutuhkan, dan status ketersediaan.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Master Hadiah</li>
@endsection

@section('content')
<div class="card mb-4 fade-in">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.merchandises.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Pencarian</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Cari nama atau deskripsi hadiah...">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:.75rem; font-weight:700;">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm text-white flex-fill" style="background:var(--rs-primary); border-radius:8px; font-weight:600;">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.merchandises.index') }}" class="btn btn-sm btn-light border flex-fill" style="border-radius:8px;">Reset</a>
                </div>
                <div class="col-md-2 text-md-end">
                    <a href="{{ route('admin.merchandises.create') }}" class="btn btn-sm text-white w-100" style="background:var(--rs-primary); border-radius:8px; font-weight:700;">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Hadiah
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card fade-in">
    <div class="card-header">
        <span class="rs-card-title"><i class="bi bi-box-seam"></i>Daftar Merchandise &amp; Hadiah</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table rs-table mb-0">
                <thead>
                    <tr>
                        <th style="width:60px;">Foto</th>
                        <th>Nama Merchandise</th>
                        <th class="text-end">Poin Dibutuhkan</th>
                        <th class="text-center">Sisa Stok</th>
                        <th>Status</th>
                        <th>Deskripsi</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($merchandises as $item)
                    <tr>
                        <td>
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" style="width:48px; height:48px; object-fit:cover; border-radius:8px; border:1px solid #E2E8F0;">
                        </td>
                        <td>
                            <div class="fw-bold" style="color:var(--rs-ink); font-size:.9rem;">{{ $item->name }}</div>
                        </td>
                        <td class="text-end fw-bold" style="color:var(--rs-accent); font-size:.92rem;">
                            {{ number_format($item->points_required) }} Poin
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $item->stock > 5 ? 'bg-success' : ($item->stock > 0 ? 'bg-warning text-dark' : 'bg-danger') }}" style="font-size:.78rem;">
                                {{ $item->stock }} unit
                            </span>
                        </td>
                        <td>
                            @if($item->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td style="font-size:.8rem; max-width:220px; color:var(--rs-muted);" class="text-truncate">
                            {{ $item->description ?: '-' }}
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('admin.merchandises.edit', $item) }}" class="btn btn-sm btn-light border" style="border-radius:6px;" title="Edit Hadiah">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.merchandises.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus merchandise ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius:6px;" title="Hapus Hadiah">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Belum ada merchandise terdaftar. Silakan klik tombol <strong>Tambah Hadiah</strong> untuk membuat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($merchandises->hasPages())
    <div class="card-footer bg-transparent border-top">
        {{ $merchandises->links() }}
    </div>
    @endif
</div>
@endsection
