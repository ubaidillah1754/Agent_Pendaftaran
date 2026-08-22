@extends('layouts.app')

@section('page-title', 'Master Data Hadiah')
@section('page-subtitle', 'Kelola daftar hadiah untuk penukaran poin petugas.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Katalog Hadiah</li>
@endsection

@section('content')
<div class="katalog-page">

    {{-- ═══════════ HEADER ═══════════ --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="section-title mb-0"><i class="bi bi-bag-heart me-2"></i>Katalog Merchandise Khusus</h5>
        <button class="btn text-white" style="background:var(--primary); border-radius:8px; font-weight:600;" data-bs-toggle="modal" data-bs-target="#addMerchandiseModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Hadiah
        </button>
    </div>

    {{-- ═══════════ MERCHANDISE ═══════════ --}}
    <div class="row g-4 mt-1">
        
        @forelse($merchandises as $item)
        <div class="col-md-6 col-lg-3">
            <div class="hadiah-card">
                <div class="img-wrapper">
                    <img src="{{ asset('images/merchandise/' . $item->image) }}" alt="{{ $item->name }}">
                    <div class="badge-poin"><i class="bi bi-star-fill text-warning me-1"></i> {{ $item->points }} Poin</div>
                </div>
                <div class="hadiah-body d-flex flex-column h-100">
                    <h6 class="hadiah-title">{{ $item->name }}</h6>
                    <p class="hadiah-desc flex-grow-1">{{ $item->description }}</p>
                    
                    <form action="{{ route('points.merchandise.destroy', $item) }}" method="POST" class="mt-3" onsubmit="return confirm('Apakah Anda yakin ingin menghapus hadiah ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100 text-white" style="border-radius:8px; font-weight:600; background:#B54545;">
                            <i class="bi bi-trash3 me-1"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-inbox d-block mb-3" style="font-size: 3rem; color: #CBD5E1;"></i>
            <p class="text-muted">Belum ada merchandise yang ditambahkan ke katalog.</p>
        </div>
        @endforelse

    </div>

</div>

<!-- Modal Tambah Merchandise -->
<div class="modal fade" id="addMerchandiseModal" tabindex="-1" aria-labelledby="addMerchandiseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--rs-radius); border: none;">
            <form action="{{ route('points.merchandise.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header" style="border-bottom: 1px solid var(--border);">
                    <h5 class="modal-title fw-bold" id="addMerchandiseModalLabel">
                        <i class="bi bi-plus-circle me-2" style="color:var(--primary);"></i> Tambah Hadiah Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: .85rem; color:var(--ink);">Nama Hadiah <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Contoh: Payung Cantik" style="border-radius:8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: .85rem; color:var(--ink);">Nilai Poin <span class="text-danger">*</span></label>
                        <input type="number" name="points" class="form-control" min="1" required placeholder="Contoh: 150" style="border-radius:8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: .85rem; color:var(--ink);">Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="Deskripsi singkat hadiah..." style="border-radius:8px;"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: .85rem; color:var(--ink);">Gambar Hadiah <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control" accept="image/*" required style="border-radius:8px;">
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border);">
                    <button type="button" class="btn text-muted" style="background:var(--bg); border-radius:8px;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary text-white" style="background:var(--primary); border-radius:8px; font-weight:600;">Simpan Hadiah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .katalog-page { font-family: 'Inter', 'Plus Jakarta Sans', sans-serif; }

    /* Section Title */
    .section-title {
        font-weight: 800;
        color: #0F172A;
        font-size: 1.1rem;
    }

    /* Cards */
    .hadiah-card {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .hadiah-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px -10px rgba(15, 123, 99, 0.15);
        border-color: #0F7B63;
    }
    .img-wrapper {
        position: relative;
        height: 220px;
        background: #F8FAFC;
        overflow: hidden;
    }
    .img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .badge-poin {
        position: absolute;
        top: 12px; right: 12px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(4px);
        padding: 6px 12px;
        border-radius: 999px;
        font-weight: 800;
        font-size: 0.85rem;
        color: #0F7B63;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .hadiah-body {
        padding: 20px;
    }
    .hadiah-title {
        font-weight: 700;
        font-size: 1rem;
        color: #0F172A;
        margin-bottom: 8px;
    }
    .hadiah-desc {
        font-size: 0.85rem;
        color: #64748B;
        line-height: 1.5;
    }
</style>
@endsection
