@extends('layouts.app')
@section('title','Edit Pasien')
@section('page-title','Edit Data Pasien')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Data Pasien</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<form action="{{ route('patients.update', $patient) }}" method="POST">
@csrf @method('PUT')
<div class="row g-4">
<div class="col-lg-8">
    <div class="card fade-in">
        <div class="card-header"><i class="bi bi-pencil-square me-2"></i>Edit Pasien — {{ $patient->nama_pasien }}</div>
        <div class="card-body">
            <div class="form-section">
                <div class="form-section-title">Identitas Diri</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">NIK <span class="text-danger">*</span></label>
                        <input type="text" name="nik" maxlength="16" class="form-control @error('nik') is-invalid @enderror"
                               value="{{ old('nik', $patient->nik) }}" required>
                        @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. Rekam Medis</label>
                        <input type="text" class="form-control" value="{{ $patient->no_rm }}" readonly
                               style="background:#f6faf7;font-family:monospace;color:var(--primary);font-weight:700;">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pasien" class="form-control @error('nama_pasien') is-invalid @enderror"
                               value="{{ old('nama_pasien', $patient->nama_pasien) }}" required>
                        @error('nama_pasien')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="L" {{ old('jenis_kelamin', $patient->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $patient->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $patient->tempat_lahir) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                               value="{{ old('tanggal_lahir', $patient->tanggal_lahir->format('Y-m-d')) }}" required>
                        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat', $patient->alamat) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon', $patient->no_telepon) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Golongan Darah</label>
                        <select name="golongan_darah" class="form-select">
                            @foreach(['Tidak Diketahui','A','B','AB','O'] as $gol)
                                <option value="{{ $gol }}" {{ old('golongan_darah', $patient->golongan_darah) === $gol ? 'selected' : '' }}>{{ $gol }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-section">
                <div class="form-section-title">Data Wali</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Wali</label>
                        <input type="text" name="nama_wali" class="form-control" value="{{ old('nama_wali', $patient->nama_wali) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. Telepon Wali</label>
                        <input type="text" name="no_telepon_wali" class="form-control" value="{{ old('no_telepon_wali', $patient->no_telepon_wali) }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-4">
    <div class="card fade-in fade-in-delay-1">
        <div class="card-header"><i class="bi bi-credit-card-2-front me-2"></i>Pembayaran</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Jenis Pembayaran</label>
                <select name="jenis_pembayaran" id="jenis_pembayaran" class="form-select" onchange="togglePembayaran()">
                    <option value="umum"     {{ old('jenis_pembayaran', $patient->jenis_pembayaran) === 'umum'     ? 'selected' : '' }}>Umum</option>
                    <option value="bpjs"     {{ old('jenis_pembayaran', $patient->jenis_pembayaran) === 'bpjs'     ? 'selected' : '' }}>BPJS Kesehatan</option>
                    <option value="asuransi" {{ old('jenis_pembayaran', $patient->jenis_pembayaran) === 'asuransi' ? 'selected' : '' }}>Asuransi</option>
                </select>
            </div>
            <div id="field-bpjs">
                <label class="form-label">No. BPJS</label>
                <input type="text" name="no_bpjs" class="form-control" value="{{ old('no_bpjs', $patient->no_bpjs) }}">
            </div>
            <div id="field-asuransi">
                <label class="form-label">No. Asuransi</label>
                <input type="text" name="no_asuransi" class="form-control" value="{{ old('no_asuransi', $patient->no_asuransi) }}">
            </div>
        </div>
    </div>
    <div class="mt-3 d-flex flex-column gap-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check2-circle me-1"></i>Simpan Perubahan</button>
        <a href="{{ route('patients.show', $patient) }}" class="btn w-100" style="background:var(--bg);color:#64766D;">Batal</a>
    </div>
</div>
</div>
</form>
@endsection
@push('scripts')
<script>
function togglePembayaran() {
    const val = document.getElementById('jenis_pembayaran').value;
    document.getElementById('field-bpjs').style.display     = val === 'bpjs'     ? 'block' : 'none';
    document.getElementById('field-asuransi').style.display = val === 'asuransi' ? 'block' : 'none';
}
togglePembayaran();
</script>
@endpush