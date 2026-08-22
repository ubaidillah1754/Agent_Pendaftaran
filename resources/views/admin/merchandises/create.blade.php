@extends('layouts.app')
@section('title', 'Tambah Hadiah / Merchandise — Admin')
@section('page-title', 'Tambah Hadiah Baru')
@section('page-subtitle', 'Tambahkan item reward baru ke dalam katalog merchandise.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.merchandises.index') }}">Master Hadiah</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center fade-in">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <span class="rs-card-title"><i class="bi bi-plus-circle-fill"></i>Formulir Merchandise Baru</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.merchandises.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Nama Merchandise / Hadiah <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Tumbler Eksklusif My Sakinah" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Poin yang Dibutuhkan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="points_required" class="form-control @error('points_required') is-invalid @enderror" value="{{ old('points_required', 100) }}" min="1" required>
                                <span class="input-group-text">Poin</span>
                            </div>
                            @error('points_required') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jumlah Stok Awal <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 20) }}" min="0" required>
                                <span class="input-group-text">Unit</span>
                            </div>
                            @error('stock') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status Ketersediaan <span class="text-danger">*</span></label>
                            <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif (Tersedia untuk ditukar)</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif (Disembunyikan dari katalog)</option>
                            </select>
                            @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Foto / Gambar Produk</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            <div class="form-text" style="font-size:.72rem;">Format: JPG, PNG, WEBP. Maks 2MB.</div>
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Deskripsi / Spesifikasi</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Jelaskan detail hadiah, spesifikasi ukuran/bahan, dan syarat pengambilan...">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 mt-4 pt-2 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.merchandises.index') }}" class="btn btn-light border px-4" style="border-radius:8px;">Batal</a>
                            <button type="submit" class="btn text-white px-4" style="background:var(--rs-primary); border-radius:8px; font-weight:700;">
                                <i class="bi bi-save me-1"></i>Simpan Merchandise
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
