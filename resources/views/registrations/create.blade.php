@extends('layouts.app')
@section('title','Form Pendaftaran')
@section('page-title','Pendaftaran Rawat Jalan')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('registrations.index') }}">Pendaftaran</a></li>
    <li class="breadcrumb-item active">Form Baru</li>
@endsection

@push('styles')
<style>
    /* Mode selection cards (Pasien Lama / Pasien Baru) */
    .mode-tab {
        position:relative; border-radius:14px; padding:20px 16px; border:1.5px solid #e7ece9;
        cursor:pointer; transition:all .2s ease; user-select:none; background:#fff;
    }
    .mode-tab:hover { border-color:#cfe0d8; }
    .mode-tab.active { border-color:var(--primary); background:#f0f9f5; }
    .mode-tab .icon-circle {
        width:44px; height:44px; border-radius:50%; margin:0 auto 10px; display:flex;
        align-items:center; justify-content:center; font-size:1.15rem;
        background:#eef2f0; color:#8fa39a; transition:all .2s ease;
    }
    .mode-tab.active .icon-circle { background:var(--primary); color:#fff; }
    .mode-tab h6 { margin:0; font-size:.88rem; font-weight:700; color:#1f2d27; }
    .mode-tab p  { margin:2px 0 0; font-size:.75rem; color:#64766D; }
    .mode-tab .check-badge {
        position:absolute; top:10px; right:10px; width:20px; height:20px; border-radius:50%;
        background:#10b981; color:#fff; display:none; align-items:center; justify-content:center; font-size:.65rem;
    }
    .mode-tab.active .check-badge { display:flex; }

    /* Search input + dropdown */
    .search-wrap { position:relative; }
    .search-wrap .bi-search {
        position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#9ca8a2; pointer-events:none;
    }
    .search-wrap input {
        border-radius:12px; border:1.5px solid #dfe6e2; padding:.65rem .9rem .65rem 2.4rem;
    }
    #search-result { position:absolute; top:calc(100% + 6px); left:0; right:0; z-index:1000; border-radius:12px; overflow:hidden; box-shadow:0 8px 30px rgba(15,23,20,.12); border:1px solid #eef2f0; }
    .search-item { padding:12px 16px; cursor:pointer; border-bottom:1px solid #f1f4f2; background:#fff; transition:background .15s; }
    .search-item:hover { background:#f6faf7; }
    .search-item:last-child { border-bottom:none; }

    .info-banner {
        display:flex; gap:10px; align-items:flex-start; background:#f0f9f5; border:1px solid #d7ead9;
        border-radius:12px; padding:12px 14px; font-size:.8rem; color:#3f5c4f;
    }

    /* Preview antrian */
    #antrian-preview {
        background:linear-gradient(135deg,#eaf4ef,#f4f9f6); border:1.5px solid #d7ead9; border-radius:14px;
        padding:18px; text-align:center; display:none;
    }
    .antrian-number { font-size:2.75rem; font-weight:900; color:var(--primary); line-height:1; }
    .kuota-bar { height:8px; border-radius:4px; background:#e5ece8; overflow:hidden; margin-top:10px; }
    .kuota-fill { height:100%; border-radius:4px; background:var(--primary); transition:width .5s; }

    #selected-patient-card {
        background:#f0f9f5; border:1.5px solid #a9d9b8; border-radius:14px; padding:16px; display:none;
    }

    /* Shared field styling to match reference screenshot */
    .form-select, .form-control { border-radius:12px; border:1.5px solid #dfe6e2; padding:.65rem .9rem; }
    .form-select:disabled { background:#f6faf7; color:#9ca8a2; }
    .card { border:1px solid #e7ece9; border-radius:16px; box-shadow:0 1px 3px rgba(15,23,20,.04); }
    .card-header { background:#fff; border-bottom:1px solid #eef2f0; font-weight:700; }

    .keluhan-wrap { position:relative; }
    .char-counter { position:absolute; right:12px; bottom:10px; font-size:.72rem; color:#9ca8a2; }
</style>
@endpush

@section('content')

<div class="row g-4">

    {{-- ===== Header: date pill, kept consistent with other pages ===== --}}
    <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div></div>
        <span class="badge d-flex align-items-center" style="background:#eff6ff;color:var(--primary);font-weight:600;padding:.55rem .9rem;">
            <i class="bi bi-calendar3 me-2"></i>{{ now()->translatedFormat('l, d F Y') }}
        </span>
    </div>

<form action="{{ route('registrations.store') }}" method="POST" id="form-pendaftaran" class="col-12">
@csrf
<div class="row g-4">

    <!-- Kolom Kiri: Data Pasien -->
    <div class="col-lg-6">
        <div class="card fade-in">
            <div class="card-header d-flex align-items-center gap-2">
                <span style="width:32px;height:32px;border-radius:9px;background:#eff6ff;color:var(--primary);display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-person"></i>
                </span>
                Data Pasien
            </div>
            <div class="card-body">

                <!-- Mode Pasien: Baru / Lama -->
                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <div class="mode-tab active text-center" id="tab-lama" onclick="setMode('lama')">
                            <span class="check-badge"><i class="bi bi-check-lg"></i></span>
                            <span class="icon-circle"><i class="bi bi-search"></i></span>
                            <h6>Pasien Lama</h6>
                            <p>Cari berdasarkan NIK / No. RM</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mode-tab text-center" id="tab-baru" onclick="setMode('baru')">
                            <span class="check-badge"><i class="bi bi-check-lg"></i></span>
                            <span class="icon-circle"><i class="bi bi-plus-lg"></i></span>
                            <h6>Pasien Baru</h6>
                            <p>Belum pernah berobat</p>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="mode_pasien" id="mode_pasien" value="lama">

                <!-- PANEL: Pasien Lama -->
                <div id="panel-lama">
                    <div class="mb-3">
                        <label class="form-label">Cari Pasien <span class="text-danger">*</span></label>
                        <div class="search-wrap">
                            <i class="bi bi-search"></i>
                            <input type="text" id="q-pasien" class="form-control" placeholder="Ketik NIK, No. RM, atau nama..." autocomplete="off">
                            <div id="search-result"></div>
                        </div>
                    </div>
                    <input type="hidden" name="patient_id" id="patient_id" value="{{ $patient?->id }}">

                    <!-- Card pasien terpilih -->
                    <div id="selected-patient-card" style="{{ $patient ? 'display:block;' : '' }}">
                        @if($patient)
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:44px;height:44px;border-radius:50% 50% 8px 8px;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:800;">
                                {{ strtoupper(substr($patient->nama_pasien,0,1)) }}
                            </div>
                            <div>
                                <div class="fw-700">{{ $patient->nama_pasien }}</div>
                                <div style="font-size:.78rem;color:var(--primary);">
                                    {{ $patient->no_rm }} · {{ $patient->nik }} · {{ $patient->umur }} thn
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @error('patient_id')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror

                    <div class="info-banner mt-3">
                        <i class="bi bi-info-circle" style="margin-top:1px;"></i>
                        <span>Pastikan data pasien sudah benar sebelum melanjutkan pendaftaran.</span>
                    </div>
                </div>

                <!-- PANEL: Pasien Baru -->
                <div id="panel-baru" style="display:none;">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">NIK <span class="text-danger">*</span></label>
                            <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" maxlength="16" placeholder="16 digit NIK" value="{{ old('nik') }}">
                            @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pasien" class="form-control" placeholder="Nama sesuai KTP" value="{{ old('nama_pasien') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="">Pilih</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Jenis Pembayaran <span class="text-danger">*</span></label>
                            <select name="jenis_pembayaran" class="form-select">
                                <option value="umum">Umum (Bayar Sendiri)</option>
                                <option value="bpjs">BPJS Kesehatan</option>
                                <option value="asuransi">Asuransi</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Data Pendaftaran -->
    <div class="col-lg-6">
        <div class="card fade-in fade-in-delay-1">
            <div class="card-header d-flex align-items-center gap-2">
                <span style="width:32px;height:32px;border-radius:9px;background:#d1fae5;color:#065f46;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-calendar2-check"></i>
                </span>
                Data Kunjungan
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Pilih Poli -->
                    <div class="col-12">
                        <label class="form-label">Pilih Poli <span class="text-danger">*</span></label>
                        <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                            <option value="">— Pilih Poli —</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->kode_poli }} — {{ $dept->nama_poli }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Pilih Dokter (diisi AJAX) -->
                    <div class="col-12">
                        <label class="form-label">Pilih Dokter <span class="text-danger">*</span></label>
                        <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required disabled>
                            <option value="">— Pilih poli dulu —</option>
                        </select>
                        @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Pilih Jadwal (diisi AJAX) -->
                    <div class="col-12">
                        <label class="form-label">Pilih Jadwal Praktik <span class="text-danger">*</span></label>
                        <select name="doctor_schedule_id" id="schedule_id" class="form-select @error('doctor_schedule_id') is-invalid @enderror" required disabled>
                            <option value="">— Pilih dokter dulu —</option>
                        </select>
                        @error('doctor_schedule_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Tanggal Kunjungan -->
                    <div class="col-12">
                        <label class="form-label">Tanggal Kunjungan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_daftar" id="tanggal_daftar" class="form-control @error('tanggal_daftar') is-invalid @enderror"
                               min="{{ date('Y-m-d') }}" value="{{ old('tanggal_daftar', date('Y-m-d')) }}" required>
                        @error('tanggal_daftar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="hari-warning" class="text-danger mt-1" style="font-size:.78rem;display:none;"></div>
                    </div>

                    <!-- Preview Antrian -->
                    <div class="col-12">
                        <div id="antrian-preview">
                            <div style="font-size:.72rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.06em;">Nomor Antrian Anda</div>
                            <div class="antrian-number" id="nomor-preview">—</div>
                            <div id="kuota-info" style="font-size:.8rem;color:#475d52;margin-top:4px;"></div>
                            <div class="kuota-bar"><div class="kuota-fill" id="kuota-fill" style="width:0%"></div></div>
                        </div>
                    </div>

                    <!-- Keluhan -->
                    <div class="col-12">
                        <label class="form-label">Keluhan / Keterangan</label>
                        <div class="keluhan-wrap">
                            <textarea name="keluhan" id="keluhan" class="form-control" rows="3" maxlength="500"
                                      placeholder="Tuliskan keluhan atau alasan kunjungan (opsional)"
                                      style="padding-bottom:1.6rem;">{{ old('keluhan') }}</textarea>
                            <span class="char-counter" id="keluhan-counter">0/500</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2 justify-content-end">
            <a href="{{ route('registrations.index') }}" class="btn" style="background:var(--bg);color:#64766D;border-radius:10px;">Batal</a>
            <button type="submit" class="btn btn-accent px-4" id="btn-submit" disabled style="border-radius:10px;">
                <i class="bi bi-check2-circle me-1"></i> Simpan Pendaftaran
            </button>
        </div>
    </div>
</div>
</form>
</div>
@endsection

@push('scripts')
<script>
const BASE = '{{ url("") }}';
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── Mode Tab ─────────────────────────────────────────────────────────────────
function setMode(mode) {
    document.getElementById('mode_pasien').value = mode;
    document.getElementById('tab-lama').classList.toggle('active', mode === 'lama');
    document.getElementById('tab-baru').classList.toggle('active', mode === 'baru');
    document.getElementById('panel-lama').style.display = mode === 'lama' ? 'block' : 'none';
    document.getElementById('panel-baru').style.display = mode === 'baru' ? 'block' : 'none';
    checkSubmitReady();
}

// ── Pencarian Pasien AJAX ─────────────────────────────────────────────────────
let searchTimer;
document.getElementById('q-pasien').addEventListener('input', function() {
    clearTimeout(searchTimer);
    const q = this.value.trim();
    if (q.length < 3) { document.getElementById('search-result').innerHTML = ''; return; }
    searchTimer = setTimeout(() => {
        fetch(`${BASE}/ajax/cari-pasien?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => renderSearchResult(data));
    }, 350);
});

function renderSearchResult(patients) {
    const box = document.getElementById('search-result');
    if (!patients.length) {
        box.innerHTML = '<div class="search-item text-muted">Pasien tidak ditemukan</div>'; return;
    }
    box.innerHTML = patients.map(p => `
        <div class="search-item" onclick="selectPatient(${p.id},'${p.nama_pasien}','${p.no_rm}','${p.nik}')">
            <div class="fw-600" style="font-size:.875rem;">${p.nama_pasien}</div>
            <div style="font-size:.75rem;color:#64766D;">${p.no_rm} · ${p.nik} · ${p.jenis_kelamin} · ${p.tanggal_lahir}</div>
        </div>`).join('');
}

function selectPatient(id, nama, noRm, nik) {
    document.getElementById('patient_id').value = id;
    document.getElementById('q-pasien').value = nama;
    document.getElementById('search-result').innerHTML = '';
    const card = document.getElementById('selected-patient-card');
    card.style.display = 'block';
    card.innerHTML = `<div class="d-flex align-items-center gap-3">
        <div style="width:44px;height:44px;border-radius:50% 50% 8px 8px;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:800;">${nama[0].toUpperCase()}</div>
        <div><div class="fw-700">${nama}</div><div style="font-size:.78rem;color:var(--primary);">${noRm} · ${nik}</div></div>
        <button type="button" onclick="clearPatient()" style="margin-left:auto;background:none;border:none;color:#ef4444;cursor:pointer;"><i class="bi bi-x-circle"></i></button>
    </div>`;
    checkSubmitReady();
}

function clearPatient() {
    document.getElementById('patient_id').value = '';
    document.getElementById('q-pasien').value = '';
    document.getElementById('selected-patient-card').style.display = 'none';
    checkSubmitReady();
}

// ── Cascading Dropdown ────────────────────────────────────────────────────────
document.getElementById('department_id').addEventListener('change', function() {
    const deptId = this.value;
    const docSel = document.getElementById('doctor_id');
    const schSel = document.getElementById('schedule_id');
    docSel.innerHTML = '<option>Memuat...</option>'; docSel.disabled = true;
    schSel.innerHTML = '<option>— Pilih dokter dulu —</option>'; schSel.disabled = true;
    hidePrev(); checkSubmitReady();
    if (!deptId) { docSel.innerHTML = '<option>— Pilih poli dulu —</option>'; return; }
    fetch(`${BASE}/ajax/doctors?department_id=${deptId}`)
        .then(r => r.json())
        .then(data => {
            docSel.innerHTML = '<option value="">— Pilih Dokter —</option>' +
                data.map(d => `<option value="${d.id}">${d.nama_dokter}${d.spesialisasi ? ' ('+d.spesialisasi+')' : ''}</option>`).join('');
            docSel.disabled = false;
        });
});

document.getElementById('doctor_id').addEventListener('change', function() {
    const docId = this.value;
    const deptId = document.getElementById('department_id').value;
    const schSel = document.getElementById('schedule_id');
    schSel.innerHTML = '<option>Memuat...</option>'; schSel.disabled = true;
    hidePrev(); checkSubmitReady();
    if (!docId) { schSel.innerHTML = '<option>— Pilih dokter dulu —</option>'; return; }
    fetch(`${BASE}/ajax/schedules?doctor_id=${docId}&department_id=${deptId}`)
        .then(r => r.json())
        .then(data => {
            schSel.innerHTML = '<option value="">— Pilih Jadwal —</option>' +
                data.map(s => `<option value="${s.id}" data-hari="${s.hari}">${s.hari}, ${s.jam_mulai}–${s.jam_selesai} (Kuota: ${s.kuota})</option>`).join('');
            schSel.disabled = false;
        });
});

document.getElementById('schedule_id').addEventListener('change', checkKuota);
document.getElementById('tanggal_daftar').addEventListener('change', checkKuota);

function checkKuota() {
    const schId  = document.getElementById('schedule_id').value;
    const tgl    = document.getElementById('tanggal_daftar').value;
    if (!schId || !tgl) { hidePrev(); checkSubmitReady(); return; }
    fetch(`${BASE}/ajax/kuota?schedule_id=${schId}&tanggal=${tgl}`)
        .then(r => r.json())
        .then(data => {
            const warn = document.getElementById('hari-warning');
            if (!data.valid) {
                warn.textContent = data.message; warn.style.display = 'block';
                hidePrev(); checkSubmitReady(); return;
            }
            warn.style.display = 'none';
            if (data.penuh) {
                hidePrev();
                document.getElementById('hari-warning').textContent = '⚠️ Kuota penuh! Pilih tanggal atau dokter lain.';
                document.getElementById('hari-warning').style.display = 'block';
                checkSubmitReady(); return;
            }
            const pct = Math.round((data.kuota_total - data.sisa_kuota) / data.kuota_total * 100);
            document.getElementById('antrian-preview').style.display = 'block';
            document.getElementById('nomor-preview').textContent = data.nomor_berikutnya;
            document.getElementById('kuota-info').textContent = data.message;
            document.getElementById('kuota-fill').style.width = pct + '%';
            checkSubmitReady();
        });
}

function hidePrev() {
    document.getElementById('antrian-preview').style.display = 'none';
    document.getElementById('nomor-preview').textContent = '—';
}

function checkSubmitReady() {
    const mode      = document.getElementById('mode_pasien').value;
    const patientOk = mode === 'baru' || document.getElementById('patient_id').value;
    const deptOk    = document.getElementById('department_id').value;
    const docOk     = document.getElementById('doctor_id').value;
    const schOk     = document.getElementById('schedule_id').value;
    const tglOk     = document.getElementById('tanggal_daftar').value;
    const penuh     = document.getElementById('hari-warning').style.display !== 'none';
    document.getElementById('btn-submit').disabled = !(patientOk && deptOk && docOk && schOk && tglOk && !penuh);
}

// Close search result on outside click
document.addEventListener('click', e => {
    if (!e.target.closest('#panel-lama')) document.getElementById('search-result').innerHTML = '';
});

// ── Keluhan character counter ────────────────────────────────────────────────
const keluhanEl = document.getElementById('keluhan');
const counterEl = document.getElementById('keluhan-counter');
function updateCounter() { counterEl.textContent = keluhanEl.value.length + '/500'; }
keluhanEl.addEventListener('input', updateCounter);
updateCounter();
</script>
@endpush