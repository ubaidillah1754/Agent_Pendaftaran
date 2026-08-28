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
                    <div class="stat-val">{{ $totalPendaftaran }}</div>
                    <div class="stat-label">Total Pendaftaran</div>
                    <div class="stat-sub">Hari ini</div>
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
    <form action="{{ route('registrations.index') }}" method="GET" class="fade-in">
        <div class="filter-wrapper">
            <div class="filter-group">
                <input type="date" name="tanggal" class="form-control" style="width: 150px;"
                    value="{{ request('tanggal', date('Y-m-d')) }}">

                <select name="department_id" class="form-select" style="width: 200px;">
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
                    <option value="dipanggil" {{ request('status') == 'dipanggil' ? 'selected' : '' }}>Dipanggil</option>
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

    <div class="row fade-in">
        <!-- Antrian Sedang Proses -->
        <div class="col-lg-6 mb-4">
            <div class="custom-card h-100 d-flex flex-column">
                <div class="custom-card-header header-green">
                    <i class="bi bi-file-earmark-text"></i> ANTRIAN (SEDANG PROSES)
                </div>
                <div class="table-container flex-grow-1">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>No. Urut</th>
                                <th>Nama Pasien</th>
                                <th>Kode Booking</th>
                                <th>Poli</th>
                                <th>Dokter</th>
                                <th>Status</th>
                                <th>Waktu Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($antrianProses as $i => $reg)
                                <tr>
                                    <td>{{ $reg->urutan_antrian }}</td>
                                    <td>{{ $reg->patient->nama_pasien }}</td>
                                    <td>{{ $reg->kode_booking }}</td>
                                    <td>{{ $reg->department->nama_poli }}</td>
                                    <td>{{ $reg->doctor->nama_dokter }}</td>
                                    <td>
                                        @php
                                            $idx = $i + 1;
                                            if ($reg->status == 'dipanggil' || $idx <= 3) {
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
                                    <td colspan="8" class="text-center text-muted py-4">Tidak ada antrian yang sedang diproses.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($antrianProses->count() > 0)
                    <div class="info-alert">
                        <i class="bi bi-info-circle-fill"></i>
                        <div class="info-alert-text">
                            <strong>Informasi Antrean</strong>
                            <ul>
                                <li>3 urutan teratas (berwarna) sedang ditangani dokter.</li>
                                <li>Setelah selesai, pasien akan otomatis pindah ke tabel SELESAI.</li>
                                <li>Pasien berikutnya akan naik urutan.</li>
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="table-footer">
                    Menampilkan {{ $antrianProses->count() }} dari {{ $antrianProses->total() }} data
                    {{ $antrianProses->links('pagination::bootstrap-4') }}
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
                                <th>Kode Booking</th>
                                <th>Poli</th>
                                <th>Dokter</th>
                                <th>Selesai Dilayani</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($antrianSelesai as $i => $reg)
                                <tr>
                                    <td>{{ $i + 1 + ($antrianSelesai->currentPage() - 1) * $antrianSelesai->perPage() }}</td>
                                    <td>{{ $reg->patient->nama_pasien }}</td>
                                    <td>{{ $reg->kode_booking }}</td>
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
                    Menampilkan {{ $antrianSelesai->count() }} dari {{ $antrianSelesai->total() }} data
                    {{ $antrianSelesai->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Data Pasien Terdaftar -->
    <div class="custom-card fade-in">
        <div class="custom-card-header header-purple" style="background-color: #faf5ff;">
            <i class="bi bi-people-fill"></i> DATA PASIEN TERDAFTAR
        </div>
        <div class="table-container">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>No. Rekam Medis</th>
                        <th>Nama Pasien</th>
                        <th>Jenis Kelamin</th>
                        <th>Tanggal Lahir</th>
                        <th>No. Telepon</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $i => $patient)
                        <tr>
                            <td>{{ $i + 1 + ($patients->currentPage() - 1) * $patients->perPage() }}</td>
                            <td>{{ $patient->no_rm }}</td>
                            <td>{{ $patient->nama_pasien }}</td>
                            <td>{{ $patient->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td>{{ \Carbon\Carbon::parse($patient->tanggal_lahir)->format('d/m/Y') }}</td>
                            <td>{{ $patient->no_telepon ?? '-' }}</td>
                            <td class="text-truncate" style="max-width: 200px;">{{ $patient->alamat }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('patients.show', $patient) }}" class="btn-action-sm btn-action-view"><i
                                            class="bi bi-eye-fill"></i></a>
                                    <a href="{{ route('patients.edit', $patient) }}" class="btn-action-sm btn-action-edit"><i
                                            class="bi bi-pencil-fill"></i></a>
                                    <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus pasien ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action-sm btn-action-delete"><i
                                                class="bi bi-trash-fill"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Data pasien tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            Menampilkan {{ $patients->count() }} dari {{ $patients->total() }} data
            <div>
                {{ $patients->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

@endsection