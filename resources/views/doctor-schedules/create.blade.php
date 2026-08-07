@extends('layouts.app')
@section('title','Tambah Jadwal')
@section('page-title','Tambah Jadwal Praktik')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('doctor-schedules.index') }}">Jadwal Praktik</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@push('styles')
<style>
    .js-profile-link {
        background:none; border:none; padding:0; font-size:.78rem; font-weight:600;
        color:var(--primary); display:inline-flex; align-items:center; gap:.3rem; cursor:pointer;
    }
    .js-profile-link:hover:not(:disabled) { text-decoration:underline; }
    .js-profile-link:disabled { color:#9aa3b2; cursor:not-allowed; }

    .jp-avatar-lg {
        width:64px; height:64px; border-radius:50%; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
        font-weight:700; font-size:1.3rem; color:#fff; object-fit:cover;
    }
    .jp-profile-spec-badge {
        font-size:.72rem; font-weight:600; padding:.25rem .6rem; border-radius:999px;
        background:#e6fbf5; color:var(--primary); display:inline-block;
    }
    
    /* Lebarkan form melewati batas container default layout */
    .jp-wide-wrap {
        width:100%;
        max-width:1200px;
        margin-left:auto;
        margin-right:auto;
    }
</style>
@endpush

@php
    // Sama seperti halaman lain — pecah "dr. Kevin, Sp.PD" jadi nama & spesialisasi.
    // Ganti ke $d->spesialisasi langsung kalau kolom itu sudah ada di tabel doctors.
    if (!function_exists('jpParseDoctorName')) {
        function jpParseDoctorName(string $fullName): array {
            $parts = array_map('trim', explode(',', $fullName, 2));
            return ['nama' => $parts[0] ?? $fullName, 'spesialisasi' => $parts[1] ?? null];
        }
    }
    $avatarColors = ['#2563eb','#c2410c','#0f9d76','#7c3aed','#db2777','#0891b2','#ca8a04'];
@endphp

@section('content')

<div class="jp-wide-wrap">
<div class="card fade-in border-0 shadow-sm" style="border-radius:16px;">
    <div class="card-body p-4 p-md-5">

        {{-- Header --}}
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4 pb-4 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div style="width:52px;height:52px;border-radius:14px;background:#e6fbf5;color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;">
                    <i class="bi bi-calendar-week"></i>
                </div>
                <div>
                    <h5 class="mb-1 fw-700">Form Tambah Jadwal Praktik</h5>
                    <p class="text-muted mb-0" style="font-size:.85rem;">Lengkapi data jadwal untuk menambahkan sesi praktik baru.</p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger d-flex align-items-start gap-2 py-2 px-3 mb-4">
                <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                <div class="small">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('doctor-schedules.store') }}" method="POST">
            @csrf
            <div class="row g-4">

                <div class="col-md-6">
                    <label class="form-label fw-600">Poli <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-building"></i></span>
                        <select name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                            <option value="">— Pilih Poli —</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->kode_poli }} — {{ $d->nama_poli }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-600 mb-0">Dokter <span class="text-danger">*</span></label>
                        <button type="button" class="js-profile-link" id="btnLihatProfil"
                                data-bs-toggle="modal" data-bs-target="#doctorProfileModal" disabled>
                            <i class="bi bi-person-lines-fill"></i> Lihat Profil
                        </button>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-person-badge"></i></span>
                        <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                            <option value="">— Pilih Dokter —</option>
                            @foreach($doctors as $i => $d)
                                @php $p = jpParseDoctorName($d->nama_dokter); @endphp
                                <option value="{{ $d->id }}"
                                    data-nama="{{ $p['nama'] }}"
                                    data-spesialisasi="{{ $p['spesialisasi'] }}"
                                    data-photo="{{ $d->photo ?? '' }}"
                                    data-color="{{ $avatarColors[$i % count($avatarColors)] }}"
                                    {{ old('doctor_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->nama_dokter }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-600">Hari <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-calendar3"></i></span>
                        <select name="hari" class="form-select @error('hari') is-invalid @enderror" required>
                            <option value="">— Pilih Hari —</option>
                            @foreach($hariList as $hari)
                                <option value="{{ $hari }}" {{ old('hari') === $hari ? 'selected' : '' }}>{{ $hari }}</option>
                            @endforeach
                        </select>
                        @error('hari')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-600">Jam Mulai <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-clock"></i></span>
                        <input type="time" name="jam_mulai" id="jam_mulai"
                               class="form-control @error('jam_mulai') is-invalid @enderror"
                               value="{{ old('jam_mulai','08:00') }}" required>
                        @error('jam_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-600">Jam Selesai <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-clock-history"></i></span>
                        <input type="time" name="jam_selesai" id="jam_selesai"
                               class="form-control @error('jam_selesai') is-invalid @enderror"
                               value="{{ old('jam_selesai','12:00') }}" required>
                        @error('jam_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-text text-danger d-none" id="jamOrderWarning">
                        <i class="bi bi-exclamation-circle me-1"></i>Jam selesai harus setelah jam mulai.
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-600">Kuota Pasien <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-people"></i></span>
                        <input type="number" name="kuota" class="form-control @error('kuota') is-invalid @enderror"
                               min="1" max="100" value="{{ old('kuota', 20) }}" required>
                        @error('kuota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-text">Maksimal pasien per sesi jadwal ini</div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex align-items-center justify-content-between p-3 h-100" style="background:#f0fdf9;border-radius:12px;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check form-switch mb-0">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                       style="width:2.6em;height:1.4em;" checked>
                            </div>
                            <div>
                                <label class="form-check-label fw-600 mb-0" for="is_active" style="font-size:.88rem;">Jadwal Aktif</label>
                                <div class="text-muted" style="font-size:.78rem;">Jadwal akan langsung bisa digunakan untuk pendaftaran pasien.</div>
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
                <a href="{{ route('doctor-schedules.index') }}" class="btn" style="background:var(--bg);color:#64748b;">
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

{{-- ===================== MODAL PROFIL DOKTER (dinamis via JS) ===================== --}}
<div class="modal fade" id="doctorProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">
            <div class="modal-body p-0">
                <div class="p-4 d-flex align-items-center gap-3" style="background:#f8fdfb; border-bottom:1px solid #eef1f4;">
                    <div id="profileAvatar" class="jp-avatar-lg" style="background:var(--primary);"></div>
                    <div>
                        <h6 class="mb-1 fw-700" id="profileNama">-</h6>
                        <span class="jp-profile-spec-badge d-none" id="profileSpesialisasi"></span>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="p-4">
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        Jadwal praktik mingguan dokter ini belum tersedia di form Tambah Jadwal — cek melalui halaman
                        <a href="{{ route('doctor-schedules.index') }}">Jadwal Praktik</a> setelah jadwal disimpan.
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn" style="background:var(--bg);color:#64748b;" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    // Validasi ringan: jam selesai harus setelah jam mulai. Client-side saja, bukan pengganti validasi server.
    const jamMulai = document.getElementById('jam_mulai');
    const jamSelesai = document.getElementById('jam_selesai');
    const warning = document.getElementById('jamOrderWarning');
    function checkOrder() {
        if (!jamMulai.value || !jamSelesai.value) { warning.classList.add('d-none'); return; }
        warning.classList.toggle('d-none', jamSelesai.value > jamMulai.value);
    }
    jamMulai?.addEventListener('change', checkOrder);
    jamSelesai?.addEventListener('change', checkOrder);
    checkOrder();

    // Aktifkan tombol "Lihat Profil" dan isi modal begitu dokter dipilih.
    const doctorSelect = document.getElementById('doctor_id');
    const btnProfil = document.getElementById('btnLihatProfil');
    const profileAvatar = document.getElementById('profileAvatar');
    const profileNama = document.getElementById('profileNama');
    const profileSpes = document.getElementById('profileSpesialisasi');

    function updateProfile() {
        const opt = doctorSelect.options[doctorSelect.selectedIndex];
        const hasDoctor = opt && opt.value !== '';

        btnProfil.disabled = !hasDoctor;
        if (!hasDoctor) return;

        const nama = opt.dataset.nama || opt.text;
        const spesialisasi = opt.dataset.spesialisasi || '';
        const photo = opt.dataset.photo || '';
        const color = opt.dataset.color || '#0f9d76';

        profileNama.textContent = nama;

        if (spesialisasi) {
            profileSpes.textContent = spesialisasi;
            profileSpes.classList.remove('d-none');
        } else {
            profileSpes.classList.add('d-none');
        }

        profileAvatar.style.background = color;
        if (photo) {
            profileAvatar.innerHTML = `<img src="/storage/${photo}" style="width:100%;height:100%;border-radius:50%;object-fit:cover;" alt="${nama}">`;
        } else {
            const initial = nama.replace(/^(dr\.|Dr\.)\s*/,'').charAt(0).toUpperCase();
            profileAvatar.innerHTML = initial;
        }
    }

    doctorSelect?.addEventListener('change', updateProfile);
    updateProfile(); // isi awal kalau old('doctor_id') sudah terisi (validasi gagal & form di-reload)
})();
</script>
@endpush
@endsection