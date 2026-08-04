@extends('layouts.app')
@section('title','Jadwal Praktik')
@section('page-title','Jadwal Praktik Dokter')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Jadwal Praktik</li>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 fw-700" style="color:var(--primary)">Jadwal Praktik Dokter</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Dikelompokkan per hari dalam seminggu</p>
    </div>
    <a href="{{ route('doctor-schedules.create') }}" class="btn btn-accent">
        <i class="bi bi-plus-circle me-1"></i> Tambah Jadwal
    </a>
</div>

@foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari)
@if(isset($schedules[$hari]) && $schedules[$hari]->count() > 0)
<div class="card mb-3 fade-in">
    <div class="card-header d-flex align-items-center gap-2">
        <span class="badge" style="background:var(--accent);color:#fff;font-size:.82rem;padding:5px 12px;">{{ $hari }}</span>
        <span style="font-size:.82rem;color:#64748b;">{{ $schedules[$hari]->count() }} jadwal</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr>
                <th>Dokter</th>
                <th>Poli</th>
                <th>Jam Praktik</th>
                <th class="text-center">Kuota</th>
                <th class="text-center">Status</th>
                <th class="text-center" width="100">Aksi</th>
            </tr></thead>
            <tbody>
            @foreach($schedules[$hari] as $sch)
            <tr>
                <td class="fw-600" style="font-size:.875rem;">{{ $sch->doctor->nama_dokter }}</td>
                <td><span class="badge" style="background:var(--primary);color:#fff;">{{ $sch->department->kode_poli }}</span> {{ $sch->department->nama_poli }}</td>
                <td style="font-size:.82rem;">
                    <i class="bi bi-clock me-1 text-muted"></i>
                    {{ substr($sch->jam_mulai,0,5) }} — {{ substr($sch->jam_selesai,0,5) }}
                </td>
                <td class="text-center">
                    <span class="badge" style="background:#eff6ff;color:var(--primary);font-size:.8rem;">{{ $sch->kuota }} pasien</span>
                </td>
                <td class="text-center">
                    <span class="badge" style="background:{{ $sch->is_active ? '#d1fae5' : '#fee2e2' }};color:{{ $sch->is_active ? '#065f46' : '#991b1b' }};">
                        {{ $sch->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">
                        <a href="{{ route('doctor-schedules.edit', $sch) }}" class="btn btn-sm btn-icon" style="background:#eff6ff;color:var(--primary);" title="Edit">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <form action="{{ route('doctor-schedules.destroy', $sch) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-icon" style="background:#fef2f2;color:#ef4444;" title="Hapus">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endforeach

@if($schedules->isEmpty())
<div class="card text-center py-5 fade-in">
    <i class="bi bi-calendar-x" style="font-size:3rem;color:#94a3b8;display:block;margin-bottom:12px;"></i>
    <p class="text-muted">Belum ada jadwal praktik. <a href="{{ route('doctor-schedules.create') }}">Tambah sekarang</a></p>
</div>
@endif
@endsection
