@extends('layouts.public')

@section('title', 'Cek Status Pendaftaran')

@section('content')
<div class="container d-flex justify-content-center">
    <div class="card border-0 shadow-sm" style="border-radius: 20px; max-width: 500px; width: 100%;">
        <div class="card-body p-5 text-center">
            <div style="width: 64px; height: 64px; background: var(--primary-soft); color: var(--primary); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px;">
                <i class="bi bi-search"></i>
            </div>
            
            <h3 class="fw-bold mb-2" style="color: var(--ink);">Cek Status Antrean</h3>
            <p class="text-muted mb-4">Masukkan kode booking yang Anda terima saat pendaftaran atau pindai QR Code pada tiket Anda.</p>

            @if(session('error'))
                <div class="alert alert-danger" style="border-radius: 10px; font-size: 0.9rem;">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('public.cek.post') }}" method="POST">
                @csrf
                <div class="mb-4 text-start">
                    <label class="form-label fw-semibold text-muted small text-uppercase" style="letter-spacing: 0.05em;">Kode Booking</label>
                    <input type="text" name="kode_booking" class="form-control form-control-lg @error('kode_booking') is-invalid @enderror" placeholder="Contoh: BKG123456" required style="border-radius: 12px; font-family: monospace; letter-spacing: 2px;">
                    @error('kode_booking')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-3" style="border-radius: 12px; font-weight: 600;">
                    Cari Pendaftaran
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
