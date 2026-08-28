@extends('layouts.app')
@section('title', 'Penyesuaian Poin Karyawan — Admin')
@section('page-title', 'Penyesuaian Poin Karyawan')
@section('page-subtitle', 'Koreksi saldo poin karyawan secara resmi dengan pencatatan audit trail.')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Penyesuaian Poin</li>
@endsection

@section('content')
<div class="row g-4 fade-in">
    <!-- Form Tambah Adjustment -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <span class="rs-card-title"><i class="bi bi-sliders2"></i>Form Penyesuaian</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.points.adjustment.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:700; font-size:.82rem;">Pilih Karyawan / Petugas <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select searchable" required>
                            <option value="">— Pilih Karyawan —</option>
                            @foreach($petugasList as $p)
                                <option value="{{ $p->id }}" {{ old('user_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} (Saldo saat ini: {{ number_format($p->point_balance) }} Poin)
                                </option>
                            @endforeach
                        </select>
                        @error('user_id') <div class="text-danger mt-1" style="font-size:.75rem;">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:700; font-size:.82rem;">Jenis Penyesuaian <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action" id="actionAdd" value="add" checked>
                                <label class="form-check-label fw-bold text-success" for="actionAdd">
                                    <i class="bi bi-plus-circle me-1"></i>Tambah Poin (+)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action" id="actionSubtract" value="subtract" {{ old('action') === 'subtract' ? 'selected' : '' }}>
                                <label class="form-check-label fw-bold text-danger" for="actionSubtract">
                                    <i class="bi bi-dash-circle me-1"></i>Kurangi Poin (-)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:700; font-size:.82rem;">Jumlah Poin <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" value="{{ old('amount', 10) }}" min="1" max="10000" required>
                        @error('amount') <div class="text-danger mt-1" style="font-size:.75rem;">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:700; font-size:.82rem;">Alasan Penyesuaian <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Contoh: Koreksi input manual pendaftaran offline / perbaikan data..." required>{{ old('reason') }}</textarea>
                        @error('reason') <div class="text-danger mt-1" style="font-size:.75rem;">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn text-white w-100" style="background:var(--rs-primary); border-radius:8px; font-weight:700;" onclick="return confirm('Apakah Anda yakin ingin memproses penyesuaian poin ini?')">
                        <i class="bi bi-check2-circle me-1"></i>Proses Penyesuaian Poin
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Riwayat Penyesuaian Poin -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="rs-card-title"><i class="bi bi-clock-history"></i>Riwayat Penyesuaian (Audit Log)</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table rs-table mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kode Ref</th>
                                <th>Karyawan</th>
                                <th>Penyesuaian</th>
                                <th>Alasan</th>
                                <th class="pe-4">Oleh Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($adjustments as $adj)
                            <tr>
                                <td style="font-size:.82rem; white-space:nowrap;">
                                    {{ $adj->created_at->format('d M Y, H:i') }}
                                </td>
                                <td>
                                    <code style="font-weight:700; color:var(--rs-ink); font-size:.8rem;">{{ $adj->reference }}</code>
                                </td>
                                <td>
                                    <div class="fw-bold" style="font-size:.88rem; color:var(--rs-ink);">{{ $adj->user->name }}</div>
                                </td>
                                <td>
                                    <span class="fw-bold" style="font-size:.9rem; color:{{ $adj->amount > 0 ? '#0F7B63' : '#B54545' }};">
                                        {{ $adj->amount > 0 ? '+' : '' }}{{ number_format($adj->amount) }} Poin
                                    </span>
                                    <div style="font-size:.7rem; color:var(--rs-muted);">
                                        {{ number_format($adj->balance_before) }} &rarr; {{ number_format($adj->balance_after) }}
                                    </div>
                                </td>
                                <td style="font-size:.82rem; max-width:200px; color:var(--rs-muted);">
                                    {{ $adj->description }}
                                </td>
                                <td class="pe-4" style="font-size:.82rem;">
                                    {{ $adj->creator?->name ?? 'Admin' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Belum ada riwayat penyesuaian poin yang dicatat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($adjustments->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $adjustments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
