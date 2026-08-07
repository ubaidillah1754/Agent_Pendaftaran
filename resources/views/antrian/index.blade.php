@extends('layouts.app')
@section('title','Monitor Antrian')
@section('page-title','Monitor Antrian')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Monitor Antrian</li>
@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Spectral:wght@600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --rs-green:      #0B6B4F;
        --rs-green-dark: #063D2C;
        --rs-gold:       #C9A227;
        --rs-gold-light: #E8C766;
        --rs-tile:       #0E7490;
    }

    /* ===== Poli filter tabs — compact pills ===== */
    .poli-tabs { display:flex; flex-wrap:wrap; gap:8px; }
    .poli-tab {
        padding:8px 14px;border-radius:10px;border:1.5px solid #e5e0d0;cursor:pointer;
        transition:all .2s;background:#fff;font-size:.78rem;font-weight:600;
        color:#475d52;text-decoration:none;white-space:nowrap;
    }
    .poli-tab.active { border-color:var(--rs-gold);background:linear-gradient(135deg,var(--rs-gold),var(--rs-gold-light));color:#fff;box-shadow:0 4px 14px rgba(201,162,39,.28); }
    .poli-tab:hover:not(.active) { border-color:var(--rs-green);color:var(--rs-green); }

    .antrian-row { display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px dashed #ece6d6;transition:background .2s; }
    .antrian-row:last-child { border-bottom:none; }
    .antrian-row:hover { background:#f6faf7;border-radius:10px;padding-left:8px;padding-right:8px; }

    /* dome-shaped queue number badges, echoing the arch motif on the TV display */
    .no-antrian {
        width:52px;height:52px;border-radius:50% 50% 12px 12px;
        display:flex;align-items:center;justify-content:center;
        font-family:'Spectral',serif; font-weight:800;font-size:.95rem;flex-shrink:0;
        border: 1px solid rgba(0,0,0,.05);
    }
    .no-menunggu  { background:#fef3c7;color:#92400e; }
    .no-dipanggil { background:#e0f2f6;color:#0c5c73;animation:pulse 1.5s infinite; }
    .no-selesai   { background:#d1fae5;color:#065f46; }
    .no-batal     { background:#fee2e2;color:#991b1b; }

    @keyframes pulse { 0%,100%{box-shadow:0 0 0 0 rgba(14,116,144,.4)} 50%{box-shadow:0 0 0 8px rgba(14,116,144,0)} }

    /* ===== Stat cards — horizontal, icon + detail link ===== */
    .stat-card {
        border-radius:14px;padding:16px 18px;display:flex;align-items:center;gap:14px;
        border:1.5px solid; position:relative; overflow:hidden;
    }
    .stat-card .icon {
        width:44px;height:44px;border-radius:22px 22px 6px 6px;flex-shrink:0;
        display:flex;align-items:center;justify-content:center;font-size:1.15rem;background:#fff;
    }
    .stat-card .stat-num { font-family:'Spectral',serif;font-size:1.6rem;font-weight:800;line-height:1; }
    .stat-card .stat-lbl { font-size:.76rem;font-weight:600;margin-top:2px; }
    .stat-card .stat-detail {
        display:block;font-size:.72rem;font-weight:700;text-decoration:none;margin-top:6px;
    }
    .stat-card .stat-detail:hover { text-decoration:underline; }

    .btn-action { border-radius:10px;font-size:.78rem;font-weight:600;padding:6px 12px; }

    .rs-diamond {
        display:inline-block;width:7px;height:7px;background:var(--rs-gold);
        transform:rotate(45deg);margin:0 2px;vertical-align:middle;
    }

    /* ===== Panggil pasien — mini arch signature, matching the TV display ===== */
    .call-arch-stage { position:relative; width:150px; height:150px; margin:0 auto 14px; display:flex; align-items:center; justify-content:center; }
    .call-arch-ring {
        position:absolute; inset:0; border-radius:50%; border:1px solid rgba(201,162,39,.35);
        animation: call-pulse 2.6s ease-out infinite;
    }
    .call-arch-ring.r2 { animation-delay:.9s; }
    @keyframes call-pulse {
        0%   { transform: scale(.75); opacity:0; }
        30%  { opacity:.8; }
        100% { transform: scale(1.15); opacity:0; }
    }
    .call-arch-frame {
        position:relative; z-index:2; width:118px; height:130px;
        background:linear-gradient(180deg,#eaf4ef,#f4f9f6);
        border:1.5px solid var(--rs-gold);
        border-radius: 59px 59px 10px 10px;
        display:flex; align-items:center; justify-content:center;
        box-shadow: 0 6px 20px rgba(11,107,79,.1);
    }
    .call-arch-frame .stat-num-big {
        font-family:'Spectral',serif; font-size:2.3rem;font-weight:800;color:var(--rs-green);line-height:1;
    }

    /* ===== Empty states ===== */
    .empty-illus {
        width:64px;height:64px;border-radius:32px 32px 8px 8px;margin:0 auto 16px;
        background:var(--primary-soft,#E9F3EE);display:flex;align-items:center;justify-content:center;
    }
    .empty-illus i { font-size:1.7rem;color:var(--rs-green); }
    .empty-illus.success { background:#d1fae5; }
    .empty-illus.success i { color:#059669; }

    @media (prefers-reduced-motion: reduce) {
        .call-arch-ring, .no-dipanggil { animation:none; }
    }
</style>
@endpush

@section('content')
<!-- Filter Poli -->
<div class="mb-4 poli-tabs fade-in">
    @foreach($departments as $dept)
    <a href="{{ route('antrian.index', ['department_id' => $dept->id]) }}"
       class="poli-tab {{ $selectedDept?->id == $dept->id ? 'active' : '' }}">
        <i class="bi bi-building me-1"></i>{{ $dept->nama_poli }}
    </a>
    @endforeach
</div>

@if($selectedDept)
<div class="row g-3 mb-4">
    <!-- Stat Cards -->
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:#fef3c7;border-color:#fde68a;">
            <div class="icon" style="color:#92400e;"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="stat-num" style="color:#92400e;">{{ $stats['menunggu'] }}</div>
                <div class="stat-lbl" style="color:#92400e;">Menunggu</div>
                <a href="{{ route('registrations.index', ['department_id' => $selectedDept->id, 'status' => 'menunggu']) }}" class="stat-detail" style="color:#92400e;">Lihat Detail &rarr;</a>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:#e0f2f6;border-color:#a5d8e6;">
            <div class="icon" style="color:#0c5c73;"><i class="bi bi-megaphone-fill"></i></div>
            <div>
                <div class="stat-num" style="color:#0c5c73;">{{ $stats['dipanggil'] }}</div>
                <div class="stat-lbl" style="color:#0c5c73;">Dipanggil</div>
                <a href="{{ route('registrations.index', ['department_id' => $selectedDept->id, 'status' => 'dipanggil']) }}" class="stat-detail" style="color:#0c5c73;">Lihat Detail &rarr;</a>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:#d1fae5;border-color:#6ee7b7;">
            <div class="icon" style="color:#065f46;"><i class="bi bi-check2-circle"></i></div>
            <div>
                <div class="stat-num" style="color:#065f46;">{{ $stats['selesai'] }}</div>
                <div class="stat-lbl" style="color:#065f46;">Selesai</div>
                <a href="{{ route('registrations.index', ['department_id' => $selectedDept->id, 'status' => 'selesai']) }}" class="stat-detail" style="color:#065f46;">Lihat Detail &rarr;</a>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:#fee2e2;border-color:#fca5a5;">
            <div class="icon" style="color:#991b1b;"><i class="bi bi-x-circle-fill"></i></div>
            <div>
                <div class="stat-num" style="color:#991b1b;">{{ $stats['batal'] }}</div>
                <div class="stat-lbl" style="color:#991b1b;">Batal</div>
                <a href="{{ route('registrations.index', ['department_id' => $selectedDept->id, 'status' => 'batal']) }}" class="stat-detail" style="color:#991b1b;">Lihat Detail &rarr;</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Daftar Antrian -->
    <div class="col-lg-8">
        <div class="card fade-in">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ol me-2"></i>Antrian &mdash; {{ $selectedDept->nama_poli }} <span class="rs-diamond"></span></span>
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
                    <div class="empty-illus"><i class="bi bi-clipboard-x"></i></div>
                    <p class="mb-1 fw-600" style="color:var(--ink,#1B2430);">Belum ada antrian hari ini untuk {{ $selectedDept->nama_poli }}</p>
                    <p class="mb-3" style="font-size:.82rem;">Silakan daftarkan pasien baru untuk menambah antrian.</p>
                    <a href="{{ route('registrations.create') }}" class="btn btn-accent btn-sm">Daftarkan Pasien</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Panel Panggil Berikutnya -->
    <div class="col-lg-4">
        <div class="card fade-in fade-in-delay-1 text-center">
            <div class="card-header"><i class="bi bi-megaphone me-2"></i>Panggil Pasien <span class="rs-diamond"></span></div>
            <div class="card-body">
                @php $berikutnya = $antrian->firstWhere('status', 'menunggu'); @endphp
                @if($berikutnya)
                <div class="mb-3" style="background:linear-gradient(135deg,#eaf4ef,#f4f9f6);border:1px solid #d7ead9;border-radius:14px;padding:22px 20px;">
                    <div style="font-size:.72rem;font-weight:700;color:#64766D;text-transform:uppercase;letter-spacing:.1em;margin-bottom:12px;">Berikutnya</div>
                    <div class="call-arch-stage">
                        <div class="call-arch-ring r1"></div>
                        <div class="call-arch-ring r2"></div>
                        <div class="call-arch-frame">
                            <div class="stat-num-big">{{ $berikutnya->nomor_antrian }}</div>
                        </div>
                    </div>
                    <div class="fw-600 mt-1" style="font-size:.95rem;">{{ $berikutnya->patient->nama_pasien }}</div>
                </div>
                <button onclick="updateStatus({{ $berikutnya->id }},'dipanggil')" class="btn w-100" style="background:linear-gradient(135deg,var(--rs-gold),var(--rs-gold-light));color:#fff;border-radius:12px;padding:12px;font-weight:700;box-shadow:0 6px 18px rgba(201,162,39,.3);">
                    <i class="bi bi-megaphone me-2"></i>Panggil Sekarang
                </button>
                @else
                <div class="py-3">
                    <div class="empty-illus success"><i class="bi bi-check-circle-fill"></i></div>
                    <p class="mb-1 fw-700" style="color:var(--ink,#1B2430);">Semua antrian sudah dilayani!</p>
                    <p class="mb-3 text-muted" style="font-size:.82rem;">Tidak ada pasien dalam antrian saat ini.</p>
                    <a href="{{ route('antrian.index', ['department_id' => $selectedDept->id]) }}" class="btn btn-sm" style="background:var(--bg,#F7F8FA);color:#64766D;border-radius:10px;">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh Data
                    </a>
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