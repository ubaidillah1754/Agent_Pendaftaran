@extends('layouts.app')
@section('title','Detail Pendaftaran')
@section('page-title','Detail Pendaftaran')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('registrations.index') }}">Pendaftaran</a></li>
    <li class="breadcrumb-item active">{{ $registration->nomor_antrian }}</li>
@endsection
@section('content')
<div class="row g-4 justify-content-center">
<div class="col-lg-8">

    <!-- Nomor Antrian Hero -->
    <div class="card mb-4 fade-in text-center" style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));color:#fff;border:none;position:relative;overflow:hidden;">
        <div style="position:absolute;inset:0;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64'%3E%3Cg fill='none' stroke='%23ffffff' stroke-width='1' opacity='0.08'%3E%3Crect x='16' y='16' width='32' height='32' transform='rotate(45 32 32)'/%3E%3Crect x='16' y='16' width='32' height='32'/%3E%3C/g%3E%3C/svg%3E&quot;);"></div>
        <div class="card-body py-4" style="position:relative;z-index:1;">
            <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.15em;opacity:.65;">Nomor Antrian</div>
            <div style="font-size:5rem;font-weight:900;line-height:1;letter-spacing:.05em;">{{ $registration->nomor_antrian }}</div>
            <span class="badge mt-2" style="background:rgba(232,199,102,.28);border:1px solid rgba(232,199,102,.5);font-size:.85rem;padding:6px 16px;">
                {{ $registration->status_label }}
            </span>
            <div class="mt-3" style="font-size:.82rem;opacity:.75;">
                {{ $registration->department->nama_poli }} · {{ $registration->tanggal_daftar->format('d F Y') }}
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Info Pasien -->
        <div class="col-md-6">
            <div class="card h-100 fade-in">
                <div class="card-header"><i class="bi bi-person-fill me-2"></i>Informasi Pasien</div>
                <div class="card-body">
                    @php $p = $registration->patient; @endphp
                    <table class="table table-borderless mb-0" style="font-size:.85rem;">
                        <tr><td class="text-muted fw-600 ps-0" width="40%">No. RM</td><td class="fw-700" style="color:var(--primary);">{{ $p->no_rm }}</td></tr>
                        <tr><td class="text-muted fw-600 ps-0">Nama</td><td class="fw-600">{{ $p->nama_pasien }}</td></tr>
                        <tr><td class="text-muted fw-600 ps-0">NIK</td><td style="font-family:monospace;">{{ $p->nik }}</td></tr>
                        <tr><td class="text-muted fw-600 ps-0">Tgl Lahir</td><td>{{ $p->tanggal_lahir->format('d M Y') }} ({{ $p->umur }} thn)</td></tr>
                        <tr><td class="text-muted fw-600 ps-0">Jenis Kel.</td><td>{{ $p->jenis_kelamin_label }}</td></tr>
                        <tr><td class="text-muted fw-600 ps-0">Pembayaran</td><td><span class="badge" style="background:#eaf4ef;color:var(--primary);">{{ strtoupper($p->jenis_pembayaran) }}</span></td></tr>
                        <tr><td class="text-muted fw-600 ps-0">No. HP</td><td>{{ $p->no_telepon ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Info Kunjungan -->
        <div class="col-md-6">
            <div class="card h-100 fade-in fade-in-delay-1">
                <div class="card-header"><i class="bi bi-calendar2-check me-2"></i>Info Kunjungan</div>
                <div class="card-body">
                    <table class="table table-borderless mb-0" style="font-size:.85rem;">
                        <tr><td class="text-muted fw-600 ps-0" width="40%">Poli</td><td class="fw-600">{{ $registration->department->nama_poli }}</td></tr>
                        <tr><td class="text-muted fw-600 ps-0">Dokter</td><td class="fw-600">{{ $registration->doctor->nama_dokter }}</td></tr>
                        <tr><td class="text-muted fw-600 ps-0">Jadwal</td>
                            <td>{{ $registration->doctorSchedule->hari }}, {{ substr($registration->doctorSchedule->jam_mulai,0,5) }}–{{ substr($registration->doctorSchedule->jam_selesai,0,5) }}</td>
                        </tr>
                        <tr><td class="text-muted fw-600 ps-0">Tgl Daftar</td><td>{{ $registration->tanggal_daftar->format('d M Y') }}</td></tr>
                        <tr><td class="text-muted fw-600 ps-0">Urutan</td><td>#{{ $registration->urutan_antrian }}</td></tr>
                        <tr><td class="text-muted fw-600 ps-0">Keluhan</td><td>{{ $registration->keluhan ?? '-' }}</td></tr>
                        <tr><td class="text-muted fw-600 ps-0">Petugas</td><td>{{ $registration->createdBy->name }}</td></tr>
                        <tr><td class="text-muted fw-600 ps-0">Waktu Daftar</td><td>{{ $registration->created_at->format('d M Y H:i') }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2 flex-wrap">
        <a href="{{ route('registrations.index') }}" class="btn" style="background:var(--bg);color:#64766D;">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        @if($registration->status === 'menunggu')
        <form action="{{ route('registrations.batal', $registration) }}" method="POST" onsubmit="return confirm('Batalkan pendaftaran ini?')">
            @csrf @method('PATCH')
            <button class="btn" style="background:#fef2f2;color:#ef4444;"><i class="bi bi-x-circle me-1"></i>Batalkan</button>
        </form>
        @endif
        <a href="{{ route('patients.show', $registration->patient) }}" class="btn" style="background:#eaf4ef;color:var(--primary);">
            <i class="bi bi-person me-1"></i>Profil Pasien
        </a>
    </div>
</div>
</div>
@endsection