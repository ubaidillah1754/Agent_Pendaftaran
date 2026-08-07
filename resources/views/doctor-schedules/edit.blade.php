@extends('layouts.app')
@section('title','Edit Jadwal')
@section('page-title','Edit Jadwal Praktik')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('doctor-schedules.index') }}">Jadwal Praktik</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@push('styles')
<style>
    .js-profile-link {
        background:none; border:none; padding:0; font-size:.78rem; font-weight:600;
        color:var(--primary); display:inline-flex; align-items:center; gap:.3rem; cursor:pointer;
    }
    .js-profile-link:hover { text-decoration:underline; }

    .jp-avatar-lg {
        width:64px; height:64px; border-radius:50%; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
        font-weight:700; font-size:1.3rem; color:#fff; object-fit:cover;
    }
    .jp-profile-spec-badge {
        font-size:.72rem; font-weight:600; padding:.25rem .6rem; border-radius:999px;
        background:#e6fbf5; color:var(--primary); display:inline-block;
    }
    .jp-modal-poli-chip {
        font-size:.72rem; font-weight:600; padding:.25rem .6rem; border-radius:999px;
        background:#eff6ff; color:#1e40af; display:inline-block; margin:0 .3rem .3rem 0;
    }
</style>
@endpush

@php
    // Sama seperti di halaman index Jadwal Praktik — pecah "dr. Kevin, Sp.PD" jadi nama & spesialisasi.
    // Kalau tabel `doctors` sudah punya kolom `spesialisasi` sendiri, ganti ke itu langsung.
    if (!function_exists('jpParseDoctorName')) {
        function jpParseDoctorName(string $fullName): array {
            $parts = array_map('trim', explode(',', $fullName, 2));
            return ['nama' => $parts[0] ?? $fullName, 'spesialisasi' => $parts[1] ?? null];
        }
    }

    $currentDoctor = $doctorSchedule->doctor;
    $parsedDoctor  = $currentDoctor ? jpParseDoctorName($currentDoctor->nama_dokter) : ['nama' => '-', 'spesialisasi' => null];

    // Diasumsikan relasi Doctor::schedules() (hasMany DoctorSchedule) tersedia di model.
    // Kalau belum ada, tabel jadwal di modal akan kosong (aman, tidak error) — tambahkan
    // relasi ini di model Doctor supaya bagian ini terisi.
    $urutanHari = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
    $currentDoctorSchedules = optional($currentDoctor)->schedules ?? collect();
    $currentDoctorSchedules = $currentDoctorSchedules->sortBy(fn($s) => array_search($s->hari, $urutanHari));
    $currentDoctorPoli = $currentDoctorSchedules->pluck('department.nama_poli')->filter()->unique();
@endphp

@section('content')

<div class="card fade-in border-0 shadow-sm" style="border-radius:16px;">
    <div class="card-body p-4 p-md-5">

        {{-- Header --}}
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4 pb-4 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div style="width:52px;height:52px;border-radius:14px;background:#e6fbf5;color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h5 class="mb-1 fw-700">Edit Jadwal Praktik</h5>
                    <p class="text-muted mb-0" style="font-size:.85rem;">
                        {{ $doctorSchedule->doctor->nama_dokter ?? '' }} — {{ $doctorSchedule->department->nama_poli ?? '' }}
                    </p>
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

        <form action="{{ route('doctor-schedules.update', $doctorSchedule) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-4">

                <div class="col-md-6">
                    <label class="form-label fw-600">Poli <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-building"></i></span>
                        <select name="department_id" id="department_id"
                                class="form-select @error('department_id') is-invalid @enderror" required>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}" {{ old('department_id', $doctorSchedule->department_id) == $d->id ? 'selected' : '' }}>
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
                        @if($currentDoctor)
                        <button type="button" class="js-profile-link"
                                data-bs-toggle="modal" data-bs-target="#doctorProfileModal">
                            <i class="bi bi-person-lines-fill"></i> Lihat Profil
                        </button>
                        @endif
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-person-badge"></i></span>
                        <select name="doctor_id" id="doctor_id"
                                class="form-select @error('doctor_id') is-invalid @enderror" required>
                            @foreach($doctors as $d)
                                <option value="{{ $d->id }}"
                                    data-department="{{ $d->department_id ?? '' }}"
                                    {{ old('doctor_id', $doctorSchedule->doctor_id) == $d->id ? 'selected' : '' }}>
                                    {{ $d->nama_dokter }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-text">
                        Tombol "Lihat Profil" menampilkan profil dokter yang tersimpan saat ini — belum mengikuti perubahan pilihan dropdown.
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-600">Hari <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-calendar3"></i></span>
                        <select name="hari" class="form-select @error('hari') is-invalid @enderror" required>
                            @foreach($hariList as $hari)
                                <option value="{{ $hari }}" {{ old('hari', $doctorSchedule->hari) === $hari ? 'selected' : '' }}>{{ $hari }}</option>
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
                               value="{{ old('jam_mulai', substr($doctorSchedule->jam_mulai,0,5)) }}" required>
                        @error('jam_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-600">Jam Selesai <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-clock-history"></i></span>
                        <input type="time" name="jam_selesai" id="jam_selesai"
                               class="form-control @error('jam_selesai') is-invalid @enderror"
                               value="{{ old('jam_selesai', substr($doctorSchedule->jam_selesai,0,5)) }}" required>
                        @error('jam_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-text text-danger d-none" id="jamOrderWarning">
                        <i class="bi bi-exclamation-circle me-1"></i>Jam selesai harus setelah jam mulai.
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-600">Kuota <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-people"></i></span>
                        <input type="number" name="kuota" class="form-control @error('kuota') is-invalid @enderror"
                               min="1" max="100" value="{{ old('kuota', $doctorSchedule->kuota) }}" required>
                        @error('kuota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex align-items-center justify-content-between p-3 h-100" style="background:#f0fdf9;border-radius:12px;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check form-switch mb-0">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                       style="width:2.6em;height:1.4em;"
                                       {{ old('is_active', $doctorSchedule->is_active) ? 'checked' : '' }}>
                            </div>
                            <div>
                                <label class="form-check-label fw-600 mb-0" for="is_active" style="font-size:.88rem;">Jadwal Aktif</label>
                                <div class="text-muted" style="font-size:.78rem;">Jadwal nonaktif tidak muncul di form pendaftaran pasien.</div>
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
                    <i class="bi bi-check2 me-1"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== MODAL PROFIL DOKTER (jadwal ini) ===================== --}}
@if($currentDoctor)
<div class="modal fade" id="doctorProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">
            <div class="modal-body p-0">
                <div class="p-4 d-flex align-items-center gap-3" style="background:#f8fdfb; border-bottom:1px solid #eef1f4;">
                    @if($currentDoctor->photo ?? null)
                        <img src="{{ asset('storage/'.$currentDoctor->photo) }}" class="jp-avatar-lg" alt="{{ $parsedDoctor['nama'] }}">
                    @else
                        <div class="jp-avatar-lg" style="background:var(--primary);">
                            {{ Str::upper(Str::substr(str_replace(['dr.','Dr.'],'',$parsedDoctor['nama']), 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h6 class="mb-1 fw-700">{{ $parsedDoctor['nama'] }}</h6>
                        @if($parsedDoctor['spesialisasi'])
                            <span class="jp-profile-spec-badge">{{ $parsedDoctor['spesialisasi'] }}</span>
                        @endif
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="p-4">
                    <div class="mb-3">
                        <div class="small text-muted fw-600 mb-2" style="text-transform:uppercase; font-size:.7rem; letter-spacing:.04em;">
                            Poli Praktik
                        </div>
                        @forelse($currentDoctorPoli as $poli)
                            <span class="jp-modal-poli-chip">{{ $poli }}</span>
                        @empty
                            <span class="text-muted small">-</span>
                        @endforelse
                    </div>

                    <div>
                        <div class="small text-muted fw-600 mb-2" style="text-transform:uppercase; font-size:.7rem; letter-spacing:.04em;">
                            Jadwal Praktik Mingguan
                        </div>
                        @if($currentDoctorSchedules->isEmpty())
                            <div class="text-muted small">
                                Data jadwal mingguan tidak tersedia di halaman ini. Pastikan relasi <code>Doctor::schedules()</code> sudah ada di model.
                            </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0" style="font-size:.82rem;">
                                <thead>
                                    <tr class="text-muted">
                                        <th>Hari</th>
                                        <th>Poli</th>
                                        <th>Jam</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($currentDoctorSchedules as $sc)
                                    <tr class="{{ $sc->id === $doctorSchedule->id ? 'table-active' : '' }}">
                                        <td class="fw-600">{{ $sc->hari }}</td>
                                        <td>{{ $sc->department->nama_poli ?? '-' }}</td>
                                        <td>{{ substr($sc->jam_mulai,0,5) }}–{{ substr($sc->jam_selesai,0,5) }}</td>
                                        <td class="text-center">
                                            @if($sc->is_active)
                                                <span class="badge rounded-pill" style="background:#d1fae5;color:#065f46;">Aktif</span>
                                            @else
                                                <span class="badge rounded-pill" style="background:#fee2e2;color:#991b1b;">Nonaktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn" style="background:var(--bg);color:#64748b;" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
(function () {
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
})();
</script>
@endpush
@endsection