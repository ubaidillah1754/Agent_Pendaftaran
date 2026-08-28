@extends('layouts.app')
@section('title', 'Katalog Reward — My Sakinah Agent')
@section('page-title', 'Katalog Reward & Hadiah')
@section('page-subtitle', 'Tukarkan akumulasi poin Anda dengan berbagai pilihan merchandise menarik.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('points.index') }}">Poin Saya</a></li>
    <li class="breadcrumb-item active">Katalog Reward</li>
@endsection

@section('content')
<!-- Header info saldo & pencarian -->
<div class="row g-3 mb-4 fade-in">
    <div class="col-md-7 col-lg-8">
        <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between" style="background:#FFFFFF; border-color:var(--rs-border) !important;">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px; height:44px; border-radius:12px; background:var(--rs-primary-soft); color:var(--rs-primary); display:flex; align-items:center; justify-content:center; font-size:1.25rem;">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div>
                    <div style="font-size:.72rem; text-transform:uppercase; font-weight:700; color:var(--rs-muted); letter-spacing:.05em;">Saldo Poin Anda</div>
                    <div style="font-size:1.4rem; font-weight:800; color:var(--rs-primary); line-height:1.2;">
                        {{ number_format($user->point_balance) }} <span style="font-size:.85rem; font-weight:600;">Poin</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('points.redemptions.index') }}" class="btn btn-sm btn-light border" style="border-radius:8px; font-weight:600; font-size:.8rem;">
                <i class="bi bi-clock-history me-1"></i>Riwayat Penukaran
            </a>
        </div>
    </div>
    <div class="col-md-5 col-lg-4">
        <form method="GET" action="{{ route('points.katalog') }}">
            <div class="input-group">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Cari nama reward..." style="border-radius:10px 0 0 10px;">
                <button class="btn btn-sm text-white" type="submit" style="background:var(--rs-primary); border-radius:0 10px 10px 0;">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Grid Reward Cards -->
<div class="row g-3 fade-in">
    @forelse($rewards as $reward)
    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
        <div class="card h-100 d-flex flex-column justify-content-between overflow-hidden shadow-sm" style="transition: transform .2s, box-shadow .2s;">
            <div>
                <div style="position:relative; width:100%; height:180px; background:#F8FAFC; overflow:hidden;">
                    <img src="{{ $reward->image_url }}" alt="{{ $reward->name }}" style="width:100%; height:100%; object-fit:cover;">
                    @if($reward->stock <= 0)
                        <span class="badge bg-danger" style="position:absolute; top:10px; right:10px;">Stok Habis</span>
                    @elseif($reward->stock <= 5)
                        <span class="badge bg-warning text-dark" style="position:absolute; top:10px; right:10px;">Sisa {{ $reward->stock }}</span>
                    @endif
                </div>
                <div class="p-3">
                    <h6 class="fw-bold mb-1" style="color:var(--rs-ink); font-size:.95rem;">{{ $reward->name }}</h6>
                    <div class="d-flex align-items-center gap-1 mb-2">
                        <span class="badge" style="background:var(--rs-accent-soft); color:var(--rs-accent); font-weight:800; font-size:.82rem;">
                            <i class="bi bi-star-fill me-1"></i>{{ number_format($reward->points_required) }} Poin
                        </span>
                        <span style="font-size:.72rem; color:var(--rs-muted);">/ item</span>
                    </div>
                    <p class="text-muted mb-2" style="font-size:.78rem; line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                        {{ $reward->description ?: 'Merchandise resmi eksklusif untuk karyawan berprestasi.' }}
                    </p>
                    <div style="font-size:.72rem; color:var(--rs-muted);">
                        Tersedia: <strong>{{ $reward->stock }}</strong> unit
                    </div>
                </div>
            </div>
            <div class="p-3 pt-0">
                @if($user->point_balance >= $reward->points_required && $reward->stock > 0)
                    <button type="button" class="btn btn-sm w-100 text-white" style="background:var(--rs-primary); border-radius:8px; font-weight:700;"
                            data-bs-toggle="modal" data-bs-target="#modalRedeem{{ $reward->id }}">
                        <i class="bi bi-gift me-1"></i>Tukar Reward
                    </button>
                @elseif($reward->stock <= 0)
                    <button class="btn btn-sm w-100 btn-light text-muted border" disabled style="border-radius:8px; font-weight:600;">
                        Stok Habis
                    </button>
                @else
                    <button class="btn btn-sm w-100 btn-light text-muted border" disabled style="border-radius:8px; font-weight:600;" title="Poin Anda tidak mencukupi">
                        <i class="bi bi-lock-fill me-1"></i>Poin Kurang (Butuh {{ number_format($reward->points_required - $user->point_balance) }} lagi)
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Penukaran -->
    <div class="modal fade" id="modalRedeem{{ $reward->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px; border:none; box-shadow:0 10px 30px rgba(0,0,0,.15);">
                <form action="{{ route('points.tukar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="merchandise_id" value="{{ $reward->id }}">
                    <div class="modal-header border-bottom">
                        <h6 class="modal-title fw-bold" style="color:var(--rs-ink);"><i class="bi bi-gift-fill me-2" style="color:var(--rs-primary);"></i>Konfirmasi Penukaran Reward</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background:#FAFDFB; border:1px solid var(--rs-border);">
                            <img src="{{ $reward->image_url }}" alt="{{ $reward->name }}" style="width:64px; height:64px; object-fit:cover; border-radius:10px;">
                            <div>
                                <div class="fw-bold" style="font-size:1rem; color:var(--rs-ink);">{{ $reward->name }}</div>
                                <div style="font-weight:700; color:var(--rs-accent); font-size:.88rem;">{{ number_format($reward->points_required) }} Poin / item</div>
                                <div style="font-size:.75rem; color:var(--rs-muted);">Sisa stok tersedia: {{ $reward->stock }} unit</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-weight:700; font-size:.82rem;">Jumlah Ditukar <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" max="{{ min($reward->stock, floor($user->point_balance / $reward->points_required)) }}" required
                                   onchange="updateTotalPoints{{ $reward->id }}(this.value, {{ $reward->points_required }})">
                            <div class="form-text" style="font-size:.72rem;">Maksimal yang bisa Anda tukar: {{ min($reward->stock, floor($user->point_balance / $reward->points_required)) }} item</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-weight:700; font-size:.82rem;">Catatan Khusus (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Ukuran L, warna hitam..."></textarea>
                        </div>

                        <div class="p-3 rounded-3" style="background:#FEF3C7; color:#92400E; font-size:.8rem;">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Saldo Poin Anda:</span>
                                <strong>{{ number_format($user->point_balance) }} Poin</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Total Poin yang Dipotong:</span>
                                <strong id="totalCost{{ $reward->id }}">{{ number_format($reward->points_required) }} Poin</strong>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-1 mt-1 border-warning">
                                <span>Sisa Saldo Poin:</span>
                                <strong id="remainingBalance{{ $reward->id }}">{{ number_format($user->point_balance - $reward->points_required) }} Poin</strong>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" style="border-radius:8px;">Batal</button>
                        <button type="submit" class="btn btn-sm text-white" style="background:var(--rs-primary); border-radius:8px; font-weight:700;">
                            <i class="bi bi-check2-circle me-1"></i>Ajukan Penukaran Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function updateTotalPoints{{ $reward->id }}(qty, pointPerItem) {
            qty = parseInt(qty) || 1;
            const total = qty * pointPerItem;
            const remaining = {{ $user->point_balance }} - total;
            document.getElementById('totalCost{{ $reward->id }}').innerText = total.toLocaleString() + ' Poin';
            document.getElementById('remainingBalance{{ $reward->id }}').innerText = remaining.toLocaleString() + ' Poin';
        }
    </script>
    @empty
    <div class="col-12">
        <div class="card p-5 text-center text-muted">
            <i class="bi bi-box-seam fs-1 text-secondary mb-2"></i>
            <div class="fw-bold">Belum ada reward yang cocok dengan pencarian Anda.</div>
            <a href="{{ route('points.katalog') }}" class="mt-2 text-decoration-none" style="color:var(--rs-primary);">Reset Filter</a>
        </div>
    </div>
    @endforelse
</div>

@if($rewards->hasPages())
<div class="mt-4">
    {{ $rewards->links() }}
</div>
@endif
@endsection
