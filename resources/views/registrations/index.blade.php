@extends('layouts.app')
@section('title','Daftar Pendaftaran')
@section('page-title','Daftar Pendaftaran')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Pendaftaran</li>
@endsection

@push('styles')
<style>
    :root {
        --rs-gold:       #C9A227;
        --rs-gold-light: #E8C766;
        --rs-teal:       #0E7490;
    }

    /* ===== Hero banner ===== */
    .reg-hero {
        position:relative;overflow:hidden;border-radius:18px;
        padding:26px 30px;margin-bottom:20px;
        background:linear-gradient(120deg,var(--primary-dark,#063D2C) 0%,var(--primary,#0B6B4F) 100%);
        color:#fff;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;
    }
    .reg-hero::before {
        content:"";position:absolute;inset:0;z-index:0;pointer-events:none;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='84' height='84'%3E%3Cg fill='none' stroke='%23E8C766' stroke-width='1' opacity='0.1'%3E%3Cpath d='M42 4 L80 42 L42 80 L4 42 Z'/%3E%3Ccircle cx='42' cy='42' r='18'/%3E%3C/g%3E%3C/svg%3E");
    }
    .reg-hero .hero-text { position:relative;z-index:1; }
    .reg-hero h4 { font-weight:800;font-size:1.3rem;margin:0 0 6px; }
    .reg-hero p { margin:0;font-size:.85rem;color:rgba(255,255,255,.72); }
    .reg-hero .hero-date {
        position:relative;z-index:1;background:rgba(255,255,255,.12);border:1px solid rgba(232,199,102,.3);
        border-radius:10px;padding:8px 16px;font-size:.8rem;font-weight:600;white-space:nowrap;
    }

    /* ===== Stat mini cards ===== */
    .reg-stat {
        border-radius:14px;padding:16px 18px;background:#fff;border:1px solid var(--border,#E7EAEF);
        display:flex;align-items:center;gap:14px;
    }
    .reg-stat .icon {
        width:44px;height:44px;border-radius:22px 22px 6px 6px;flex-shrink:0;
        display:flex;align-items:center;justify-content:center;font-size:1.2rem;
    }
    .reg-stat .num { font-size:1.5rem;font-weight:800;line-height:1;color:var(--ink,#1B2430); }
    .reg-stat .lbl { font-size:.76rem;color:#64766D;margin-top:3px; }

    /* ===== Filter card ===== */
    .filter-card .form-control, .filter-card .form-select { border-radius:10px; }
    .search-box { position:relative; }
    .search-box i { position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#A2ACB8; }
    .search-box input { padding-left:38px;border-radius:10px; }

    /* ===== Table refinements ===== */
    .table-card thead th {
        background:#F6FAF7;font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;
        color:#64766D;font-weight:700;border-bottom:1px solid var(--border,#E7EAEF);
    }
    .btn-icon {
        width:34px;height:34px;border-radius:12px 12px 4px 4px;border:none;
        display:inline-flex;align-items:center;justify-content:center;
    }

    /* ===== Empty state ===== */
    .empty-state-rich { text-align:center;padding:56px 20px; }
    .empty-state-rich .icon-wrap {
        width:74px;height:74px;border-radius:37px 37px 8px 8px;margin:0 auto 18px;
        background:var(--primary-soft,#E9F3EE);display:flex;align-items:center;justify-content:center;
        position:relative;
    }
    .empty-state-rich .icon-wrap i { font-size:1.8rem;color:var(--primary,#0B6B4F); }
    .empty-state-rich .icon-wrap .badge-plus {
        position:absolute;bottom:-4px;right:-4px;width:26px;height:26px;border-radius:50%;
        background:var(--rs-gold);color:#fff;display:flex;align-items:center;justify-content:center;
        font-size:.85rem;border:2px solid #fff;
    }
    .empty-state-rich h6 { font-weight:800;margin-bottom:4px; }
    .empty-state-rich p { color:#64766D;font-size:.85rem;margin-bottom:18px; }
</style>
@endpush

@section('content')

<!-- Hero -->
<div class="reg-hero fade-in">
    <div class="hero-text">
        <h4><i class="bi bi-clipboard2-data me-2"></i>Manajemen Pendaftaran Rawat Jalan</h4>
        <p>Kelola data pendaftaran pasien dengan mudah, cepat, dan akurat</p>
    </div>
    <div class="hero-date">
        <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <p class="text-muted mb-0" style="font-size:.82rem;">Total {{ $registrations->count() }} pendaftaran ditemukan</p>
    </div>
    <a href="{{ route('registrations.create') }}" class="btn btn-accent">
        <i class="bi bi-clipboard2-plus me-1"></i> Pendaftaran Baru
    </a>
</div>

<!-- Stat mini -->
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="reg-stat">
            <div class="icon" style="background:#E9F3EE;color:var(--primary,#0B6B4F);"><i class="bi bi-clipboard2-check-fill"></i></div>
            <div>
                <div class="num">{{ $registrations->count() }}</div>
                <div class="lbl">Total Pendaftaran</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="reg-stat">
            <div class="icon" style="background:#FBF6E9;color:var(--rs-gold);"><i class="bi bi-building"></i></div>
            <div>
                <div class="num">{{ $departments->count() }}</div>
                <div class="lbl">Poli Tersedia</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="reg-stat">
            <div class="icon" style="background:#E0F2F6;color:var(--rs-teal);"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="num">{{ $registrations->where('status','menunggu')->count() }}</div>
                <div class="lbl">Menunggu (hal. ini)</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="reg-stat">
            <div class="icon" style="background:#F3EBFA;color:#7C3AED;"><i class="bi bi-check2-circle"></i></div>
            <div>
                <div class="num">{{ $registrations->where('status','selesai')->count() }}</div>
                <div class="lbl">Selesai (hal. ini)</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-3 fade-in filter-card">
    <div class="card-body py-3">
        <form action="{{ route('registrations.index') }}" method="GET">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal', date('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <select name="department_id" class="form-select">
                        <option value="">Semua Poli</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_poli }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach(['menunggu','dipanggil','selesai','batal'] as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-auto">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button>
                    <a href="{{ route('registrations.index') }}" class="btn ms-1" style="background:var(--bg);color:#64766D;">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card table-card fade-in">
    @if($registrations->count())
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 datatable">
            <thead>
                <tr>
                    <th>No. Antrian</th>
                    <th>Pasien</th>
                    <th>Poli</th>
                    <th>Dokter</th>
                    <th>Tgl Kunjungan</th>
                    <th>Pembayaran</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registrations as $reg)
                <tr>
                    <td>
                        <span class="fw-900" style="font-size:1.1rem;color:var(--primary);letter-spacing:.05em;">{{ $reg->nomor_antrian }}</span>
                    </td>
                    <td>
                        <div class="fw-600" style="font-size:.875rem;">{{ $reg->patient->nama_pasien }}</div>
                        <div style="font-size:.72rem;color:#64766D;">{{ $reg->patient->no_rm }}</div>
                    </td>
                    <td style="font-size:.82rem;">{{ $reg->department->nama_poli }}</td>
                    <td style="font-size:.82rem;">{{ $reg->doctor->nama_dokter }}</td>
                    <td style="font-size:.82rem;">{{ $reg->tanggal_daftar->format('d M Y') }}</td>
                    <td>
                        <span class="badge" style="background:#f1efe4;color:#4a4335;">{{ strtoupper($reg->patient->jenis_pembayaran) }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-{{ $reg->status }}">{{ $reg->status_label }}</span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('registrations.cetak', $reg) }}" target="_blank" class="btn btn-sm btn-icon" style="background:#fef3c7;color:#d97706;" title="Cetak Antrian">
                                <i class="bi bi-printer-fill"></i>
                            </a>
                            <a href="{{ route('registrations.show', $reg) }}" class="btn btn-sm btn-icon" style="background:#eaf6f8;color:var(--tile,#0E7490);" title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            @if($reg->status === 'menunggu')
                            <form action="{{ route('registrations.batal', $reg) }}" method="POST" onsubmit="return confirm('Batalkan pendaftaran ini?')">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-icon" style="background:#fef2f2;color:#ef4444;" title="Batal">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state-rich">
        <div class="icon-wrap">
            <i class="bi bi-clipboard2-x"></i>
            <span class="badge-plus"><i class="bi bi-plus"></i></span>
        </div>
        <h6>Tidak ada pendaftaran untuk filter ini</h6>
        <p>Coba ubah filter pencarian atau lakukan pendaftaran baru</p>
        <div class="d-flex gap-2 justify-content-center flex-wrap">
            <a href="{{ route('registrations.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg me-1"></i>Daftarkan Pasien Baru</a>
            <a href="{{ route('registrations.index') }}" class="btn btn-sm" style="background:var(--bg);color:#64766D;"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filter</a>
        </div>
    </div>
    @endif

</div>
@endsection