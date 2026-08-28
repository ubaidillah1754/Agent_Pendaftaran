@extends('layouts.app')
@section('title','Data Pasien Terdaftar')
@section('page-title','Data Pasien Terdaftar')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Data Pasien</li>
@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --hp-primary:   #0F766E;
        --hp-secondary: #14B8A6;
        --hp-accent:    #D4AF37;
        --hp-danger:    #EF4444;
        --hp-bg:        #F8FAFC;
        --hp-border:    #E5E7EB;
        --hp-radius:    18px;
        --hp-shadow:    0 10px 40px rgba(15,118,110,.08);
    }
    .dp-wrap { font-family:'Poppins', sans-serif; }

    .dp-hero {
        position:relative; overflow:hidden;
        border-radius:24px; padding:26px 30px; margin-bottom:20px;
        background: linear-gradient(115deg, #D1FAE5 0%, #99F6E4 55%, #5EEAD4 100%);
        display:flex; align-items:center; gap:18px; box-shadow: var(--hp-shadow);
    }
    .dp-hero .hero-icon {
        width:56px;height:56px;border-radius:16px;background:#fff;display:flex;align-items:center;justify-content:center;
        font-size:1.5rem;color:var(--hp-primary);flex-shrink:0;box-shadow:0 6px 16px rgba(15,118,110,.18);
    }
    .dp-hero h5 { margin:0 0 4px;font-weight:700;font-size:1.2rem;color:#064E3B; }
    .dp-hero p { margin:0;font-size:.85rem;color:#0f766e;opacity:.85; }

    .dp-stat {
        border-radius:14px;padding:16px 18px;background:#fff;border:1px solid var(--hp-border);
        display:flex;align-items:center;gap:14px;box-shadow:var(--hp-shadow);
    }
    .dp-stat .icon {
        width:44px;height:44px;border-radius:22px 22px 6px 6px;flex-shrink:0;
        display:flex;align-items:center;justify-content:center;font-size:1.15rem;
    }
    .dp-stat .num { font-size:1.5rem;font-weight:800;line-height:1;color:#0f172a; }
    .dp-stat .lbl { font-size:.76rem;color:#64766D;margin-top:3px; }

    .dp-actionbar {
        border-radius:16px;background:#fff;border:1px solid var(--hp-border);box-shadow:var(--hp-shadow);
        padding:14px 18px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px;
    }
    .dp-search { position:relative;flex:1;min-width:240px; }
    .dp-search i { position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8; }
    .dp-search input {
        width:100%;padding:10px 14px 10px 40px;border-radius:12px;border:1.5px solid var(--hp-border);
        font-size:.86rem;font-family:inherit;
    }
    .dp-search input:focus { outline:none;border-color:var(--hp-secondary);box-shadow:0 0 0 4px rgba(20,184,166,.14); }

    .btn-dp-gold {
        background:linear-gradient(135deg,var(--hp-accent),#e8c766);color:#fff;border:none;
        border-radius:12px;padding:10px 16px;font-weight:600;font-size:.84rem;white-space:nowrap;text-decoration:none;
    }

    .dp-table-card { border-radius:var(--hp-radius);border:1px solid var(--hp-border);box-shadow:var(--hp-shadow);overflow:hidden; }
    .dp-table-card thead th {
        background:#F8FAFC;font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;color:#64766D;
        font-weight:700;border-bottom:1px solid var(--hp-border);padding:12px 16px;
    }
    .dp-table-card tbody td { padding:12px 16px; }
    .dp-table-card tbody tr:hover { background:#F0FDFA; }

    .avatar-circle {
        width:36px;height:36px;border-radius:18px 18px 6px 6px;color:#fff;display:flex;
        align-items:center;justify-content:center;font-size:.78rem;font-weight:700;flex-shrink:0;
    }
    .pay-badge { font-size:.68rem;font-weight:700;padding:4px 10px;border-radius:20px; }
    .visit-badge { font-size:.72rem;font-weight:700;padding:4px 10px;border-radius:20px;background:#ECFDF5;color:var(--hp-primary); }

    .btn-dp-icon {
        width:32px;height:32px;border-radius:11px 11px 4px 4px;border:none;display:inline-flex;
        align-items:center;justify-content:center;font-size:.85rem;text-decoration:none;
    }

    .dp-empty { text-align:center;padding:56px 20px; }
    .dp-empty .icon-wrap {
        width:74px;height:74px;border-radius:37px 37px 8px 8px;margin:0 auto 18px;background:#ECFDF5;
        display:flex;align-items:center;justify-content:center;
    }
    .dp-empty .icon-wrap i { font-size:1.8rem;color:var(--hp-primary); }
</style>
@endpush

@section('content')
<div class="dp-wrap">

<!-- Hero -->
<div class="dp-hero fade-in">
    <div class="hero-icon"><i class="bi bi-people-fill"></i></div>
    <div>
        <h5>Data Pasien Terdaftar</h5>
        <p>Daftar pasien yang terdaftar di Sistem RS Islam Sakinah.</p>
    </div>
</div>

<!-- Stat cards -->
<div class="row g-3 mb-3">
    <div class="col-6 col-md-4">
        <div class="dp-stat">
            <div class="icon" style="background:#E0F2F6;color:var(--hp-secondary);"><i class="bi bi-people-fill"></i></div>
            <div><div class="num">{{ $patients->total() }}</div><div class="lbl">Total Pasien Terdaftar</div></div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="dp-stat">
            <div class="icon" style="background:#DCFCE7;color:#15803D;"><i class="bi bi-hospital-fill"></i></div>
            <div><div class="num">{{ $patients->where('jenis_pembayaran','bpjs')->count() }}</div><div class="lbl">Pasien BPJS (Hal. ini)</div></div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="dp-stat">
            <div class="icon" style="background:#F1F5F9;color:#475569;"><i class="bi bi-credit-card-fill"></i></div>
            <div><div class="num">{{ $patients->where('jenis_pembayaran','umum')->count() }}</div><div class="lbl">Pasien Umum (Hal. ini)</div></div>
        </div>
    </div>
</div>

<!-- Search bar -->
<div class="dp-actionbar fade-in">
    <form action="{{ route('patients.index') }}" method="GET" class="w-100 d-flex gap-2 align-items-center">
        <div class="dp-search">
            <i class="bi bi-search"></i>
            <input type="text" name="q" placeholder="Cari NIK, No. RM, atau Nama Pasien..." value="{{ request('q') }}">
        </div>
        <button type="submit" class="btn btn-sm text-white px-3 py-2 fw-bold" style="background:var(--hp-primary); border-radius:10px;">
            <i class="bi bi-search me-1"></i>Cari
        </button>
        @if(request('q'))
            <a href="{{ route('patients.index') }}" class="btn btn-sm btn-light border px-3 py-2" style="border-radius:10px;">Reset</a>
        @endif
        <a href="{{ route('registrations.create') }}" class="btn-dp-gold ms-auto"><i class="bi bi-clipboard2-plus me-1"></i>Daftar Rawat Jalan</a>
    </form>
</div>

<div class="dp-table-card fade-in">
    @if($patients->count())
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No. RM</th>
                    <th>Nama Pasien</th>
                    <th>NIK</th>
                    <th>Tgl Lahir / Umur</th>
                    <th>No. HP</th>
                    <th>Pembayaran</th>
                    <th class="text-center">Kunjungan</th>
                    <th class="text-center" width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patients as $patient)
                <tr>
                    <td><span class="fw-700" style="color:var(--hp-primary);font-size:.82rem;">{{ $patient->no_rm }}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle" style="background:{{ $patient->jenis_kelamin === 'L' ? '#0F766E' : '#B8447A' }};">
                                {{ $patient->jenis_kelamin }}
                            </div>
                            <div>
                                <div class="fw-600" style="font-size:.875rem;">{{ $patient->nama_pasien }}</div>
                                <div style="font-size:.72rem;color:#64766D;">{{ $patient->tempat_lahir ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-family:monospace;font-size:.82rem;color:#475d52;">{{ $patient->nik }}</td>
                    <td style="font-size:.82rem;">
                        {{ $patient->tanggal_lahir->format('d M Y') }}<br>
                        <span style="color:#64766D;font-size:.75rem;">{{ $patient->umur }} tahun</span>
                    </td>
                    <td style="font-size:.82rem;">{{ $patient->no_telepon ?? '-' }}</td>
                    <td>
                        @php $jenis = $patient->jenis_pembayaran; @endphp
                        <span class="pay-badge" style="background:{{ $jenis==='bpjs'?'#DCFCE7':($jenis==='asuransi'?'#fdf1d3':'#f1efe4') }};
                              color:{{ $jenis==='bpjs'?'#15803D':($jenis==='asuransi'?'#9C7A1A':'#4a4335') }};">
                            {{ strtoupper($jenis) }}
                        </span>
                    </td>
                    <td class="text-center"><span class="visit-badge">{{ $patient->registrations_count }}&times;</span></td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('patients.show', $patient) }}" class="btn-dp-icon" style="background:#eaf6f8;color:#0E7490;" title="Detail"><i class="bi bi-eye-fill"></i></a>
                            <a href="{{ route('registrations.create', ['patient_id'=>$patient->id]) }}" class="btn-dp-icon" style="background:#fdf7e6;color:var(--hp-accent);" title="Daftar Rawat Jalan"><i class="bi bi-clipboard2-plus-fill"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center">
        <span class="text-muted" style="font-size: 0.8rem;">
            Menampilkan {{ $patients->firstItem() ?? 0 }}–{{ $patients->lastItem() ?? 0 }} dari {{ $patients->total() }} data
        </span>
        <div>
            {{ $patients->links() }}
        </div>
    </div>
    @else
    <div class="dp-empty">
        <div class="icon-wrap"><i class="bi bi-person-x"></i></div>
        @if(request('q'))
            <h6>Tidak ditemukan</h6>
            <p>Tidak ada pasien terdaftar dengan kata kunci &ldquo;{{ request('q') }}&rdquo;</p>
            <a href="{{ route('patients.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset Pencarian</a>
        @else
            <h6>Belum ada data pasien terdaftar</h6>
            <p>Pasien baru wajib melalui pendaftaran via WhatsApp Admin RSI Sakinah.</p>
        @endif
    </div>
    @endif

</div>
</div>
@endsection