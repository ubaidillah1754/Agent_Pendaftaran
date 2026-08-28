@extends('layouts.app')
@section('page-title', 'Pengajuan Poin Saya')
@section('page-subtitle', 'Riwayat pengajuan poin yang telah Anda buat.')
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

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        {{-- Saldo Poin --}}
        <div class="px-4 py-3 rounded-3 d-flex align-items-center gap-3" style="background:linear-gradient(120deg,#0A5644,#0F7B63); color:#fff; min-width:220px;">
            <i class="bi bi-star-fill" style="font-size:1.6rem; opacity:.8;"></i>
            <div>
                <div style="font-size:.7rem; font-weight:600; opacity:.75; letter-spacing:.06em; text-transform:uppercase;">Poin Saya</div>
                <div style="font-size:1.5rem; font-weight:800; line-height:1.1;">{{ number_format($totalPoin) }} Poin</div>
            </div>
        </div>
        <a href="{{ route('point-requests.create') }}" class="btn text-white" style="background:var(--primary); border-radius:10px; font-weight:700;">
            <i class="bi bi-plus-circle me-1"></i> Ajukan Poin Baru
        </a>
    </div>

    {{-- Tabel riwayat pengajuan --}}
    <div class="card" style="border-radius:16px; border:1px solid #E2E8F0; overflow:hidden; box-shadow:0 2px 12px -6px rgba(0,0,0,.08);">
        <div class="card-header bg-transparent py-3 px-4" style="border-bottom:1px solid #E2E8F0;">
            <span style="font-weight:700; color:#0F172A; font-size:.95rem;">
                <i class="bi bi-list-check me-2" style="color:var(--primary);"></i>Riwayat Pengajuan Poin
            </span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0" style="font-size:.875rem;">
                <thead>
                    <tr style="background:#F8FAFC;">
                        <th class="ps-4 fw-700" style="color:#64748B; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; padding:12px 14px;">Tanggal</th>
                        <th style="color:#64748B; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; padding:12px 14px;">Jumlah Poin</th>
                        <th style="color:#64748B; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; padding:12px 14px;">Alasan</th>
                        <th class="text-center" style="color:#64748B; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; padding:12px 14px;">Status</th>
                        <th style="color:#64748B; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; padding:12px 14px;">Catatan Admin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr style="border-bottom:1px solid #F1F5F9;">
                        <td class="ps-4" style="padding:14px; color:#64748B; white-space:nowrap;">
                            {{ $req->created_at->translatedFormat('d M Y') }}
                            <div style="font-size:.72rem; color:#94A3B8;">{{ $req->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td style="padding:14px;">
                            <span style="font-weight:800; color:#B8912E; font-size:1rem;">{{ number_format($req->points) }}</span>
                            <span style="color:#64748B;"> poin</span>
                        </td>
                        <td style="padding:14px; max-width:240px;">
                            <span style="color:#0F172A;">{{ Str::limit($req->reason, 60) }}</span>
                            @if(strlen($req->reason) > 60)
                                <button type="button" class="btn btn-link p-0 ms-1" style="font-size:.75rem; color:var(--primary);" data-bs-toggle="modal" data-bs-target="#reasonModal{{ $req->id }}">
                                    selengkapnya
                                </button>
                                {{-- Modal reason --}}
                                <div class="modal fade" id="reasonModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content" style="border-radius:16px;">
                                            <div class="modal-header" style="border-bottom:1px solid #E2E8F0;">
                                                <h6 class="modal-title fw-bold">Alasan Pengajuan</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body" style="line-height:1.7;">{{ $req->reason }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td class="text-center" style="padding:14px;">
                            @php $sc = $req->status_color; @endphp
                            <span style="display:inline-block; padding:4px 12px; background:{{ $sc['bg'] }}; color:{{ $sc['color'] }}; border-radius:999px; font-size:.73rem; font-weight:700; text-transform:uppercase; letter-spacing:.03em;">
                                {{ $req->status_label }}
                            </span>
                            @if($req->status === 'approved' && $req->approved_at)
                                <div style="font-size:.65rem; color:#94A3B8; margin-top:3px;">{{ $req->approved_at->format('d M Y') }}</div>
                            @elseif($req->status === 'rejected' && $req->rejected_at)
                                <div style="font-size:.65rem; color:#94A3B8; margin-top:3px;">{{ $req->rejected_at->format('d M Y') }}</div>
                            @endif
                        </td>
                        <td style="padding:14px; color:#64748B; font-size:.82rem; max-width:200px;">
                            {{ $req->admin_note ?: '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5" style="color:#94A3B8;">
                            <i class="bi bi-inbox d-block mb-2" style="font-size:2rem; opacity:.4;"></i>
                            <span style="font-size:.875rem;">Belum ada pengajuan poin.</span>
                            <div class="mt-2">
                                <a href="{{ route('point-requests.create') }}" class="btn btn-sm text-white" style="background:var(--primary); border-radius:8px; font-weight:600;">
                                    Ajukan Sekarang
                                </a>
                            </div>
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
@endsection
