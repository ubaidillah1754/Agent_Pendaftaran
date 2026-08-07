@extends('layouts.app')
@section('title','Jadwal Praktik')
@section('page-title','Jadwal Praktik Dokter')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Jadwal Praktik</li>
@endsection

@push('styles')
<style>
    .jp-stat-card {
        display:flex; align-items:center; gap:.65rem;
        padding:.6rem 1rem; border-radius:12px;
        background:#fff; border:1px solid #eef1f4;
        box-shadow:0 1px 2px rgba(16,24,40,.04);
        min-width:130px;
    }
    .jp-stat-icon {
        width:36px; height:36px; border-radius:10px;
        display:flex; align-items:center; justify-content:center;
        font-size:.95rem; flex-shrink:0;
    }
    .jp-stat-value { font-size:1.05rem; font-weight:700; line-height:1.1; color:#111827; }
    .jp-stat-label { font-size:.68rem; color:#8a93a3; font-weight:600; }

    .jp-tabs {
        display:flex; gap:.5rem; flex-wrap:wrap;
        padding-bottom:1rem; margin-bottom:1rem;
        border-bottom:1px solid #eef1f4;
    }
    .jp-tab-btn {
        display:flex; align-items:center; gap:.5rem;
        border:1px solid #e5e7eb; background:#fff; color:#374151;
        border-radius:10px; padding:.5rem .9rem; font-size:.85rem; font-weight:600;
        cursor:pointer; transition:all .15s ease;
    }
    .jp-tab-btn:hover { border-color:var(--primary); }
    .jp-tab-btn .jp-tab-count {
        font-size:.72rem; font-weight:600; padding:.1rem .5rem; border-radius:999px;
        background:#f1f5f9; color:#64748b;
    }
    .jp-tab-btn.active {
        background:var(--primary); border-color:var(--primary); color:#fff;
    }
    .jp-tab-btn.active .jp-tab-count { background:rgba(255,255,255,.25); color:#fff; }

    .jp-panel { display:none; }
    .jp-panel.active { display:block; }

    .jp-table thead th {
        background:#f8fafb; color:#6b7280; font-size:.72rem; font-weight:700;
        text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid #eef1f4;
        padding:.85rem .9rem; white-space:nowrap;
    }
    .jp-table tbody td { padding:.75rem .9rem; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
    .jp-table tbody tr:last-child td { border-bottom:none; }
    .jp-table tbody tr:hover { background:#f8fdfb; }

    .jp-avatar {
        width:36px; height:36px; border-radius:50%; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
        font-weight:700; font-size:.8rem; color:#fff; object-fit:cover;
    }
    .jp-avatar-lg {
        width:64px; height:64px; border-radius:50%; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
        font-weight:700; font-size:1.3rem; color:#fff; object-fit:cover;
    }
    .jp-poli-badge {
        font-size:.7rem; font-weight:700; letter-spacing:.03em;
        padding:.28rem .55rem; border-radius:6px; background:var(--primary); color:#fff;
        display:inline-block;
    }
    .jp-status-dot { width:6px; height:6px; border-radius:50%; display:inline-block; margin-right:.35rem; }

    .btn-icon-soft {
        width:30px; height:30px; border-radius:8px; border:none;
        display:inline-flex; align-items:center; justify-content:center;
        transition:filter .12s ease, transform .12s ease;
    }
    .btn-icon-soft:hover { filter:brightness(.95); transform:translateY(-1px); }

    /* Trigger profil dokter — dibuat terlihat seperti teks biasa, bukan tombol,
       tapi tetap punya afordansi klik (hover state). */
    .jp-doctor-trigger {
        background:none; border:none; padding:0; text-align:left; cursor:pointer;
        display:flex; align-items:center; gap:.5rem; width:100%;
        border-radius:8px; transition:background .12s ease;
    }
    .jp-doctor-trigger:hover { background:#f1f9f6; }
    .jp-doctor-trigger:hover .jp-doctor-name { color:var(--primary); text-decoration:underline; }
    .jp-doctor-name { color:#111827; transition:color .12s ease; }

    .jp-profile-spec-badge {
        font-size:.72rem; font-weight:600; padding:.25rem .6rem; border-radius:999px;
        background:#e6f7f1; color:#0f9d76; display:inline-block;
    }
    .jp-modal-poli-chip {
        font-size:.72rem; font-weight:600; padding:.25rem .6rem; border-radius:999px;
        background:#eff6ff; color:#1e40af; display:inline-block; margin:0 .3rem .3rem 0;
    }
</style>
@endpush

@php
    // Kumpulkan seluruh jadwal jadi satu koleksi untuk keperluan statistik & profil dokter.
    $allSchedules = collect();
    foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h) {
        if (isset($schedules[$h])) {
            $allSchedules = $allSchedules->merge($schedules[$h]);
        }
    }

    $totalDokterCount = $totalDokter ?? $allSchedules->map(fn($s) => $s->doctor->id ?? null)->filter()->unique()->count();
    $totalPoliCount   = $totalPoli   ?? $allSchedules->map(fn($s) => $s->department->id ?? null)->filter()->unique()->count();

    // Mapping manual dayOfWeek -> nama hari Indonesia, agar tidak bergantung pada locale Carbon global.
    $hariMap = [0=>'Minggu',1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat',6=>'Sabtu'];
    $todayName = $hariMap[now()->dayOfWeek];
    $jadwalHariIniCount = $jadwalHariIni ?? ($schedules[$todayName] ?? collect())->count();

    $avatarColors = ['#2563eb','#c2410c','#0f9d76','#7c3aed','#db2777','#0891b2','#ca8a04'];
    $urutanHari = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];

    // Tab pertama yang punya data jadi default aktif.
    $firstActiveDay = null;
    foreach ($urutanHari as $h) {
        if (isset($schedules[$h]) && $schedules[$h]->count() > 0) { $firstActiveDay = $h; break; }
    }

    // Pecah "dr. Kevin Alexander, Sp.PD" -> nama: "dr. Kevin Alexander", spesialisasi: "Sp.PD"
    // CATATAN: kalau tabel `doctors` sudah punya kolom `spesialisasi` sendiri, pakai itu
    // langsung ($d->spesialisasi) dan hapus fungsi parsing ini — lebih andal daripada
    // mengandalkan format string nama.
    if (!function_exists('jpParseDoctorName')) {
        function jpParseDoctorName(string $fullName): array {
            $parts = array_map('trim', explode(',', $fullName, 2));
            return [
                'nama' => $parts[0] ?? $fullName,
                'spesialisasi' => $parts[1] ?? null,
            ];
        }
    }

    // Kelompokkan seluruh jadwal per dokter untuk isi modal profil.
    $doctorProfiles = $allSchedules->groupBy('doctor_id');
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
    <div>
        <h5 class="mb-1 fw-700" style="color:var(--primary)">Jadwal Praktik Dokter</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Dikelompokkan per hari dalam seminggu</p>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="jp-stat-card">
            <div class="jp-stat-icon" style="background:#e6f7f1;color:#0f9d76;"><i class="bi bi-person-badge"></i></div>
            <div>
                <div class="jp-stat-value">{{ $totalDokterCount }}</div>
                <div class="jp-stat-label">Total Dokter</div>
            </div>
        </div>
        <div class="jp-stat-card">
            <div class="jp-stat-icon" style="background:#eaf2ff;color:#2563eb;"><i class="bi bi-building"></i></div>
            <div>
                <div class="jp-stat-value">{{ $totalPoliCount }}</div>
                <div class="jp-stat-label">Total Poli</div>
            </div>
        </div>
        <div class="jp-stat-card">
            <div class="jp-stat-icon" style="background:#f3e8fd;color:#7c3aed;"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="jp-stat-value">{{ $jadwalHariIniCount }}</div>
                <div class="jp-stat-label">Jadwal Hari Ini</div>
            </div>
        </div>

        <a href="{{ route('doctor-schedules.create') }}" class="btn btn-accent">
            <i class="bi bi-plus-circle me-1"></i> Tambah Jadwal
        </a>
    </div>
</div>

<div class="card fade-in p-3">

    <div class="jp-tabs">
        @foreach($urutanHari as $hari)
            @php $count = isset($schedules[$hari]) ? $schedules[$hari]->count() : 0; @endphp
            <button type="button"
                    class="jp-tab-btn {{ $hari === $firstActiveDay ? 'active' : '' }}"
                    data-day="{{ $hari }}">
                {{ $hari }}
                <span class="jp-tab-count">{{ $count }} jadwal</span>
            </button>
        @endforeach
    </div>

    @foreach($urutanHari as $hari)
    <div class="jp-panel {{ $hari === $firstActiveDay ? 'active' : '' }}" data-day-panel="{{ $hari }}">
        @if(isset($schedules[$hari]) && $schedules[$hari]->count() > 0)
        <div class="table-responsive">
            <table class="table jp-table align-middle mb-0">
                <thead><tr>
                    <th>Dokter</th>
                    <th>Poli</th>
                    <th>Jam Praktik</th>
                    <th class="text-center">Kuota</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" width="100">Aksi</th>
                </tr></thead>
                <tbody>
                @foreach($schedules[$hari] as $i => $sch)
                    @php $parsed = jpParseDoctorName($sch->doctor->nama_dokter); @endphp
                    <tr>
                        <td>
                            <button type="button" class="jp-doctor-trigger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#doctorProfileModal{{ $sch->doctor_id }}">
                                @if($sch->doctor->photo ?? null)
                                    <img src="{{ asset('storage/'.$sch->doctor->photo) }}" class="jp-avatar" alt="{{ $parsed['nama'] }}">
                                @else
                                    <div class="jp-avatar" style="background:{{ $avatarColors[$sch->doctor_id % count($avatarColors)] }};">
                                        {{ Str::upper(Str::substr(str_replace(['dr.','Dr.'],'',$parsed['nama']), 0, 1)) }}
                                    </div>
                                @endif
                                <span class="fw-600 jp-doctor-name" style="font-size:.875rem;">{{ $parsed['nama'] }}</span>
                            </button>
                        </td>
                        <td>
                            <span class="jp-poli-badge">{{ $sch->department->kode_poli }}</span>
                            <span class="ms-1">{{ $sch->department->nama_poli }}</span>
                        </td>
                        <td style="font-size:.82rem;">
                            <i class="bi bi-clock me-1 text-muted"></i>
                            {{ substr($sch->jam_mulai,0,5) }} — {{ substr($sch->jam_selesai,0,5) }}
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill" style="background:#eff6ff;color:#1e40af;padding:.35rem .6rem;">
                                {{ $sch->kuota }} pasien
                            </span>
                        </td>
                        <td class="text-center">
                            @if($sch->is_active)
                                <span class="badge rounded-pill" style="background:#d1fae5;color:#065f46;padding:.35rem .6rem;">
                                    <span class="jp-status-dot" style="background:#0f9d76;"></span>Aktif
                                </span>
                            @else
                                <span class="badge rounded-pill" style="background:#fee2e2;color:#991b1b;padding:.35rem .6rem;">
                                    <span class="jp-status-dot" style="background:#ef4444;"></span>Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('doctor-schedules.edit', $sch) }}" class="btn-icon-soft" style="background:#eff6ff;color:var(--primary);" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form action="{{ route('doctor-schedules.destroy', $sch) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon-soft" style="background:#fef2f2;color:#ef4444;" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-calendar-x" style="font-size:2.2rem;display:block;margin-bottom:8px;color:#d1d5db;"></i>
            Tidak ada jadwal praktik di hari {{ $hari }}.
        </div>
        @endif
    </div>
    @endforeach

</div>

@if($schedules->isEmpty())
<div class="card text-center py-5 fade-in mt-3">
    <i class="bi bi-calendar-x" style="font-size:3rem;color:#94a3b8;display:block;margin-bottom:12px;"></i>
    <p class="text-muted">Belum ada jadwal praktik. <a href="{{ route('doctor-schedules.create') }}">Tambah sekarang</a></p>
</div>
@endif

{{-- ===================== MODAL PROFIL DOKTER ===================== --}}
{{-- Satu modal per dokter unik, dibangun dari data jadwal yang sudah dimuat di halaman ini —
     tidak butuh request AJAX tambahan. --}}
@foreach($doctorProfiles as $doctorId => $doctorSchedules)
    @php
        $doctor = $doctorSchedules->first()->doctor;
        $parsed = jpParseDoctorName($doctor->nama_dokter);
        $poliList = $doctorSchedules->pluck('department.nama_poli')->filter()->unique();
        $sortedSchedules = $doctorSchedules->sortBy(fn($s) => array_search($s->hari, $urutanHari));
        $avatarColor = $avatarColors[$doctorId % count($avatarColors)];
    @endphp
    <div class="modal fade" id="doctorProfileModal{{ $doctorId }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">

                <div class="modal-body p-0">
                    <div class="p-4 d-flex align-items-center gap-3" style="background:#f8fdfb; border-bottom:1px solid #eef1f4;">
                        @if($doctor->photo ?? null)
                            <img src="{{ asset('storage/'.$doctor->photo) }}" class="jp-avatar-lg" alt="{{ $parsed['nama'] }}">
                        @else
                            <div class="jp-avatar-lg" style="background:{{ $avatarColor }};">
                                {{ Str::upper(Str::substr(str_replace(['dr.','Dr.'],'',$parsed['nama']), 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h6 class="mb-1 fw-700">{{ $parsed['nama'] }}</h6>
                            @if($parsed['spesialisasi'])
                                <span class="jp-profile-spec-badge">{{ $parsed['spesialisasi'] }}</span>
                            @endif
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="p-4">
                        <div class="mb-3">
                            <div class="small text-muted fw-600 mb-2" style="text-transform:uppercase; font-size:.7rem; letter-spacing:.04em;">
                                Poli Praktik
                            </div>
                            @forelse($poliList as $poli)
                                <span class="jp-modal-poli-chip">{{ $poli }}</span>
                            @empty
                                <span class="text-muted small">-</span>
                            @endforelse
                        </div>

                        <div>
                            <div class="small text-muted fw-600 mb-2" style="text-transform:uppercase; font-size:.7rem; letter-spacing:.04em;">
                                Jadwal Praktik Mingguan
                            </div>
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
                                    @foreach($sortedSchedules as $sc)
                                        <tr>
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
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn" style="background:var(--bg);color:#64748b;" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection

@push('scripts')
<script>
(function () {
    const tabs = document.querySelectorAll('.jp-tab-btn');
    const panels = document.querySelectorAll('[data-day-panel]');

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const day = this.dataset.day;

            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            panels.forEach(p => p.classList.toggle('active', p.dataset.dayPanel === day));
        });
    });
})();
</script>
@endpush