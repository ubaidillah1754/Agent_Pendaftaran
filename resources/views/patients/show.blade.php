@extends('layouts.app')
@section('title','Profil Pasien')
@section('page-title','Profil Pasien')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Data Pasien</a></li>
    <li class="breadcrumb-item active">{{ $patient->nama_pasien }}</li>
@endsection
@section('content')
<div class="row g-4">
    <!-- Card Profil -->
    <div class="col-lg-4">
        <div class="card fade-in text-center">
            <div class="card-body py-4">
                <div style="width:80px;height:80px;border-radius:22px;background:{{ $patient->jenis_kelamin==='L'?'var(--primary)':'#be185d' }};
                     color:#fff;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:900;margin:0 auto 16px;">
                    {{ strtoupper(substr($patient->nama_pasien,0,1)) }}
                </div>
                <h5 class="fw-800 mb-1">{{ $patient->nama_pasien }}</h5>
                <div class="text-muted" style="font-size:.82rem;">{{ $patient->jenis_kelamin_label }} · {{ $patient->umur }} tahun</div>

                <div class="mt-3 p-3 rounded-3" style="background:var(--bg);text-align:left;">
                    <div class="d-flex justify-content-between mb-1" style="font-size:.8rem;">
                        <span class="text-muted">No. RM</span>
                        <strong style="font-family:monospace;color:var(--primary);">{{ $patient->no_rm }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size:.8rem;">
                        <span class="text-muted">NIK</span>
                        <span style="font-family:monospace;">{{ $patient->nik }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size:.8rem;">
                        <span class="text-muted">Gol. Darah</span>
                        <span class="badge" style="background:var(--primary);color:#fff;">{{ $patient->golongan_darah }}</span>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size:.8rem;">
                        <span class="text-muted">Pembayaran</span>
                        <span class="badge" style="background:#d1fae5;color:#065f46;">{{ strtoupper($patient->jenis_pembayaran) }}</span>
                    </div>
                </div>

                <div class="mt-3 d-flex flex-column gap-2">
                    <a href="{{ route('registrations.create', ['patient_id'=>$patient->id]) }}" class="btn btn-accent w-100">
                        <i class="bi bi-clipboard2-plus me-1"></i>Daftarkan Rawat Jalan
                    </a>
                    <a href="{{ route('patients.edit', $patient) }}" class="btn w-100" style="background:#eff6ff;color:var(--primary);">
                        <i class="bi bi-pencil me-1"></i>Edit Data
                    </a>
                </div>
            </div>
        </div>

        <!-- Info Kontak -->
        <div class="card mt-3 fade-in">
            <div class="card-header"><i class="bi bi-telephone me-2"></i>Kontak</div>
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
                <div style="font-size:.83rem;">
                    <div class="text-muted">Wali / Penanggung Jawab</div>
                    <strong>{{ $patient->nama_wali }}</strong>
                    @if($patient->no_telepon_wali)
                        <div style="color:#64748b;">{{ $patient->no_telepon_wali }}</div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Riwayat Kunjungan -->
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
                            <th>No. Antrian</th>
                            <th>Poli</th>
                            <th>Dokter</th>
                            <th>Tanggal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patient->registrations as $reg)
                        <tr>
                            <td class="fw-900" style="color:var(--primary);font-size:1rem;">{{ $reg->nomor_antrian }}</td>
                            <td style="font-size:.82rem;">{{ $reg->department->nama_poli }}</td>
                            <td style="font-size:.82rem;">{{ $reg->doctor->nama_dokter }}</td>
                            <td style="font-size:.82rem;">{{ $reg->tanggal_daftar->format('d M Y') }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $reg->status }}">{{ $reg->status_label }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('registrations.show', $reg) }}" class="btn btn-sm btn-icon" style="background:#f0f9ff;color:#0891b2;">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-clipboard-x" style="font-size:2rem;display:block;"></i>
                            Belum ada riwayat kunjungan
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
