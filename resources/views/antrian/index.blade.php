@extends('layouts.app')
@section('title','Monitor Antrian')
@section('page-title','Monitor Antrian')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Monitor Antrian</li>
@endsection

@push('styles')
<style>
    :root {
        --rs-green:  #0B6B4F;
        --rs-green-dark: #063D2C;
        --rs-gold:   #C9A227;
        --rs-gold-light: #E8C766;
        --rs-tile:   #0E7490;
    }

    .poli-tab {
        padding:10px 18px;border-radius:12px;border:2px solid #e5e0d0;cursor:pointer;
        transition:all .2s;background:#fff;font-size:.83rem;font-weight:600;
        color:#475d52;text-decoration:none;white-space:nowrap;
    }
    .poli-tab.active { border-color:var(--rs-gold);background:var(--rs-gold);color:#fff; }
    .poli-tab:hover:not(.active) { border-color:var(--rs-green);color:var(--rs-green); }

    .antrian-row { display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px dashed #ece6d6;transition:background .2s; }
    .antrian-row:last-child { border-bottom:none; }
    .antrian-row:hover { background:#f6faf7;border-radius:10px;padding-left:8px;padding-right:8px; }

    /* dome-shaped queue number badges */
    .no-antrian {
        width:52px;height:52px;border-radius:50% 50% 12px 12px;
        display:flex;align-items:center;justify-content:center;
        font-weight:900;font-size:.85rem;flex-shrink:0;
        border: 1px solid rgba(0,0,0,.05);
    }
    .no-menunggu  { background:#fef3c7;color:#92400e; }
    .no-dipanggil { background:#e0f2f6;color:#0c5c73;animation:pulse 1.5s infinite; }
    .no-selesai   { background:#d1fae5;color:#065f46; }
    .no-batal     { background:#fee2e2;color:#991b1b; }

    @keyframes pulse { 0%,100%{box-shadow:0 0 0 0 rgba(14,116,144,.4)} 50%{box-shadow:0 0 0 8px rgba(14,116,144,0)} }

    .stat-mini { border-radius:14px;padding:14px;text-align:center;position:relative;overflow:hidden; }
    .stat-mini::after {
        content:"";position:absolute;top:-14px;right:-14px;width:52px;height:52px;opacity:.25;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64'%3E%3Cg fill='none' stroke='%23000000' stroke-width='2'%3E%3Crect x='16' y='16' width='32' height='32' transform='rotate(45 32 32)'/%3E%3Crect x='16' y='16' width='32' height='32'/%3E%3C/g%3E%3C/svg%3E");
    }
    .btn-action { border-radius:10px;font-size:.78rem;font-weight:600;padding:6px 12px; }

    .rs-star-bullet { color: var(--rs-gold); font-size:.7rem; }
</style>
@endpush

@section('content')
<!-- Filter Poli -->
<div class="mb-4 d-flex gap-2 flex-wrap fade-in">
    @foreach($departments as $dept)
    <a href="{{ route('antrian.index', ['department_id' => $dept->id]) }}"
       class="poli-tab {{ $selectedDept?->id == $dept->id ? 'active' : '' }}">
        <i class="bi bi-building me-1"></i>{{ $dept->nama_poli }}
    </a>
    @endforeach
</div>

@if($selectedDept)
<div class="row g-3 mb-4">
    <!-- Stat Mini -->
    <div class="col-6 col-md-3">
        <div class="stat-mini card" style="background:#fef3c7;border:2px solid #fde68a;">
            <div style="font-size:1.8rem;font-weight:900;color:#92400e;">{{ $stats['menunggu'] }}</div>
            <div style="font-size:.75rem;color:#92400e;font-weight:600;">Menunggu</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini card" style="background:#e0f2f6;border:2px solid #a5d8e6;">
            <div style="font-size:1.8rem;font-weight:900;color:#0c5c73;">{{ $stats['dipanggil'] }}</div>
            <div style="font-size:.75rem;color:#0c5c73;font-weight:600;">Dipanggil</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini card" style="background:#d1fae5;border:2px solid #6ee7b7;">
            <div style="font-size:1.8rem;font-weight:900;color:#065f46;">{{ $stats['selesai'] }}</div>
            <div style="font-size:.75rem;color:#065f46;font-weight:600;">Selesai</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini card" style="background:#fee2e2;border:2px solid #fca5a5;">
            <div style="font-size:1.8rem;font-weight:900;color:#991b1b;">{{ $stats['batal'] }}</div>
            <div style="font-size:.75rem;color:#991b1b;font-weight:600;">Batal</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Daftar Antrian -->
    <div class="col-lg-8">
        <div class="card fade-in">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ol me-2"></i>Antrian — {{ $selectedDept->nama_poli }} <span class="rs-star-bullet">✦</span></span>
                <a href="{{ route('antrian.display', $selectedDept) }}" target="_blank"
                   class="btn btn-sm" style="background:#eaf4ef;color:var(--rs-green-dark);border-radius:8px;font-size:.75rem;font-weight:700;">
                    <i class="bi bi-display me-1"></i>Display TV
                </a>
            </div>
            <div class="card-body py-2" id="antrian-list">
                @forelse($antrian as $reg)
                <div class="antrian-row" id="row-{{ $reg->id }}">
                    <div class="no-antrian no-{{ $reg->status }}">{{ $reg->nomor_antrian }}</div>
                    <div class="flex-1">
                        <div class="fw-700" style="font-size:.9rem;">{{ $reg->patient->nama_pasien }}</div>
                        <div style="font-size:.75rem;color:#64766D;">dr. {{ $reg->doctor->nama_dokter ?? '-' }}</div>
                    </div>
                    <span class="badge badge-{{ $reg->status }}" id="badge-{{ $reg->id }}">{{ $reg->status_label }}</span>

                    @if(in_array($reg->status, ['menunggu', 'dipanggil']))
                    <div class="d-flex gap-1">
                        @if($reg->status === 'menunggu')
                        <button onclick="updateStatus({{ $reg->id }},'dipanggil')" class="btn btn-action" style="background:#e0f2f6;color:#0c5c73;" title="Panggil">
                            <i class="bi bi-megaphone"></i> Panggil
                        </button>
                        @endif
                        @if($reg->status === 'dipanggil')
                        <button onclick="updateStatus({{ $reg->id }},'selesai')" class="btn btn-action" style="background:#d1fae5;color:#065f46;" title="Selesai">
                            <i class="bi bi-check2"></i> Selesai
                        </button>
                        @endif
                        <button onclick="updateStatus({{ $reg->id }},'batal')" class="btn btn-action btn-sm" style="background:#fee2e2;color:#ef4444;" title="Batal"
                                onclick="return confirm('Batalkan antrian ini?')">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x" style="font-size:3rem;display:block;"></i>
                    <p class="mt-2">Belum ada antrian hari ini untuk {{ $selectedDept->nama_poli }}</p>
                    <a href="{{ route('registrations.create') }}" class="btn btn-accent btn-sm">Daftarkan Pasien</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Panel Panggil Berikutnya -->
    <div class="col-lg-4">
        <div class="card fade-in fade-in-delay-1 text-center">
            <div class="card-header"><i class="bi bi-megaphone me-2"></i>Panggil Pasien <span class="rs-star-bullet">✦</span></div>
            <div class="card-body">
                @php $berikutnya = $antrian->firstWhere('status', 'menunggu'); @endphp
                @if($berikutnya)
                <div class="mb-3" style="background:linear-gradient(135deg,#eaf4ef,#f4f9f6);border:1px solid #d7ead9;border-radius:14px;padding:20px;">
                    <div style="font-size:.72rem;font-weight:700;color:#64766D;text-transform:uppercase;letter-spacing:.1em;">Berikutnya</div>
                    <div style="font-size:3rem;font-weight:900;color:var(--rs-green);line-height:1.1;">{{ $berikutnya->nomor_antrian }}</div>
                    <div class="fw-600 mt-1" style="font-size:.9rem;">{{ $berikutnya->patient->nama_pasien }}</div>
                </div>
                <button onclick="updateStatus({{ $berikutnya->id }},'dipanggil')" class="btn w-100" style="background:linear-gradient(135deg,var(--rs-gold),var(--rs-gold-light));color:#fff;border-radius:12px;padding:12px;font-weight:700;">
                    <i class="bi bi-megaphone me-2"></i>Panggil Sekarang
                </button>
                @else
                <div class="py-4 text-muted">
                    <i class="bi bi-check-circle-fill" style="font-size:3rem;color:#10b981;display:block;margin-bottom:8px;"></i>
                    <p class="mb-0 fw-600">Semua antrian sudah dilayani!</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@else
<div class="card text-center py-5 fade-in">
    <i class="bi bi-building-x" style="font-size:3rem;color:#94a3a0;display:block;margin-bottom:12px;"></i>
    <p class="text-muted">Pilih poli untuk melihat antrian</p>
</div>
@endif
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function updateStatus(id, status) {
    if (status === 'batal' && !confirm('Batalkan antrian ini?')) return;
    fetch(`/antrian/${id}/status`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ status })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Reload untuk refresh tampilan
            window.location.reload();
        } else {
            alert(data.message);
        }
    });
}
</script>
@endpush