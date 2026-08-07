@extends('layouts.app')
@section('title','Edit Poli')
@section('page-title','Edit Data Poli')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Data Poli</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card fade-in">
    <div class="card-body">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-circle"
                     style="width:44px;height:44px;background:#e6f7f1;color:#0f9d76;">
                    <i class="bi bi-pencil-square fs-5"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-semibold">Edit Poli — {{ $department->nama_poli }}</h6>
                    <small class="text-muted">Perbarui data poli di bawah ini.</small>
                </div>
            </div>
            @if($department->updated_at)
                <small class="text-muted">
                    Diubah {{ $department->updated_at->diffForHumans() }}
                </small>
            @endif
        </div>

        @if($department->is_active)
        <div class="alert alert-warning d-flex align-items-start gap-2 py-2 px-3 mb-4" id="deactivateWarning" style="display:none !important;">
            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
            <div class="small">
                Menonaktifkan poli ini akan menyembunyikannya dari form pendaftaran pasien.
                Jadwal praktik dan antrian yang sudah ada tidak akan terhapus, tapi poli tidak bisa dipilih untuk pendaftaran baru.
            </div>
        </div>
        @endif

        <form action="{{ route('departments.update', $department) }}" method="POST">
            @csrf @method('PUT')

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
                           value="{{ old('kode_poli', $department->kode_poli) }}"
                           style="text-transform:uppercase" maxlength="10" required>
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
                           value="{{ old('nama_poli', $department->nama_poli) }}" required>
                    @error('nama_poli')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Deskripsi</label>
                <div class="input-group">
                    <span class="input-group-text bg-white align-items-start pt-2">
                        <i class="bi bi-file-text text-success"></i>
                    </span>
                    <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $department->deskripsi) }}</textarea>
                </div>
            </div>

            <div class="mb-4 p-3 rounded d-flex align-items-center justify-content-between"
                 style="background:#e6f7f1;">
                <div class="form-check form-switch mb-0">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                           id="is_active" {{ old('is_active', $department->is_active) ? 'checked' : '' }}
                           data-was-active="{{ $department->is_active ? '1' : '0' }}">
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
                    <i class="bi bi-check2 me-1"></i>Simpan Perubahan
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

const toggle = document.getElementById('is_active');
const warning = document.getElementById('deactivateWarning');
if (toggle && warning) {
    const sync = () => {
        const wasActive = toggle.dataset.wasActive === '1';
        warning.style.setProperty('display', (wasActive && !toggle.checked) ? 'flex' : 'none', 'important');
    };
    toggle.addEventListener('change', sync);
    sync();
}
</script>
@endpush
@endsection