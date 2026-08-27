@extends('layouts.app')
@section('title','Edit Pendaftaran')
@section('page-title','Edit Pendaftaran')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('registrations.index') }}">Pendaftaran</a></li>
    <li class="breadcrumb-item"><a href="{{ route('registrations.show', $registration) }}">{{ $registration->kode_booking }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@push('styles')
<style>
    .edit-hero {
        position:relative;overflow:hidden;border-radius:18px;
        padding:26px 30px;margin-bottom:24px;
        background:linear-gradient(120deg,var(--primary-dark,#063D2C) 0%,var(--primary,#0B6B4F) 100%);
        color:#fff;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;
    }
    .edit-hero::before {
        content:"";position:absolute;inset:0;z-index:0;pointer-events:none;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='84' height='84'%3E%3Cg fill='none' stroke='%23E8C766' stroke-width='1' opacity='0.1'%3E%3Cpath d='M42 4 L80 42 L42 80 L4 42 Z'/%3E%3Ccircle cx='42' cy='42' r='18'/%3E%3C/g%3E%3C/svg%3E");
    }
    .edit-hero .hero-text { position:relative;z-index:1; }
    .edit-hero h4 { font-weight:800;font-size:1.2rem;margin:0 0 4px; }
    .edit-hero p  { margin:0;font-size:.82rem;color:rgba(255,255,255,.72); }
    .edit-hero .antrian-badge {
        position:relative;z-index:1;background:rgba(255,255,255,.15);
        border:1px solid rgba(255,255,255,.25);border-radius:12px;
        padding:10px 22px;text-align:center;
    }
    .edit-hero .antrian-badge .num { font-size:2rem;font-weight:900;line-height:1; }
    .edit-hero .antrian-badge .lbl { font-size:.68rem;opacity:.7;text-transform:uppercase;letter-spacing:.08em; }

    .info-row  { display:flex;justify-content:space-between;gap:12px;padding:.45rem 0;border-bottom:1px dashed #eef2f0;font-size:.83rem; }
    .info-row:last-child { border-bottom:none; }
    .info-row .label { color:#6b7c74;flex:0 0 42%; }
    .info-row .value { text-align:right;font-weight:600;color:#1f2d27; }
    .form-label { font-weight:600;font-size:.83rem;color:#374151; }
    .form-control, .form-select { border-radius:10px; }
</style>
@endpush

@section('content')
<div class="row g-4 justify-content-center">
<div class="col-lg-8">

    {{-- Hero --}}
    <div class="edit-hero fade-in">
        <div class="hero-text">
            <h4><i class="bi bi-pencil-square me-2"></i>Edit Pendaftaran</h4>
            <p>Formulir ubah data pendaftaran pasien</p>
        </div>
        <div class="antrian-badge">
            <div class="num">{{ $registration->kode_booking }}</div>
            <div class="lbl">No. Antrian</div>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger fade-in mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row g-3">
        {{-- Info Pasien (readonly) --}}
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
                    <div class="info-row"><span class="label">Jenis Kel.</span><span class="value" style="font-weight:400;">{{ $p->jenis_kelamin_label }}</span></div>
                    <div class="info-row">
                        <span class="label">Pembayaran</span>
                        <span class="value"><span class="badge" style="background:#d1fae5;color:#065f46;">{{ strtoupper($p->jenis_pembayaran) }}</span></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Kunjungan (readonly) --}}
        <div class="col-md-6">
            <div class="card h-100 fade-in">
                <div class="card-header d-flex align-items-center gap-2">
                    <span style="width:32px;height:32px;border-radius:9px;background:#d1fae5;color:#065f46;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-calendar2-check"></i>
                    </span>
                    Info Kunjungan
                </div>
                <div class="card-body py-3">
                    <div class="info-row"><span class="label">Poli</span><span class="value">{{ $registration->department->nama_poli }}</span></div>
                    <div class="info-row"><span class="label">Dokter</span><span class="value">{{ $registration->doctor->nama_dokter }}</span></div>
                    <div class="info-row"><span class="label">Jadwal</span><span class="value" style="font-weight:400;">{{ $registration->doctorSchedule->hari }}</span></div>
                    <div class="info-row"><span class="label">Tgl Kunjungan</span><span class="value" style="color:var(--primary);">{{ $registration->tanggal_kunjungan->format('d M Y') }}</span></div>
                    <div class="info-row">
                        <span class="label">Status</span>
                        <span class="value">
                            @php
                                $statusColors = ['menunggu'=>'#fef9c3,#854d0e','diperiksa'=>'#dbeafe,#1e40af','selesai'=>'#d1fae5,#065f46','batal'=>'#fee2e2,#991b1b'];
                                [$bg,$fg] = explode(',', $statusColors[$registration->status] ?? '#f3f4f6,#374151');
                            @endphp
                            <span class="badge" style="background:{{ $bg }};color:{{ $fg }};">{{ $registration->status_label }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Edit --}}
        <div class="col-12">
            <div class="card fade-in">
                <div class="card-header d-flex align-items-center gap-2">
                    <span style="width:32px;height:32px;border-radius:9px;background:#fef3c7;color:#d97706;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-pencil-fill"></i>
                    </span>
                    Data yang Dapat Diubah
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('registrations.update', $registration) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('registrations.show', $registration) }}"
                               class="btn"
                               style="background:var(--bg);color:#64766D;border-radius:10px;">
                                <i class="bi bi-arrow-left me-1"></i>Batal
                            </a>
                            <button type="submit"
                                    class="btn btn-primary ms-auto"
                                    style="border-radius:10px;min-width:140px;">
                                <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
@endsection
