@extends('layouts.app')
@section('title','Tambah Poli')
@section('page-title','Tambah Poli Baru')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Data Poli</a></li>
    <li class="breadcrumb-item active">Tambah Poli</li>
@endsection
@section('content')
<style>
    .form-card.premium {
        border-radius: 18px;
        border: 1px solid #eef0f3;
        box-shadow: 0 1px 2px rgba(16,24,32,.03), 0 20px 40px -24px rgba(16,24,32,.16);
    }
    .form-card.premium .card-body { padding: 32px; }

    .icon-ring-lg {
        width: 46px; height: 46px;
        border-radius: 23px 23px 6px 6px;
        display: flex; align-items: center; justify-content: center;
        background: #E9F3EE; color: var(--primary);
        border: 1px solid rgba(201,162,39,.25);
    }

    .form-label.premium { font-size: .82rem; font-weight: 600; color: #142019; margin-bottom: 6px; }
    .form-label.premium .req { color: #DC2626; }
    .form-label.premium i { color: #A2ACB8; font-size: .85rem; cursor: help; }

    .input-group.premium .input-group-text {
        background: #F7F8FA; border-color: #E7EAEF; color: var(--primary);
    }
    .input-group.premium .form-control,
    .input-group.premium .input-group-text { border-radius: 10px; }
    .input-group.premium .input-group-text { border-top-right-radius:0; border-bottom-right-radius:0; }
    .input-group.premium .form-control { border-top-left-radius:0; border-bottom-left-radius:0; }
    .input-group.premium .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(11,107,79,.12);
    }

    .status-toggle-card {
        background: #E9F3EE;
        border: 1px solid rgba(201,162,39,.2);
        border-radius: 14px;
    }
    .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }
    .form-check-input:focus { box-shadow: 0 0 0 3px rgba(11,107,79,.15); }

    .btn-cancel.premium {
        background: #F7F8FA; color: #6B7684; border: 1px solid #E7EAEF; font-weight: 600;
    }
    .btn-cancel.premium:hover { background: #eef0f3; color: #142019; }

    .btn-save.premium {
        background: linear-gradient(135deg, var(--primary), #12885F);
        color: #fff; border: none; font-weight: 700;
        box-shadow: 0 8px 18px -8px rgba(11,107,79,.5);
    }
    .btn-save.premium:hover { color: #fff; opacity: .94; }
</style>

<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card form-card premium fade-in">
    <div class="card-body">

        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="icon-ring-lg">
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
                <label class="form-label premium">
                    Kode Poli <span class="req">*</span>
                    <i class="bi bi-info-circle" data-bs-toggle="tooltip"
                       title="Kode unik untuk poli ini"></i>
                </label>
                <div class="input-group premium">
                    <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
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
                <label class="form-label premium">
                    Nama Poli <span class="req">*</span>
                    <i class="bi bi-info-circle" data-bs-toggle="tooltip"
                       title="Nama lengkap poli"></i>
                </label>
                <div class="input-group premium">
                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                    <input type="text" name="nama_poli"
                           class="form-control @error('nama_poli') is-invalid @enderror"
                           placeholder="Contoh: Poli Umum" value="{{ old('nama_poli') }}" required>
                    @error('nama_poli')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label premium">Deskripsi</label>
                <div class="input-group premium">
                    <span class="input-group-text align-items-start pt-2">
                        <i class="bi bi-file-text"></i>
                    </span>
                    <textarea name="deskripsi" class="form-control" rows="3"
                              placeholder="Deskripsi singkat poli ini">{{ old('deskripsi') }}</textarea>
                </div>
            </div>

            <div class="mb-4 p-3 status-toggle-card d-flex align-items-center justify-content-between">
                <div class="form-check form-switch mb-0">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                           id="is_active" checked>
                    <label class="form-check-label fw-semibold" for="is_active">Poli Aktif</label>
                    <div class="small text-muted">Poli akan ditampilkan dan bisa digunakan untuk pendaftaran pasien.</div>
                </div>
                <i class="bi bi-shield-check fs-5" style="color:var(--primary)"></i>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('departments.index') }}" class="btn btn-cancel premium">
                    <i class="bi bi-x-lg me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-save premium">
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