@extends('layouts.public')

@section('title', 'Jadwal Dokter')

@section('content')
<div class="container">
    <div class="d-flex align-items-center mb-4 gap-3">
        <div style="width: 48px; height: 48px; background: var(--primary-soft); color: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="bi bi-calendar-week"></i>
        </div>
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--ink);">Jadwal Praktik Dokter</h3>
            <p class="text-muted mb-0">Informasi jadwal layanan dokter spesialis dan umum.</p>
        </div>
    </div>

    <!-- Filter Jadwal -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4">
            <form action="{{ route('public.jadwal') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted text-uppercase mb-1">Pilih Poli</label>
                        <select name="department_id" class="form-select border-0 bg-light" style="border-radius: 8px;">
                            <option value="">Semua Poli</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_poli }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted text-uppercase mb-1">Pilih Hari</label>
                        <select name="hari" class="form-select border-0 bg-light" style="border-radius: 8px;">
                            <option value="">Semua Hari</option>
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h)
                                <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-fill" style="border-radius: 8px;">Filter</button>
                        <a href="{{ route('public.jadwal') }}" class="btn btn-light border flex-fill" style="border-radius: 8px;">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Jadwal -->
    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        @if($schedules->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: var(--bg);">
                    <tr>
                        <th class="py-3 px-4 text-muted text-uppercase small" style="letter-spacing: 0.05em; font-weight: 600;">Hari</th>
                        <th class="py-3 px-4 text-muted text-uppercase small" style="letter-spacing: 0.05em; font-weight: 600;">Jam Praktik</th>
                        <th class="py-3 px-4 text-muted text-uppercase small" style="letter-spacing: 0.05em; font-weight: 600;">Poli</th>
                        <th class="py-3 px-4 text-muted text-uppercase small" style="letter-spacing: 0.05em; font-weight: 600;">Dokter</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedules as $s)
                    <tr>
                        <td class="px-4 py-3 fw-semibold">{{ $s->hari }}</td>
                        <td class="px-4 py-3">
                            <span class="badge bg-light text-dark border">
                                {{ substr($s->jam_mulai,0,5) }} - {{ substr($s->jam_selesai,0,5) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $s->department->nama_poli }}</td>
                        <td class="px-4 py-3">
                            <div class="fw-semibold text-dark">{{ $s->doctor->nama_dokter }}</div>
                            <small class="text-muted">{{ $s->doctor->spesialisasi }}</small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <div style="font-size: 3rem; color: var(--border); mb-3"><i class="bi bi-calendar-x"></i></div>
            <h5 class="text-muted">Tidak ada jadwal dokter yang ditemukan.</h5>
        </div>
        @endif
    </div>
</div>
@endsection
