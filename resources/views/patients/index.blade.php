@extends('layouts.app')
@section('title','Data Pasien')
@section('page-title','Data Pasien')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Data Pasien</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="mb-1 fw-700" style="color:var(--primary)"><i class="bi bi-people me-1"></i>Daftar Pasien Terdaftar</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Total {{ $patients->total() }} pasien terdaftar dalam sistem</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('patients.create') }}" class="btn btn-accent">
            <i class="bi bi-person-plus me-1"></i> Pasien Baru
        </a>
        <a href="{{ route('registrations.create') }}" class="btn btn-primary">
            <i class="bi bi-clipboard2-plus me-1"></i> Daftar Rawat Jalan
        </a>
    </div>
</div>

<!-- Search -->
<div class="card mb-3 fade-in">
    <div class="card-body py-3">
        <form action="{{ route('patients.index') }}" method="GET">
            <div class="input-group">
                <span class="input-group-text" style="background:var(--bg);border-color:#e5e0d0;border-radius:10px 0 0 10px;">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="q" class="form-control" style="border-left:none;border-radius:0 10px 10px 0;"
                       placeholder="Cari nama pasien, NIK, atau No. RM..." value="{{ request('q') }}">
                <button class="btn btn-primary ms-2" type="submit" style="border-radius:10px;">Cari</button>
                @if(request('q'))
                    <a href="{{ route('patients.index') }}" class="btn ms-1" style="background:var(--bg);color:#64766D;border-radius:10px;">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card table-card fade-in">
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
                    <th class="text-center" width="130">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                <tr>
                    <td>
                        <span class="fw-700" style="color:var(--primary);font-size:.82rem;">{{ $patient->no_rm }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px;height:34px;border-radius:50% 50% 8px 8px;background:{{ $patient->jenis_kelamin === 'L' ? 'var(--primary)' : '#B8447A' }};color:#fff;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0;">
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
                        <span class="badge" style="background:{{ $jenis==='bpjs'?'#d1fae5':($jenis==='asuransi'?'#fdf1d3':'#f1efe4') }};
                              color:{{ $jenis==='bpjs'?'#065f46':($jenis==='asuransi'?'#9C7A1A':'#4a4335') }};">
                            {{ strtoupper($jenis) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge" style="background:#eaf4ef;color:var(--primary);">{{ $patient->registrations_count }}×</span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-icon" style="background:#eaf6f8;color:var(--tile,#0E7490);" title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            <a href="{{ route('registrations.create', ['patient_id'=>$patient->id]) }}" class="btn btn-sm btn-icon" style="background:#fdf7e6;color:var(--accent);" title="Daftarkan">
                                <i class="bi bi-clipboard2-plus-fill"></i>
                            </a>
                            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-icon" style="background:#eaf4ef;color:var(--primary);" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-5 text-muted">
                    <i class="bi bi-person-x" style="font-size:2.5rem;display:block;margin-bottom:8px;"></i>
                    @if(request('q')) Tidak ditemukan pasien dengan kata kunci "{{ request('q') }}"
                    @else Belum ada data pasien. <a href="{{ route('patients.create') }}">Tambahkan pasien baru</a> @endif
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($patients->hasPages())
    <div class="card-body border-top d-flex justify-content-between align-items-center py-3">
        <small class="text-muted">Menampilkan {{ $patients->firstItem() }}–{{ $patients->lastItem() }} dari {{ $patients->total() }} pasien</small>
        {{ $patients->links() }}
    </div>
    @endif
</div>
@endsection