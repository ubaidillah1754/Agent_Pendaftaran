@extends('layouts.app')
@section('title','Tambah Poli')
@section('page-title','Tambah Poli Baru')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Data Poli</a></li>
    <li class="breadcrumb-item active">Tambah Poli</li>
@endsection
@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card fade-in">
    <div class="card-body">

        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="d-flex align-items-center justify-content-center rounded-circle"
                 style="width:44px;height:44px;background:#e6f7f1;color:#0f9d76;">
                <i class="bi bi-person-badge fs-5"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-semibold">Form Tambah Poli</h6>
                <small class="text-muted">Lengkapi data poli untuk menambahkan poli baru ke sistem.</small>
            </div>
        </div>

        <form action="{{ route('departments.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">
                    Kode Poli <span class="text-danger">*</span>
                    <i class="bi bi-info-circle text-muted" data-bs-toggle="tooltip"
                       title="Kode unik untuk poli ini"></i>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-shield-check text-success"></i></span>
                    <input type="text" name="kode_poli" id="kode_poli"
                           class="form-control @error('kode_poli') is-invalid @enderror"
                           placeholder="Contoh: PU, GG, AN" maxlength="10"
                           style="text-transform:uppercase"
                           value="{{ old('kode_poli') }}" required>
                    @error('kode_poli')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-text">Huruf kapital & angka saja, max 10 karakter. Digunakan sebagai prefix nomor antrian.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Nama Poli <span class="text-danger">*</span>
                    <i class="bi bi-info-circle text-muted" data-bs-toggle="tooltip"
                       title="Nama lengkap poli"></i>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-building text-success"></i></span>
                    <input type="text" name="nama_poli"
                           class="form-control @error('nama_poli') is-invalid @enderror"
                           placeholder="Contoh: Poli Umum" value="{{ old('nama_poli') }}" required>
                    @error('nama_poli')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Deskripsi</label>
                <div class="input-group">
                    <span class="input-group-text bg-white align-items-start pt-2">
                        <i class="bi bi-file-text text-success"></i>
                    </span>
                    <textarea name="deskripsi" class="form-control" rows="3"
                              placeholder="Deskripsi singkat poli ini">{{ old('deskripsi') }}</textarea>
                </div>
            </div>

            <div class="mb-4 p-3 rounded d-flex align-items-center justify-content-between"
                 style="background:#e6f7f1;">
                <div class="form-check form-switch mb-0">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                           id="is_active" checked>
                    <label class="form-check-label fw-semibold" for="is_active">Poli Aktif</label>
                    <div class="small text-muted">Poli akan ditampilkan dan bisa digunakan untuk pendaftaran pasien.</div>
                </div>
                <i class="bi bi-shield-check fs-5 text-success"></i>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('departments.index') }}" class="btn"
                   style="background:var(--bg);color:#64748b;">
                    <i class="bi bi-x-lg me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2 me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
</div>
</div>

@push('scripts')
<script>
document.getElementById('kode_poli')?.addEventListener('input', function (e) {
    e.target.value = e.target.value.toUpperCase();
});
</script>
@endpush
@endsection