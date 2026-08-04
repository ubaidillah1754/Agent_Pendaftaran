@extends('layouts.app')
@section('title','Tambah Dokter')
@section('page-title','Tambah Dokter Baru')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('doctors.index') }}">Data Dokter</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection
@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card fade-in">
    <div class="card-header"><i class="bi bi-person-badge me-2"></i>Form Tambah Dokter</div>
    <div class="card-body">
        <form action="{{ route('doctors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Nama Dokter <span class="text-danger">*</span></label>
                    <input type="text" name="nama_dokter" class="form-control @error('nama_dokter') is-invalid @enderror"
                           placeholder="Contoh: dr. Budi Santoso, Sp.JP" value="{{ old('nama_dokter') }}" required>
                    @error('nama_dokter')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIP</label>
                    <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                           placeholder="Nomor Induk Pegawai" value="{{ old('nip') }}">
                    @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="no_telepon" class="form-control" placeholder="08xxxxxxxxxx" value="{{ old('no_telepon') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Poli Utama <span class="text-danger">*</span></label>
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
                <div class="col-md-6">
                    <label class="form-label">Spesialisasi</label>
                    <input type="text" name="spesialisasi" class="form-control" placeholder="Contoh: Spesialis Jantung" value="{{ old('spesialisasi') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Foto Dokter</label>
                    <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                    <div class="form-text">JPG/PNG, max 2MB</div>
                    @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
                        <label class="form-check-label" for="is_active">Dokter Aktif</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ route('doctors.index') }}" class="btn" style="background:var(--bg);color:#64748b;">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
