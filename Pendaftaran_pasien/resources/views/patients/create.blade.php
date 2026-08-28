@extends('layouts.app')
@section('title','Tambah Pasien')
@section('page-title','Tambah Pasien Baru')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Data Pasien</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@push('styles')
<style>
    .wizard-steps { display:flex; align-items:center; gap:0; margin-bottom:1.75rem; }
    .wizard-step { display:flex; align-items:center; gap:.6rem; flex:1; cursor:pointer; }
    .wizard-step .circle {
        width:32px; height:32px; border-radius:50%; flex:0 0 auto;
        display:flex; align-items:center; justify-content:center;
        font-weight:700; font-size:.85rem; background:#eef2f0; color:#8fa39a;
        border:2px solid #eef2f0; transition:all .2s ease;
    }
    .wizard-step .label { font-size:.83rem; font-weight:600; color:#8fa39a; transition:color .2s ease; }
    .wizard-step.active .circle { background:var(--primary); border-color:var(--primary); color:#fff; }
    .wizard-step.active .label { color:#1f2d27; }
    .wizard-step.done .circle { background:#d1fae5; border-color:#10b981; color:#065f46; }
    .wizard-step.done .label { color:#1f2d27; }
    .wizard-line { flex:0 0 40px; height:2px; background:#eef2f0; margin:0 4px; }

    .pill-group { display:flex; flex-wrap:wrap; gap:.5rem; }
    .pill-option input { position:absolute; opacity:0; pointer-events:none; }
    .pill-option label {
        display:inline-flex; align-items:center; gap:.4rem; cursor:pointer;
        padding:.55rem 1rem; border-radius:999px; border:1.5px solid #dfe6e2;
        font-size:.85rem; font-weight:600; color:#4b5f56; background:#fff; transition:all .15s ease;
    }
    .pill-option input:checked + label {
        border-color:var(--primary); background:var(--primary); color:#fff;
    }

    .form-floating > label { font-size:.85rem; color:#6b7c74; }
    .form-floating > .form-control,
    .form-floating > .form-select {
        border-radius:12px; border:1.5px solid #dfe6e2; min-height:calc(3.2rem + 2px);
    }
    .form-floating > textarea.form-control { min-height:90px; }

    .preview-card { position:sticky; top:1.5rem; }
    .preview-header {
        background:linear-gradient(135deg, var(--primary), #0f9d7c);
        border-radius:16px 16px 0 0; padding:1.75rem 1.5rem 4.25rem;
        color:#fff; position:relative;
    }
    .preview-avatar {
        width:76px; height:76px; border-radius:20px; background:#fff; color:var(--primary);
        display:flex; align-items:center; justify-content:center; font-size:1.8rem; font-weight:900;
        position:absolute; left:1.5rem; bottom:-32px; box-shadow:0 6px 16px rgba(15,23,20,.15);
    }
    .preview-body { padding:2.5rem 1.5rem 1.5rem; }
    .preview-row { display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px dashed #eef2f0; font-size:.82rem; }
    .preview-row:last-child { border-bottom:none; }

    .step-panel { display:none; }
    .step-panel.active { display:block; }

    @media (max-width: 991px) {
        .preview-card { position:static; margin-bottom:1.5rem; }
    }
</style>
@endpush

@section('content')

<div class="row g-4">

    <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="text-muted" style="font-size:.85rem;">Lengkapi data pasien dalam 3 langkah singkat.</div>
        <span class="badge d-flex align-items-center" style="background:#eff6ff;color:var(--primary);font-weight:600;padding:.55rem .9rem;">
            <i class="bi bi-calendar3 me-2"></i>{{ now()->translatedFormat('l, d F Y') }}
        </span>
    </div>

    @if ($errors->any())
    <div class="col-12">
        <div class="d-flex align-items-start gap-3 p-3" style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;">
            <i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;font-size:1.1rem;margin-top:2px;"></i>
            <div>
                <div class="fw-700" style="color:#991b1b;font-size:.9rem;">Periksa kembali isian Anda</div>
                <ul class="mb-0 mt-1" style="font-size:.83rem;color:#991b1b;padding-left:1.1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

<form action="{{ route('patients.store') }}" method="POST" class="col-12" id="patientForm">
@csrf
<div class="row g-4">

    {{-- ===== Left: live preview panel ===== --}}
    <div class="col-lg-4">
        <div class="card preview-card fade-in" style="border:1px solid #e7ece9;border-radius:16px;overflow:hidden;">
            <div class="preview-header">
                <div style="font-size:.75rem;opacity:.85;">Pratinjau Pasien</div>
                <div class="fw-800" id="prevName" style="font-size:1.15rem;margin-top:2px;">Nama Pasien</div>
                <div class="preview-avatar" id="prevAvatar">?</div>
            </div>
            <div class="preview-body">
                <div class="preview-row"><span class="text-muted">No. RM</span><strong style="font-family:monospace;color:var(--primary);">{{ $noRM }}</strong></div>
                <div class="preview-row"><span class="text-muted">NIK</span><span id="prevNik" style="font-family:monospace;">-</span></div>
                <div class="preview-row"><span class="text-muted">Jenis Kelamin</span><span id="prevGender">-</span></div>
                <div class="preview-row"><span class="text-muted">Usia</span><span id="prevAge">-</span></div>
                <div class="preview-row"><span class="text-muted">Gol. Darah</span><span id="prevBlood">-</span></div>
                <div class="preview-row"><span class="text-muted">Pembayaran</span><span id="prevPayment">Umum</span></div>
            </div>
        </div>
    </div>

    {{-- ===== Right: wizard form ===== --}}
    <div class="col-lg-8">
        <div class="card fade-in" style="border:1px solid #e7ece9;border-radius:16px;">
            <div class="card-body">

                <div class="wizard-steps">
                    <div class="wizard-step active" data-goto="1">
                        <span class="circle">1</span><span class="label">Data Diri</span>
                    </div>
                    <div class="wizard-line"></div>
                    <div class="wizard-step" data-goto="2">
                        <span class="circle">2</span><span class="label">Alamat &amp; Kontak</span>
                    </div>
                    <div class="wizard-line"></div>
                    <div class="wizard-step" data-goto="3">
                        <span class="circle">3</span><span class="label">Pembayaran</span>
                    </div>
                </div>

                {{-- ===== Step 1: Data Diri ===== --}}
                <div class="step-panel active" data-step="1">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="nik" name="nik" maxlength="16" inputmode="numeric" pattern="\d{16}"
                                       autocomplete="off" class="form-control @error('nik') is-invalid @enderror"
                                       placeholder="NIK" value="{{ old('nik') }}" required>
                                <label for="nik">NIK <span class="text-danger">*</span></label>
                            </div>
                            @error('nik')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" value="{{ $noRM }}" readonly tabindex="-1"
                                       style="background:#f6faf7;font-family:monospace;color:var(--primary);font-weight:700;">
                                <label>No. Rekam Medis</label>
                            </div>
                            <div class="form-text"><i class="bi bi-info-circle me-1"></i>Otomatis di-generate sistem</div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" id="nama_pasien" name="nama_pasien" autocomplete="name"
                                       class="form-control @error('nama_pasien') is-invalid @enderror"
                                       placeholder="Nama Lengkap" value="{{ old('nama_pasien') }}" required>
                                <label for="nama_pasien">Nama Lengkap <span class="text-danger">*</span></label>
                            </div>
                            @error('nama_pasien')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label mb-2">Jenis Kelamin <span class="text-danger">*</span></label>
                            <div class="pill-group" id="genderGroup">
                                <div class="pill-option">
                                    <input type="radio" id="gender_l" name="jenis_kelamin" value="L" {{ old('jenis_kelamin')==='L'?'checked':'' }} required>
                                    <label for="gender_l"><i class="bi bi-gender-male"></i> Laki-laki</label>
                                </div>
                                <div class="pill-option">
                                    <input type="radio" id="gender_p" name="jenis_kelamin" value="P" {{ old('jenis_kelamin')==='P'?'checked':'' }}>
                                    <label for="gender_p"><i class="bi bi-gender-female"></i> Perempuan</label>
                                </div>
                            </div>
                            @error('jenis_kelamin')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="tempat_lahir" name="tempat_lahir" class="form-control"
                                       placeholder="Tempat Lahir" value="{{ old('tempat_lahir') }}">
                                <label for="tempat_lahir">Tempat Lahir</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                                       class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                       placeholder="Tanggal Lahir" value="{{ old('tanggal_lahir') }}" required
                                       max="{{ date('Y-m-d', strtotime('-1 day')) }}">
                                <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                            </div>
                            @error('tanggal_lahir')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label mb-2">Golongan Darah</label>
                            <div class="pill-group" id="bloodGroup">
                                @foreach(['Tidak Diketahui','A','B','AB','O'] as $i => $gol)
                                    <div class="pill-option">
                                        <input type="radio" id="gol_{{ $i }}" name="golongan_darah" value="{{ $gol }}"
                                               {{ old('golongan_darah', 'Tidak Diketahui')===$gol?'checked':'' }}>
                                        <label for="gol_{{ $i }}">{{ $gol }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== Step 2: Alamat & Kontak ===== --}}
                <div class="step-panel" data-step="2">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                                          placeholder="Alamat" required>{{ old('alamat') }}</textarea>
                                <label for="alamat">Alamat Lengkap <span class="text-danger">*</span></label>
                            </div>
                            @error('alamat')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="no_telepon" name="no_telepon" inputmode="tel" autocomplete="tel"
                                       class="form-control" placeholder="No. Telepon" value="{{ old('no_telepon') }}">
                                <label for="no_telepon">No. Telepon</label>
                            </div>
                        </div>
                    </div>

                    <hr style="border-color:#eef2f0;margin:1.5rem 0;">
                    <div class="text-muted mb-2" style="font-size:.8rem;font-weight:600;">
                        Data Wali / Penanggung Jawab <span class="fw-400">(opsional)</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="nama_wali" name="nama_wali" class="form-control"
                                       placeholder="Nama Wali" value="{{ old('nama_wali') }}">
                                <label for="nama_wali">Nama Wali</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="no_telepon_wali" name="no_telepon_wali" inputmode="tel" class="form-control"
                                       placeholder="No. Telepon Wali" value="{{ old('no_telepon_wali') }}">
                                <label for="no_telepon_wali">No. Telepon Wali</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== Step 3: Pembayaran ===== --}}
                <div class="step-panel" data-step="3">
                    <label class="form-label mb-2">Jenis Pembayaran <span class="text-danger">*</span></label>
                    <div class="pill-group mb-3" id="paymentGroup">
                        <div class="pill-option">
                            <input type="radio" id="pay_umum" name="jenis_pembayaran" value="umum" {{ old('jenis_pembayaran','umum')==='umum'?'checked':'' }}>
                            <label for="pay_umum"><i class="bi bi-cash-coin"></i> Umum</label>
                        </div>
                        <div class="pill-option">
                            <input type="radio" id="pay_bpjs" name="jenis_pembayaran" value="bpjs" {{ old('jenis_pembayaran')==='bpjs'?'checked':'' }}>
                            <label for="pay_bpjs"><i class="bi bi-shield-plus"></i> BPJS Kesehatan</label>
                        </div>
                        <div class="pill-option">
                            <input type="radio" id="pay_asuransi" name="jenis_pembayaran" value="asuransi" {{ old('jenis_pembayaran')==='asuransi'?'checked':'' }}>
                            <label for="pay_asuransi"><i class="bi bi-file-earmark-medical"></i> Asuransi</label>
                        </div>
                    </div>

                    <div id="field-bpjs" class="mb-3" style="display:none;">
                        <div class="form-floating">
                            <input type="text" id="no_bpjs" name="no_bpjs" class="form-control" placeholder="No. BPJS" value="{{ old('no_bpjs') }}">
                            <label for="no_bpjs">No. BPJS</label>
                        </div>
                    </div>
                    <div id="field-asuransi" class="mb-3" style="display:none;">
                        <div class="form-floating">
                            <input type="text" id="no_asuransi" name="no_asuransi" class="form-control" placeholder="No. Asuransi" value="{{ old('no_asuransi') }}">
                            <label for="no_asuransi">No. Asuransi</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2 align-items-start p-3" style="background:#f6faf7;border-radius:12px;">
                        <i class="bi bi-shield-check" style="color:var(--primary);margin-top:2px;"></i>
                        <div style="font-size:.8rem;color:#4b5f56;">
                            Pastikan seluruh data sudah benar. Data ini menjadi dasar rekam medis dan riwayat kunjungan pasien.
                        </div>
                    </div>
                </div>

                {{-- ===== Footer navigation ===== --}}
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top:1px solid #eef2f0;">
                    <div>
                        <a href="{{ route('patients.index') }}" class="btn" style="background:var(--bg);color:#64766D;border-radius:10px;">Batal</a>
                        <button type="button" id="btnPrev" class="btn ms-1" style="background:var(--bg);color:#64766D;border-radius:10px;display:none;">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </button>
                    </div>
                    <div>
                        <button type="button" id="btnNext" class="btn btn-primary" style="border-radius:10px;">
                            Lanjut<i class="bi bi-arrow-right ms-1"></i>
                        </button>
                        <button type="submit" id="btnSubmit" class="btn btn-primary" style="border-radius:10px;display:none;">
                            <i class="bi bi-check2-circle me-1"></i>Simpan Data Pasien
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const totalSteps = 3;
    let current = 1;
    const hasServerErrors = @json($errors->any());

    const steps      = document.querySelectorAll('.step-panel');
    const stepTabs    = document.querySelectorAll('.wizard-step');
    const btnPrev     = document.getElementById('btnPrev');
    const btnNext     = document.getElementById('btnNext');
    const btnSubmit   = document.getElementById('btnSubmit');

    function requiredFieldsFor(step) {
        if (step === 1) return ['nik', 'nama_pasien', 'tanggal_lahir'];
        if (step === 2) return ['alamat'];
        return [];
    }

    function validateStep(step) {
        let valid = true;
        requiredFieldsFor(step).forEach(function (id) {
            const el = document.getElementById(id);
            if (!el) return;
            if (!el.value || (el.value.trim() === '')) {
                valid = false;
                el.classList.add('is-invalid');
            } else {
                el.classList.remove('is-invalid');
            }
        });
        if (step === 1) {
            const genderChecked = document.querySelector('input[name="jenis_kelamin"]:checked');
            if (!genderChecked) {
                valid = false;
                document.getElementById('genderGroup').style.outline = '1px solid #dc2626';
            } else {
                document.getElementById('genderGroup').style.outline = 'none';
            }
        }
        return valid;
    }

    function render() {
        steps.forEach(function (panel) {
            panel.classList.toggle('active', parseInt(panel.dataset.step, 10) === current);
        });
        stepTabs.forEach(function (tab) {
            const n = parseInt(tab.dataset.goto, 10);
            tab.classList.remove('active', 'done');
            if (n === current) tab.classList.add('active');
            else if (n < current) tab.classList.add('done');
        });
        btnPrev.style.display   = current > 1 ? 'inline-flex' : 'none';
        btnNext.style.display   = current < totalSteps ? 'inline-flex' : 'none';
        btnSubmit.style.display = current === totalSteps ? 'inline-flex' : 'none';
    }

    btnNext.addEventListener('click', function () {
        if (!validateStep(current)) return;
        current = Math.min(current + 1, totalSteps);
        render();
        window.scrollTo({ top: document.getElementById('patientForm').offsetTop - 80, behavior: 'smooth' });
    });
    btnPrev.addEventListener('click', function () {
        current = Math.max(current - 1, 1);
        render();
    });
    stepTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            const target = parseInt(tab.dataset.goto, 10);
            if (target > current && !validateStep(current)) return;
            current = target;
            render();
        });
    });

    // If the server round-tripped with validation errors, show everything so
    // nothing needed for correction is hidden behind an unvisited step.
    if (hasServerErrors) {
        document.querySelectorAll('.step-panel').forEach(function (p) { p.classList.add('active'); });
        document.querySelector('.wizard-steps').style.display = 'none';
        btnPrev.style.display = 'none';
        btnNext.style.display = 'none';
        btnSubmit.style.display = 'inline-flex';
    } else {
        render();
    }

    // ---- Live preview panel ----
    const prevName    = document.getElementById('prevName');
    const prevAvatar  = document.getElementById('prevAvatar');
    const prevNik     = document.getElementById('prevNik');
    const prevGender  = document.getElementById('prevGender');
    const prevAge     = document.getElementById('prevAge');
    const prevBlood   = document.getElementById('prevBlood');
    const prevPayment = document.getElementById('prevPayment');

    document.getElementById('nama_pasien').addEventListener('input', function (e) {
        const val = e.target.value.trim();
        prevName.textContent = val || 'Nama Pasien';
        prevAvatar.textContent = val ? val.charAt(0).toUpperCase() : '?';
    });
    document.getElementById('nik').addEventListener('input', function (e) {
        prevNik.textContent = e.target.value || '-';
    });
    document.querySelectorAll('input[name="jenis_kelamin"]').forEach(function (r) {
        r.addEventListener('change', function () {
            prevGender.textContent = r.value === 'L' ? 'Laki-laki' : 'Perempuan';
        });
    });
    document.getElementById('tanggal_lahir').addEventListener('change', function (e) {
        if (!e.target.value) { prevAge.textContent = '-'; return; }
        const dob = new Date(e.target.value);
        const now = new Date();
        let age = now.getFullYear() - dob.getFullYear();
        const m = now.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && now.getDate() < dob.getDate())) age--;
        prevAge.textContent = age >= 0 ? age + ' tahun' : '-';
    });
    document.querySelectorAll('input[name="golongan_darah"]').forEach(function (r) {
        r.addEventListener('change', function () { prevBlood.textContent = r.value; });
    });

    // ---- Payment conditional fields ----
    const fieldBpjs = document.getElementById('field-bpjs');
    const fieldAsuransi = document.getElementById('field-asuransi');
    document.querySelectorAll('input[name="jenis_pembayaran"]').forEach(function (r) {
        r.addEventListener('change', function () {
            fieldBpjs.style.display     = r.value === 'bpjs' && r.checked ? 'block' : (r.checked ? 'none' : fieldBpjs.style.display);
            fieldAsuransi.style.display = r.value === 'asuransi' && r.checked ? 'block' : (r.checked ? 'none' : fieldAsuransi.style.display);
            prevPayment.textContent = r.checked ? ({umum:'Umum', bpjs:'BPJS Kesehatan', asuransi:'Asuransi'}[r.value]) : prevPayment.textContent;
        });
    });
    // initialize on load (covers old() re-fill after validation error)
    const checkedPayment = document.querySelector('input[name="jenis_pembayaran"]:checked');
    if (checkedPayment) checkedPayment.dispatchEvent(new Event('change'));
})();
</script>
@endpush