@extends('layouts.app')
@section('title', 'Daftar Pendaftaran')
@section('page-title', 'Daftar Pendaftaran')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Pendaftaran</li>
@endsection

@push('styles')
    <style>
        /* Hero Banner */
        .hero-banner {
            background-color: #115e59;
            /* Emerald 800 */
            border-radius: 12px;
            padding: 18px 24px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .hero-banner::after {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 300px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05));
        }

        .hero-title {
            font-size: 1.15rem;
            font-weight: 600;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hero-desc {
            font-size: 0.8rem;
            color: #a7f3d0;
            /* Emerald 200 */
            margin: 0;
        }

        .hero-date {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Stat Cards */
        .stat-card-custom {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-icon-custom {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-icon-green {
            background: #d1fae5;
            color: #059669;
        }

        .stat-icon-yellow {
            background: #fef3c7;
            color: #d97706;
        }

        .stat-icon-blue {
            background: #e0f2fe;
            color: #0284c7;
        }

        .stat-icon-purple {
            background: #f3e8ff;
            color: #7e22ce;
        }

        .stat-info .stat-val {
            font-size: 1.35rem;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
        }

        .stat-info .stat-label {
            font-size: 0.75rem;
            color: #4b5563;
            font-weight: 600;
        }

        .stat-info .stat-sub {
            font-size: 0.65rem;
            color: #6b7280;
        }

        /* Filter Card */
        .filter-wrapper {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            gap: 10px;
            flex: 1;
            align-items: center;
        }

        .filter-group .form-control,
        .filter-group .form-select,
        .filter-group .btn {
            font-size: 0.8rem;
            padding: 6px 12px;
        }

        .search-box {
            position: relative;
            width: 220px;
        }

        .search-box input {
            width: 100%;
            padding-left: 32px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding-top: 6px;
            padding-bottom: 6px;
            font-size: 0.8rem;
        }

        .search-box i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.8rem;
        }

        /* Table Cards */
        .custom-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .custom-card-header {
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fdfdfd;
        }

        .header-green {
            color: #059669;
        }

        .header-orange {
            color: #d97706;
        }

        .header-purple {
            color: #7e22ce;
        }

        .table-container {
            padding: 0;
            overflow-x: auto;
        }

        .table-custom {
            width: 100%;
            min-width: 600px;
            border-collapse: collapse;
        }

        .table-custom th {
            background: #f9fafb;
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #6b7280;
            padding: 10px 14px;
            text-align: left;
            font-weight: 700;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-custom td {
            padding: 10px 14px;
            font-size: 0.8rem;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .table-custom tr:last-child td {
            border-bottom: none;
        }

        .table-footer {
            padding: 10px 16px;
            font-size: 0.8rem;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Info Alert */
        .info-alert {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 12px 16px;
            margin: 16px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .info-alert i {
            color: #3b82f6;
            font-size: 1.25rem;
        }

        .info-alert-text {
            font-size: 0.8125rem;
            color: #1e40af;
        }

        .info-alert-text ul {
            margin: 4px 0 0;
            padding-left: 16px;
        }

        /* Status Badges Custom */
        .badge-proses-1 {
            background: #dcfce7;
            color: #166534;
        }

        .badge-proses-2 {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-proses-3 {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge-menunggu-abu {
            background: #f3f4f6;
            color: #4b5563;
        }

        .btn-action-sm {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            border: 1px solid transparent;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-action-view {
            background: #eff6ff;
            color: #3b82f6;
            border-color: #bfdbfe;
        }

        .btn-action-edit {
            background: #fffbeb;
            color: #d97706;
            border-color: #fde68a;
        }

        .btn-action-delete {
            background: #fef2f2;
            color: #ef4444;
            border-color: #fecaca;
        }

        .btn-action-sm:hover {
            filter: brightness(0.95);
        }

        /* Custom Pagination */
        .custom-pagination {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .custom-pagination .pag-btn {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            border: 1px solid #e5e7eb;
            background: white;
            color: #374151;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.78rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }

        .custom-pagination .pag-btn:hover:not(.disabled) {
            background: #059669;
            color: white;
            border-color: #059669;
        }

        .custom-pagination .pag-btn.active {
            background: #059669;
            color: white;
            border-color: #059669;
        }

        .custom-pagination .pag-btn.disabled {
            opacity: 0.4;
            cursor: default;
            pointer-events: none;
        }

        .custom-pagination .pag-info {
            font-size: 0.75rem;
            color: #6b7280;
            padding: 0 6px;
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')

    <!-- Hero Section -->
    <div class="hero-banner fade-in">
        <div>
            <div class="hero-title">
                <i class="bi bi-file-earmark-medical"></i> Manajemen Pendaftaran Rawat Jalan
            </div>
            <p class="hero-desc">Kelola data pendaftaran pasien dengan mudah, cepat, dan akurat</p>
        </div>
        <div class="hero-date">
            <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4 fade-in">
        <div class="col-md-3">
            <div class="stat-card-custom">
                <div class="stat-icon-custom stat-icon-green">
                    <i class="bi bi-clipboard2-check-fill"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-val">{{ $totalAntrean }}</div>
                    <div class="stat-label">Total Antrean</div>
                    <div class="stat-sub">Hari ini (sudah ambil nomor)</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-custom">
                <div class="stat-icon-custom stat-icon-yellow">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-val">{{ $departments->count() }}</div>
                    <div class="stat-label">Poli Tersedia</div>
                    <div class="stat-sub">Aktif</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-custom">
                <div class="stat-icon-custom stat-icon-blue">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-val">{{ $totalMenunggu }}</div>
                    <div class="stat-label">Menunggu (hari ini)</div>
                    <div class="stat-sub">Pasien dalam antrean</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-custom">
                <div class="stat-icon-custom stat-icon-purple">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-val">{{ $totalSelesai }}</div>
                    <div class="stat-label">Selesai (hari ini)</div>
                    <div class="stat-sub">Pasien selesai dilayani</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('registrations.index') }}" method="GET" class="fade-in" style="position: relative; z-index: 10;">
        <div class="filter-wrapper">
            <div class="filter-group">
                <input type="date" name="tanggal" class="form-control" style="width: 150px;"
                    value="{{ request('tanggal', date('Y-m-d')) }}">

                <select name="department_id" class="form-select searchable" placeholder="Semua Poli" style="width: 200px;">
                    <option value="">Semua Poli</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->nama_poli }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="form-select" style="width: 150px;">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="diperiksa" {{ request('status') == 'diperiksa' ? 'selected' : '' }}>Diperiksa</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>

                <button type="submit" class="btn btn-success" style="background-color: #059669; border-color: #059669;"><i
                        class="bi bi-funnel me-1"></i> Filter</button>
                <a href="{{ route('registrations.index') }}" class="btn btn-light"
                    style="border: 1px solid #d1d5db;">Reset</a>
            </div>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" name="search" placeholder="Cari pasien..." value="{{ request('search') }}">
            </div>
        </div>
    </form>

    <!-- Panel Antrean -->
    @if(request('department_id'))
    <div class="custom-card fade-in mb-4" style="border: 2px solid #059669;">
        <div class="custom-card-header text-white" style="background: #059669; font-size: 1rem; padding: 16px 20px;">
            <i class="bi bi-display me-2"></i> PANEL ANTREAN - {{ $departments->firstWhere('id', request('department_id'))->nama_poli ?? 'Poli' }}
        </div>
        <div class="card-body p-4">
            <div class="row text-center mb-4">
                <!-- Pasien Sedang Ditangani -->
                <div class="col-md-6 border-end">
                    <h6 class="text-muted fw-bold mb-3" style="letter-spacing: 1px;">PASIEN SEDANG DITANGANI</h6>
                    @if($pasienAktif)
                        <div class="p-3 bg-light rounded-3 d-inline-block shadow-sm mb-2" style="min-width: 250px;">
                            <h2 class="fw-bold mb-1" style="color: #059669; font-size: 2.5rem;">{{ $pasienAktif->nomor_antrian }}</h2>
                            <h5 class="mb-2 text-dark">{{ $pasienAktif->patient->nama_pasien }}</h5>
                            <span class="badge" style="background-color: #059669; font-size: 0.9rem; padding: 8px 16px;">DIPROSES</span>
                        </div>
                    @else
                        <div class="p-3 mt-4">
                            <h5 class="text-muted"><i class="bi bi-person-x fs-2 d-block mb-2 text-light"></i>Tidak ada pasien</h5>
                        </div>
                    @endif
                </div>

                <!-- Pasien Berikutnya -->
                <div class="col-md-6">
                    <h6 class="text-muted fw-bold mb-3" style="letter-spacing: 1px;">PASIEN BERIKUTNYA</h6>
                    @if($pasienBerikutnya)
                        <div class="p-3 bg-light rounded-3 d-inline-block shadow-sm mb-2" style="min-width: 250px; opacity: 0.8;">
                            <h2 class="fw-bold mb-1 text-secondary" style="font-size: 2.5rem;">{{ $pasienBerikutnya->nomor_antrian }}</h2>
                            <h5 class="mb-2 text-dark">{{ $pasienBerikutnya->patient->nama_pasien }}</h5>
                            <span class="badge badge-menunggu-abu" style="font-size: 0.9rem; padding: 8px 16px;">MENUNGGU</span>
                        </div>
                    @else
                        <div class="p-3 mt-4">
                            <h5 class="text-muted"><i class="bi bi-person-dash fs-2 d-block mb-2 text-light"></i>Tidak ada pasien berikutnya</h5>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="text-center mt-2">
                <form action="{{ route('registrations.panggil-berikutnya') }}" method="POST" id="form-panggil-berikutnya" onsubmit="disablePanggilButton()">
                    @csrf
                    <input type="hidden" name="department_id" value="{{ request('department_id') }}">
                    <input type="hidden" name="tanggal" value="{{ request('tanggal', date('Y-m-d')) }}">
                    <button type="submit" class="btn btn-lg fw-bold px-5 py-3 shadow" 
                            style="background: #059669; color: white; border-radius: 50px; font-size: 1.1rem; transition: all 0.3s;" 
                            id="btn-panggil-berikutnya" 
                            {{ !$pasienAktif && !$pasienBerikutnya ? 'disabled' : '' }}>
                        <i class="bi bi-megaphone-fill me-2"></i> PANGGIL PASIEN BERIKUTNYA
                    </button>
                </form>
            </div>
        </div>
    </div>
    @else
    <div class="info-alert fade-in mb-4">
        <i class="bi bi-info-circle-fill" style="color: #059669;"></i>
        <div class="info-alert-text" style="color: #065f46;">
            <strong>Panel Antrean disembunyikan.</strong> Silakan pilih <strong>Poli</strong> pada form filter di atas untuk mengaktifkan fitur Panggil Pasien Berikutnya.
        </div>
    </div>
    @endif

    <div class="row fade-in">
        <!-- Antrian Sedang Proses -->
        <div class="col-lg-6 mb-4">
            <div class="custom-card h-100 d-flex flex-column">
                <div class="custom-card-header header-green">
                    <i class="bi bi-file-earmark-text"></i> PENDAFTARAN (SEDANG PROSES)
                </div>
                <div class="table-container flex-grow-1">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Pasien</th>
                                <th>No. Antrean</th>
                                <th>Poli</th>
                                <th>Dokter</th>
                                <th>Status</th>
                                <th>Waktu Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendaftaranProses as $i => $reg)
                                <tr>
                                    <td>{{ $i + 1 + ($pendaftaranProses->currentPage() - 1) * $pendaftaranProses->perPage() }}</td>
                                    <td>
                                        {{ $reg->patient->nama_pasien }}
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ $reg->patient->no_rm }}</div>
                                    </td>
                                    <td>
                                        <span class="badge" style="font-size: 0.85rem; padding: 5px 10px; background: var(--bs-indigo); color: white;">
                                            {{ $reg->nomor_antrian ?? '-' }}
                                        </span>
                                    </td>
                                    <td>{{ $reg->department->nama_poli }}</td>
                                    <td>{{ $reg->doctor->nama_dokter }}</td>
                                    <td>
                                        @php
                                            $idx = $i + 1;
                                            if ($reg->status == 'diperiksa' || $idx <= 3) {
                                                if ($idx == 1)
                                                    $badgeClass = 'badge-proses-1';
                                                elseif ($idx == 2)
                                                    $badgeClass = 'badge-proses-2';
                                                else
                                                    $badgeClass = 'badge-proses-3';
                                                $statusText = 'Proses ' . $idx;
                                            } else {
                                                $badgeClass = 'badge-menunggu-abu';
                                                $statusText = 'Menunggu';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>{{ $reg->created_at->format('H:i') }}</td>
                                    <td>
                                        <a href="{{ route('registrations.show', $reg) }}" class="btn-action-sm btn-action-view"
                                            title="Detail"><i class="bi bi-eye-fill"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Tidak ada pendaftaran yang sedang diproses.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="table-footer mt-auto">
                    <span>Menampilkan {{ $pendaftaranProses->firstItem() ?? 0 }}–{{ $pendaftaranProses->lastItem() ?? 0 }} dari {{ $pendaftaranProses->total() }} data</span>
                    <div class="custom-pagination">
                        @if($pendaftaranProses->onFirstPage())
                            <span class="pag-btn disabled"><i class="bi bi-chevron-double-left"></i></span>
                            <span class="pag-btn disabled"><i class="bi bi-chevron-left"></i></span>
                        @else
                            <a href="{{ $pendaftaranProses->url(1) }}" class="pag-btn" title="Halaman pertama"><i class="bi bi-chevron-double-left"></i></a>
                            <a href="{{ $pendaftaranProses->previousPageUrl() }}" class="pag-btn" title="Sebelumnya"><i class="bi bi-chevron-left"></i></a>
                        @endif

                        <span class="pag-info">Hal {{ $pendaftaranProses->currentPage() }} / {{ $pendaftaranProses->lastPage() }}</span>

                        @if($pendaftaranProses->hasMorePages())
                            <a href="{{ $pendaftaranProses->nextPageUrl() }}" class="pag-btn" title="Berikutnya"><i class="bi bi-chevron-right"></i></a>
                            <a href="{{ $pendaftaranProses->url($pendaftaranProses->lastPage()) }}" class="pag-btn" title="Halaman terakhir"><i class="bi bi-chevron-double-right"></i></a>
                        @else
                            <span class="pag-btn disabled"><i class="bi bi-chevron-right"></i></span>
                            <span class="pag-btn disabled"><i class="bi bi-chevron-double-right"></i></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Selesai (Hari Ini) -->
        <div class="col-lg-6 mb-4">
            <div class="custom-card h-100 d-flex flex-column">
                <div class="custom-card-header header-orange">
                    <i class="bi bi-check-square-fill"></i> SELESAI (HARI INI)
                </div>
                <div class="table-container flex-grow-1">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Pasien</th>
                                <th>No. Antrean</th>
                                <th>Poli</th>
                                <th>Dokter</th>
                                <th>Selesai Dilayani</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendaftaranSelesai as $i => $reg)
                                <tr>
                                    <td>{{ $i + 1 + ($pendaftaranSelesai->currentPage() - 1) * $pendaftaranSelesai->perPage() }}</td>
                                    <td>{{ $reg->patient->nama_pasien }}<div class="text-muted" style="font-size: 0.7rem;">{{ $reg->patient->no_rm }}</div></td>
                                    <td>
                                        <span class="badge" style="font-size: 0.85rem; padding: 5px 10px; background: var(--bs-indigo); color: white;">
                                            {{ $reg->nomor_antrian ?? '-' }}
                                        </span>
                                    </td>
                                    <td>{{ $reg->department->nama_poli }}</td>
                                    <td>{{ $reg->doctor->nama_dokter }}</td>
                                    <td>{{ $reg->updated_at->format('H:i') }}</td>
                                    <td>
                                        <a href="{{ route('registrations.show', $reg) }}" class="btn-action-sm btn-action-view"
                                            title="Detail"><i class="bi bi-eye-fill"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada pasien yang selesai dilayani
                                        hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="table-footer mt-auto">
                    <span>Menampilkan {{ $pendaftaranSelesai->firstItem() ?? 0 }}–{{ $pendaftaranSelesai->lastItem() ?? 0 }} dari {{ $pendaftaranSelesai->total() }} data</span>
                    <div class="custom-pagination">
                        @if($pendaftaranSelesai->onFirstPage())
                            <span class="pag-btn disabled"><i class="bi bi-chevron-double-left"></i></span>
                            <span class="pag-btn disabled"><i class="bi bi-chevron-left"></i></span>
                        @else
                            <a href="{{ $pendaftaranSelesai->url(1) }}" class="pag-btn" title="Halaman pertama"><i class="bi bi-chevron-double-left"></i></a>
                            <a href="{{ $pendaftaranSelesai->previousPageUrl() }}" class="pag-btn" title="Sebelumnya"><i class="bi bi-chevron-left"></i></a>
                        @endif

                        <span class="pag-info">Hal {{ $pendaftaranSelesai->currentPage() }} / {{ $pendaftaranSelesai->lastPage() }}</span>

                        @if($pendaftaranSelesai->hasMorePages())
                            <a href="{{ $pendaftaranSelesai->nextPageUrl() }}" class="pag-btn" title="Berikutnya"><i class="bi bi-chevron-right"></i></a>
                            <a href="{{ $pendaftaranSelesai->url($pendaftaranSelesai->lastPage()) }}" class="pag-btn" title="Halaman terakhir"><i class="bi bi-chevron-double-right"></i></a>
                        @else
                            <span class="pag-btn disabled"><i class="bi bi-chevron-right"></i></span>
                            <span class="pag-btn disabled"><i class="bi bi-chevron-double-right"></i></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seluruh Data Pendaftaran -->
    <div class="custom-card fade-in">
        <div class="custom-card-header header-purple" style="background-color: #faf5ff; display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 10;">
            <div>
                <i class="bi bi-card-list"></i> SELURUH DATA PENDAFTARAN
            </div>
            <div>
                <form action="{{ route('registrations.index') }}" method="GET" class="d-flex" style="margin: 0;">
                    @if(request('department_id'))
                        <input type="hidden" name="department_id" value="{{ request('department_id') }}">
                    @endif
                    @if(request('tanggal'))
                        <input type="hidden" name="tanggal" value="{{ request('tanggal') }}">
                    @endif
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select name="filter_poli" class="form-select form-select-sm searchable" placeholder="Semua Poli" onchange="this.form.submit()" style="min-width: 200px; border-radius: 20px;">
                        <option value="">Semua Poli</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('filter_poli') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->nama_poli }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
        <div class="table-container">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode Booking</th>
                        <th>No. RM</th>
                        <th>Nama Pasien</th>
                        <th>Poli</th>
                        <th>Dokter</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allRegistrations as $i => $reg)
                        <tr>
                            <td>{{ $i + 1 + ($allRegistrations->currentPage() - 1) * $allRegistrations->perPage() }}</td>
                            <td><span style="font-family: monospace; font-weight: bold;">{{ $reg->kode_booking ?? '-' }}</span></td>
                            <td>{{ $reg->patient->no_rm }}</td>
                            <td>{{ $reg->patient->nama_pasien }}</td>
                            <td>{{ $reg->department->nama_poli }}</td>
                            <td>{{ $reg->doctor->nama_dokter }}</td>
                            <td>{{ \Carbon\Carbon::parse($reg->tanggal_kunjungan)->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $badgeClass = match($reg->status) {
                                        'menunggu'  => 'badge-menunggu',
                                        'diperiksa' => 'badge-diperiksa',
                                        'selesai'   => 'badge-selesai',
                                        'batal'     => 'badge-batal',
                                        default     => '',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($reg->status) }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('registrations.show', $reg) }}" class="btn-action-sm btn-action-view"><i
                                            class="bi bi-eye-fill"></i></a>
                                    <a href="{{ route('registrations.edit', $reg) }}" class="btn-action-sm btn-action-edit"><i
                                            class="bi bi-pencil-fill"></i></a>
                                    <form action="{{ route('registrations.destroy', $reg) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus pendaftaran ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action-sm btn-action-delete"><i
                                                class="bi bi-trash-fill"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Data pendaftaran tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            Menampilkan {{ $allRegistrations->count() }} dari {{ $allRegistrations->total() }} data
            {{ $allRegistrations->links('pagination::bootstrap-4') }}
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function disablePanggilButton() {
        const btn = document.getElementById('btn-panggil-berikutnya');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses...';
        }
        return true;
    }
</script>
@endpush