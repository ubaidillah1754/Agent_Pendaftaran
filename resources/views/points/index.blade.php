{{-- resources/views/points/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="points-page">

    {{-- ═══════════ HEADER ═══════════ --}}
    <div class="page-header mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <div>
                <nav class="breadcrumb-custom mb-2">
                    <a href="{{ route('dashboard') }}"><i class="bi bi-house"></i> Dashboard</a>
                    <i class="bi bi-chevron-right mx-1"></i>
                    <span>Poin Petugas</span>
                </nav>
                <h3 class="fw-bold mb-1" style="color:#0F172A; letter-spacing:-.5px;">Poin Petugas Pendaftaran</h3>
                <p class="mb-0 text-muted" style="font-size:14px;">
                    Pantau kinerja dan apresiasi atas aktivitas pendaftaran pasien yang Anda proses.
                </p>
            </div>
            <span class="date-badge">
                <i class="bi bi-calendar-check"></i> {{ now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>

    {{-- ═══════════ HERO STRIP (samain sama dashboard) ═══════════ --}}
    <div class="hero-strip mb-4">
        <div>
            <div class="hero-eyebrow"><i class="bi bi-moon-stars"></i> POIN ANDA HARI INI</div>
            <div class="hero-title">{{ $totalPoin }} Poin Terkumpul</div>
            <div class="hero-sub">dari {{ $riwayat->count() }} pendaftaran yang berhasil Anda proses</div>
        </div>
        <i class="bi bi-award hero-icon"></i>
    </div>

    {{-- ═══════════ STAT CARDS ═══════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon icon-mint"><i class="bi bi-award-fill"></i></div>
                <div class="stat-label">TOTAL POIN</div>
                <div class="stat-value">{{ $totalPoin }}</div>
                <div class="stat-sub">Poin terkumpul</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon icon-amber"><i class="bi bi-hospital"></i></div>
                <div class="stat-label">POLI TERAKTIF</div>
                @php $topPoli = $rekapPerPoli->sortByDesc('total')->first(); @endphp
                <div class="stat-value-sm">{{ $topPoli->department->name ?? '-' }}</div>
                <div class="stat-sub">{{ $topPoli->total ?? 0 }} poin dari poli ini</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon icon-blue"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="stat-label">TOTAL POLI DITANGANI</div>
                <div class="stat-value-sm">{{ $rekapPerPoli->count() }} Poli</div>
                <div class="stat-sub">Sebaran kontribusi pendaftaran</div>
            </div>
        </div>
    </div>

    {{-- ═══════════ REKAP PER POLI ═══════════ --}}
    <div class="panel mb-4">
        <div class="panel-header">
            <i class="bi bi-clipboard2-pulse"></i>
            <span>Rekap Poin per Poli</span>
        </div>
        <div class="panel-body">
            <div class="d-flex flex-wrap gap-2">
                @forelse($rekapPerPoli as $rekap)
                    <div class="poli-chip">
                        <span class="poli-dot"></span>
                        {{ $rekap->department->name ?? '-' }}
                        <b>{{ $rekap->total }}</b>
                    </div>
                @empty
                    <span class="text-muted" style="font-size:13px;">Belum ada poin tercatat.</span>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ═══════════ RIWAYAT ═══════════ --}}
    <div class="panel">
        <div class="panel-header d-flex justify-content-between">
            <span><i class="bi bi-clock-history"></i> Riwayat Poin</span>
        </div>

        <div class="table-responsive">
            <table class="table points-table mb-0 datatable">
                <thead>
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Pasien</th>
                        <th>Poli</th>
                        <th class="text-end pe-4">Poin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)
                        @php $nama = $item->registration->patient->name ?? '-'; @endphp
                        <tr>
                            <td class="ps-4 text-muted">
                                {{ $item->created_at->translatedFormat('d M Y') }}
                                <div style="font-size:12px; color:#94A3B8;">{{ $item->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-initial">{{ strtoupper(substr($nama, 0, 1)) }}</div>
                                    <span class="fw-medium" style="color:#0F172A;">{{ $nama }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="poli-badge">{{ $item->department->name ?? '-' }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <span class="point-pill">+{{ $item->points }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size:32px; color:#CBD5E1;"></i>
                                <div class="text-muted mt-2" style="font-size:14px;">Belum ada riwayat poin.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
    .points-page { font-family: 'Inter', 'Plus Jakarta Sans', sans-serif; }

    /* Breadcrumb */
    .breadcrumb-custom { font-size: 13px; color: #64748B; }
    .breadcrumb-custom a { color: #64748B; text-decoration: none; }
    .breadcrumb-custom a:hover { color: #0B4D3C; }

    .date-badge {
        display: inline-flex; align-items: center; gap: 8px;
        background: #fff; color: #0B4D3C;
        font-size: 13px; font-weight: 600;
        padding: 10px 16px; border-radius: 999px;
        border: 1px solid #E2E8F0;
    }

    /* Hero strip — samain persis sama dashboard (hijau tua gradasi) */
    .hero-strip {
        background: linear-gradient(135deg, #0B4D3C 0%, #146B52 100%);
        border-radius: 20px;
        padding: 28px 32px;
        display: flex; align-items: center; justify-content: space-between;
        position: relative; overflow: hidden;
        color: #fff;
    }
    .hero-eyebrow {
        font-size: 12px; font-weight: 600; letter-spacing: .5px;
        color: #A7F3D0; margin-bottom: 8px;
        display: flex; align-items: center; gap: 6px;
    }
    .hero-title { font-size: 26px; font-weight: 700; margin-bottom: 4px; }
    .hero-sub { font-size: 14px; color: rgba(255,255,255,.8); }
    .hero-icon { font-size: 90px; opacity: .12; position: absolute; right: 28px; top: 50%; transform: translateY(-50%); }

    /* Stat Cards — putih dengan ikon berwarna soft, sama seperti kartu dashboard */
    .stat-card {
        background: #fff; border: 1px solid #E2E8F0; border-radius: 16px;
        padding: 20px 22px; height: 100%;
    }
    .stat-icon {
        width: 42px; height: 42px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; margin-bottom: 14px;
    }
    .icon-mint  { background: #E9F7EF; color: #198754; }
    .icon-amber { background: #FEF3C7; color: #D97706; }
    .icon-blue  { background: #E0F2FE; color: #0284C7; }

    .stat-label { font-size: 12px; font-weight: 600; letter-spacing: .4px; color: #94A3B8; margin-bottom: 6px; }
    .stat-value { font-size: 32px; font-weight: 700; color: #0F172A; line-height: 1.1; }
    .stat-value-sm { font-size: 18px; font-weight: 700; color: #0F172A; line-height: 1.3; }
    .stat-sub { font-size: 12.5px; color: #94A3B8; margin-top: 6px; }

    /* Panels */
    .panel { background: #fff; border: 1px solid #E2E8F0; border-radius: 18px; overflow: hidden; }
    .panel-header {
        display: flex; align-items: center; gap: 10px;
        padding: 18px 20px; border-bottom: 1px solid #F1F5F9;
        font-weight: 600; font-size: 14.5px; color: #0F172A;
    }
    .panel-header i { color: #0B4D3C; font-size: 18px; }
    .panel-body { padding: 20px; }

    /* Poli chip */
    .poli-chip {
        display: inline-flex; align-items: center; gap: 8px;
        background: #F0FDF6; border: 1px solid #D3F0E0;
        padding: 9px 14px; border-radius: 10px;
        font-size: 13px; color: #14532D;
    }
    .poli-chip b { color: #0B4D3C; margin-left: 2px; }
    .poli-dot { width: 8px; height: 8px; border-radius: 50%; background: #198754; }

    /* Table */
    .points-table thead tr { background: #F8FAFC; }
    .points-table thead th {
        font-size: 12.5px; font-weight: 600; color: #64748B;
        text-transform: uppercase; letter-spacing: .4px;
        padding: 14px 12px; border-bottom: 1px solid #E2E8F0;
    }
    .points-table tbody td { padding: 14px 12px; font-size: 14px; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
    .points-table tbody tr:hover { background: #F8FAFC; }
    .points-table tbody tr:last-child td { border-bottom: none; }

    .avatar-initial {
        width: 32px; height: 32px; border-radius: 50%;
        background: #E9F7EF; color: #198754;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 13px;
    }
    .poli-badge {
        display: inline-block; background: #E0F2FE; color: #0369A1;
        font-size: 12px; font-weight: 600; padding: 5px 10px; border-radius: 8px;
    }
    .point-pill {
        display: inline-block; background: #DCFCE7; color: #15803D;
        font-weight: 700; font-size: 13px; padding: 5px 12px; border-radius: 999px;
    }
</style>
@endsection