@extends('layouts.app')
@section('title','Data Poli')
@section('page-title','Master Data Poli')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Data Poli</li>
@endsection

@section('content')
<style>
    .poli-stat-row { display: flex; gap: 12px; flex-wrap: wrap; }
    .poli-stat {
        display: flex; align-items: center; gap: 12px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 10px 16px 10px 12px;
        min-width: 148px;
    }
    .poli-stat .icon-ring {
        flex: none; width: 38px; height: 38px;
        border-radius: 19px 19px 6px 6px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
    }
    .poli-stat .icon-ring.primary { background: #E9F3EE; color: var(--primary); }
    .poli-stat .icon-ring.gold    { background: #FBF6E9; color: #C9A227; }
    .poli-stat .icon-ring.success { background: #ECFDF5; color: #059669; }
    .poli-stat .stat-num { font-weight: 800; font-size: 1.15rem; line-height: 1; color: #142019; }
    .poli-stat .stat-label { font-size: .7rem; color: #6B7684; margin-top: 3px; }

    .table-card.premium {
        border-radius: 18px;
        border: 1px solid #eef0f3;
        box-shadow: 0 1px 2px rgba(16,24,32,.03), 0 16px 32px -22px rgba(16,24,32,.16);
        overflow: hidden;
    }
    .table-card.premium thead tr { background: #F7F9F8; }
    .table-card.premium thead th {
        font-size: .72rem; letter-spacing: .04em; text-transform: uppercase;
        color: #6B7684; font-weight: 700; border-bottom: 1px solid #eef0f3;
        padding-top: 14px; padding-bottom: 14px;
    }
    .table-card.premium tbody tr { transition: background .12s; }
    .table-card.premium tbody tr:hover { background: #FAFBFA; }
    .table-card.premium tbody td { padding-top: 13px; padding-bottom: 13px; }

    .kode-badge {
        display: inline-block;
        background: linear-gradient(135deg, var(--primary), #12885F);
        color: #fff; font-weight: 800; font-size: .65rem; letter-spacing: .08em;
        padding: 2px 7px; border-radius: 5px;
        text-transform: uppercase; white-space: nowrap;
    }
    .status-pill {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: .74rem; font-weight: 700; padding: 4px 10px; border-radius: 999px;
    }
    .status-pill.active { background: #d1fae5; color: #065f46; }
    .status-pill.inactive { background: #fee2e2; color: #991b1b; }
    .status-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    .btn-icon.premium {
        width: 34px; height: 34px; border-radius: 9px;
        display: inline-flex; align-items: center; justify-content: center;
        border: none; transition: transform .12s;
    }
    .btn-icon.premium:hover { transform: translateY(-1px); }

    .btn-accent.premium {
        background: linear-gradient(135deg, var(--primary), #12885F);
        color: #fff; border: none; font-weight: 700;
        box-shadow: 0 8px 18px -8px rgba(11,107,79,.5);
    }
    .btn-accent.premium:hover { color: #fff; opacity: .94; }
</style>

<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
    <div>
        <h5 class="mb-1 fw-700" style="color:var(--primary)">Daftar Poli / Departemen</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Kelola data poli pelayanan rumah sakit</p>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="poli-stat-row">
            <div class="poli-stat">
                <div class="icon-ring primary"><i class="bi bi-building"></i></div>
                <div>
                    <div class="stat-num">{{ $totalPoli ?? $departments->count() }}</div>
                    <div class="stat-label">Total Poli</div>
                </div>
            </div>
            <div class="poli-stat">
                <div class="icon-ring gold"><i class="bi bi-person-badge"></i></div>
                <div>
                    <div class="stat-num">{{ $totalDokter ?? 0 }}</div>
                    <div class="stat-label">Total Dokter</div>
                </div>
            </div>
            <div class="poli-stat">
                <div class="icon-ring success"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $poliAktifCount ?? 0 }}</div>
                    <div class="stat-label">Poli Aktif</div>
                </div>
            </div>
        </div>

        <a href="{{ route('departments.create') }}" class="btn btn-accent premium">
            <i class="bi bi-plus-circle me-1"></i> Tambah Poli
        </a>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 mb-3">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="small">{{ session('error') }}</div>
    </div>
@endif

<div class="card table-card premium fade-in">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 datatable">
            <thead>
                <tr>
                    <th width="60">#</th>
                    <th width="70">Kode</th>
                    <th>Nama Poli</th>
                    <th>Deskripsi</th>
                    <th width="80" class="text-center">Dokter</th>
                    <th width="90" class="text-center">Status</th>
                    <th width="120" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $i => $dept)
                <tr>
                    <td class="text-muted" style="font-size:.8rem;">
                        {{ $departments instanceof \Illuminate\Pagination\AbstractPaginator ? $departments->firstItem() + $i : $i + 1 }}
                    </td>
                    <td>
                        <span class="kode-badge">{{ $dept->kode_poli }}</span>
                    </td>
                    <td class="fw-600">{{ $dept->nama_poli }}</td>
                    <td class="text-muted" style="font-size:.82rem;max-width:250px;">
                        {{ $dept->deskripsi ? Str::limit($dept->deskripsi, 60, '...') : '-' }}
                    </td>
                    <td class="text-center">
                        <span class="badge" style="background:#eff6ff;color:#1e40af;">{{ $dept->doctors_count }}</span>
                    </td>
                    <td class="text-center">
                        @if($dept->is_active)
                            <span class="status-pill active"><span class="dot"></span>Aktif</span>
                        @else
                            <span class="status-pill inactive"><span class="dot"></span>Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('departments.edit', $dept) }}" class="btn-icon premium" style="background:#eff6ff;color:var(--primary);" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <form action="{{ route('departments.destroy', $dept) }}" method="POST"
                                  onsubmit="return confirm('Yakin hapus poli {{ $dept->nama_poli }}?{{ $dept->doctors_count > 0 ? ' Poli ini memiliki '.$dept->doctors_count.' dokter terdaftar.' : '' }}')">
                                @csrf @method('DELETE')
                                <button class="btn-icon premium" style="background:#fef2f2;color:#ef4444;" title="Hapus">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size:2.5rem;display:block;margin-bottom:8px;"></i>
                    Belum ada data poli. <a href="{{ route('departments.create') }}">Tambah sekarang</a>
                </td></tr>
                @endforelse
            </tbody>
        </table>
</div>
@endsection