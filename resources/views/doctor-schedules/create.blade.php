@extends('layouts.app')
@section('title','Tambah Jadwal')
@section('page-title','Tambah Jadwal Praktik')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('doctor-schedules.index') }}">Jadwal Praktik</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection
@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card fade-in">
    <div class="card-header"><i class="bi bi-calendar-week me-2"></i>Form Tambah Jadwal Praktik</div>
    <div class="card-body">
        <form action="{{ route('doctor-schedules.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Poli <span class="text-danger">*</span></label>
                    <select name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                        <option value="">— Pilih Poli —</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_poli }}</option>
                        @endforeach
                    </select>
                    @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dokter <span class="text-danger">*</span></label>
                    <select name="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                        <option value="">— Pilih Dokter —</option>
                        @foreach($doctors as $d)
                            <option value="{{ $d->id }}" {{ old('doctor_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_dokter }}</option>
                        @endforeach
                    </select>
                    @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Hari <span class="text-danger">*</span></label>
                    <select name="hari" class="form-select @error('hari') is-invalid @enderror" required>
                        <option value="">— Pilih Hari —</option>
                        @foreach($hariList as $hari)
                            <option value="{{ $hari }}" {{ old('hari') === $hari ? 'selected' : '' }}>{{ $hari }}</option>
                        @endforeach
                    </select>
                    @error('hari')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                    <input type="time" name="jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror" value="{{ old('jam_mulai','08:00') }}" required>
                    @error('jam_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                    <input type="time" name="jam_selesai" class="form-control @error('jam_selesai') is-invalid @enderror" value="{{ old('jam_selesai','12:00') }}" required>
                    @error('jam_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kuota Pasien <span class="text-danger">*</span></label>
                    <input type="number" name="kuota" class="form-control @error('kuota') is-invalid @enderror" min="1" max="100" value="{{ old('kuota', 20) }}" required>
                    <div class="form-text">Maksimal pasien per sesi jadwal ini</div>
                    @error('kuota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 d-flex align-items-end pb-1">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
                        <label class="form-check-label" for="is_active">Jadwal Aktif</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ route('doctor-schedules.index') }}" class="btn" style="background:var(--bg);color:#64748b;">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
