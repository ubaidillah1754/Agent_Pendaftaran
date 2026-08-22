@extends('layouts.app')

@section('page-title', 'Katalog Penukaran Poin')
@section('page-subtitle', 'Tukarkan poin Anda dengan merchandise eksklusif atau uang tunai.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('points.index') }}">Poin Saya</a></li>
    <li class="breadcrumb-item active" aria-current="page">Katalog Hadiah</li>
@endsection

@section('content')
<div class="katalog-page">

    {{-- ═══════════ HERO ═══════════ --}}
    <div class="hero-strip mb-4">
        <div>
            <div class="hero-eyebrow"><i class="bi bi-wallet2"></i> SALDO POIN ANDA</div>
            <div class="hero-title">{{ number_format($totalPoin) }} Poin</div>
            <div class="hero-sub">Tukarkan dengan hadiah menarik di bawah ini</div>
        </div>
        <i class="bi bi-gift hero-icon"></i>
    </div>

    {{-- ═══════════ UANG TUNAI ═══════════ --}}
    <div class="mb-5">
        <h5 class="section-title"><i class="bi bi-cash-coin me-2"></i>Pencairan Uang Tunai</h5>
        <div class="row g-4 mt-1">
            <div class="col-md-6 col-lg-4">
                <div class="hadiah-card">
                    <div class="img-wrapper">
                        <img src="{{ asset('images/merchandise/uang_tunai_1787316722660.jpg') }}" alt="Uang Tunai">
                        <div class="badge-poin"><i class="bi bi-star-fill text-warning me-1"></i> Rp 1.000 / Poin</div>
                    </div>
                    <div class="hadiah-body">
                        <h6 class="hadiah-title">Pencairan Dana (Cash)</h6>
                        <p class="hadiah-desc">Tukarkan poin Anda dengan uang tunai. Saldo minimal penukaran adalah 500 Poin (Rp 500.000).</p>
                        
                        <form action="{{ route('points.request_redeem') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="Uang Tunai">
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-white" style="border-radius:10px 0 0 10px; border-color:#E2E8F0;"><i class="bi bi-star"></i></span>
                                <input type="number" name="points" class="form-control border-start-0" placeholder="Jml poin..." min="500" max="{{ $totalPoin }}" required style="border-radius:0 10px 10px 0; border-color:#E2E8F0;">
                            </div>
                            <button type="submit" class="btn btn-brand w-100 {{ $totalPoin < 500 ? 'disabled' : '' }}">
                                Ajukan Pencairan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ MERCHANDISE ═══════════ --}}
    <div class="mb-4">
        <h5 class="section-title"><i class="bi bi-bag-heart me-2"></i>Katalog Merchandise Khusus</h5>
        <div class="row g-4 mt-1">
            
            @foreach($merchandises as $item)
            <div class="col-md-6 col-lg-3">
                <div class="hadiah-card">
                    <div class="img-wrapper">
                        <img src="{{ asset('images/merchandise/' . $item->image) }}" alt="{{ $item->name }}">
                        <div class="badge-poin"><i class="bi bi-star-fill text-warning me-1"></i> {{ $item->points }} Poin</div>
                    </div>
                    <div class="hadiah-body d-flex flex-column h-100">
                        <h6 class="hadiah-title">{{ $item->name }}</h6>
                        <p class="hadiah-desc flex-grow-1">{{ $item->description }}</p>
                        
                        <form action="{{ route('points.request_redeem') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="type" value="Merchandise - {{ $item->name }}">
                            <input type="hidden" name="points" value="{{ $item->points }}">
                            
                            @if($totalPoin >= $item->points)
                                @php
                                    $maxQty = floor($totalPoin / $item->points);
                                @endphp
                                <div class="d-flex gap-2 mb-2">
                                    <input type="number" name="qty" class="form-control text-center" value="1" min="1" max="{{ $maxQty }}" style="width: 75px; padding: 0.375rem 0.5rem;" title="Jumlah Barang">
                                    <button type="button" class="btn btn-brand flex-grow-1" onclick="confirmTukarQty(this, '{{ $item->name }}', {{ $item->points }})">
                                        Tukar
                                    </button>
                                </div>
                            @else
                                <button type="button" class="btn btn-secondary w-100 disabled" style="background:#E2E8F0; border:none; color:#94A3B8;">
                                    Poin Kurang
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>

</div>

<script>
    function confirmTukarQty(btn, namaItem, basePoints) {
        const qtyInput = btn.closest('form').querySelector('input[name="qty"]');
        const qty = parseInt(qtyInput.value) || 1;
        const totalPoints = basePoints * qty;
        
        if(confirm(`Anda yakin ingin menukar ${totalPoints} Poin dengan ${qty}x ${namaItem}?\n\nPoin Anda akan berstatus 'Menunggu' sampai Admin menyetujuinya.`)) {
            btn.closest('form').submit();
        }
    }
</script>

<style>
    .katalog-page { font-family: 'Inter', 'Plus Jakarta Sans', sans-serif; }

    /* Hero */
    .hero-strip {
        background: linear-gradient(135deg, #0F7B63 0%, #0E7490 100%);
        border-radius: 20px;
        padding: 28px 32px;
        display: flex; align-items: center; justify-content: space-between;
        position: relative; overflow: hidden;
        color: #fff;
    }
    .hero-eyebrow { font-size: 12px; font-weight: 600; letter-spacing: .5px; color: #A7F3D0; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
    .hero-title { font-size: 28px; font-weight: 800; margin-bottom: 4px; }
    .hero-sub { font-size: 14px; color: rgba(255,255,255,.8); }
    .hero-icon { font-size: 90px; opacity: .12; position: absolute; right: 28px; top: 50%; transform: translateY(-50%); }

    /* Section Title */
    .section-title {
        font-weight: 800;
        color: #0F172A;
        font-size: 1.1rem;
        border-bottom: 2px solid #E2E8F0;
        padding-bottom: 12px;
    }

    /* Cards */
    .hadiah-card {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .hadiah-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px -10px rgba(15, 123, 99, 0.15);
        border-color: #0F7B63;
    }
    .img-wrapper {
        position: relative;
        height: 220px;
        background: #F8FAFC;
        overflow: hidden;
    }
    .img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .badge-poin {
        position: absolute;
        top: 12px; right: 12px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(4px);
        padding: 6px 12px;
        border-radius: 999px;
        font-weight: 800;
        font-size: 0.85rem;
        color: #0F7B63;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .hadiah-body {
        padding: 20px;
    }
    .hadiah-title {
        font-weight: 700;
        font-size: 1rem;
        color: #0F172A;
        margin-bottom: 8px;
    }
    .hadiah-desc {
        font-size: 0.85rem;
        color: #64748B;
        line-height: 1.5;
    }
</style>
@endsection
