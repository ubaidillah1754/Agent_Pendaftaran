@extends('layouts.app')
@section('page-title', 'Pengajuan Poin — Admin')
@section('page-subtitle', 'Kelola dan proses seluruh pengajuan poin petugas.')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Pengajuan Poin</li>
@endsection

@section('content')
<div class="page-content">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stat Badge --}}
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <div class="px-4 py-3 rounded-3 d-flex align-items-center gap-3" style="background:linear-gradient(120deg,#92400E,#D97706); color:#fff;">
                <i class="bi bi-hourglass-split" style="font-size:1.5rem; opacity:.85;"></i>
                <div>
                    <div style="font-size:.7rem; opacity:.75; font-weight:600; text-transform:uppercase; letter-spacing:.06em;">Menunggu Persetujuan</div>
                    <div style="font-size:1.6rem; font-weight:800; line-height:1.1;">{{ $pendingCount }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card mb-4" style="border-radius:12px; border:1px solid #E2E8F0;">
        <div class="card-body py-3 px-4">
            <form method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                <label class="fw-600" style="font-size:.82rem; color:#64748B;">Filter Status:</label>
                <select name="status" class="form-select form-select-sm" style="max-width:160px; border-radius:8px;" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Menunggu</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
                @if(request('status'))
                    <a href="{{ route('point-requests.admin') }}" class="btn btn-sm text-muted" style="background:#F1F5F9; border-radius:8px;">Reset</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card" style="border-radius:16px; border:1px solid #E2E8F0; overflow:hidden; box-shadow:0 2px 12px -6px rgba(0,0,0,.08);">
        <div class="card-header bg-transparent py-3 px-4" style="border-bottom:1px solid #E2E8F0;">
            <span style="font-weight:700; color:#0F172A; font-size:.95rem;">
                <i class="bi bi-person-check me-2" style="color:var(--primary);"></i>Daftar Pengajuan Poin Petugas
            </span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0" style="font-size:.875rem;">
                <thead>
                    <tr style="background:#F8FAFC;">
                        <th class="ps-4" style="color:#64748B; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; padding:12px 14px;">Petugas</th>
                        <th style="color:#64748B; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; padding:12px 14px;">Poin</th>
                        <th style="color:#64748B; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; padding:12px 14px;">Alasan</th>
                        <th style="color:#64748B; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; padding:12px 14px;">Tanggal</th>
                        <th class="text-center" style="color:#64748B; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; padding:12px 14px;">Status</th>
                        <th class="text-end pe-4" style="color:#64748B; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; padding:12px 14px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr style="border-bottom:1px solid #F1F5F9;">
                        <td class="ps-4" style="padding:14px;">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px; height:32px; border-radius:50%; background:#E6F6F0; color:#0F7B63; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.8rem; flex-shrink:0;">
                                    {{ strtoupper(substr($req->user->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600; color:#0F172A;">{{ $req->user->name ?? '-' }}</div>
                                    <div style="font-size:.72rem; color:#94A3B8;">{{ $req->user->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:14px;">
                            <span style="font-weight:800; color:#B8912E; font-size:1.05rem;">{{ number_format($req->points) }}</span>
                            <span style="color:#64748B; font-size:.82rem;"> poin</span>
                        </td>
                        <td style="padding:14px; max-width:220px;">
                            <span style="color:#0F172A; font-size:.85rem;">{{ Str::limit($req->reason, 55) }}</span>
                            @if(strlen($req->reason) > 55)
                                <button type="button" class="btn btn-link p-0 ms-1" style="font-size:.75rem; color:var(--primary);" data-bs-toggle="modal" data-bs-target="#reasonModal{{ $req->id }}">lihat</button>
                                <div class="modal fade" id="reasonModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content" style="border-radius:16px;">
                                            <div class="modal-header" style="border-bottom:1px solid #E2E8F0;">
                                                <h6 class="modal-title fw-bold">Alasan dari {{ $req->user->name }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body" style="line-height:1.7;">{{ $req->reason }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td style="padding:14px; color:#64748B; white-space:nowrap;">
                            {{ $req->created_at->translatedFormat('d M Y') }}
                            <div style="font-size:.72rem; color:#94A3B8;">{{ $req->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td class="text-center" style="padding:14px;">
                            @php $sc = $req->status_color; @endphp
                            <span style="display:inline-block; padding:4px 12px; background:{{ $sc['bg'] }}; color:{{ $sc['color'] }}; border-radius:999px; font-size:.73rem; font-weight:700; text-transform:uppercase;">
                                {{ $req->status_label }}
                            </span>
                            @if($req->admin)
                                <div style="font-size:.65rem; color:#94A3B8; margin-top:3px;">oleh {{ $req->admin->name }}</div>
                            @endif
                        </td>
                        <td class="text-end pe-4" style="padding:14px;">
                            @if($req->isPending())
                            <div class="d-flex justify-content-end gap-2">
                                {{-- Tombol Setujui --}}
                                <button type="button"
                                    class="btn btn-sm text-white"
                                    style="background:#059669; border-radius:7px; font-weight:600; white-space:nowrap;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#approveModal{{ $req->id }}">
                                    <i class="bi bi-check-lg me-1"></i>Setujui
                                </button>

                                {{-- Tombol Tolak --}}
                                <button type="button"
                                    class="btn btn-sm text-white"
                                    style="background:#DC2626; border-radius:7px; font-weight:600; white-space:nowrap;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectModal{{ $req->id }}">
                                    <i class="bi bi-x-lg me-1"></i>Tolak
                                </button>
                            </div>

                            {{-- Modal Setujui --}}
                            <div class="modal fade" id="approveModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="border-radius:16px; border:none;">
                                        <div class="modal-header" style="border-bottom:1px solid #E2E8F0; border-radius:16px 16px 0 0;">
                                            <h6 class="modal-title fw-bold"><i class="bi bi-check-circle-fill me-2" style="color:#059669;"></i>Konfirmasi Persetujuan</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <p style="color:#374151; line-height:1.6;">
                                                Apakah Anda yakin ingin menyetujui pengajuan
                                                <strong>{{ number_format($req->points) }} poin</strong>
                                                dari <strong>{{ $req->user->name }}</strong>?
                                            </p>
                                            <div class="p-3 rounded-3" style="background:#F0FDF4; border:1px solid #BBF7D0;">
                                                <div style="font-size:.82rem; color:#065F46;">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Poin akan langsung ditambahkan ke saldo petugas setelah Anda menyetujui.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="border-top:1px solid #E2E8F0;">
                                            <button type="button" class="btn text-muted" style="background:#F1F5F9; border-radius:8px;" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('point-requests.approve', $req) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn text-white" style="background:#059669; border-radius:8px; font-weight:700;">
                                                    <i class="bi bi-check-lg me-1"></i>Ya, Setujui
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal Tolak --}}
                            <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="border-radius:16px; border:none;">
                                        <div class="modal-header" style="border-bottom:1px solid #E2E8F0; border-radius:16px 16px 0 0;">
                                            <h6 class="modal-title fw-bold"><i class="bi bi-x-circle-fill me-2" style="color:#DC2626;"></i>Tolak Pengajuan</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('point-requests.reject', $req) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <div class="modal-body p-4">
                                                <p style="color:#374151; line-height:1.6;">
                                                    Anda akan menolak pengajuan <strong>{{ number_format($req->points) }} poin</strong> dari <strong>{{ $req->user->name }}</strong>.
                                                </p>
                                                <div class="mb-0">
                                                    <label class="form-label fw-600" style="font-size:.85rem; color:#0F172A;">Alasan Penolakan <span style="color:#94A3B8;">(opsional)</span></label>
                                                    <textarea name="admin_note" class="form-control" rows="3" placeholder="Berikan alasan penolakan kepada petugas..." style="border-radius:10px; border-color:#E2E8F0; resize:vertical;"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer" style="border-top:1px solid #E2E8F0;">
                                                <button type="button" class="btn text-muted" style="background:#F1F5F9; border-radius:8px;" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn text-white" style="background:#DC2626; border-radius:8px; font-weight:700;">
                                                    <i class="bi bi-x-lg me-1"></i>Tolak Pengajuan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            @else
                                {{-- Sudah diproses --}}
                                <span style="font-size:.8rem; color:#94A3B8;">
                                    @if($req->admin_note)
                                        <i class="bi bi-chat-square-text me-1"></i>
                                        <span data-bs-toggle="tooltip" title="{{ $req->admin_note }}">Catatan</span>
                                    @else
                                        —
                                    @endif
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5" style="color:#94A3B8;">
                            <i class="bi bi-inbox d-block mb-2" style="font-size:2rem; opacity:.4;"></i>
                            <span style="font-size:.875rem;">Belum ada pengajuan poin.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
        <div class="px-4 py-3" style="border-top:1px solid #E2E8F0;">
            {{ $requests->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    // Aktifkan Bootstrap Tooltips
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipEls.forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    });
</script>
@endpush
@endsection
