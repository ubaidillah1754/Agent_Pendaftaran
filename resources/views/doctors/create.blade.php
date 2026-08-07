@extends('layouts.app')
@section('title','Tambah Dokter')
@section('page-title','Tambah Dokter Baru')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('doctors.index') }}">Data Dokter</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection
@section('content')

<div class="card fade-in border-0 shadow-sm" style="border-radius:16px;">
    <div class="card-body p-4 p-md-5">

        {{-- Header --}}
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4 pb-4 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div style="width:52px;height:52px;border-radius:14px;background:#e6fbf5;color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <h5 class="mb-1 fw-700">Form Tambah Dokter</h5>
                    <p class="text-muted mb-0" style="font-size:.85rem;">Lengkapi data dokter baru dengan lengkap dan benar.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('doctors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                <div class="col-12">
                    <label class="form-label fw-600">Nama Dokter <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                        <input type="text" name="nama_dokter" class="form-control @error('nama_dokter') is-invalid @enderror"
                               placeholder="Contoh: dr. Budi Santoso, Sp.JP" value="{{ old('nama_dokter') }}" required>
                        @error('nama_dokter')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-600">NIP</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-credit-card-2-front"></i></span>
                        <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                               placeholder="Nomor Induk Pegawai" value="{{ old('nip') }}">
                        @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-600">No. Telepon</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-telephone"></i></span>
                        <input type="text" name="no_telepon" class="form-control" placeholder="08xxxxxxxxxx" value="{{ old('no_telepon') }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-600">Poli Utama <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-hospital"></i></span>
                        <select name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                            <option value="">— Pilih Poli —</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->kode_poli }} — {{ $dept->nama_poli }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-600">Spesialisasi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-award"></i></span>
                        <input type="text" name="spesialisasi" class="form-control" placeholder="Contoh: Spesialis Jantung" value="{{ old('spesialisasi') }}">
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-600">Foto Dokter</label>

                    <label for="fotoInput" class="d-flex align-items-center gap-3 p-3" style="background:var(--bg);border:1px dashed #cbd5e1;border-radius:12px;cursor:pointer;">
                        <div style="width:38px;height:38px;border-radius:10px;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">
                            <i class="bi bi-cloud-arrow-up"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-600" style="font-size:.85rem;">Pilih File</div>
                            <div id="fotoFileName" class="text-muted" style="font-size:.78rem;">Tidak ada file yang dipilih</div>
                        </div>
                        <i class="bi bi-image text-muted"></i>
                    </label>
                    <input type="file" name="foto" id="fotoInput" class="d-none @error('foto') is-invalid @enderror" accept="image/*">
                    @error('foto')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    <div class="form-text">JPG/PNG, max 2MB</div>
                </div>

                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between p-3" style="background:#f0fdf9;border-radius:12px;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check form-switch mb-0">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                       style="width:2.6em;height:1.4em;" checked>
                            </div>
                            <div>
                                <label class="form-check-label fw-600 mb-0" for="is_active" style="font-size:.88rem;">Dokter Aktif</label>
                                <div class="text-muted" style="font-size:.78rem;">Dokter yang aktif akan ditampilkan pada daftar dokter.</div>
                            </div>
                        </div>
                        <div style="width:32px;height:32px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('doctors.index') }}" class="btn" style="background:var(--bg);color:#64748b;">
                    <i class="bi bi-x-lg me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2 me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('fotoInput').addEventListener('change', function(e){
        const label = document.getElementById('fotoFileName');
        label.textContent = e.target.files[0] ? e.target.files[0].name : 'Tidak ada file yang dipilih';
    });
</script>
@endpush