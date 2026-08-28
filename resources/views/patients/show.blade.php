@extends('layouts.app')
@section('title', 'Profil Pasien')
@section('page-title', 'Profil Pasien')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Data Pasien</a></li>
    <li class="breadcrumb-item active">{{ $patient->nama_pasien }}</li>
@endsection
@section('content')

    @php
        $noBpjs = $patient->no_bpjs ?? null;
        $tempatLahir = $patient->tempat_lahir ?? null;
        $tanggalLahir = $patient->tanggal_lahir ?? null;
        $lastReg = $patient->registrations->sortByDesc('tanggal_daftar')->first();
        $regsThisYear = $patient->registrations->filter(fn($r) => $r->tanggal_daftar && $r->tanggal_daftar->year === now()->year)->count();
    @endphp

    <div class="row g-4">

        {{-- ===== Header row: page date ===== --}}
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
                            <span class="badge" style="background:#fef3c7;color:#92400e;">AB {{ $patient->golongan_darah }}</span>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-2" style="font-size:.8rem;">
                            <span class="text-muted"><i class="bi bi-file-earmark-medical me-1"></i>{{ $patient->no_rm }}</span>
                            <span class="badge" style="background:#d1fae5;color:#065f46;">{{ strtoupper($patient->jenis_pembayaran) }}</span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('registrations.create', ['patient_id' => $patient->id]) }}" class="btn btn-accent">
                            <i class="bi bi-clipboard2-plus me-1"></i>Daftar Rawat Jalan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Quick stat cards ===== --}}
        <div class="col-6 col-lg-4">
            <div class="card fade-in h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="btn-icon"
                        style="width:44px;height:44px;background:#eff6ff;color:var(--primary);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-folder2-open"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.75rem;">Nomor Rekam Medis</div>
                        <div class="fw-800" style="font-family:monospace;">{{ $patient->no_rm }}</div>
                        <div class="text-muted" style="font-size:.7rem;">Terdaftar {{ $patient->created_at?->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
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
        <div class="col-12 col-lg-4">
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
                        <span class="text-muted">No. BPJS</span><span style="font-family:monospace;">{{ $noBpjs ?? '-' }}</span>
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
                        <span class="badge" style="background:var(--primary);color:#fff;">{{ $patient->golongan_darah }}</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between" style="font-size:.83rem;">
                        <span class="text-muted">Pembayaran</span>
                        <span class="badge" style="background:#d1fae5;color:#065f46;">{{ strtoupper($patient->jenis_pembayaran) }}</span>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('registrations.create', ['patient_id' => $patient->id]) }}" class="btn btn-accent w-100">
                            <i class="bi bi-clipboard2-plus me-1"></i>Daftar Rawat Jalan
                        </a>
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

        {{-- ===== Middle column: Riwayat Kunjungan ===== --}}
        <div class="col-lg-8">
            <div class="card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history me-2"></i>Riwayat Kunjungan</span>
                    <span class="badge" style="background:var(--primary);color:#fff;">{{ $patient->registrations->count() }} kunjungan</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Antrean</th>
                                <th>Kode Booking</th>
                                <th>Poli</th>
                                <th>Dokter</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patient->registrations->sortByDesc('tanggal_daftar') as $reg)
                                <tr>
                                    <td style="font-size:.82rem;">{{ $reg->tanggal_daftar->format('d M Y') }}</td>
                                    <td><span class="badge bg-success fs-6">{{ $reg->nomor_antrian }}</span></td>
                                    <td class="fw-700" style="color:var(--primary);font-size:.9rem;font-family:monospace;">{{ $reg->kode_booking }}</td>
                                    <td style="font-size:.82rem;">{{ $reg->department->nama_poli }}</td>
                                    <td style="font-size:.82rem;">{{ $reg->doctor->nama_dokter }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('registrations.show', $reg) }}" class="btn btn-sm btn-icon" style="background:#f0f9ff;color:#0891b2;" title="Detail">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('registrations.cetak', $reg) }}" target="_blank" class="btn btn-sm btn-icon" style="background:#f0fdf4;color:#059669;" title="Cetak Tracer">
                                            <i class="bi bi-printer-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-clipboard-x" style="font-size:2rem;display:block;"></i>
                                        Belum ada riwayat kunjungan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        @media print {
            #sidebar, #topbar, .breadcrumb, .d-print-none, .btn { display: none !important; }
            #main-content { margin-left: 0 !important; padding: 0 !important; background: #fff !important; }
            body { background: #fff !important; }
            .card { box-shadow: none !important; border: 1px solid #ccc !important; }
        }
    </style>
@endpush