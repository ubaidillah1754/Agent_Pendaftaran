{{-- resources/views/points/katalog.blade.php --}}
@extends('layouts.app')

@section('title', 'Katalog Reward')
@section('page-title', 'Katalog Penukaran Poin')
@section('page-subtitle', 'Tukarkan poin Anda dengan hadiah eksklusif dari RSI Sakinah.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('points.index') }}">Poin Saya</a></li>
    <li class="breadcrumb-item active" aria-current="page">Katalog Hadiah</li>
@endsection

@section('content')
<div class="katalog-page">

    {{-- ══════════ HERO BALANCE ══════════ --}}
    <div class="balance-hero mb-5">
        <div class="balance-hero-bg">
            <div class="bh-circle bh-c1"></div>
            <div class="bh-circle bh-c2"></div>
        </div>
        <div class="balance-hero-inner">
            <div class="bh-left">
                <div class="bh-eyebrow"><i class="bi bi-wallet2"></i> Saldo Poin Anda</div>
                <div class="bh-amount">
                    <span class="bh-num">{{ number_format($totalPoin) }}</span>
                    <span class="bh-unit">Poin</span>
                </div>
                <div class="bh-sub">Setara dengan <strong>Rp {{ number_format($totalPoin * 1000, 0, ',', '.') }}</strong> nilai tunai</div>
            </div>
            <div class="bh-right">
                <a href="{{ route('points.index') }}" class="bh-link-btn">
                    <i class="bi bi-arrow-left-circle"></i> Kembali ke Poin Saya
                </a>
                <div class="bh-badge-wrap">
                    <div class="bh-level-badge">
                        <i class="bi bi-award-fill"></i>
                        @if($totalPoin >= 500) Platinum
                        @elseif($totalPoin >= 200) Gold
                        @elseif($totalPoin >= 50) Silver
                        @else Bronze @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Info strip --}}
        <div class="bh-info-strip">
            <div class="bh-info-item">
                <i class="bi bi-shield-check-fill"></i>
                <span>Penukaran aman &amp; terverifikasi</span>
            </div>
            <div class="bh-info-divider"></div>
            <div class="bh-info-item">
                <i class="bi bi-clock-fill"></i>
                <span>Proses 1–3 hari kerja</span>
            </div>
            <div class="bh-info-divider"></div>
            <div class="bh-info-item">
                <i class="bi bi-headset"></i>
                <span>Bantuan via Admin RS</span>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="rs-alert rs-alert-success mb-4">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
        <button type="button" class="rs-alert-close" onclick="this.parentElement.remove()">&times;</button>
    </div>
    @endif
    @if(session('error'))
    <div class="rs-alert rs-alert-danger mb-4">
        <i class="bi bi-exclamation-triangle-fill"></i>
        {{ session('error') }}
        <button type="button" class="rs-alert-close" onclick="this.parentElement.remove()">&times;</button>
    </div>
    @endif

    {{-- ══════════ PENCAIRAN TUNAI ══════════ --}}
    <div class="catalog-section mb-5">
        <div class="catalog-section-head">
            <div class="csh-left">
                <div class="csh-icon cash"><i class="bi bi-cash-coin"></i></div>
                <div>
                    <div class="csh-title">Pencairan Uang Tunai</div>
                    <div class="csh-sub">1 Poin = Rp 1.000 uang tunai</div>
                </div>
            </div>
            <div class="csh-badge">Populer</div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-md-6 col-lg-5">
                <div class="reward-card reward-cash">
                    <div class="rc-img-wrap">
                        <img src="{{ asset('images/merchandise/uang_tunai_1787316722660.jpg') }}" alt="Uang Tunai" loading="lazy">
                        <div class="rc-overlay">
                            <div class="rc-pts-badge"><i class="bi bi-star-fill"></i> Rp 1.000 / Poin</div>
                        </div>
                    </div>
                    <div class="rc-body">
                        <div class="rc-cat">Pencairan Dana</div>
                        <h3 class="rc-title">Pencairan Cash</h3>
                        <p class="rc-desc">Tukarkan poin Anda langsung ke uang tunai. Setiap 1 poin senilai Rp 1.000. Proses transfer 1–3 hari kerja setelah disetujui admin.</p>

                        <div class="rc-divider"></div>

                        <form action="{{ route('points.request_redeem') }}" method="POST" id="cashForm">
                            @csrf
                            <input type="hidden" name="type" value="Uang Tunai">
                            <div class="rc-input-group">
                                <label>Jumlah Poin yang Ditukar</label>
                                <div class="rc-input-wrap">
                                    <span class="rc-input-prefix"><i class="bi bi-star-fill"></i></span>
                                    <input type="number" name="points" id="cashPoin" class="rc-input"
                                        placeholder="Masukkan jumlah poin..."
                                        min="1" max="{{ $totalPoin }}" required>
                                    <span class="rc-input-suffix" id="cashEq">= Rp 0</span>
                                </div>
                            </div>
                            <div class="rc-max-hint">Maksimum: <strong>{{ number_format($totalPoin) }} poin</strong></div>
                            <button type="button" class="rc-btn rc-btn-cash" onclick="submitCash()">
                                <i class="bi bi-send-fill"></i> Ajukan Pencairan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-7">
                <div class="cash-info-box">
                    <div class="cib-title"><i class="bi bi-info-circle-fill"></i> Cara Pencairan Dana</div>
                    <div class="cib-steps">
                        <div class="cib-step">
                            <div class="cib-step-num">01</div>
                            <div>
                                <div class="cib-step-title">Masukkan Jumlah Poin</div>
                                <div class="cib-step-desc">Tentukan berapa poin yang ingin Anda tukarkan dengan uang tunai.</div>
                            </div>
                        </div>
                        <div class="cib-step">
                            <div class="cib-step-num">02</div>
                            <div>
                                <div class="cib-step-title">Pengajuan Diproses Admin</div>
                                <div class="cib-step-desc">Admin rumah sakit akan memverifikasi pengajuan Anda dalam 1–3 hari kerja.</div>
                            </div>
                        </div>
                        <div class="cib-step">
                            <div class="cib-step-num">03</div>
                            <div>
                                <div class="cib-step-title">Dana Diterima</div>
                                <div class="cib-step-desc">Setelah disetujui, dana akan ditransfer ke rekening Anda yang terdaftar di RS.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════ MERCHANDISE KATALOG ══════════ --}}
    <div class="catalog-section">
        <div class="catalog-section-head">
            <div class="csh-left">
                <div class="csh-icon merch"><i class="bi bi-bag-heart-fill"></i></div>
                <div>
                    <div class="csh-title">Katalog Merchandise Eksklusif</div>
                    <div class="csh-sub">Hadiah istimewa untuk petugas berdedikasi</div>
                </div>
            </div>
            <span class="csh-count">{{ $merchandises->count() }} item tersedia</span>
        </div>

        @if($merchandises->count() > 0)
        <div class="row g-4 mt-1">
            @foreach($merchandises as $item)
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <div class="reward-card reward-merch h-100">
                    <div class="rc-img-wrap">
                        <img src="{{ asset('images/merchandise/' . $item->image) }}" alt="{{ $item->name }}" loading="lazy">
                        <div class="rc-overlay">
                            <div class="rc-pts-badge"><i class="bi bi-star-fill"></i> {{ $item->points }} Poin</div>
                        </div>
                        @if($totalPoin >= $item->points)
                        <div class="rc-redeemable"><i class="bi bi-check-circle-fill"></i> Bisa Ditukar</div>
                        @else
                        <div class="rc-locked"><i class="bi bi-lock-fill"></i> Poin Kurang</div>
                        @endif
                    </div>
                    <div class="rc-body d-flex flex-column" style="flex:1;">
                        <div class="rc-cat">Merchandise</div>
                        <h3 class="rc-title">{{ $item->name }}</h3>
                        <p class="rc-desc flex-grow-1">{{ $item->description }}</p>

                        <div class="rc-divider"></div>
                        <div class="rc-pts-display">
                            <span class="rc-pts-icon"><i class="bi bi-star-fill"></i></span>
                            <span class="rc-pts-val">{{ number_format($item->points) }}</span>
                            <span class="rc-pts-lbl">poin / item</span>
                        </div>

                        <form action="{{ route('points.request_redeem') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="type" value="Merchandise - {{ $item->name }}">
                            <input type="hidden" name="points" value="{{ $item->points }}">
                            <div class="rc-qty-row">
                                <div class="rc-qty-wrap">
                                    <button type="button" class="rc-qty-btn" onclick="changeQty(this,-1)">−</button>
                                    <input type="number" name="qty" class="rc-qty-input" value="1" min="1">
                                    <button type="button" class="rc-qty-btn" onclick="changeQty(this,1)">+</button>
                                </div>
                                @if($totalPoin >= $item->points)
                                <button type="button" class="rc-btn rc-btn-merch flex-grow-1"
                                    onclick="confirmTukarQty(this, '{{ $item->name }}', {{ $item->points }})">
                                    <i class="bi bi-gift-fill"></i> Tukar
                                </button>
                                @else
                                <button type="button" class="rc-btn rc-btn-disabled flex-grow-1" disabled>
                                    <i class="bi bi-lock-fill"></i> Poin Kurang
                                </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-catalog">
            <div class="ec-icon"><i class="bi bi-bag-x"></i></div>
            <div class="ec-title">Katalog Belum Tersedia</div>
            <div class="ec-sub">Merchandise akan segera ditambahkan oleh admin. Pantau terus halaman ini!</div>
        </div>
        @endif
    </div>

</div>

<script>
// Cash equivalent calculator
const cashInput = document.getElementById('cashPoin');
const cashEq = document.getElementById('cashEq');
if (cashInput) {
    cashInput.addEventListener('input', function() {
        const v = parseInt(this.value) || 0;
        cashEq.textContent = '= Rp ' + (v * 1000).toLocaleString('id-ID');
    });
}

function submitCash() {
    const pts = parseInt(document.getElementById('cashPoin').value) || 0;
    if (pts < 1) { alert('Masukkan jumlah poin yang valid.'); return; }
    const rp = (pts * 1000).toLocaleString('id-ID');
    if (confirm(`Ajukan pencairan ${pts.toLocaleString('id-ID')} poin senilai Rp ${rp}?\n\nPengajuan akan diverifikasi oleh admin.`)) {
        document.getElementById('cashForm').submit();
    }
}

function changeQty(btn, delta) {
    const input = btn.closest('.rc-qty-wrap').querySelector('.rc-qty-input');
    let v = parseInt(input.value) || 1;
    v = Math.max(1, v + delta);
    input.value = v;
}

function confirmTukarQty(btn, nama, basePoints) {
    const form = btn.closest('form');
    const qty = parseInt(form.querySelector('.rc-qty-input').value) || 1;
    const total = basePoints * qty;
    if (confirm(`Tukar ${total.toLocaleString('id-ID')} Poin dengan ${qty}× ${nama}?\n\nStatus akan "Menunggu" sampai admin menyetujui.`)) {
        form.submit();
    }
}
</script>

<style>
/* ── BASE ── */
.katalog-page { font-family: 'Plus Jakarta Sans', sans-serif; }

/* ── BALANCE HERO ── */
.balance-hero {
    background: linear-gradient(135deg, #063D2C 0%, #0F7B63 60%, #0E7490 100%);
    border-radius: 24px; overflow: hidden;
    box-shadow: 0 20px 60px -20px rgba(6,61,44,.45);
    color: #fff; position: relative;
}
.balance-hero-bg { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
.bh-circle {
    position: absolute; border-radius: 50%;
    background: rgba(255,255,255,.06);
}
.bh-c1 { width: 340px; height: 340px; top: -120px; right: -60px; }
.bh-c2 { width: 200px; height: 200px; bottom: -60px; left: 300px; }

.balance-hero-inner {
    position: relative; z-index: 1;
    display: flex; align-items: center;
    justify-content: space-between; flex-wrap: wrap; gap: 20px;
    padding: 32px 36px 24px;
}
.bh-eyebrow {
    display: inline-flex; align-items: center; gap: 7px;
    font-size: 11.5px; font-weight: 700; letter-spacing: .08em;
    color: #A7F3D0; text-transform: uppercase; margin-bottom: 8px;
}
.bh-amount { display: flex; align-items: baseline; gap: 10px; margin-bottom: 6px; }
.bh-num {
    font-family: 'Spectral', serif; font-size: 3.6rem;
    font-weight: 900; line-height: 1; letter-spacing: -2px;
}
.bh-unit { font-size: 1.1rem; font-weight: 600; opacity: .7; }
.bh-sub { font-size: 13px; color: rgba(255,255,255,.65); }

.bh-right { display: flex; flex-direction: column; align-items: flex-end; gap: 12px; }
.bh-link-btn {
    display: inline-flex; align-items: center; gap: 7px;
    background: rgba(255,255,255,.15); color: #fff;
    border: 1px solid rgba(255,255,255,.25);
    font-size: 12.5px; font-weight: 600;
    padding: 8px 16px; border-radius: 10px;
    text-decoration: none; backdrop-filter: blur(6px);
    transition: background .2s;
}
.bh-link-btn:hover { background: rgba(255,255,255,.25); color: #fff; }
.bh-level-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(252,211,77,.2); color: #FCD34D;
    border: 1px solid rgba(252,211,77,.35);
    font-size: 12.5px; font-weight: 800; letter-spacing: .06em;
    padding: 6px 14px; border-radius: 999px; text-transform: uppercase;
}

.bh-info-strip {
    position: relative; z-index: 1;
    display: flex; align-items: center;
    padding: 14px 36px; border-top: 1px solid rgba(255,255,255,.12);
    gap: 0; background: rgba(0,0,0,.1);
    flex-wrap: wrap;
}
.bh-info-item {
    display: flex; align-items: center; gap: 8px;
    font-size: 12.5px; color: rgba(255,255,255,.75);
    padding: 0 20px 0 0;
}
.bh-info-item i { color: #A7F3D0; font-size: 14px; }
.bh-info-divider {
    width: 1px; height: 16px; background: rgba(255,255,255,.2);
    margin-right: 20px; flex-shrink: 0;
}

/* ── ALERTS ── */
.rs-alert {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 18px; border-radius: 14px;
    font-size: 13.5px; font-weight: 600; position: relative;
}
.rs-alert-success { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
.rs-alert-danger  { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
.rs-alert-close {
    position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
    background: none; border: none; font-size: 1.2rem; cursor: pointer; opacity: .5;
}
.rs-alert-close:hover { opacity: 1; }

/* ── CATALOG SECTION ── */
.catalog-section { margin-bottom: 16px; }
.catalog-section-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 4px; flex-wrap: wrap; gap: 12px;
}
.csh-left { display: flex; align-items: center; gap: 14px; }
.csh-icon {
    width: 48px; height: 48px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; flex-shrink: 0;
}
.csh-icon.cash { background: linear-gradient(135deg, #D4A017, #F59E0B); color: #fff; }
.csh-icon.merch { background: linear-gradient(135deg, #0F7B63, #34D399); color: #fff; }
.csh-title { font-size: 1.1rem; font-weight: 800; color: #0B1D17; }
.csh-sub { font-size: 12.5px; color: #64748B; margin-top: 2px; }
.csh-badge {
    background: linear-gradient(135deg, #F59E0B, #D97706);
    color: #fff; font-size: 11.5px; font-weight: 800;
    padding: 5px 14px; border-radius: 999px;
    letter-spacing: .05em; text-transform: uppercase;
}
.csh-count {
    background: #F1F5F9; color: #64748B;
    font-size: 12px; font-weight: 600;
    padding: 5px 14px; border-radius: 999px;
}

/* ── REWARD CARD ── */
.reward-card {
    background: #fff; border: 1.5px solid #E2E8F0;
    border-radius: 20px; overflow: hidden;
    transition: transform .25s, box-shadow .25s, border-color .25s;
    display: flex; flex-direction: column;
}
.reward-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 50px -15px rgba(15,123,99,.2);
    border-color: var(--primary);
}

.rc-img-wrap {
    position: relative; height: 200px;
    background: linear-gradient(135deg, #F0FDF6, #E0F2FE);
    overflow: hidden; flex-shrink: 0;
}
.rc-img-wrap img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .4s ease;
}
.reward-card:hover .rc-img-wrap img { transform: scale(1.06); }

.rc-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,.35) 0%, transparent 60%);
    display: flex; align-items: flex-start; justify-content: flex-end;
    padding: 12px;
}
.rc-pts-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(255,255,255,.95); backdrop-filter: blur(4px);
    color: #0F7B63; font-size: 12.5px; font-weight: 800;
    padding: 5px 12px; border-radius: 999px;
    box-shadow: 0 2px 10px rgba(0,0,0,.1);
}
.rc-pts-badge .bi-star-fill { color: #F59E0B; }

.rc-redeemable {
    position: absolute; bottom: 12px; left: 12px;
    background: #059669; color: #fff;
    font-size: 11px; font-weight: 700;
    padding: 4px 10px; border-radius: 999px;
    display: flex; align-items: center; gap: 5px;
}
.rc-locked {
    position: absolute; bottom: 12px; left: 12px;
    background: rgba(0,0,0,.5); color: rgba(255,255,255,.8);
    font-size: 11px; font-weight: 700;
    padding: 4px 10px; border-radius: 999px;
    display: flex; align-items: center; gap: 5px;
}

.rc-body { padding: 20px; }
.rc-cat {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .08em; color: var(--primary); margin-bottom: 6px;
}
.rc-title {
    font-size: 1rem; font-weight: 800; color: #0B1D17;
    margin: 0 0 8px; line-height: 1.3;
}
.rc-desc { font-size: 12.5px; color: #64748B; line-height: 1.55; }

.rc-divider { height: 1px; background: #F1F5F9; margin: 14px 0; }

.rc-pts-display {
    display: flex; align-items: center; gap: 6px; margin-bottom: 4px;
}
.rc-pts-icon { color: #F59E0B; font-size: 14px; }
.rc-pts-val { font-size: 1.1rem; font-weight: 800; color: #0B1D17; }
.rc-pts-lbl { font-size: 12px; color: #94A3B8; }

/* Input group for cash */
.rc-input-group label {
    display: block; font-size: 12px; font-weight: 700;
    color: #64748B; margin-bottom: 8px; text-transform: uppercase; letter-spacing: .04em;
}
.rc-input-wrap {
    display: flex; align-items: center;
    border: 2px solid #E2E8F0; border-radius: 12px;
    overflow: hidden; background: #F8FAFC;
    transition: border-color .2s;
}
.rc-input-wrap:focus-within { border-color: var(--primary); background: #fff; }
.rc-input-prefix, .rc-input-suffix {
    padding: 0 12px; color: var(--primary); font-size: 14px;
    background: transparent; white-space: nowrap;
}
.rc-input-suffix { font-size: 12px; color: #64748B; font-weight: 600; }
.rc-input {
    flex: 1; border: none; background: transparent; padding: 12px 0;
    font-size: 15px; font-weight: 700; color: #0B1D17;
    outline: none; min-width: 0;
}
.rc-max-hint { font-size: 11.5px; color: #94A3B8; margin: 6px 0 16px; }

/* Qty row for merch */
.rc-qty-row { display: flex; align-items: center; gap: 10px; }
.rc-qty-wrap {
    display: flex; align-items: center;
    border: 2px solid #E2E8F0; border-radius: 10px; overflow: hidden;
}
.rc-qty-btn {
    width: 36px; height: 36px; border: none; background: #F1F5F9;
    color: #374151; font-size: 1rem; font-weight: 700;
    cursor: pointer; transition: background .2s; flex-shrink: 0;
}
.rc-qty-btn:hover { background: var(--primary-soft); color: var(--primary); }
.rc-qty-input {
    width: 44px; border: none; text-align: center;
    font-size: 14px; font-weight: 700; color: #0B1D17;
    padding: 8px 0; outline: none; background: transparent;
}

/* Buttons */
.rc-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 7px;
    border: none; border-radius: 10px; cursor: pointer;
    font-size: 13px; font-weight: 700; padding: 10px 18px;
    transition: all .2s;
}
.rc-btn-cash {
    width: 100%;
    background: linear-gradient(135deg, #D4A017, #B8912E);
    color: #fff; box-shadow: 0 4px 14px -4px rgba(184,145,46,.5);
}
.rc-btn-cash:hover { background: linear-gradient(135deg, #B8912E, #9C7A1A); transform: translateY(-1px); }
.rc-btn-merch {
    background: var(--primary);
    color: #fff; box-shadow: 0 4px 14px -4px rgba(15,123,99,.4);
}
.rc-btn-merch:hover { background: var(--primary-dark); transform: translateY(-1px); }
.rc-btn-disabled {
    background: #E2E8F0; color: #94A3B8; cursor: not-allowed;
}

/* Cash info box */
.cash-info-box {
    background: linear-gradient(135deg, #F0FDF6, #E0F2FE);
    border: 1.5px solid #D3F0E0; border-radius: 20px;
    padding: 28px; height: 100%;
}
.cib-title {
    font-size: 14px; font-weight: 800; color: #0B1D17;
    margin-bottom: 24px; display: flex; align-items: center; gap: 8px;
}
.cib-title i { color: var(--primary); font-size: 16px; }
.cib-steps { display: flex; flex-direction: column; gap: 20px; }
.cib-step { display: flex; align-items: flex-start; gap: 16px; }
.cib-step-num {
    width: 40px; height: 40px; border-radius: 12px;
    background: var(--primary); color: #fff;
    font-size: 13px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.cib-step-title { font-size: 13.5px; font-weight: 700; color: #0B1D17; margin-bottom: 4px; }
.cib-step-desc { font-size: 12.5px; color: #64748B; line-height: 1.5; }

/* Empty catalog */
.empty-catalog {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; padding: 60px 20px;
    background: #F8FAFC; border-radius: 20px; text-align: center;
    border: 2px dashed #E2E8F0;
}
.ec-icon { font-size: 3.5rem; color: #CBD5E1; margin-bottom: 14px; }
.ec-title { font-size: 1.1rem; font-weight: 700; color: #374151; margin-bottom: 8px; }
.ec-sub { font-size: 13px; color: #94A3B8; max-width: 360px; }

@media (max-width: 768px) {
    .balance-hero-inner { padding: 24px 20px 16px; }
    .bh-num { font-size: 2.8rem; }
    .bh-info-strip { padding: 12px 20px; gap: 12px; }
    .bh-info-divider { display: none; }
    .bh-right { align-items: flex-start; }
}
</style>
@endsection
