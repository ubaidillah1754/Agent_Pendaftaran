@extends('layouts.public')

@section('title', 'Beranda')

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, var(--primary-soft) 0%, white 100%);
        border-radius: 24px;
        padding: 60px 40px;
        text-align: center;
        margin-bottom: 40px;
        border: 1px solid var(--border);
    }
    .hero-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--primary-dark);
        margin-bottom: 16px;
    }
    .hero-subtitle {
        font-size: 1.1rem;
        color: var(--muted);
        max-width: 600px;
        margin: 0 auto 30px;
        line-height: 1.6;
    }
    .btn-hero {
        background: var(--primary);
        color: white;
        padding: 12px 30px;
        border-radius: 999px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }
    .btn-hero:hover {
        background: var(--primary-dark);
        color: white;
        transform: translateY(-2px);
    }
    .btn-hero-outline {
        background: transparent;
        color: var(--primary);
        border: 2px solid var(--primary);
        padding: 10px 30px;
        border-radius: 999px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }
    .btn-hero-outline:hover {
        background: var(--primary-soft);
    }
    .dept-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 24px;
        height: 100%;
        transition: all 0.3s;
    }
    .dept-card:hover {
        border-color: var(--primary-light);
        box-shadow: 0 10px 30px rgba(15, 123, 99, 0.08);
        transform: translateY(-4px);
    }
    .dept-icon {
        width: 48px;
        height: 48px;
        background: var(--primary-soft);
        color: var(--primary);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 16px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="hero-section">
        <h1 class="hero-title">Sistem Pendaftaran Rawat Jalan</h1>
        <p class="hero-subtitle">
            Selamat datang di portal layanan pasien RS Islam Sakinah. Pantau jadwal dokter favorit Anda dan cek status antrean pendaftaran dengan mudah.
        </p>
        <div class="d-flex flex-wrap gap-3 justify-content-center">
            <a href="{{ route('public.jadwal') }}" class="btn-hero">
                <i class="bi bi-calendar-check"></i> Lihat Jadwal Dokter
            </a>
            <a href="{{ route('public.cek') }}" class="btn-hero-outline">
                <i class="bi bi-search"></i> Cek Status Pendaftaran
            </a>
        </div>
    </div>

    <div class="mt-5">
        <h3 class="fw-bold mb-4 text-center" style="color: var(--ink);">Layanan Poli Tersedia</h3>
        <div class="row g-4 justify-content-center">
            @forelse($departments as $dept)
            <div class="col-md-4 col-sm-6">
                <div class="dept-card text-center">
                    <div class="dept-icon mx-auto">
                        <i class="bi bi-hospital"></i>
                    </div>
                    <h5 class="fw-bold mb-2">{{ $dept->nama_poli }}</h5>
                    <p class="text-muted small mb-0">{{ $dept->deskripsi ?? 'Pelayanan rawat jalan terpadu.' }}</p>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted">Belum ada data poli.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
