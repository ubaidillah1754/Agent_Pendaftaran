@extends('layouts.public')

@section('title', 'Cek Status Pendaftaran')

@section('content')
<div class="container d-flex justify-content-center">
    <div class="card border-0 shadow-sm" style="border-radius: 20px; max-width: 520px; width: 100%;">
        <div class="card-body p-5">
            {{-- Icon --}}
            <div class="text-center mb-4">
                <div style="width:64px; height:64px; background:var(--primary-soft); color:var(--primary);
                            border-radius:16px; display:flex; align-items:center; justify-content:center;
                            font-size:2rem; margin:0 auto 16px;">
                    <i class="bi bi-search"></i>
                </div>
                <h3 class="fw-bold mb-1" style="color:var(--ink);">Cek Status Pendaftaran</h3>
                <p class="text-muted mb-0" style="font-size:0.9rem;">
                    Masukkan kode booking yang Anda terima saat mendaftar.
                </p>
            </div>

            {{-- Alert Error --}}
            @if(session('error'))
                <div class="alert d-flex align-items-start gap-2 mb-4"
                     style="background:#FEF2F2; border:1px solid #FECACA; border-radius:12px; color:#991B1B;">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
                    <span style="font-size:0.88rem;">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Alert Success --}}
            @if(session('success'))
                <div class="alert d-flex align-items-start gap-2 mb-4"
                     style="background:#ECFDF5; border:1px solid #BBF7D0; border-radius:12px; color:#065F46;">
                    <i class="bi bi-check-circle-fill flex-shrink-0 mt-1"></i>
                    <span style="font-size:0.88rem;">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('public.cek.post') }}" method="POST" id="form-cek">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-semibold text-muted small text-uppercase"
                           style="letter-spacing:0.05em;">Kode Booking</label>
                    <input type="text"
                           name="kode_booking"
                           id="kode_booking"
                           class="form-control form-control-lg @error('kode_booking') is-invalid @enderror"
                           placeholder="Contoh: BK-A7F2"
                           value="{{ old('kode_booking', $kodeBooking ?? '') }}"
                           required
                           autocomplete="off"
                           style="border-radius:12px; font-family:monospace; letter-spacing:3px;
                                  font-size:1.1rem; text-transform:uppercase;">
                    @error('kode_booking')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text" style="font-size:0.78rem; margin-top:6px;">
                        <i class="bi bi-info-circle me-1"></i>
                        Format: BK- diikuti 4 karakter (contoh: BK-A7F2, BK-92KD)
                    </div>
                </div>

                <button type="submit" id="btn-cari"
                        class="btn btn-primary w-100 py-3"
                        style="border-radius:12px; font-weight:700; font-size:0.95rem;">
                    <i class="bi bi-search me-2"></i>Cari Pendaftaran
                </button>
            </form>

            {{-- Info --}}
            <div class="mt-4 pt-4" style="border-top:1px dashed #E2E8E5;">
                <p class="text-muted mb-2" style="font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">
                    Informasi
                </p>
                <ul class="list-unstyled mb-0" style="font-size:0.85rem; color:#5A7168;">
                    <li class="mb-2"><i class="bi bi-dot" style="font-size:1.2rem;"></i>Kode booking diterima setelah mendaftar lewat petugas.</li>
                    <li class="mb-2"><i class="bi bi-dot" style="font-size:1.2rem;"></i>Gunakan kode booking untuk mengambil nomor antrean.</li>
                    <li><i class="bi bi-dot" style="font-size:1.2rem;"></i>Antrean dapat diambil mulai <strong>1 jam sebelum</strong> jadwal praktik dokter.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-uppercase input
document.getElementById('kode_booking').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Loading state on submit
document.getElementById('form-cek').addEventListener('submit', function() {
    const btn = document.getElementById('btn-cari');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Mencari...';
});
</script>
@endsection
