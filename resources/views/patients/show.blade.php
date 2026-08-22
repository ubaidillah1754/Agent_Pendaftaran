@extends('layouts.app')
@section('title', 'Profil Pasien')
@section('page-title', 'Profil Pasien')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Data Pasien</a></li>
    <li class="breadcrumb-item active">{{ $patient->nama_pasien }}</li>
@endsection
@section('content')

    {{-- ===== BANNER CETAK TRACER (muncul setelah pasien baru dibuat) ===== --}}
    @if(session('show_print_tracer'))
    <div class="alert alert-success alert-dismissible fade show mb-4 d-flex align-items-center gap-3"
         role="alert"
         style="border-left: 4px solid #0F7B63; border-radius: 14px; padding: 16px 20px; background: #ecfdf5;">
        <div style="width:44px; height:44px; border-radius:12px; background:#D1FAE5; color:#059669; display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex-shrink:0;">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-700" style="color:#065f46; font-size:.95rem;">
                Pasien <strong>{{ $patient->nama_pasien }}</strong> berhasil didaftarkan!
            </div>
            <div style="font-size:.82rem; color:#047857; margin-top:3px;">
                No. RM: <strong>{{ $patient->no_rm }}</strong> · Silakan cetak tracer untuk keperluan administrasi.
            </div>
        </div>
        <a href="{{ route('patients.tracer', $patient) }}"
           target="_blank"
           class="btn btn-sm d-flex align-items-center gap-1"
           style="background:#0F7B63; color:#fff; border-radius:9px; font-weight:700; padding:8px 16px; flex-shrink:0; text-decoration:none; white-space:nowrap;">
            <i class="bi bi-printer" aria-hidden="true"></i> Cetak Tracer
        </a>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
    @endif

    @php
        // ---- Fallbacks for fields that may not exist yet on the Patient model ----
        // If your `patients` table doesn't have these columns yet, add them via a
        // migration so the real values show up instead of the "-" placeholders.
        $noBpjs = $patient->no_bpjs ?? null;
        $agama = $patient->agama ?? null;
        $pekerjaan = $patient->pekerjaan ?? null;
        $statusNikah = $patient->status_pernikahan ?? null;
        $tempatLahir = $patient->tempat_lahir ?? null;
        $tanggalLahir = $patient->tanggal_lahir ?? null;
        $statusPasien = $patient->status_pasien ?? 'aktif';
        $lastReg = $patient->registrations->sortByDesc('tanggal_daftar')->first();
        $regsThisYear = $patient->registrations->filter(fn($r) => $r->tanggal_daftar && $r->tanggal_daftar->year === now()->year)->count();
    @endphp

    <div class="row g-4">

        {{-- ===== Header row: page date + quick action ===== --}}
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div></div>
            <div class="d-flex gap-2">
                <span class="badge d-flex align-items-center d-print-none"
                    style="background:#eff6ff;color:var(--primary);font-weight:600;padding:.55rem .9rem;">
                    <i class="bi bi-calendar3 me-2"></i>{{ now()->translatedFormat('l, d F Y') }}
                </span>
                <button onclick="window.print()" class="btn btn-primary d-print-none">
                    <i class="bi bi-printer me-1"></i>Cetak Profil
                </button>
                <a href="{{ route('patients.create') }}" class="btn btn-accent d-print-none">
                    <i class="bi bi-plus-lg me-1"></i>Daftar Pasien
                </a>
            </div>
        </div>

        {{-- ===== Hero: patient identity card ===== --}}
        <div class="col-12">
            <div class="card fade-in">
                <div class="card-body py-4 d-flex flex-wrap align-items-center gap-4">
                    <div
                        style="width:80px;height:80px;flex:0 0 auto;border-radius:22px;background:{{ $patient->jenis_kelamin === 'L' ? 'var(--primary)' : '#be185d' }};
                         color:#fff;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:900;">
                        {{ strtoupper(substr($patient->nama_pasien, 0, 1)) }}
                    </div>

                    <div class="flex-grow-1">
                        <h4 class="fw-800 mb-1">{{ $patient->nama_pasien }}</h4>
                        <div class="text-muted d-flex flex-wrap align-items-center gap-2" style="font-size:.85rem;">
                            <span><i class="bi bi-gender-ambiguous me-1"></i>{{ $patient->jenis_kelamin_label }}</span>
                            <span>&middot;</span>
                            <span><i class="bi bi-cake2 me-1"></i>{{ $patient->umur }} tahun</span>
                            @if($tanggalLahir)
                                <span>&middot;</span>
                                <span>{{ \Carbon\Carbon::parse($tanggalLahir)->format('d M Y') }}</span>
                            @endif
                            <span class="badge" style="background:#fef3c7;color:#92400e;">AB
                                {{ $patient->golongan_darah }}</span>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-2" style="font-size:.8rem;">
                            <span class="text-muted"><i
                                    class="bi bi-file-earmark-medical me-1"></i>{{ $patient->no_rm }}</span>
                            <span class="badge"
                                style="background:#d1fae5;color:#065f46;">{{ strtoupper($patient->jenis_pembayaran) }}</span>
                            <span class="badge"
                                style="background:{{ $statusPasien === 'aktif' ? '#dcfce7' : '#fee2e2' }};color:{{ $statusPasien === 'aktif' ? '#166534' : '#991b1b' }};">
                                {{ ucfirst($statusPasien) }}
                            </span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('patients.edit', $patient) }}" class="btn"
                            style="background:#eff6ff;color:var(--primary);">
                            <i class="bi bi-pencil me-1"></i>Edit Data
                        </a>
                        <a href="{{ route('registrations.create', ['patient_id' => $patient->id]) }}" class="btn btn-accent">
                            <i class="bi bi-clipboard2-plus me-1"></i>Daftar Rawat Jalan
                        </a>
                        @if(Route::has('patients.print'))
                            <a href="{{ route('patients.print', $patient) }}" class="btn" style="background:var(--bg);">
                                <i class="bi bi-printer me-1"></i>Cetak Kartu
                            </a>
                        @endif
                        @if(Route::has('patients.exportPdf'))
                            <a href="{{ route('patients.exportPdf', $patient) }}" class="btn" style="background:var(--bg);">
                                <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                            </a>
                        @endif
                        <div class="dropdown">
                            <button class="btn btn-icon" style="background:var(--bg);" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('patients.edit', $patient) }}"><i
                                            class="bi bi-pencil me-2"></i>Edit Data</a></li>
                                <li><a class="dropdown-item text-danger" href="#"
                                        onclick="event.preventDefault(); document.getElementById('delete-patient-form').submit();"><i
                                            class="bi bi-trash me-2"></i>Hapus Pasien</a></li>
                            </ul>
                            <form id="delete-patient-form" action="{{ route('patients.destroy', $patient) }}" method="POST"
                                class="d-none">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Quick stat cards ===== --}}
        <div class="col-6 col-lg-3">
            <div class="card fade-in h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="btn-icon"
                        style="width:44px;height:44px;background:#eff6ff;color:var(--primary);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-folder2-open"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.75rem;">Nomor Rekam Medis</div>
                        <div class="fw-800" style="font-family:monospace;">{{ $patient->no_rm }}</div>
                        <div class="text-muted" style="font-size:.7rem;">Terdaftar
                            {{ $patient->created_at?->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card fade-in h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="btn-icon"
                        style="width:44px;height:44px;background:#f0f9ff;color:#0891b2;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.75rem;">Total Kunjungan</div>
                        <div class="fw-800">{{ $patient->registrations->count() }}</div>
                        <div class="text-muted" style="font-size:.7rem;">
                            Kunjungan terakhir {{ $lastReg?->tanggal_daftar?->format('d M Y') ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card fade-in h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="btn-icon"
                        style="width:44px;height:44px;background:#dcfce7;color:#166534;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.75rem;">Status Pasien</div>
                        <div class="fw-800">{{ ucfirst($statusPasien) }}</div>
                        <div class="text-muted" style="font-size:.7rem;">Pasien terdaftar aktif</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card fade-in h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="btn-icon"
                        style="width:44px;height:44px;background:#fef3c7;color:#92400e;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-credit-card-2-front"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.75rem;">Pembayaran</div>
                        <div class="fw-800">{{ strtoupper($patient->jenis_pembayaran) }}</div>
                        <div class="text-muted" style="font-size:.7rem;">No. {{ $noBpjs ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Left column: Informasi Pasien + Kontak & Alamat ===== --}}
        <div class="col-lg-4">
            <div class="card fade-in">
                <div class="card-header"><i class="bi bi-person-vcard me-2"></i>Informasi Pasien</div>
                <div class="card-body py-3">
                    <div class="mb-2 d-flex justify-content-between" style="font-size:.83rem;">
                        <span class="text-muted">NIK</span><span style="font-family:monospace;">{{ $patient->nik }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between" style="font-size:.83rem;">
                        <span class="text-muted">No. BPJS</span><span
                            style="font-family:monospace;">{{ $noBpjs ?? '-' }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between" style="font-size:.83rem;">
                        <span class="text-muted">Tempat, Tgl Lahir</span>
                        <span>{{ $tempatLahir ?? '-' }}{{ $tanggalLahir ? ', ' . \Carbon\Carbon::parse($tanggalLahir)->format('d M Y') : '' }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between" style="font-size:.83rem;">
                        <span class="text-muted">Jenis Kelamin</span><span>{{ $patient->jenis_kelamin_label }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between" style="font-size:.83rem;">
                        <span class="text-muted">Golongan Darah</span>
                        <span class="badge"
                            style="background:var(--primary);color:#fff;">{{ $patient->golongan_darah }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between" style="font-size:.83rem;">
                        <span class="text-muted">Pembayaran</span>
                        <span class="badge"
                            style="background:#d1fae5;color:#065f46;">{{ strtoupper($patient->jenis_pembayaran) }}</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between" style="font-size:.83rem;">
                        <span class="text-muted">Status</span>
                        <span class="badge" style="background:#dcfce7;color:#166534;">{{ ucfirst($statusPasien) }}</span>
                    </div>

                    {{-- QR code placeholder: hook up a package such as simplesoftwareio/simple-qrcode
                    and swap this block for {!! QrCode::size(120)->generate(route('patients.show',$patient)) !!} --}}
                    <div class="d-flex justify-content-center mb-3">
                        <div
                            style="width:120px;height:120px;border:1px dashed #cbd5e1;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#94a3b8;">
                            <i class="bi bi-qr-code" style="font-size:2rem;"></i>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('patients.edit', $patient) }}" class="btn w-100"
                            style="background:#eff6ff;color:var(--primary);">
                            <i class="bi bi-pencil me-1"></i>Edit Data
                        </a>
                        <a href="{{ route('registrations.create', ['patient_id' => $patient->id]) }}"
                            class="btn btn-accent w-100">
                            <i class="bi bi-clipboard2-plus me-1"></i>Daftar Rawat Jalan
                        </a>
                        @if(Route::has('patients.print'))
                            <a href="{{ route('patients.print', $patient) }}" class="btn w-100" style="background:var(--bg);">
                                <i class="bi bi-printer me-1"></i>Cetak Kartu Pasien
                            </a>
                        @endif
                        @if(Route::has('patients.exportPdf'))
                            <a href="{{ route('patients.exportPdf', $patient) }}" class="btn w-100"
                                style="background:var(--bg);">
                                <i class="bi bi-download me-1"></i>Download PDF
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card mt-3 fade-in">
                <div class="card-header"><i class="bi bi-telephone me-2"></i>Kontak &amp; Alamat</div>
                <div class="card-body py-3">
                    <div class="mb-2" style="font-size:.83rem;">
                        <div class="text-muted">No. Telepon</div>
                        <strong>{{ $patient->no_telepon ?? '-' }}</strong>
                    </div>
                    <div class="mb-2" style="font-size:.83rem;">
                        <div class="text-muted">Alamat</div>
                        <div>{{ $patient->alamat }}</div>
                    </div>
                    @if($patient->nama_wali)
                        <div class="mb-2" style="font-size:.83rem;">
                            <div class="text-muted">Wali / Penanggung Jawab</div>
                            <strong>{{ $patient->nama_wali }}</strong>
                        </div>
                    @endif
                    @if($patient->no_telepon_wali)
                        <div style="font-size:.83rem;">
                            <div class="text-muted">No. Telepon Wali</div>
                            <strong>{{ $patient->no_telepon_wali }}</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== Middle column: Informasi Medis + Riwayat Kunjungan ===== --}}
        <div class="col-lg-5">
            <div class="card fade-in">
                <div class="card-header"><i class="bi bi-heart-pulse me-2"></i>Informasi Medis</div>
                <div class="card-body py-3">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-2" style="font-size:.83rem;">
                                <div class="text-muted">NIK</div><span
                                    style="font-family:monospace;">{{ $patient->nik }}</span>
                            </div>
                            <div class="mb-2" style="font-size:.83rem;">
                                <div class="text-muted">No. Rekam Medis</div><span
                                    style="font-family:monospace;">{{ $patient->no_rm }}</span>
                            </div>
                            <div class="mb-2" style="font-size:.83rem;">
                                <div class="text-muted">Tempat Lahir</div><span>{{ $tempatLahir ?? '-' }}</span>
                            </div>
                            <div class="mb-2" style="font-size:.83rem;">
                                <div class="text-muted">Tanggal Lahir</div>
                                <span>{{ $tanggalLahir ? \Carbon\Carbon::parse($tanggalLahir)->format('d M Y') : '-' }}</span>
                            </div>
                            <div class="mb-2" style="font-size:.83rem;">
                                <div class="text-muted">Jenis Kelamin</div><span>{{ $patient->jenis_kelamin_label }}</span>
                            </div>
                            <div style="font-size:.83rem;">
                                <div class="text-muted">Golongan Darah</div>
                                <span class="badge"
                                    style="background:var(--primary);color:#fff;">{{ $patient->golongan_darah }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2" style="font-size:.83rem;">
                                <div class="text-muted">Agama</div><span>{{ $agama ?? '-' }}</span>
                            </div>
                            <div class="mb-2" style="font-size:.83rem;">
                                <div class="text-muted">Pekerjaan</div><span>{{ $pekerjaan ?? '-' }}</span>
                            </div>
                            <div class="mb-2" style="font-size:.83rem;">
                                <div class="text-muted">Status Pernikahan</div><span>{{ $statusNikah ?? '-' }}</span>
                            </div>
                            <div class="mb-2" style="font-size:.83rem;">
                                <div class="text-muted">No. Telepon</div><span>{{ $patient->no_telepon ?? '-' }}</span>
                            </div>
                            <div class="mb-2" style="font-size:.83rem;">
                                <div class="text-muted">Pembayaran</div>
                                <span class="badge"
                                    style="background:#d1fae5;color:#065f46;">{{ strtoupper($patient->jenis_pembayaran) }}</span>
                            </div>
                            <div style="font-size:.83rem;">
                                <div class="text-muted">No. BPJS</div><span
                                    style="font-family:monospace;">{{ $noBpjs ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div style="font-size:.83rem;">
                        <div class="text-muted">Alamat</div>
                        <div>{{ $patient->alamat }}</div>
                    </div>
                </div>
            </div>

            <div class="card mt-3 fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history me-2"></i>Riwayat Kunjungan</span>
                    <span class="badge" style="background:var(--primary);color:#fff;">{{ $patient->registrations->count() }}
                        kunjungan</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Antrian</th>
                                <th>Poli</th>
                                <th>Dokter</th>
                                <th>Diagnosa</th>
                                <th>Pembayaran</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patient->registrations->sortByDesc('tanggal_daftar') as $reg)
                                <tr>
                                    <td style="font-size:.82rem;">{{ $reg->tanggal_daftar->format('d M Y') }}<br><span
                                            class="text-muted"
                                            style="font-size:.72rem;">{{ $reg->tanggal_daftar->format('H:i') }} WIB</span></td>
                                    <td class="fw-900" style="color:var(--primary);font-size:1rem;">{{ $reg->nomor_antrian }}
                                    </td>
                                    <td style="font-size:.82rem;">{{ $reg->department->nama_poli }}</td>
                                    <td style="font-size:.82rem;">{{ $reg->doctor->nama_dokter }}</td>
                                    <td style="font-size:.82rem;">{{ $reg->diagnosa ?? '-' }}</td>
                                    <td style="font-size:.82rem;">
                                        <span class="badge"
                                            style="background:#d1fae5;color:#065f46;">{{ strtoupper($reg->jenis_pembayaran ?? $patient->jenis_pembayaran) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $reg->status }}">{{ $reg->status_label }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('registrations.show', $reg) }}" class="btn btn-sm btn-icon"
                                            style="background:#f0f9ff;color:#0891b2;">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-clipboard-x" style="font-size:2rem;display:block;"></i>
                                        Belum ada riwayat kunjungan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($patient->registrations->count() > 0)
                    <div class="card-footer d-flex justify-content-between align-items-center" style="font-size:.8rem;">
                        <span class="text-muted">Menampilkan 1 - {{ $patient->registrations->count() }} dari
                            {{ $patient->registrations->count() }} data</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- ===== Right column: Ringkasan Cepat + Riwayat Aktivitas ===== --}}
        <div class="col-lg-3">
            <div class="card fade-in">
                <div class="card-header"><i class="bi bi-lightning-charge me-2"></i>Ringkasan Cepat</div>
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="btn-icon"
                            style="width:34px;height:34px;background:#eff6ff;color:var(--primary);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-collection"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted" style="font-size:.72rem;">Total Kunjungan</div>
                            <div class="fw-800" style="font-size:.85rem;">{{ $patient->registrations->count() }} kali</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="btn-icon"
                            style="width:34px;height:34px;background:#f0f9ff;color:#0891b2;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted" style="font-size:.72rem;">Kunjungan Tahun Ini</div>
                            <div class="fw-800" style="font-size:.85rem;">{{ $regsThisYear }} kali</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="btn-icon"
                            style="width:34px;height:34px;background:#fef3c7;color:#92400e;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-hospital"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted" style="font-size:.72rem;">Poli Terakhir</div>
                            <div class="fw-800" style="font-size:.85rem;">{{ $lastReg?->department?->nama_poli ?? '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="btn-icon"
                            style="width:34px;height:34px;background:#dcfce7;color:#166534;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted" style="font-size:.72rem;">Dokter Terakhir</div>
                            <div class="fw-800" style="font-size:.85rem;">{{ $lastReg?->doctor?->nama_dokter ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="btn-icon"
                            style="width:34px;height:34px;background:#fee2e2;color:#991b1b;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted" style="font-size:.72rem;">Kunjungan Berikutnya</div>
                            <div class="fw-800" style="font-size:.85rem;">-</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Activity timeline: built from registration timestamps. For a true
            audit trail (status changes, who did what), log entries to an
            `activity_logs` table and loop over that collection instead. --}}
            <div class="card mt-3 fade-in">
                <div class="card-header"><i class="bi bi-activity me-2"></i>Riwayat Aktivitas</div>
                <div class="card-body py-3">
                    @forelse($patient->registrations->sortByDesc('tanggal_daftar')->take(5) as $reg)
                        <div class="d-flex gap-2 mb-3">
                            <div class="text-muted" style="font-size:.7rem;white-space:nowrap;padding-top:2px;">
                                {{ $reg->tanggal_daftar->format('d M Y') }}<br>{{ $reg->tanggal_daftar->format('H:i') }}
                            </div>
                            <div class="flex-grow-1 ps-2" style="border-left:2px solid #e2e8f0;">
                                <div class="fw-700" style="font-size:.8rem;">Terdaftar Rawat Jalan</div>
                                <div class="text-muted" style="font-size:.75rem;">{{ $reg->department->nama_poli }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3" style="font-size:.82rem;">
                            Belum ada aktivitas
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        @media print {

            #sidebar,
            #topbar,
            .breadcrumb,
            .d-print-none,
            .btn {
                display: none !important;
            }

            #main-content {
                margin-left: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }

            body {
                background: #fff !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ccc !important;
            }
        }
    </style>
@endpush