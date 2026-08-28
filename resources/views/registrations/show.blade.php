@extends('layouts.app')
@section('title','Detail Pendaftaran')
@section('page-title','Detail Pendaftaran')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('registrations.index') }}">Pendaftaran</a></li>
    <li class="breadcrumb-item active">{{ $registration->kode_booking }}</li>
@endsection

@push('styles')
<style>
    .card { border:1px solid #e7ece9; border-radius:16px; box-shadow:0 1px 3px rgba(15,23,20,.04); }
    .card-header { background:#fff; border-bottom:1px solid #eef2f0; font-weight:700; }
    .info-row { display:flex; justify-content:space-between; gap:12px; padding:.5rem 0; border-bottom:1px dashed #eef2f0; font-size:.83rem; }
    .info-row:last-child { border-bottom:none; }
    .info-row .label { color:#6b7c74; flex:0 0 40%; }
    .info-row .value { text-align:right; font-weight:600; color:#1f2d27; }
</style>
@endpush

@section('content')
<div class="row g-4 justify-content-center">
<div class="col-lg-8">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div></div>
        <span class="badge d-flex align-items-center" style="background:#eff6ff;color:var(--primary);font-weight:600;padding:.55rem .9rem;">
            <i class="bi bi-calendar3 me-2"></i>{{ now()->translatedFormat('l, d F Y') }}
        </span>
    </div>

    <!-- Nomor Antrean & Kode Booking Hero -->
    <div class="card mb-4 fade-in text-center" style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));color:#fff;border:none;border-radius:16px;position:relative;overflow:hidden;">
        <div style="position:absolute;inset:0;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64'%3E%3Cg fill='none' stroke='%23ffffff' stroke-width='1' opacity='0.08'%3E%3Crect x='16' y='16' width='32' height='32' transform='rotate(45 32 32)'/%3E%3Crect x='16' y='16' width='32' height='32'/%3E%3C/g%3E%3C/svg%3E&quot;);"></div>
        <div class="card-body py-4" style="position:relative;z-index:1;">
            <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.15em;opacity:.8;">
                <i class="bi bi-ticket-perforated me-1"></i>NOMOR ANTREAN
            </div>
            <div style="font-size:4.5rem;font-weight:900;line-height:1;letter-spacing:.05em;">{{ $registration->nomor_antrian }}</div>
            <div class="mt-2" style="font-size:.9rem;opacity:.9;">
                Kode Booking: <code style="color:#fef08a;font-weight:700;font-size:1.1rem;">{{ $registration->kode_booking }}</code>
            </div>
            <div class="mt-3 d-flex align-items-center justify-content-center gap-2" style="font-size:.85rem;opacity:.9;">
                <i class="bi bi-hospital"></i>{{ $registration->department->nama_poli }}
                <span>&middot;</span>
                <i class="bi bi-calendar-event"></i>{{ $registration->tanggal_kunjungan->format('d F Y') }}
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Info Pasien -->
        <div class="col-md-6">
            <div class="card h-100 fade-in">
                <div class="card-header d-flex align-items-center gap-2">
                    <span style="width:32px;height:32px;border-radius:9px;background:#eff6ff;color:var(--primary);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    Informasi Pasien
                </div>
                <div class="card-body py-3">
                    @php $p = $registration->patient; @endphp
                    <div class="info-row"><span class="label">No. RM</span><span class="value" style="color:var(--primary);font-family:monospace;">{{ $p->no_rm }}</span></div>
                    <div class="info-row"><span class="label">Nama</span><span class="value">{{ $p->nama_pasien }}</span></div>
                    <div class="info-row"><span class="label">NIK</span><span class="value" style="font-family:monospace;font-weight:400;">{{ $p->nik }}</span></div>
                    <div class="info-row"><span class="label">Tgl Lahir</span><span class="value" style="font-weight:400;">{{ $p->tanggal_lahir->format('d M Y') }} ({{ $p->umur }} thn)</span></div>
                    <div class="info-row"><span class="label">Jenis Kel.</span><span class="value" style="font-weight:400;">{{ $p->jenis_kelamin_label }}</span></div>
                    <div class="info-row">
                        <span class="label">Pembayaran</span>
                        <span class="value"><span class="badge" style="background:#d1fae5;color:#065f46;">{{ strtoupper($p->jenis_pembayaran) }}</span></span>
                    </div>
                    <div class="info-row"><span class="label">No. HP</span><span class="value" style="font-weight:400;">{{ $p->no_telepon ?? '-' }}</span></div>
                </div>
            </div>
        </div>

        <!-- Info Kunjungan -->
        <div class="col-md-6">
            <div class="card h-100 fade-in fade-in-delay-1">
                <div class="card-header d-flex align-items-center gap-2">
                    <span style="width:32px;height:32px;border-radius:9px;background:#d1fae5;color:#065f46;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-calendar2-check"></i>
                    </span>
                    Info Kunjungan
                </div>
                <div class="card-body py-3">
                    <div class="info-row"><span class="label">Poli</span><span class="value">{{ $registration->department->nama_poli }}</span></div>
                    <div class="info-row"><span class="label">Dokter</span><span class="value">{{ $registration->doctor->nama_dokter }}</span></div>
                    <div class="info-row">
                        <span class="label">Jadwal</span>
                        <span class="value" style="font-weight:400;">{{ $registration->doctorSchedule->hari }}, {{ substr($registration->doctorSchedule->jam_mulai,0,5) }}–{{ substr($registration->doctorSchedule->jam_selesai,0,5) }}</span>
                    </div>
                    <div class="info-row"><span class="label">Tgl Kunjungan</span><span class="value" style="font-weight:400;color:var(--primary);">{{ $registration->tanggal_kunjungan->format('d M Y') }}</span></div>
                    <div class="info-row"><span class="label">Tgl Daftar</span><span class="value" style="font-weight:400;">{{ $registration->tanggal_daftar->format('d M Y') }}</span></div>
                    <div class="info-row"><span class="label">Petugas</span><span class="value" style="font-weight:400;">{{ $registration->createdBy->name }}</span></div>
                    <div class="info-row"><span class="label">Waktu Daftar</span><span class="value" style="font-weight:400;">{{ $registration->created_at->format('d M Y H:i') }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2 flex-wrap align-items-center">
        <a href="{{ route('registrations.index') }}" class="btn" style="background:var(--bg);color:#64766D;border-radius:10px;">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        
        <div class="ms-auto d-flex gap-2 flex-wrap">
            <a href="{{ route('patients.show', $registration->patient) }}" class="btn" style="background:#eff6ff;color:var(--primary);border-radius:10px;">
                <i class="bi bi-person me-1"></i>Profil Pasien
            </a>
            <a href="{{ route('registrations.cetak', $registration) }}" target="_blank" class="btn" style="background:var(--primary);color:#fff;border-radius:10px;">
                <i class="bi bi-printer me-1"></i>Cetak Tracer
            </a>
        </div>
    </div>
</div>
</div>
@endsection