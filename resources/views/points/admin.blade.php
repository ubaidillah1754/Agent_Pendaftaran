@extends('layouts.app')

@section('page-title', 'Poin Karyawan')
@section('page-subtitle', 'Rekap dan ranking poin seluruh petugas pendaftaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Poin Karyawan</li>
@endsection

@section('content')
<div class="poin-karyawan-page">
<style>
    /* Rapikan jarak page-title, subtitle, breadcrumb di topbar */
    .page-title { margin-bottom: 2px !important; }
    .page-subtitle { margin: 0 0 4px !important; }
    #topbar .breadcrumb { margin-top: 2px !important; }

    /* Wrapper tunggal — containment agar tidak overflow */
    .poin-karyawan-page {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        min-width: 0;
    }

    .poin-karyawan-page .stat-card {
        min-width: 0;
        box-sizing: border-box;
        word-break: break-word;
    }

    .poin-karyawan-page .filter-bar {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--card-radius);
        padding: 14px 18px;
        box-sizing: border-box;
        width: 100%;
        max-width: 100%;
    }

    .poin-karyawan-page .filter-select {
        min-width: 140px;
        max-width: 200px;
        width: auto;
        padding: 8px 14px !important;
        font-size: .82rem !important;
        border-radius: 10px !important;
        border: 1.5px solid var(--border) !important;
        background-color: #fff;
        box-sizing: border-box;
    }

    .poin-karyawan-page .table-card {
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    .poin-karyawan-page .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        max-width: 100%;
    }

    .poin-karyawan-page #search-riwayat {
        max-width: 180px;
        box-sizing: border-box;
    }
</style>

@php
    $totalPoinBulan = $rekapPetugas->sum('total_poin');
    $totalPendaftaranBulan = $rekapPetugas->sum('total_pendaftaran');
    $topPetugas = $rekapPetugas->sortByDesc('total_poin')->first();
    $namaBulan = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y');
@endphp

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- ═══════════ HERO SUMMARY ═══════════ --}}
<div class="fade-in mb-4" style="background: linear-gradient(190deg, var(--primary) 0%, var(--primary-dark) 100%); border-radius: var(--card-radius); padding: 30px 32px; position: relative; overflow: hidden;">
    <div class="d-flex flex-wrap justify-content-between align-items-center" style="position: relative; z-index: 1;">
        <div>
            <div style="font-size:.68rem; font-weight:700; letter-spacing:.09em; text-transform:uppercase; color:rgba(255,255,255,.6); margin-bottom:8px; line-height:1;">
                <i class="bi bi-bar-chart-line" aria-hidden="true"></i> Rekap Kinerja Petugas
            </div>
            <h4 class="fw-bold mb-2" style="color:#fff; font-family:'Spectral', serif; font-size:1.4rem; line-height:1.2;">
                {{ $namaBulan }}
            </h4>
            <p class="mb-0" style="color:rgba(255,255,255,.75); font-size:.83rem; line-height:1.5;">
                {{ $totalPoinBulan }} poin terkumpul dari {{ $totalPendaftaranBulan }} pendaftaran seluruh petugas
            </p>
        </div>
        <i class="bi bi-award" style="font-size:5.5rem; color:rgba(255,255,255,.1); position:absolute; right:8px; top:50%; transform:translateY(-50%);" aria-hidden="true"></i>
    </div>
</div>

{{-- ═══════════ STAT CARDS ═══════════ --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card fade-in fade-in-delay-1">
            <div class="stat-icon"><i class="bi bi-people" aria-hidden="true"></i></div>
            <div>
                <div class="stat-label mb-1">Total Petugas Aktif</div>
                <div class="stat-value">{{ $rekapPetugas->count() }}</div>
                <div class="stat-sub">Terhitung bulan {{ $namaBulan }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card fade-in fade-in-delay-2">
            <div class="stat-icon" style="background:var(--accent-soft); color:var(--accent);">
                <i class="bi bi-star-fill" aria-hidden="true"></i>
            </div>
            <div>
                <div class="stat-label mb-1">Petugas Terbaik</div>
                <div class="stat-value" style="font-size:1.1rem; line-height:1.3;">{{ $topPetugas->name ?? '-' }}</div>
                <div class="stat-sub">{{ $topPetugas->total_poin ?? 0 }} poin bulan ini</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card fade-in fade-in-delay-3">
            <div class="stat-icon" style="background:var(--tile-soft); color:var(--tile);">
                <i class="bi bi-clipboard2-check" aria-hidden="true"></i>
            </div>
            <div>
                <div class="stat-label mb-1">Total Pendaftaran</div>
                <div class="stat-value">{{ $totalPendaftaranBulan }}</div>
                <div class="stat-sub">Diproses seluruh petugas</div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════ FILTER ═══════════ --}}
<div class="filter-bar mb-4 fade-in">
    <form method="GET" class="d-flex flex-wrap gap-3 align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-calendar3" style="color:var(--muted); font-size:.85rem;" aria-hidden="true"></i>
            <select name="bulan" class="form-select filter-select" onchange="this.form.submit()" aria-label="Pilih bulan">
                @foreach($bulanTersedia as $b)
                    <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $b)->translatedFormat('F Y') }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-person" style="color:var(--muted); font-size:.85rem;" aria-hidden="true"></i>
            <select name="user_id" class="form-select filter-select" onchange="this.form.submit()" aria-label="Filter petugas">
                <option value="">Semua Petugas</option>
                @foreach($petugasList as $p)
                    <option value="{{ $p->id }}" {{ request('user_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </div>

        @if(request('user_id'))
            <a href="{{ route('points.admin', ['bulan' => $bulan]) }}"
               class="d-inline-flex align-items-center gap-1 px-3"
               style="height:38px; background:var(--bg); color:var(--muted); border-radius:10px; font-size:.8rem; font-weight:600; text-decoration:none;">
                <i class="bi bi-x-lg" style="font-size:.7rem;"></i> Reset
            </a>
        @endif
    </form>
</div>

{{-- ═══════════ LEADERBOARD ═══════════ --}}
<div class="table-card mb-4 fade-in">
    <div class="card-header d-flex align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-trophy" style="color:var(--accent);" aria-hidden="true"></i>
            <span>Ranking Poin Petugas</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0" id="tbl-leaderboard">
            <thead>
                <tr>
                    <th class="ps-4" style="width:56px;">#</th>
                    <th>Petugas</th>
                    <th>Total Pendaftaran</th>
                    <th class="text-end">Poin Bulan Ini</th>
                    <th class="text-end">Saldo Poin</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapPetugas as $i => $petugas)
                    <tr>
                        <td class="ps-4">
                            @if($i === 0 && $petugas->total_poin > 0)
                                <i class="bi bi-trophy-fill" style="color:#D4AF37; font-size:1.05rem;" aria-label="Peringkat 1"></i>
                            @elseif($i === 1 && $petugas->total_poin > 0)
                                <i class="bi bi-trophy-fill" style="color:#A8A8A8; font-size:.98rem;" aria-label="Peringkat 2"></i>
                            @elseif($i === 2 && $petugas->total_poin > 0)
                                <i class="bi bi-trophy-fill" style="color:#C08552; font-size:.92rem;" aria-label="Peringkat 3"></i>
                            @else
                                <span style="color:var(--muted); font-weight:600; font-size:.85rem;">{{ $i + 1 }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px; height:32px; border-radius:var(--arch-sm); background:var(--primary-soft); color:var(--primary-dark); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.76rem; flex-shrink:0;">
                                    {{ strtoupper(substr($petugas->name, 0, 1)) }}
                                </div>
                                <span class="fw-semibold" style="color:var(--ink); font-size:.87rem;">{{ $petugas->name }}</span>
                            </div>
                        </td>
                        <td style="color:var(--muted); font-size:.85rem;">{{ $petugas->total_pendaftaran ?? 0 }} pendaftaran</td>
                        <td class="text-end">
                            <span class="badge" style="background:var(--primary-soft); color:var(--primary-dark); font-size:.76rem;">
                                {{ $petugas->total_poin ?? 0 }} poin
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="badge" style="background:var(--tile-soft); color:var(--tile); font-size:.76rem;">
                                {{ $petugas->totalPoints() }} poin
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm" style="background:var(--bg); color:var(--primary); font-weight:700;" onclick="openRedeemModal({{ $petugas->id }}, '{{ $petugas->name }}', {{ $petugas->totalPoints() }})">
                                Tukar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5" style="color:var(--muted);">
                            <i class="bi bi-people d-block mb-2" style="font-size:1.8rem; opacity:.4;" aria-hidden="true"></i>
                            <span style="font-size:.85rem;">Belum ada data petugas.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ═══════════ RIWAYAT GABUNGAN ═══════════ --}}
<div class="table-card fade-in">
    <div class="card-header d-flex align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-clock-history" aria-hidden="true"></i>
            <span>Riwayat Poin Semua Petugas</span>
        </div>
        {{-- Search box native, tanpa library eksternal --}}
        <div style="position:relative;">
            <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--muted); font-size:.8rem; pointer-events:none;"></i>
            <input type="text" id="search-riwayat" placeholder="Cari..."
                   style="padding:6px 12px 6px 30px; border:1.5px solid var(--border); border-radius:9px; font-size:.82rem; width:180px; outline:none;"
                   oninput="filterTable('tbl-riwayat', this.value)">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0" id="tbl-riwayat">
            <thead>
                <tr>
                    <th class="ps-4">Tanggal</th>
                    <th>Petugas</th>
                    <th>Pasien</th>
                    <th>Poli</th>
                    <th class="text-end pe-4">Poin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $item)
                    <tr>
                        <td class="ps-4" style="color:var(--muted); font-size:.85rem; line-height:1.4;">
                            {{ $item->created_at->translatedFormat('d M Y') }}
                            <div style="font-size:.72rem; color:#9AA6A1;">{{ $item->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td class="fw-semibold" style="color:var(--ink); font-size:.87rem;">{{ $item->user->name ?? '-' }}</td>
                        <td style="color:var(--muted); font-size:.85rem;">{{ $item->registration->patient->name ?? '-' }}</td>
                        <td>
                            <span class="badge" style="background:var(--tile-soft); color:var(--tile);">
                                {{ $item->department->name ?? '-' }}
                            </span>
                        </td>
                        <td class="text-end pe-4 fw-semibold" style="color:#0F7B63; font-size:.87rem;">+{{ $item->points }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5" style="color:var(--muted);">
                            <i class="bi bi-inbox d-block mb-2" style="font-size:1.8rem; opacity:.4;" aria-hidden="true"></i>
                            <span style="font-size:.85rem;">Belum ada riwayat poin bulan ini.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ═══════════ RIWAYAT PENUKARAN POIN (CRUD) ═══════════ --}}
<div class="table-card mt-4 fade-in">
    <div class="card-header d-flex align-items-center justify-content-between gap-2 flex-wrap">
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <i class="bi bi-arrow-left-right" style="color:var(--accent);" aria-hidden="true"></i>
            <span>Riwayat Penukaran Poin</span>
        </div>
        {{-- Filter status penukaran --}}
        <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <select name="filter_status" class="form-select form-select-sm" style="max-width:130px; min-width:110px; border-radius:8px;"
                    onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="pending"   {{ request('filter_status') === 'pending'   ? 'selected' : '' }}>Menunggu</option>
                <option value="disetujui" {{ request('filter_status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="selesai"   {{ request('filter_status') === 'selesai'   ? 'selected' : '' }}>Selesai</option>
                <option value="ditolak"   {{ request('filter_status') === 'ditolak'   ? 'selected' : '' }}>Ditolak</option>
            </select>
            <select name="filter_user_id" class="form-select form-select-sm" style="max-width:150px; min-width:110px; border-radius:8px;"
                    onchange="this.form.submit()">
                <option value="">Semua Petugas</option>
                @foreach($petugasList as $p)
                    <option value="{{ $p->id }}" {{ request('filter_user_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Tanggal</th>
                    <th>Petugas</th>
                    <th class="text-center">Poin</th>
                    <th class="text-center">Jenis</th>
                    <th class="text-center">Status</th>
                    <th>Catatan</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatTukar as $item)
                <tr>
                    <td class="ps-4" style="font-size:.85rem; color:var(--muted); white-space:nowrap;">
                        {{ $item->created_at->translatedFormat('d M Y') }}
                        <div style="font-size:.72rem; color:#9AA6A1;">{{ $item->created_at->format('H:i') }} WIB</div>
                    </td>
                    <td style="font-size:.875rem; font-weight:600; color:var(--ink);">
                        {{ $item->user->name ?? '-' }}
                    </td>
                    <td class="text-center">
                        <span style="font-family:'Spectral',serif; font-weight:800; color:var(--accent); font-size:1rem;">
                            {{ number_format($item->points) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge" style="background:var(--accent-soft); color:#7A5E17; font-size:.73rem;">
                            {{ $item->type_label }}
                        </span>
                    </td>
                    <td class="text-center">
                        @php $sc = $item->status_color; @endphp
                        <span class="badge"
                              style="background:{{ $sc['bg'] }}; color:{{ $sc['color'] }}; font-size:.73rem;">
                            {{ $item->status_label }}
                        </span>
                    </td>
                    <td style="font-size:.8rem; color:var(--muted); max-width:160px;">
                        {{ $item->catatan ? \Illuminate\Support\Str::limit($item->catatan, 40) : '—' }}
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex gap-1 justify-content-end">
                            @if(!$item->isFinal())
                            <button type="button"
                                    class="btn btn-sm"
                                    style="background:var(--primary-soft); color:var(--primary); border-radius:7px;"
                                    onclick="openUpdateModal({{ $item->id }}, '{{ $item->status }}', '{{ addslashes($item->catatan ?? '') }}')"
                                    title="Update status">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            @endif
                            @if($item->status === 'pending')
                            <form action="{{ route('points.redemption.destroy', $item) }}" method="POST"
                                  onsubmit="return confirm('Hapus data penukaran ini? Tindakan tidak dapat dibatalkan.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="btn btn-sm"
                                        style="background:#FEE2E2; color:#991B1B; border-radius:7px;"
                                        title="Hapus">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5" style="color:var(--muted);">
                        <i class="bi bi-inbox d-block mb-2" style="font-size:1.8rem; opacity:.4;" aria-hidden="true"></i>
                        <span style="font-size:.85rem;">Belum ada riwayat penukaran poin.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($riwayatTukar->hasPages())
    <div class="px-4 py-3" style="border-top:1px solid var(--border);">
        {{ $riwayatTukar->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<!-- Modal Update Status Penukaran -->
<div class="modal fade" id="updateRedemptionModal" tabindex="-1" aria-labelledby="updateRedemptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--card-radius); border: none;">
            <form id="updateRedemptionForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header" style="border-bottom: 1px solid var(--border);">
                    <h5 class="modal-title fw-bold" id="updateRedemptionModalLabel">
                        <i class="bi bi-arrow-left-right me-2" style="color:var(--accent);"></i>Update Status Penukaran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="updateStatus" class="form-select" required>
                            <option value="pending">Menunggu</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="selesai">Selesai</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Admin</label>
                        <textarea name="catatan" id="updateCatatan" class="form-control" rows="3"
                                  placeholder="Keterangan tambahan (opsional)"
                                  style="resize:vertical; min-height:80px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border);">
                    <button type="button" class="btn text-muted" style="background:var(--bg);" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tukar Poin (existing, updated with catatan field) -->
<div class="modal fade" id="redeemModal" tabindex="-1" aria-labelledby="redeemModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: var(--card-radius); border: none;">
      <form action="{{ route('points.redeem') }}" method="POST">
        @csrf
        <div class="modal-header" style="border-bottom: 1px solid var(--border);">
          <h5 class="modal-title fw-bold" id="redeemModalLabel">Tukar Poin Karyawan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <p class="mb-3" style="font-size: .9rem;">Tukar poin milik <strong id="redeemUserName" style="color:var(--ink);"></strong>. Saldo saat ini: <strong id="redeemUserBalance" style="color:var(--primary);"></strong> poin.</p>
          <input type="hidden" name="user_id" id="redeemUserId">
          <div class="mb-3">
            <label class="form-label">Jumlah Poin <span class="text-danger">*</span></label>
            <input type="number" name="points" id="redeemPoints" class="form-control" min="1" required placeholder="Masukkan jumlah poin yang ingin ditukar">
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis Penukaran <span class="text-danger">*</span></label>
            <select name="type" class="form-select" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="uang">Uang Tunai</option>
                <option value="merchandise">Merchandise</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea name="catatan" class="form-control" rows="2"
                      placeholder="Catatan tambahan (opsional)"
                      style="resize:vertical;"></textarea>
          </div>
        </div>
        <div class="modal-footer" style="border-top: 1px solid var(--border);">
          <button type="button" class="btn text-muted" style="background:var(--bg);" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Buat Permintaan Tukar</button>
        </div>
      </form>
    </div>
  </div>
</div>

</div>{{-- /.poin-karyawan-page --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openRedeemModal(userId, userName, balance) {
    document.getElementById('redeemUserId').value = userId;
    document.getElementById('redeemUserName').textContent = userName;
    document.getElementById('redeemUserBalance').textContent = balance;
    document.getElementById('redeemPoints').max = balance;
    var modal = new bootstrap.Modal(document.getElementById('redeemModal'));
    modal.show();
}

function openUpdateModal(redemptionId, currentStatus, currentCatatan) {
    const form = document.getElementById('updateRedemptionForm');
    form.action = '/poin/tukar/' + redemptionId + '/status';
    document.getElementById('updateStatus').value = currentStatus;
    document.getElementById('updateCatatan').value = currentCatatan;
    var modal = new bootstrap.Modal(document.getElementById('updateRedemptionModal'));
    modal.show();
}

/**
 * Filter tabel secara native — tanpa library eksternal.
 * Menyembunyikan baris yang tidak mengandung teks pencarian.
 */
function filterTable(tableId, query) {
    const table = document.getElementById(tableId);
    if (!table) return;
    const q = query.toLowerCase().trim();
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        row.style.display = q === '' || row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
@endpush