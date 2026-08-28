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
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Spectral:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --rs-green:      #0B6B4F;
        --rs-green-dark: #063D2C;
        --rs-green-pale: #EAF4EF;
        --rs-gold:       #C9A227;
        --rs-gold-light: #E8C766;
        --rs-tile:       #0E7490;
        --rs-ink:        #16241E;
        --rs-body-font:  'Plus Jakarta Sans', system-ui, sans-serif;
    }

    #content-wrap, .card, .poli-tab, .stat-card, .btn-action { font-family: var(--rs-body-font); }

    /* ===== Trust / status strip — the "known hospital" credibility cue ===== */
    .rs-trust-bar {
        display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px;
        background:linear-gradient(90deg,var(--rs-green-dark),var(--rs-green));
        color:#fff;border-radius:14px;padding:12px 20px;margin-bottom:18px;
        box-shadow:0 8px 22px rgba(6,61,44,.18);
    }
    .rs-trust-bar .rs-trust-items { display:flex;flex-wrap:wrap;gap:18px;align-items:center; }
    .rs-trust-item { display:flex;align-items:center;gap:7px;font-size:.74rem;font-weight:600;color:#dff2e8;letter-spacing:.02em; }
    .rs-trust-item i { color:var(--rs-gold-light);font-size:.85rem; }
    .rs-trust-live { display:flex;align-items:center;gap:8px;font-size:.74rem;font-weight:700;color:#fff; }
    .rs-live-dot { width:7px;height:7px;border-radius:50%;background:#5EEAA0;box-shadow:0 0 0 0 rgba(94,234,160,.6);animation:live-ping 1.8s infinite; }
    @keyframes live-ping { 0%{box-shadow:0 0 0 0 rgba(94,234,160,.55)} 70%{box-shadow:0 0 0 7px rgba(94,234,160,0)} 100%{box-shadow:0 0 0 0 rgba(94,234,160,0)} }

    /* ===== Poli filter tabs — arch-badge pills ===== */
    .poli-tabs { display:flex; flex-wrap:wrap; gap:9px; }
    .poli-tab {
        display:inline-flex;align-items:center;gap:8px;
        padding:9px 16px 9px 10px;border-radius:32px 32px 10px 10px;border:1.5px solid #e5e0d0;cursor:pointer;
        transition:all .2s;background:#fff;font-size:.78rem;font-weight:700;
        color:#475d52;text-decoration:none;white-space:nowrap;
    }
    .poli-tab .poli-tab-ic {
        width:24px;height:24px;border-radius:12px;display:flex;align-items:center;justify-content:center;
        background:var(--rs-green-pale);color:var(--rs-green);font-size:.72rem;flex-shrink:0;
    }
    .poli-tab.active { border-color:var(--rs-gold);background:linear-gradient(135deg,var(--rs-gold),var(--rs-gold-light));color:#fff;box-shadow:0 4px 14px rgba(201,162,39,.28); }
    .poli-tab.active .poli-tab-ic { background:rgba(255,255,255,.25);color:#fff; }
    .poli-tab:hover:not(.active) { border-color:var(--rs-green);color:var(--rs-green); }

    .antrian-row { display:flex;align-items:center;gap:14px;padding:13px 0;border-bottom:1px dashed #ece6d6;transition:background .2s; }
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

    .patient-doctor { display:flex; align-items:center; gap:6px; font-size:.75rem; color:#64766D; }
    .patient-doctor i { color:var(--rs-tile); font-size:.72rem; }

    /* ===== Stat cards — horizontal, arch icon + detail link ===== */
    .stat-card {
        border-radius:16px;padding:16px 18px;display:flex;align-items:center;gap:14px;
        border:1.5px solid; position:relative; overflow:hidden; transition:transform .18s, box-shadow .18s;
    }
    .stat-card:hover { transform:translateY(-2px); box-shadow:0 10px 22px rgba(6,61,44,.08); }
    .stat-card .icon {
        width:46px;height:46px;border-radius:23px 23px 7px 7px;flex-shrink:0;
        display:flex;align-items:center;justify-content:center;font-size:1.2rem;background:#fff;
    }
    .stat-card .stat-num { font-family:'Spectral',serif;font-size:1.65rem;font-weight:800;line-height:1; }
    .stat-card .stat-lbl { font-size:.76rem;font-weight:700;margin-top:2px;text-transform:uppercase;letter-spacing:.04em; }
    .stat-card .stat-detail {
        display:block;font-size:.72rem;font-weight:700;text-decoration:none;margin-top:6px;
    }
    .stat-card .stat-detail:hover { text-decoration:underline; }

    .btn-action { border-radius:10px;font-size:.78rem;font-weight:600;padding:6px 12px; }

    .rs-diamond {
        display:inline-block;width:7px;height:7px;background:var(--rs-gold);
        transform:rotate(45deg);margin:0 2px;vertical-align:middle;
    }

    /* ===== Panggil pasien — arch signature, matching the TV display ===== */
    .call-arch-stage { position:relative; width:158px; height:158px; margin:0 auto 14px; display:flex; align-items:center; justify-content:center; }
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
        position:relative; z-index:2; width:122px; height:134px;
        background:linear-gradient(180deg,#eaf4ef,#f4f9f6);
        border:1.5px solid var(--rs-gold);
        border-radius: 61px 61px 10px 10px;
        display:flex; align-items:center; justify-content:center;
        box-shadow: 0 6px 20px rgba(11,107,79,.1);
    }
    .call-arch-frame .stat-num-big {
        font-family:'Spectral',serif; font-size:2.3rem;font-weight:800;color:var(--rs-green);line-height:1;
    }
    .rs-eta { font-size:.72rem;color:#64766D;margin-top:8px; }

    /* ===== Empty states ===== */
    .empty-illus {
        width:64px;height:64px;border-radius:32px 32px 8px 8px;margin:0 auto 16px;
        background:var(--primary-soft,#E9F3EE);display:flex;align-items:center;justify-content:center;
    }
    .empty-illus i { font-size:1.7rem;color:var(--rs-green); }
    .empty-illus.success { background:#d1fae5; }
    .empty-illus.success i { color:#059669; }

    /* ===== Footer accreditation strip ===== */
    .rs-accred-strip {
        display:flex;flex-wrap:wrap;gap:8px 22px;align-items:center;justify-content:center;
        margin-top:18px;padding:14px 16px;border-top:1px dashed #ece6d6;
    }
    .rs-accred-item { display:flex;align-items:center;gap:7px;font-size:.72rem;font-weight:600;color:#64766D; }
    .rs-accred-item i { color:var(--rs-gold);font-size:.85rem; }

    @media (prefers-reduced-motion: reduce) {
        .call-arch-ring, .no-dipanggil, .rs-live-dot { animation:none; }
    }
</style>
@endpush

@section('content')
<!-- Trust strip -->
<div class="rs-trust-bar fade-in">
    <div class="rs-trust-items">
        <span class="rs-trust-item"><i class="bi bi-patch-check-fill"></i>Terakreditasi KARS Paripurna</span>
        <span class="rs-trust-item"><i class="bi bi-shield-check"></i>Mitra BPJS Kesehatan</span>
        <span class="rs-trust-item"><i class="bi bi-telephone-fill"></i>IGD 24 Jam &mdash; 119</span>
    </div>
    <div class="rs-trust-live"><span class="rs-live-dot"></span>Papan antrian real-time</div>
</div>

<!-- Filter Poli -->
<div class="mb-4 poli-tabs fade-in">
    @foreach($departments as $dept)
    <a href="{{ route('antrian.index', ['department_id' => $dept->id]) }}"
       class="poli-tab {{ $selectedDept?->id == $dept->id ? 'active' : '' }}">
        <span class="poli-tab-ic"><i class="bi bi-building"></i></span>{{ $dept->nama_poli }}
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
                        <div class="patient-doctor"><i class="bi bi-person-badge"></i>dr. {{ $reg->doctor->nama_dokter ?? '-' }}</div>
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
                    <div class="rs-eta"><i class="bi bi-clock-history me-1"></i>Poli {{ $selectedDept->nama_poli }}</div>
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

<!-- Accreditation / trust footer -->
<div class="rs-accred-strip fade-in">
    <span class="rs-accred-item"><i class="bi bi-award-fill"></i>Akreditasi KARS Paripurna</span>
    <span class="rs-accred-item"><i class="bi bi-globe2"></i>ISO 9001:2015</span>
    <span class="rs-accred-item"><i class="bi bi-heart-pulse-fill"></i>Mitra BPJS &amp; Asuransi Swasta</span>
    <span class="rs-accred-item"><i class="bi bi-people-fill"></i>Dokter Spesialis Berpengalaman</span>
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