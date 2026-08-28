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

{{-- ══════════ BALANCE HERO ══════════ --}}
<div class="kr-hero mb-4">
    <div class="kr-hero-bg">
        <div class="kr-circle kr-c1"></div>
        <div class="kr-circle kr-c2"></div>
    </div>
    <div class="kr-hero-inner">
        <div class="kr-hero-left">
            <div class="kr-hero-eyebrow"><i class="bi bi-wallet2"></i> Saldo Poin Aktif Anda</div>
            <div class="kr-hero-pts">
                <span class="kr-pts-num">{{ number_format($user->point_balance) }}</span>
                <div class="kr-pts-meta">
                    <span class="kr-pts-unit">Poin</span>
                    <span class="kr-pts-eq">≈ Rp {{ number_format($user->point_balance * 1000, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="kr-hero-hint">
                <i class="bi bi-info-circle-fill"></i>
                Pilih reward di bawah dan klik <strong>Tukar Reward</strong> untuk mengajukan penukaran.
            </div>
        </div>
        <div class="kr-hero-right">
            <a href="{{ route('points.redemptions.index') }}" class="kr-link-btn">
                <i class="bi bi-clock-history"></i> Riwayat Penukaran
            </a>
            <a href="{{ route('points.index') }}" class="kr-link-btn outline">
                <i class="bi bi-arrow-left-circle"></i> Kembali
            </a>
        </div>
    </div>
    <div class="kr-strip">
        <div class="kr-strip-item"><i class="bi bi-shield-check-fill"></i> Penukaran aman & terverifikasi</div>
        <div class="kr-strip-div"></div>
        <div class="kr-strip-item"><i class="bi bi-clock-fill"></i> Proses 1–3 hari kerja</div>
        <div class="kr-strip-div"></div>
        <div class="kr-strip-item"><i class="bi bi-star-fill"></i> 1 Poin = Rp 1.000</div>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="kr-alert kr-alert-ok mb-4">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button onclick="this.parentElement.remove()" class="kr-alert-x">&times;</button>
</div>
@endif
@if(session('error'))
<div class="kr-alert kr-alert-err mb-4">
    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
    <button onclick="this.parentElement.remove()" class="kr-alert-x">&times;</button>
</div>
@endif

{{-- ══════════ SEARCH + FILTER BAR ══════════ --}}
<div class="kr-search-bar mb-4">
    <form method="GET" action="{{ route('points.katalog') }}" class="kr-search-form">
        <div class="kr-search-wrap">
            <i class="bi bi-search kr-search-icon"></i>
            <input type="text" name="q" value="{{ request('q') }}"
                class="kr-search-input" placeholder="Cari nama reward atau merchandise...">
            @if(request('q'))
            <a href="{{ route('points.katalog') }}" class="kr-search-clear">
                <i class="bi bi-x-lg"></i>
            </a>
            @endif
        </div>
        <button type="submit" class="kr-search-btn">
            <i class="bi bi-search me-1"></i> Cari
        </button>
    </form>
    @if(request('q'))
    <div class="kr-search-result">
        Menampilkan hasil untuk: <strong>"{{ request('q') }}"</strong>
        ({{ $rewards->total() }} item)
    </div>
    @endif
</div>

{{-- ══════════ REWARD GRID ══════════ --}}
<div class="kr-section-head mb-3">
    <div class="kr-sec-left">
        <div class="kr-sec-icon"><i class="bi bi-bag-heart-fill"></i></div>
        <div>
            <div class="kr-sec-title">{{ request('q') ? 'Hasil Pencarian' : 'Semua Merchandise' }}</div>
            <div class="kr-sec-sub">{{ $rewards->total() }} item tersedia</div>
        </div>
    </div>
</div>

<div class="row g-3 fade-in">
    @forelse($rewards as $reward)
    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
        <div class="kr-card h-100">
            {{-- Image --}}
            <div class="kr-card-img">
                <img src="{{ $reward->image_url }}" alt="{{ $reward->name }}" loading="lazy">

                {{-- Badges --}}
                <div class="kr-card-pts-badge">
                    <i class="bi bi-star-fill"></i> {{ number_format($reward->points_required) }} Poin
                </div>
                @if($reward->stock <= 0)
                    <div class="kr-card-status kr-sts-out">Stok Habis</div>
                @elseif($reward->stock <= 5)
                    <div class="kr-card-status kr-sts-low">Sisa {{ $reward->stock }}</div>
                @elseif($user->point_balance >= $reward->points_required)
                    <div class="kr-card-status kr-sts-ok"><i class="bi bi-check-circle-fill"></i> Bisa Ditukar</div>
                @endif
            </div>

            {{-- Body --}}
            <div class="kr-card-body">
                <div class="kr-card-cat">Merchandise Eksklusif</div>
                <h3 class="kr-card-name">{{ $reward->name }}</h3>
                <p class="kr-card-desc">{{ $reward->description ?: 'Merchandise resmi eksklusif untuk karyawan berprestasi RSI Sakinah.' }}</p>

                <div class="kr-card-stock">
                    <i class="bi bi-box-seam"></i>
                    Tersedia: <strong>{{ $reward->stock }}</strong> unit
                </div>

                <div class="kr-card-divider"></div>

                <div class="kr-card-pts-row">
                    <span class="kr-pts-star"><i class="bi bi-star-fill"></i></span>
                    <span class="kr-pts-big">{{ number_format($reward->points_required) }}</span>
                    <span class="kr-pts-lbl">poin / item</span>
                </div>

                {{-- CTA --}}
                @if($user->point_balance >= $reward->points_required && $reward->stock > 0)
                <button type="button" class="kr-btn-tukar"
                    data-bs-toggle="modal" data-bs-target="#krModal{{ $reward->id }}">
                    <i class="bi bi-gift-fill"></i> Tukar Reward
                </button>
                @elseif($reward->stock <= 0)
                <button class="kr-btn-disabled" disabled>
                    <i class="bi bi-x-circle-fill"></i> Stok Habis
                </button>
                @else
                <button class="kr-btn-locked" disabled
                    title="Anda butuh {{ number_format($reward->points_required - $user->point_balance) }} poin lagi">
                    <i class="bi bi-lock-fill"></i>
                    Butuh {{ number_format($reward->points_required - $user->point_balance) }} poin lagi
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ── MODAL KONFIRMASI ── --}}
    <div class="modal fade" id="krModal{{ $reward->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content kr-modal">
                <form action="{{ route('points.tukar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="merchandise_id" value="{{ $reward->id }}">
                    <div class="kr-modal-head">
                        <div class="kr-modal-title">
                            <i class="bi bi-gift-fill"></i> Konfirmasi Penukaran
                        </div>
                        <button type="button" class="kr-modal-close" data-bs-dismiss="modal">&times;</button>
                    </div>
                    <div class="kr-modal-body">
                        {{-- Item preview --}}
                        <div class="kr-modal-item">
                            <img src="{{ $reward->image_url }}" alt="{{ $reward->name }}">
                            <div>
                                <div class="kr-modal-item-name">{{ $reward->name }}</div>
                                <div class="kr-modal-item-pts">
                                    <i class="bi bi-star-fill"></i>
                                    {{ number_format($reward->points_required) }} Poin / item
                                </div>
                                <div class="kr-modal-item-stok">Stok tersedia: {{ $reward->stock }} unit</div>
                            </div>
                        </div>

                        {{-- Qty --}}
                        <div class="kr-modal-field">
                            <label>Jumlah Ditukar <span class="text-danger">*</span></label>
                            <div class="kr-qty-wrap">
                                <button type="button" class="kr-qty-btn" onclick="krQty(this,-1)">−</button>
                                <input type="number" name="quantity" class="kr-qty-input"
                                    value="1" min="1"
                                    max="{{ min($reward->stock, floor($user->point_balance / $reward->points_required)) }}"
                                    onchange="krCalc{{ $reward->id }}(this.value)"
                                    required>
                                <button type="button" class="kr-qty-btn" onclick="krQty(this,1)">+</button>
                            </div>
                            <div class="kr-qty-hint">
                                Maks. yang bisa Anda tukar: <strong>{{ min($reward->stock, floor($user->point_balance / $reward->points_required)) }}</strong> item
                            </div>
                        </div>

                        {{-- Catatan --}}
                        <div class="kr-modal-field">
                            <label>Catatan Khusus <span class="kr-optional">(Opsional)</span></label>
                            <textarea name="notes" class="kr-modal-textarea" rows="2"
                                placeholder="Contoh: Ukuran L, warna biru..."></textarea>
                        </div>

                        {{-- Summary --}}
                        <div class="kr-modal-summary">
                            <div class="kr-sum-row">
                                <span>Saldo Poin Anda</span>
                                <strong>{{ number_format($user->point_balance) }} Poin</strong>
                            </div>
                            <div class="kr-sum-row deduct">
                                <span>Poin Dipotong</span>
                                <strong id="krCost{{ $reward->id }}">{{ number_format($reward->points_required) }} Poin</strong>
                            </div>
                            <div class="kr-sum-divider"></div>
                            <div class="kr-sum-row remain">
                                <span>Sisa Saldo</span>
                                <strong id="krRemain{{ $reward->id }}">{{ number_format($user->point_balance - $reward->points_required) }} Poin</strong>
                            </div>
                        </div>
                    </div>
                    <div class="kr-modal-foot">
                        <button type="button" class="kr-modal-cancel" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="kr-modal-submit">
                            <i class="bi bi-check2-circle me-1"></i> Ajukan Penukaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    function krCalc{{ $reward->id }}(qty) {
        qty = parseInt(qty) || 1;
        const cost = qty * {{ $reward->points_required }};
        const remain = {{ $user->point_balance }} - cost;
        document.getElementById('krCost{{ $reward->id }}').textContent = cost.toLocaleString('id-ID') + ' Poin';
        document.getElementById('krRemain{{ $reward->id }}').textContent = remain.toLocaleString('id-ID') + ' Poin';
    }
    </script>
    @empty
    <div class="col-12">
        <div class="kr-empty">
            <div class="kr-empty-icon"><i class="bi bi-bag-x"></i></div>
            <div class="kr-empty-title">
                {{ request('q') ? 'Reward Tidak Ditemukan' : 'Belum Ada Reward' }}
            </div>
            <p class="kr-empty-sub">
                {{ request('q') ? 'Coba kata kunci yang berbeda atau reset pencarian.' : 'Katalog reward akan segera tersedia. Pantau terus halaman ini!' }}
            </p>
            @if(request('q'))
            <a href="{{ route('points.katalog') }}" class="kr-empty-btn">Reset Pencarian</a>
            @endif
        </div>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($rewards->hasPages())
<div class="kr-pagination mt-4">
    {{ $rewards->links() }}
</div>
@endif

<script>
function krQty(btn, delta) {
    const wrap = btn.closest('.kr-qty-wrap');
    const input = wrap.querySelector('.kr-qty-input');
    const max = parseInt(input.max) || 999;
    let v = Math.max(1, Math.min(max, (parseInt(input.value) || 1) + delta));
    input.value = v;
    input.dispatchEvent(new Event('change'));
}
</script>

<style>
/* ── HERO ── */
.kr-hero {
    background: linear-gradient(135deg, #063D2C 0%, #0A5644 45%, #0F7B63 80%, #0E7490 100%);
    border-radius: 24px; overflow: hidden;
    box-shadow: 0 20px 60px -20px rgba(6,61,44,.5);
    color: #fff; position: relative;
}
.kr-hero-bg { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
.kr-circle { position: absolute; border-radius: 50%; background: rgba(255,255,255,.06); }
.kr-c1 { width: 360px; height: 360px; top: -120px; right: -40px; }
.kr-c2 { width: 200px; height: 200px; bottom: -60px; left: 280px; }
.kr-hero-inner {
    position: relative; z-index: 1;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 20px; padding: 30px 36px 22px;
}
.kr-hero-eyebrow {
    display: inline-flex; align-items: center; gap: 7px;
    font-size: 11px; font-weight: 700; letter-spacing: .07em;
    color: #A7F3D0; text-transform: uppercase; margin-bottom: 8px;
}
.kr-hero-pts { display: flex; align-items: baseline; gap: 12px; margin-bottom: 10px; }
.kr-pts-num {
    font-family: 'Spectral', serif; font-size: 3.4rem;
    font-weight: 900; line-height: 1; letter-spacing: -2px;
}
.kr-pts-meta { display: flex; flex-direction: column; gap: 2px; }
.kr-pts-unit { font-size: 1rem; font-weight: 600; opacity: .7; }
.kr-pts-eq { font-size: 12px; color: #A7F3D0; font-weight: 600; }
.kr-hero-hint {
    font-size: 12.5px; color: rgba(255,255,255,.65);
    display: flex; align-items: center; gap: 6px;
}
.kr-hero-hint i { color: #A7F3D0; }

.kr-hero-right { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
.kr-link-btn {
    display: inline-flex; align-items: center; gap: 7px;
    background: rgba(255,255,255,.15); color: #fff;
    border: 1px solid rgba(255,255,255,.25); backdrop-filter: blur(6px);
    font-size: 12.5px; font-weight: 700; padding: 9px 18px;
    border-radius: 10px; text-decoration: none; white-space: nowrap;
    transition: background .2s;
}
.kr-link-btn:hover { background: rgba(255,255,255,.25); color: #fff; }
.kr-link-btn.outline { background: transparent; }

.kr-strip {
    position: relative; z-index: 1;
    display: flex; align-items: center;
    padding: 13px 36px; border-top: 1px solid rgba(255,255,255,.12);
    background: rgba(0,0,0,.1); flex-wrap: wrap; gap: 12px;
}
.kr-strip-item {
    display: flex; align-items: center; gap: 7px;
    font-size: 12.5px; color: rgba(255,255,255,.72);
}
.kr-strip-item i { color: #A7F3D0; }
.kr-strip-div { width: 1px; height: 16px; background: rgba(255,255,255,.2); flex-shrink: 0; }

/* ── ALERTS ── */
.kr-alert {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 18px; border-radius: 14px;
    font-size: 13.5px; font-weight: 600; position: relative;
}
.kr-alert-ok  { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
.kr-alert-err { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
.kr-alert-x {
    position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
    background: none; border: none; font-size: 1.2rem; cursor: pointer; opacity: .5;
}
.kr-alert-x:hover { opacity: 1; }

/* ── SEARCH ── */
.kr-search-bar { display: flex; flex-direction: column; gap: 8px; }
.kr-search-form { display: flex; gap: 10px; flex-wrap: wrap; }
.kr-search-wrap {
    flex: 1; min-width: 240px;
    display: flex; align-items: center;
    background: #fff; border: 2px solid #E2E8F0;
    border-radius: 12px; overflow: hidden;
    transition: border-color .2s;
}
.kr-search-wrap:focus-within { border-color: var(--primary); }
.kr-search-icon { padding: 0 12px; color: #94A3B8; font-size: 15px; }
.kr-search-input {
    flex: 1; border: none; outline: none;
    font-size: 14px; padding: 12px 0; background: transparent;
    color: #0B1D17; font-family: 'Plus Jakarta Sans', sans-serif;
}
.kr-search-clear {
    padding: 0 12px; color: #94A3B8; text-decoration: none;
    transition: color .2s; font-size: 13px;
}
.kr-search-clear:hover { color: #DC2626; }
.kr-search-btn {
    background: var(--primary); color: #fff;
    border: none; border-radius: 12px; cursor: pointer;
    font-size: 13px; font-weight: 700;
    padding: 12px 20px; white-space: nowrap;
    transition: background .2s;
}
.kr-search-btn:hover { background: var(--primary-dark); }
.kr-search-result { font-size: 13px; color: #64748B; }

/* ── SECTION HEAD ── */
.kr-section-head { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
.kr-sec-left { display: flex; align-items: center; gap: 14px; }
.kr-sec-icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: linear-gradient(135deg, #0F7B63, #34D399); color: #fff;
    display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
}
.kr-sec-title { font-size: 1rem; font-weight: 800; color: #0B1D17; }
.kr-sec-sub { font-size: 12.5px; color: #64748B; }

/* ── CARDS ── */
.kr-card {
    background: #fff; border: 1.5px solid #E2E8F0;
    border-radius: 20px; overflow: hidden;
    transition: transform .25s, box-shadow .25s, border-color .25s;
    display: flex; flex-direction: column;
}
.kr-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 50px -15px rgba(15,123,99,.2);
    border-color: var(--primary);
}
.kr-card-img {
    position: relative; height: 190px;
    background: linear-gradient(135deg, #F0FDF6, #E7F4F6);
    overflow: hidden; flex-shrink: 0;
}
.kr-card-img img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .4s ease;
}
.kr-card:hover .kr-card-img img { transform: scale(1.07); }

.kr-card-pts-badge {
    position: absolute; top: 10px; right: 10px;
    background: rgba(255,255,255,.95); backdrop-filter: blur(4px);
    color: #0F7B63; font-size: 12px; font-weight: 800;
    padding: 5px 11px; border-radius: 999px;
    box-shadow: 0 2px 10px rgba(0,0,0,.1);
    display: flex; align-items: center; gap: 5px;
}
.kr-card-pts-badge i { color: #F59E0B; }

.kr-card-status {
    position: absolute; bottom: 10px; left: 10px;
    font-size: 11px; font-weight: 700; padding: 4px 10px;
    border-radius: 999px; display: flex; align-items: center; gap: 5px;
}
.kr-sts-ok  { background: #059669; color: #fff; }
.kr-sts-low { background: #D97706; color: #fff; }
.kr-sts-out { background: rgba(0,0,0,.55); color: rgba(255,255,255,.85); }

.kr-card-body { padding: 18px; flex: 1; display: flex; flex-direction: column; }
.kr-card-cat { font-size: 10.5px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .07em; color: var(--primary); margin-bottom: 5px; }
.kr-card-name { font-size: 15px; font-weight: 800; color: #0B1D17;
    margin: 0 0 7px; line-height: 1.3; }
.kr-card-desc { font-size: 12.5px; color: #64748B; line-height: 1.55;
    flex: 1; display: -webkit-box; -webkit-line-clamp: 2;
    -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 10px; }
.kr-card-stock { font-size: 12px; color: #94A3B8; display: flex; align-items: center; gap: 5px; margin-bottom: 12px; }
.kr-card-stock i { color: var(--primary); }
.kr-card-divider { height: 1px; background: #F1F5F9; margin-bottom: 12px; }
.kr-card-pts-row { display: flex; align-items: baseline; gap: 5px; margin-bottom: 14px; }
.kr-pts-star { color: #F59E0B; font-size: 13px; }
.kr-pts-big { font-family: 'Spectral', serif; font-size: 1.3rem;
    font-weight: 900; color: #0B1D17; }
.kr-pts-lbl { font-size: 12px; color: #94A3B8; }

.kr-btn-tukar {
    width: 100%; border: none; border-radius: 11px; cursor: pointer;
    background: linear-gradient(135deg, #0F7B63, #059669);
    color: #fff; font-size: 13.5px; font-weight: 800;
    padding: 11px; display: flex; align-items: center;
    justify-content: center; gap: 7px;
    box-shadow: 0 4px 14px -4px rgba(15,123,99,.4);
    transition: all .2s;
}
.kr-btn-tukar:hover { background: linear-gradient(135deg, #0A5644, #047857);
    transform: translateY(-1px); }
.kr-btn-disabled, .kr-btn-locked {
    width: 100%; border: none; border-radius: 11px;
    padding: 11px; display: flex; align-items: center;
    justify-content: center; gap: 7px;
    font-size: 13px; font-weight: 700; cursor: not-allowed;
}
.kr-btn-disabled { background: #F1F5F9; color: #94A3B8; }
.kr-btn-locked { background: #FEF2F2; color: #DC2626; font-size: 12px; }

/* ── MODAL ── */
.kr-modal { border: none; border-radius: 22px; overflow: hidden;
    box-shadow: 0 24px 60px -20px rgba(0,0,0,.22); }
.kr-modal-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 24px; border-bottom: 1px solid #F1F5F9;
}
.kr-modal-title { font-size: 15px; font-weight: 800; color: #0B1D17;
    display: flex; align-items: center; gap: 8px; }
.kr-modal-title i { color: var(--primary); }
.kr-modal-close {
    background: none; border: none; font-size: 1.4rem; cursor: pointer;
    color: #94A3B8; line-height: 1;
}
.kr-modal-close:hover { color: #0B1D17; }
.kr-modal-body { padding: 22px 24px; }

.kr-modal-item {
    display: flex; align-items: center; gap: 14px;
    background: #F8FAFC; border: 1px solid #E2E8F0;
    border-radius: 14px; padding: 14px; margin-bottom: 18px;
}
.kr-modal-item img {
    width: 68px; height: 68px; object-fit: cover;
    border-radius: 10px; flex-shrink: 0;
    border: 1px solid #E2E8F0;
}
.kr-modal-item-name { font-size: 14.5px; font-weight: 800; color: #0B1D17; margin-bottom: 4px; }
.kr-modal-item-pts { font-size: 13px; font-weight: 700; color: #D97706;
    display: flex; align-items: center; gap: 5px; margin-bottom: 2px; }
.kr-modal-item-pts i { color: #F59E0B; }
.kr-modal-item-stok { font-size: 12px; color: #94A3B8; }

.kr-modal-field { margin-bottom: 16px; }
.kr-modal-field label {
    display: block; font-size: 12.5px; font-weight: 700; color: #374151;
    text-transform: uppercase; letter-spacing: .04em; margin-bottom: 8px;
}
.kr-optional { text-transform: none; color: #94A3B8; font-weight: 500; }

.kr-qty-wrap {
    display: inline-flex; align-items: center;
    border: 2px solid #E2E8F0; border-radius: 10px; overflow: hidden;
    transition: border-color .2s;
}
.kr-qty-wrap:focus-within { border-color: var(--primary); }
.kr-qty-btn {
    width: 38px; height: 38px; border: none; background: #F8FAFC;
    color: #374151; font-size: 1.1rem; font-weight: 700;
    cursor: pointer; transition: background .2s;
}
.kr-qty-btn:hover { background: var(--primary-soft); color: var(--primary); }
.kr-qty-input {
    width: 55px; border: none; text-align: center;
    font-size: 15px; font-weight: 800; color: #0B1D17;
    padding: 8px 0; outline: none; background: transparent;
}
.kr-qty-hint { font-size: 12px; color: #94A3B8; margin-top: 6px; }

.kr-modal-textarea {
    width: 100%; border: 2px solid #E2E8F0; border-radius: 10px;
    padding: 10px 12px; font-size: 13.5px; outline: none;
    font-family: 'Plus Jakarta Sans', sans-serif;
    transition: border-color .2s; resize: none;
}
.kr-modal-textarea:focus { border-color: var(--primary); }

.kr-modal-summary {
    background: #FFFBEB; border: 1px solid #FDE68A;
    border-radius: 12px; padding: 14px 16px;
}
.kr-sum-row {
    display: flex; justify-content: space-between; align-items: center;
    font-size: 13px; color: #78350F; padding: 3px 0;
}
.kr-sum-row.deduct { color: #DC2626; }
.kr-sum-row.remain { color: #065F46; font-size: 14px; }
.kr-sum-divider { height: 1px; background: #FDE68A; margin: 8px 0; }

.kr-modal-foot {
    display: flex; justify-content: flex-end; gap: 10px;
    padding: 16px 24px; border-top: 1px solid #F1F5F9;
    background: #FAFCFF;
}
.kr-modal-cancel {
    background: #F1F5F9; border: none; color: #374151;
    font-size: 13px; font-weight: 700; padding: 10px 20px;
    border-radius: 10px; cursor: pointer; transition: background .2s;
}
.kr-modal-cancel:hover { background: #E2E8F0; }
.kr-modal-submit {
    background: linear-gradient(135deg, #0F7B63, #059669); border: none;
    color: #fff; font-size: 13px; font-weight: 800;
    padding: 10px 24px; border-radius: 10px; cursor: pointer;
    box-shadow: 0 4px 14px -4px rgba(15,123,99,.4); transition: all .2s;
}
.kr-modal-submit:hover { background: linear-gradient(135deg, #0A5644, #047857); }

/* ── EMPTY ── */
.kr-empty {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; padding: 60px 24px; text-align: center;
    background: #F8FAFC; border: 2px dashed #E2E8F0; border-radius: 20px;
}
.kr-empty-icon { font-size: 3rem; color: #CBD5E1; margin-bottom: 14px; }
.kr-empty-title { font-size: 1.1rem; font-weight: 800; color: #374151; margin-bottom: 8px; }
.kr-empty-sub { font-size: 13px; color: #94A3B8; max-width: 360px; line-height: 1.6; margin-bottom: 16px; }
.kr-empty-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--primary); color: #fff; font-size: 13px; font-weight: 700;
    padding: 9px 20px; border-radius: 10px; text-decoration: none;
    transition: background .2s;
}
.kr-empty-btn:hover { background: var(--primary-dark); color: #fff; }

/* ── PAGINATION ── */
.kr-pagination { display: flex; justify-content: center; }

/* ── RESPONSIVE ── */
@media (max-width: 768px) {
    .kr-hero-inner { padding: 22px 20px 16px; flex-direction: column; }
    .kr-pts-num { font-size: 2.6rem; }
    .kr-strip { padding: 12px 20px; }
    .kr-strip-div { display: none; }
    .kr-hero-right { flex-direction: row; }
}
</style>
@endsection
