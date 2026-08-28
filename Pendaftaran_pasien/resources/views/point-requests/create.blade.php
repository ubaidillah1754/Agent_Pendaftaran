@extends('layouts.app')
@section('page-title', 'Pengajuan Poin')
@section('page-subtitle', 'Ajukan penambahan poin kepada Admin.')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('point-requests.index') }}">Pengajuan Poin</a></li>
    <li class="breadcrumb-item active">Ajukan Baru</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">

        {{-- Info poin saat ini --}}
        <div class="mb-4 p-4 rounded-3" style="background:linear-gradient(120deg,#0A5644,#0F7B63); color:#fff;">
            <div style="font-size:.75rem; font-weight:600; letter-spacing:.08em; opacity:.8; text-transform:uppercase;">Saldo Poin Saat Ini</div>
            <div style="font-size:2.2rem; font-weight:800; line-height:1;">{{ number_format(auth()->user()->totalPoints()) }}<span style="font-size:1rem; font-weight:500; opacity:.7;"> Poin</span></div>
            <div style="font-size:.82rem; opacity:.75; margin-top:4px;">Poin yang Anda ajukan akan ditambahkan setelah Admin menyetujui.</div>
        </div>

        <div class="card" style="border-radius:16px; border:1px solid #E2E8F0; box-shadow:0 4px 16px -8px rgba(0,0,0,.08);">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4" style="color:#0F172A;"><i class="bi bi-plus-circle me-2" style="color:var(--primary);"></i>Form Pengajuan Poin</h5>

                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('point-requests.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold" style="font-size:.85rem; color:#0F172A;">
                            Jumlah Poin yang Diajukan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white" style="border-radius:10px 0 0 10px; border-color:#E2E8F0;">
                                <i class="bi bi-star-fill" style="color:#B8912E;"></i>
                            </span>
                            <input type="number"
                                   name="points"
                                   id="points"
                                   class="form-control @error('points') is-invalid @enderror"
                                   placeholder="Masukkan jumlah poin (contoh: 100)"
                                   value="{{ old('points') }}"
                                   min="1"
                                   required
                                   style="border-radius:0 10px 10px 0; border-color:#E2E8F0; font-size:1rem; font-weight:600;">
                        </div>
                        <small class="text-muted">Tidak ada batas maksimal — masukkan sesuai kebutuhan Anda.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold" style="font-size:.85rem; color:#0F172A;">
                            Alasan / Keterangan Pengajuan <span class="text-danger">*</span>
                        </label>
                        <textarea name="reason"
                                  id="reason"
                                  class="form-control @error('reason') is-invalid @enderror"
                                  rows="4"
                                  placeholder="Jelaskan alasan pengajuan poin ini..."
                                  required
                                  style="border-radius:10px; border-color:#E2E8F0; resize:vertical;">{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Berikan keterangan yang jelas agar Admin dapat memproses pengajuan Anda.</small>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('point-requests.index') }}" class="btn text-muted" style="background:#F1F5F9; border-radius:10px; font-weight:600;">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn text-white flex-grow-1" style="background:var(--primary); border-radius:10px; font-weight:700;">
                            <i class="bi bi-send me-1"></i> Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
