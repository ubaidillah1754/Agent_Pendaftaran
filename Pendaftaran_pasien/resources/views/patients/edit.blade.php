@extends('layouts.app')
@section('title','Edit Pasien')
@section('page-title','Edit Data Pasien')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Data Pasien</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --hp-primary:   #0F766E;
        --hp-secondary: #14B8A6;
        --hp-accent:    #D4AF37;
        --hp-danger:    #EF4444;
        --hp-bg:        #F8FAFC;
        --hp-border:    #E5E7EB;
        --hp-radius:    18px;
        --hp-shadow:    0 10px 40px rgba(15,118,110,.08);
    }

    .edit-pasien-wrap { font-family:'Poppins', sans-serif; }

    /* ===== Mint hero with illustration ===== */
    .edit-hero {
        position:relative; overflow:hidden;
        border-radius:24px; padding:26px 30px; margin-bottom:22px;
        background: linear-gradient(115deg, #D1FAE5 0%, #99F6E4 55%, #5EEAD4 100%);
        display:flex; align-items:center; gap:18px;
        box-shadow: var(--hp-shadow);
    }
    .edit-hero .hero-icon {
        width:56px; height:56px; border-radius:16px; background:#fff;
        display:flex; align-items:center; justify-content:center;
        font-size:1.5rem; color:var(--hp-primary); flex-shrink:0;
        box-shadow: 0 6px 16px rgba(15,118,110,.18);
    }
    .edit-hero h5 { margin:0 0 4px; font-weight:700; font-size:1.2rem; color:#064E3B; }
    .edit-hero p { margin:0; font-size:.85rem; color:#0f766e; opacity:.85; }
    .edit-hero .hero-illus {
        margin-left:auto; display:flex; align-items:center; gap:0; opacity:.9; flex-shrink:0;
    }
    .edit-hero .hero-illus svg { width:150px; height:100px; }

    .ep-card {
        border-radius: var(--hp-radius); border:1px solid var(--hp-border);
        box-shadow: var(--hp-shadow); background:#fff; overflow:hidden;
    }
    .ep-card .card-header {
        background:#fff; border-bottom:1px solid var(--hp-border);
        font-weight:600; font-size:.92rem; padding:16px 22px; color:#0f172a;
        display:flex; align-items:center; gap:10px;
    }
    .ep-card .card-header .hd-icon {
        width:32px; height:32px; border-radius:10px; background:#ECFDF5; color:var(--hp-primary);
        display:flex; align-items:center; justify-content:center; font-size:.95rem;
    }
    .ep-card .card-body { padding:22px; }

    .form-section { margin-bottom:22px; }
    .form-section:last-child { margin-bottom:0; }
    .form-section-title {
        font-size:.8rem; font-weight:700; color:#0f172a; margin-bottom:14px;
        display:flex; align-items:center; gap:8px; padding-bottom:10px; border-bottom:1px solid var(--hp-border);
    }
    .form-section-title .opsional { font-weight:500; color:#94a3b8; font-size:.74rem; }
    .form-section.boxed {
        border:1px solid var(--hp-border); border-radius:14px; padding:18px; background:#FAFBFC;
    }

    .form-label { font-size:.8rem; font-weight:600; color:#334155; margin-bottom:6px; display:flex; align-items:center; gap:6px; }
    .form-label i { color:var(--hp-secondary); font-size:.85rem; }

    .form-control, .form-select {
        border-radius:12px; border:1.5px solid var(--hp-border); font-family:inherit;
        padding:10px 14px; font-size:.88rem; transition: all .18s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--hp-secondary); box-shadow: 0 0 0 4px rgba(20,184,166,.14);
    }

    .rm-chip {
        display:inline-flex; align-items:center; gap:8px; background:#ECFDF5;
        border:1.5px dashed #A7F3D0; border-radius:12px; padding:10px 14px;
        font-family:'Poppins',monospace; color:var(--hp-primary); font-weight:700; font-size:.88rem; width:100%;
    }

    /* payment type row with colored badge */
    .payment-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
    .payment-badge {
        font-size:.72rem; font-weight:700; padding:4px 12px; border-radius:20px;
        display:inline-flex; align-items:center; gap:4px;
    }
    .payment-badge.pb-bpjs     { background:#DCFCE7; color:#15803D; }
    .payment-badge.pb-umum     { background:#F1F5F9; color:#475569; }
    .payment-badge.pb-asuransi { background:#DBEAFE; color:#1D4ED8; }

    .input-check-wrap { position:relative; }
    .input-check-wrap .check-ic { position:absolute; right:12px; top:50%; transform:translateY(-50%); color:#16A34A; }

    .verify-note {
        margin-top:14px; background:#ECFDF5; border:1px solid #A7F3D0; border-radius:12px;
        padding:12px 14px; display:flex; align-items:flex-start; gap:10px;
    }
    .verify-note i { color:#0F766E; font-size:1.1rem; margin-top:1px; }
    .verify-note strong { display:block; font-size:.8rem; color:#065F46; }
    .verify-note span { font-size:.74rem; color:#0f766e; opacity:.85; }

    .btn-save {
        background: var(--hp-primary);
        color:#fff; border:none; border-radius:12px; padding:12px; font-weight:700; font-size:.92rem;
        box-shadow: 0 8px 20px -6px rgba(15,118,110,.5); transition: transform .15s, box-shadow .15s;
    }
    .btn-save:hover { background:#0d5f58; transform: translateY(-2px); color:#fff; }

    .btn-cancel {
        border-radius:12px; padding:12px; font-weight:600; font-size:.88rem;
        background:var(--hp-bg); color:#64748b; border:1.5px solid var(--hp-border); transition: all .15s;
    }
    .btn-cancel:hover { background:#eef2f6; color:#334155; }
</style>
@endpush

@section('content')
<div class="edit-pasien-wrap">

<div class="edit-hero fade-in">
    <div class="hero-icon"><i class="bi bi-person-vcard-fill"></i></div>
    <div>
        <h5>Edit Data Pasien</h5>
        <p>Perbarui informasi pasien dengan data yang akurat.</p>
    </div>
    <div class="hero-illus" aria-hidden="true">
        <svg viewBox="0 0 220 140" xmlns="http://www.w3.org/2000/svg">
            <circle cx="150" cy="70" r="62" fill="#ffffff" opacity=".35"/>
            <!-- building -->
            <rect x="130" y="40" width="64" height="90" rx="4" fill="#0F766E" opacity=".85"/>
            <g fill="#ffffff" opacity=".8">
                <rect x="140" y="52" width="10" height="10" rx="1"/><rect x="156" y="52" width="10" height="10" rx="1"/><rect x="172" y="52" width="10" height="10" rx="1"/>
                <rect x="140" y="68" width="10" height="10" rx="1"/><rect x="156" y="68" width="10" height="10" rx="1"/><rect x="172" y="68" width="10" height="10" rx="1"/>
                <rect x="140" y="84" width="10" height="10" rx="1"/><rect x="156" y="84" width="10" height="10" rx="1"/><rect x="172" y="84" width="10" height="10" rx="1"/>
            </g>
            <rect x="157" y="18" width="8" height="20" fill="#D4AF37"/>
            <rect x="151" y="24" width="20" height="8" fill="#D4AF37"/>
            <!-- clipboard -->
            <rect x="18" y="30" width="70" height="90" rx="10" fill="#ffffff" stroke="#5EEAD4" stroke-width="2"/>
            <rect x="38" y="24" width="30" height="14" rx="4" fill="#14B8A6"/>
            <rect x="32" y="50" width="46" height="7" rx="3" fill="#99F6E4"/>
            <rect x="32" y="64" width="46" height="7" rx="3" fill="#99F6E4"/>
            <rect x="32" y="78" width="30" height="7" rx="3" fill="#99F6E4"/>
            <circle cx="66" cy="98" r="12" fill="#D1FAE5"/>
            <path d="M60 98 l4 4 l8 -9" stroke="#0F766E" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
</div>

<form action="{{ route('patients.update', $patient) }}" method="POST">
@csrf @method('PUT')
<div class="row g-4">
<div class="col-lg-8">
    <div class="ep-card fade-in">
        <div class="card-header"><span class="hd-icon"><i class="bi bi-person-lines-fill"></i></span>Informasi Pasien</div>
        <div class="card-body">
            <div class="form-section">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-person-badge"></i>NIK <span class="text-danger">*</span></label>
                        <input type="text" name="nik" maxlength="16" class="form-control @error('nik') is-invalid @enderror"
                               value="{{ old('nik', $patient->nik) }}" required>
                        @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. Rekam Medis</label>
                        <div class="rm-chip"><i class="bi bi-upc-scan"></i>{{ $patient->no_rm }}</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pasien" class="form-control @error('nama_pasien') is-invalid @enderror"
                               value="{{ old('nama_pasien', $patient->nama_pasien) }}" required>
                        @error('nama_pasien')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><i class="bi bi-gender-ambiguous"></i>Jenis Kelamin <span class="text-danger">*</span></label>
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
                        <label class="form-label"><i class="bi bi-telephone"></i>No. Telepon</label>
                        <input type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon', $patient->no_telepon) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-droplet-half"></i>Golongan Darah</label>
                        <select name="golongan_darah" class="form-select">
                            @foreach(['Tidak Diketahui','A','B','AB','O'] as $gol)
                                <option value="{{ $gol }}" {{ old('golongan_darah', $patient->golongan_darah) === $gol ? 'selected' : '' }}>{{ $gol }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-section boxed">
                <div class="form-section-title"><i class="bi bi-people" style="color:var(--hp-secondary);"></i>Data Wali <span class="opsional">(Opsional)</span></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-person"></i>Nama Wali</label>
                        <input type="text" name="nama_wali" class="form-control" value="{{ old('nama_wali', $patient->nama_wali) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-telephone"></i>No. Telepon Wali</label>
                        <input type="text" name="no_telepon_wali" class="form-control" value="{{ old('no_telepon_wali', $patient->no_telepon_wali) }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-4">
    <div class="ep-card fade-in fade-in-delay-1">
        <div class="card-header"><span class="hd-icon" style="background:#FEF9E7;color:var(--hp-accent);"><i class="bi bi-credit-card-2-front"></i></span>Informasi Pembayaran</div>
        <div class="card-body">
            <div class="mb-3">
                <div class="payment-row">
                    <label class="form-label mb-0">Jenis Pembayaran <span class="text-danger">*</span></label>
                    <span class="payment-badge" id="payment-badge">{{ ucfirst(old('jenis_pembayaran', $patient->jenis_pembayaran)) }}</span>
                </div>
                <select name="jenis_pembayaran" id="jenis_pembayaran" class="form-select" onchange="togglePembayaran()">
                    <option value="umum"     {{ old('jenis_pembayaran', $patient->jenis_pembayaran) === 'umum'     ? 'selected' : '' }}>Umum</option>
                    <option value="bpjs"     {{ old('jenis_pembayaran', $patient->jenis_pembayaran) === 'bpjs'     ? 'selected' : '' }}>BPJS Kesehatan</option>
                    <option value="asuransi" {{ old('jenis_pembayaran', $patient->jenis_pembayaran) === 'asuransi' ? 'selected' : '' }}>Asuransi</option>
                </select>
            </div>
            <div id="field-bpjs">
                <label class="form-label">No. BPJS <span class="text-danger">*</span></label>
                <div class="input-check-wrap">
                    <input type="text" name="no_bpjs" class="form-control" value="{{ old('no_bpjs', $patient->no_bpjs) }}">
                    <i class="bi bi-patch-check-fill check-ic"></i>
                </div>
                <div class="verify-note">
                    <i class="bi bi-shield-check"></i>
                    <div>
                        <strong>Pastikan nomor BPJS sudah benar</strong>
                        <span>Data akan diverifikasi saat pendaftaran.</span>
                    </div>
                </div>
            </div>
            <div id="field-asuransi">
                <label class="form-label">No. Asuransi</label>
                <input type="text" name="no_asuransi" class="form-control" value="{{ old('no_asuransi', $patient->no_asuransi) }}">
            </div>
        </div>
    </div>
    <div class="mt-3 d-flex flex-column gap-2">
        <button type="submit" class="btn btn-save"><i class="bi bi-check2-circle me-1"></i>Simpan Perubahan</button>
        <a href="{{ route('patients.show', $patient) }}" class="btn btn-cancel"><i class="bi bi-x-lg me-1"></i>Batal</a>
    </div>
</div>
</div>
</form>
</div>
@endsection

@push('scripts')
<script>
function togglePembayaran() {
    const val = document.getElementById('jenis_pembayaran').value;
    document.getElementById('field-bpjs').style.display     = val === 'bpjs'     ? 'block' : 'none';
    document.getElementById('field-asuransi').style.display = val === 'asuransi' ? 'block' : 'none';

    // Cosmetic only: color the payment badge to match the selected type.
    const badge = document.getElementById('payment-badge');
    const labels = { umum: 'Umum', bpjs: 'BPJS', asuransi: 'Asuransi' };
    badge.textContent = labels[val] || val;
    badge.className = 'payment-badge pb-' + val;
}
togglePembayaran();
</script>
@endpush