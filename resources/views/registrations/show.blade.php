@extends('layouts.app')
@section('title','Konfirmasi Pendaftaran')
@section('page-title','Konfirmasi Pendaftaran')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('registrations.index') }}">Pendaftaran</a></li>
    <li class="breadcrumb-item active">{{ $registration->kode_booking }}</li>
@endsection

@push('styles')
<style>
    /* ── Card base ── */
    .card { border:1px solid #e7ece9; border-radius:16px; box-shadow:0 2px 8px rgba(15,123,99,.06); }
    .card-header { background:#fff; border-bottom:1px solid #eef2f0; font-weight:700; padding:14px 20px; }
    .card-body { padding:20px; }

    /* ── Step bar ── */
    .step-bar {
        display: flex; align-items: center;
        background: #fff; border: 1px solid #e7ece9; border-radius: 14px;
        padding: 14px 20px; margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(15,123,99,.06);
    }
    .step-item { display: flex; align-items: center; gap: 10px; flex: 1; }
    .step-circle {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .8rem; font-weight: 800; flex-shrink: 0;
    }
    .step-circle.done  { background: var(--primary); color: #fff; }
    .step-circle.active{ background: #fef3c7; color: #d97706; border: 2px solid #fcd34d; }
    .step-circle.idle  { background: #f1f5f9; color: #94a3b8; }
    .step-label { font-size: .78rem; font-weight: 700; line-height: 1.25; }
    .step-label small { display:block; font-weight: 400; color: #94a3b8; font-size: .68rem; }
    .step-div { flex: 0 0 28px; height: 2px; background: #e7ece9; border-radius: 1px; margin: 0 4px; }
    .step-div.done { background: var(--primary); }

    /* ── Hero antrian ── */
    .hero-antrian {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 55%, #14966e 100%);
        border-radius: 18px; color: #fff; padding: 26px 24px;
        position: relative; overflow: hidden; margin-bottom: 20px;
        box-shadow: 0 8px 28px rgba(15,123,99,.28);
    }
    .hero-antrian::before {
        content: '';
        position: absolute; inset: 0;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80'%3E%3Cg fill='none' stroke='%23ffffff' stroke-width='1.2' opacity='0.07'%3E%3Ccircle cx='40' cy='40' r='30'/%3E%3Ccircle cx='40' cy='40' r='18'/%3E%3Ccircle cx='40' cy='40' r='6'/%3E%3C/g%3E%3C/svg%3E") repeat;
    }
    .hero-inner { position: relative; z-index: 1; }
    .hero-label { font-size: .64rem; font-weight: 700; text-transform: uppercase; letter-spacing: .14em; opacity: .75; margin-bottom: 2px; }
    .hero-number { font-size: 3.8rem; font-weight: 900; line-height: 1; letter-spacing: .04em; text-shadow: 0 2px 12px rgba(0,0,0,.2); }
    .hero-booking { margin-top: 6px; font-size: .85rem; opacity: .85; }
    .hero-booking code { color: #fef08a; font-weight: 800; font-size: .98rem; letter-spacing: 1px; }
    .hero-meta {
        margin-top: 14px; padding-top: 14px;
        border-top: 1px solid rgba(255,255,255,.2);
        display: flex; gap: 20px; flex-wrap: wrap;
    }
    .hero-meta-item { display: flex; align-items: center; gap: 6px; font-size: .8rem; opacity: .9; }

    /* ── Info rows ── */
    .info-row {
        display: flex; justify-content: space-between; align-items: center;
        gap: 12px; padding: .55rem 0; border-bottom: 1px dashed #eef2f0; font-size: .83rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row .label { color: #6b7c74; flex: 0 0 46%; font-size: .79rem; }
    .info-row .value { text-align: right; font-weight: 600; color: #1f2d27; }

    /* ── Action box ── */
    .action-box {
        border: 2px solid #a7f3d0;
        border-radius: 18px;
        background: linear-gradient(135deg, #f0fdf8 0%, #ecfdf5 100%);
        padding: 24px; margin-top: 20px;
        box-shadow: 0 4px 16px rgba(15,123,99,.08);
    }
    .action-box-title {
        text-align: center; font-size: .72rem; font-weight: 800;
        text-transform: uppercase; letter-spacing: .12em;
        color: var(--primary); margin-bottom: 4px;
        display: flex; align-items: center; justify-content: center; gap: 6px;
    }
    .action-box-sub { text-align: center; font-size: .8rem; color: #5a7367; margin-bottom: 20px; }
    .action-buttons { display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; align-items: center; }

    /* ── Buttons ── */
    .btn-cetak {
        background: var(--primary); color: #fff;
        border-radius: 12px; padding: .75rem 2rem;
        font-weight: 800; font-size: .9rem; border: none;
        text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
        transition: all .2s; box-shadow: 0 4px 16px rgba(15,123,99,.3);
    }
    .btn-cetak:hover { background: var(--primary-dark); color: #fff; transform: translateY(-2px); box-shadow: 0 8px 22px rgba(15,123,99,.38); }
    .btn-cetak:active { transform: translateY(0); }

    .btn-batal {
        background: #fff; color: #ef4444;
        border-radius: 12px; padding: .72rem 1.5rem;
        font-weight: 700; font-size: .88rem;
        border: 1.5px solid #fca5a5;
        display: inline-flex; align-items: center; gap: 8px;
        transition: all .2s; cursor: pointer;
        box-shadow: 0 2px 8px rgba(239,68,68,.08);
    }
    .btn-batal:hover { background: #fef2f2; border-color: #f87171; transform: translateY(-1px); }

    .btn-kembali {
        background: transparent; color: #64766D;
        border-radius: 12px; padding: .72rem 1.2rem;
        font-size: .85rem; border: 1px solid #d9e6de;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        transition: all .2s;
    }
    .btn-kembali:hover { background: #f0f5f2; color: #3d5248; }

    .section-icon {
        width: 32px; height: 32px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .alert-reg-success {
        background: linear-gradient(120deg, #d1fae5, #a7f3d0);
        border: 1.5px solid #6ee7b7; border-radius: 12px;
        padding: 12px 18px; margin-bottom: 20px;
        display: flex; align-items: center; gap: 12px;
        font-size: .84rem; color: #064e3b; font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="row g-4 justify-content-center">
<div class="col-xl-9 col-lg-10">

    {{-- Date badge --}}
    <div class="d-flex justify-content-end mb-2">
        <span class="badge d-flex align-items-center" style="background:#eff6ff;color:var(--primary);font-weight:600;padding:.5rem .9rem;border-radius:10px;">
            <i class="bi bi-calendar3 me-2"></i>{{ now()->translatedFormat('l, d F Y') }}
        </span>
    </div>

    {{-- ── Step Bar ── --}}
    <div class="step-bar fade-in">
        <div class="step-item">
            <div class="step-circle done"><i class="bi bi-check-lg"></i></div>
            <div class="step-label">Form Diisi<small>Data pasien & jadwal</small></div>
        </div>
        <div class="step-div done"></div>
        <div class="step-item">
            <div class="step-circle done"><i class="bi bi-check-lg"></i></div>
            <div class="step-label">Tersimpan<small>Antrean dibuat</small></div>
        </div>
        <div class="step-div done"></div>
        <div class="step-item">
            <div class="step-circle active"><i class="bi bi-printer"></i></div>
            <div class="step-label">Cetak Tracer<small>Konfirmasi & cetak</small></div>
        </div>
    </div>

    {{-- ── Flash session ── --}}
    @if(session('success'))
        <div class="alert-reg-success fade-in">
            <i class="bi bi-check-circle-fill" style="color:#059669;font-size:1.1rem;flex-shrink:0;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- ── Hero Antrian ── --}}
    <div class="hero-antrian fade-in">
        <div class="hero-inner">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="hero-label"><i class="bi bi-ticket-perforated me-1"></i> Nomor Antrean</div>
                    <div class="hero-number">{{ $registration->nomor_antrian }}</div>
                    <div class="hero-booking">
                        Kode Booking: <code>{{ $registration->kode_booking }}</code>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:.63rem;opacity:.7;text-transform:uppercase;letter-spacing:.1em;font-weight:600;">Terdaftar</div>
                    <div style="font-size:1.1rem;font-weight:800;margin-top:2px;">{{ $registration->tanggal_daftar->format('d M Y') }}</div>
                    <div style="font-size:.75rem;opacity:.75;margin-top:2px;">{{ $registration->created_at->format('H:i') }} WIB</div>
                </div>
            </div>
            <div class="hero-meta">
                <div class="hero-meta-item"><i class="bi bi-hospital-fill"></i> {{ $registration->department->nama_poli }}</div>
                <div class="hero-meta-item"><i class="bi bi-person-badge-fill"></i> {{ $registration->doctor->nama_dokter }}</div>
                <div class="hero-meta-item"><i class="bi bi-calendar-event-fill"></i> Kunjungan: {{ $registration->tanggal_kunjungan->translatedFormat('d M Y') }}</div>
            </div>
        </div>
    </div>

    {{-- ── Info Cards ── --}}
    <div class="row g-3">
        {{-- Info Pasien --}}
        <div class="col-md-6">
            <div class="card h-100 fade-in">
                <div class="card-header d-flex align-items-center gap-2">
                    <div class="section-icon" style="background:#eff6ff;color:var(--primary);"><i class="bi bi-person-fill"></i></div>
                    <span style="font-size:.9rem;">Informasi Pasien</span>
                </div>
                <div class="card-body">
                    @php $p = $registration->patient; @endphp
                    <div class="info-row">
                        <span class="label">No. Rekam Medis</span>
                        <span class="value" style="color:var(--primary);font-family:monospace;font-size:.8rem;">{{ $p->no_rm }}</span>
                    </div>
                    <div class="info-row"><span class="label">Nama Pasien</span><span class="value">{{ $p->nama_pasien }}</span></div>
                    <div class="info-row">
                        <span class="label">NIK</span>
                        <span class="value" style="font-family:monospace;font-weight:500;font-size:.76rem;">{{ $p->nik }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Tgl Lahir</span>
                        <span class="value" style="font-weight:500;">{{ $p->tanggal_lahir->format('d M Y') }} <span style="color:#94a3b8;font-size:.73rem;">({{ $p->umur }} thn)</span></span>
                    </div>
                    <div class="info-row"><span class="label">Jenis Kelamin</span><span class="value" style="font-weight:500;">{{ $p->jenis_kelamin_label }}</span></div>
                    <div class="info-row">
                        <span class="label">Pembayaran</span>
                        <span class="value">
                            <span style="background:#d1fae5;color:#065f46;padding:2px 10px;border-radius:999px;font-size:.72rem;font-weight:700;">{{ strtoupper($p->jenis_pembayaran) }}</span>
                        </span>
                    </div>
                    <div class="info-row"><span class="label">No. Telepon</span><span class="value" style="font-weight:500;">{{ $p->no_telepon ?? '—' }}</span></div>
                </div>
            </div>
        </div>

        {{-- Info Kunjungan --}}
        <div class="col-md-6">
            <div class="card h-100 fade-in fade-in-delay-1">
                <div class="card-header d-flex align-items-center gap-2">
                    <div class="section-icon" style="background:#d1fae5;color:#065f46;"><i class="bi bi-calendar2-check"></i></div>
                    <span style="font-size:.9rem;">Informasi Kunjungan</span>
                </div>
                <div class="card-body">
                    <div class="info-row"><span class="label">Poli Tujuan</span><span class="value">{{ $registration->department->nama_poli }}</span></div>
                    <div class="info-row"><span class="label">Dokter</span><span class="value">{{ $registration->doctor->nama_dokter }}</span></div>
                    <div class="info-row">
                        <span class="label">Jadwal Praktik</span>
                        <span class="value" style="font-weight:500;">{{ $registration->doctorSchedule->hari }}, {{ substr($registration->doctorSchedule->jam_mulai,0,5) }}–{{ substr($registration->doctorSchedule->jam_selesai,0,5) }} WIB</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Tanggal Kunjungan</span>
                        <span class="value" style="color:var(--primary);">{{ $registration->tanggal_kunjungan->translatedFormat('d M Y') }}</span>
                    </div>
                    <div class="info-row"><span class="label">Tanggal Daftar</span><span class="value" style="font-weight:500;">{{ $registration->tanggal_daftar->format('d M Y') }}</span></div>
                    <div class="info-row"><span class="label">Petugas</span><span class="value" style="font-weight:500;">{{ $registration->createdBy->name }}</span></div>
                    <div class="info-row"><span class="label">Waktu Daftar</span><span class="value" style="font-weight:500;">{{ $registration->created_at->format('d M Y, H:i') }} WIB</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Action Box ── --}}
    <div class="action-box fade-in">
        <div class="action-box-title">
            <i class="bi bi-ui-checks-grid"></i> Konfirmasi Tindakan
        </div>
        <div class="action-box-sub">Cetak tracer antrean untuk diberikan ke pasien, atau batalkan pendaftaran jika tidak jadi.</div>
        <div class="action-buttons">

            {{-- Lihat Daftar --}}
            <a href="{{ route('registrations.index') }}" class="btn-kembali">
                <i class="bi bi-list-ul"></i> Lihat Daftar
            </a>

            {{-- Batalkan Pendaftaran --}}
            <form action="{{ route('registrations.destroy', $registration) }}" method="POST"
                  onsubmit="return confirm('Yakin ingin MEMBATALKAN pendaftaran ini?\nData antrean akan dihapus dan tidak dapat dikembalikan.');"
                  style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-batal">
                    <i class="bi bi-x-circle-fill"></i> Batalkan Pendaftaran
                </button>
            </form>

            {{-- Cetak Tracer --}}
            <a href="{{ route('registrations.cetak', $registration) }}" target="_blank" class="btn-cetak">
                <i class="bi bi-printer-fill"></i> Cetak Tracer
            </a>

        </div>
    </div>

</div>
</div>
@endsection