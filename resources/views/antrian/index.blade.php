@extends('layouts.app')

@section('page-title', 'Manajemen Antrean')
@section('breadcrumb')
    <li class="breadcrumb-item active">Antrean</li>
@endsection

@push('styles')
<style>
    .queue-card {
        border-radius: 16px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .queue-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
    }
    .queue-header {
        background: var(--primary-soft);
        padding: 16px;
        border-bottom: 1px solid var(--border);
        border-radius: 16px 16px 0 0;
        text-align: center;
    }
    .queue-number {
        font-family: 'Spectral', serif;
        font-size: 3rem;
        font-weight: 800;
        color: var(--primary-dark);
        line-height: 1;
        letter-spacing: 0.05em;
    }
    .queue-body {
        padding: 16px;
        flex: 1;
    }
    .patient-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .queue-info {
        font-size: 0.85rem;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .queue-actions {
        padding: 16px;
        border-top: 1px dashed var(--border);
        display: flex;
        gap: 8px;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.75rem;
        margin-bottom: 12px;
    }
    .status-menunggu { background: #FFF7ED; color: #C2410C; border: 1px solid #FFEDD5; }
    .status-diperiksa { background: #EFF6FF; color: #1D4ED8; border: 1px solid #DBEAFE; }

    /* Highlight untuk pasien yang sedang diperiksa */
    .queue-card.active-exam {
        border: 2px solid var(--primary);
        box-shadow: 0 8px 25px rgba(15, 123, 99, 0.15);
    }
    .queue-card.active-exam .queue-header {
        background: var(--primary);
        color: white;
    }
    .queue-card.active-exam .queue-number {
        color: white;
    }
</style>
@endpush

@section('content')

<div class="card mb-4" style="border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
    <div class="card-body p-4">
        <form action="{{ route('antrian.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 600; color: var(--muted);">Tanggal Kunjungan</label>
                <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}">
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 600; color: var(--muted);">Filter Poli</label>
                <select name="department_id" class="form-select">
                    <option value="">-- Semua Poli --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                            {{ $dept->nama_poli }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <button type="submit" class="btn btn-primary h-100 px-4">
                    <i class="bi bi-filter me-2"></i>Filter
                </button>
                <a href="{{ route('antrian.index') }}" class="btn btn-outline-secondary h-100 px-4 ms-2">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Stats --}}
<div class="row mb-4 g-3">
    <div class="col-md-3">
        <div class="card" style="border-radius: 16px; border-left: 4px solid var(--primary);">
            <div class="card-body py-3">
                <div class="text-muted" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Total Antrean</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--ink);">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="border-radius: 16px; border-left: 4px solid #F59E0B;">
            <div class="card-body py-3">
                <div class="text-muted" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Menunggu</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--ink);">{{ $stats['menunggu'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="border-radius: 16px; border-left: 4px solid #3B82F6;">
            <div class="card-body py-3">
                <div class="text-muted" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Sedang Diperiksa</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--ink);">{{ $stats['diperiksa'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="border-radius: 16px; border-left: 4px solid #10B981;">
            <div class="card-body py-3">
                <div class="text-muted" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Selesai</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--ink);">{{ $stats['selesai'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Antrean Berjalan (Diperiksa) --}}
@if($diperiksa->count() > 0)
<h5 class="fw-bold mb-3" style="color: var(--ink);">Sedang Diperiksa</h5>
<div class="row g-3 mb-5">
    @foreach($diperiksa as $reg)
    <div class="col-md-4">
        <div class="queue-card active-exam">
            <div class="queue-header">
                <div class="queue-number">{{ $reg->nomor_antrian }}</div>
            </div>
            <div class="queue-body">
                <div class="status-badge status-diperiksa">
                    <i class="bi bi-person-check-fill"></i> Diperiksa
                </div>
                <div class="patient-name">{{ $reg->patient->nama_pasien }}</div>
                <div class="queue-info mb-1"><i class="bi bi-hospital"></i> {{ $reg->department->nama_poli }}</div>
                <div class="queue-info"><i class="bi bi-person-badge"></i> {{ $reg->doctor->nama_dokter }}</div>
            </div>
            <div class="queue-actions">
                <form action="{{ route('antrian.selesai', $reg) }}" method="POST" class="w-100">
                    @csrf @method('PATCH')
                    <button class="btn btn-success w-100" style="border-radius: 12px; font-weight: 600;" onclick="return confirm('Tandai pasien selesai diperiksa?')">
                        <i class="bi bi-check-circle me-1"></i> Selesai
                    </button>
                </form>
                <form action="{{ route('antrian.tunda', $reg) }}" method="POST">
                    @csrf @method('PATCH')
                    <button class="btn btn-outline-warning" style="border-radius: 12px;" title="Kembalikan ke menunggu" onclick="return confirm('Kembalikan ke status menunggu?')">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Antrean Menunggu --}}
<h5 class="fw-bold mb-3" style="color: var(--ink);">Antrean Menunggu</h5>
@if($menunggu->count() > 0)
<div class="row g-3 mb-5">
    @foreach($menunggu as $reg)
    <div class="col-md-3">
        <div class="queue-card">
            <div class="queue-header">
                <div class="queue-number">{{ $reg->nomor_antrian }}</div>
            </div>
            <div class="queue-body">
                <div class="status-badge status-menunggu">
                    <i class="bi bi-hourglass-split"></i> Menunggu
                </div>
                <div class="patient-name" title="{{ $reg->patient->nama_pasien }}">{{ $reg->patient->nama_pasien }}</div>
                <div class="queue-info mb-1"><i class="bi bi-hospital"></i> {{ $reg->department->nama_poli }}</div>
                <div class="queue-info"><i class="bi bi-person-badge"></i> {{ $reg->doctor->nama_dokter }}</div>
            </div>
            <div class="queue-actions">
                <form action="{{ route('antrian.panggil', $reg) }}" method="POST" class="w-100">
                    @csrf @method('PATCH')
                    <button class="btn btn-primary w-100" style="border-radius: 12px; font-weight: 600;">
                        <i class="bi bi-megaphone-fill me-1"></i> Panggil
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="alert alert-light border text-center py-5" style="border-radius: 16px;">
    <div style="font-size: 3rem; color: var(--muted); opacity: 0.3; margin-bottom: 10px;">
        <i class="bi bi-cup-hot"></i>
    </div>
    <div class="fw-bold" style="color: var(--muted);">Tidak ada antrean menunggu</div>
    <p class="text-muted small mb-0">Belum ada pasien yang mengambil antrean atau semua pasien sudah diperiksa.</p>
</div>
@endif

@endsection
