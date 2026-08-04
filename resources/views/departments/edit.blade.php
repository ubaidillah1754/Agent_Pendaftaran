@extends('layouts.app')
@section('title','Edit Poli')
@section('page-title','Edit Data Poli')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Data Poli</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card fade-in">
    <div class="card-header"><i class="bi bi-pencil-square me-2"></i>Edit Poli — {{ $department->nama_poli }}</div>
    <div class="card-body">
        <form action="{{ route('departments.update', $department) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Kode Poli <span class="text-danger">*</span></label>
                <input type="text" name="kode_poli" class="form-control @error('kode_poli') is-invalid @enderror"
                       value="{{ old('kode_poli', $department->kode_poli) }}" style="text-transform:uppercase" required>
                @error('kode_poli')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Poli <span class="text-danger">*</span></label>
                <input type="text" name="nama_poli" class="form-control @error('nama_poli') is-invalid @enderror"
                       value="{{ old('nama_poli', $department->nama_poli) }}" required>
                @error('nama_poli')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $department->deskripsi) }}</textarea>
            </div>
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                           {{ $department->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Poli Aktif</label>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('departments.index') }}" class="btn" style="background:var(--bg);color:#64748b;">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
