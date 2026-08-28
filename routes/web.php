<?php

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Petugas\PointController as PetugasPointController;
use App\Http\Controllers\Admin\MerchandiseController as AdminMerchandiseController;
use App\Http\Controllers\Admin\PointRedemptionController as AdminPointRedemptionController;
use App\Http\Controllers\Admin\PointAdjustmentController as AdminPointAdjustmentController;
use App\Http\Controllers\Admin\PointReportController as AdminPointReportController;
use Illuminate\Support\Facades\Route;

// ── Root Route ───────────────────────────────────────────────────────────────
Route::get('/', function () { return redirect()->route('login'); });

// ── Auth Routes (tamu saja) ──────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Master Data (Admin saja) ─────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {

        // Manajemen Poli/Departemen
        Route::resource('departments', DepartmentController::class)
            ->names([
                'index'   => 'departments.index',
                'create'  => 'departments.create',
                'store'   => 'departments.store',
                'show'    => 'departments.show',
                'edit'    => 'departments.edit',
                'update'  => 'departments.update',
                'destroy' => 'departments.destroy',
            ]);

        // Manajemen Dokter
        Route::resource('doctors', DoctorController::class)
            ->names([
                'index'   => 'doctors.index',
                'create'  => 'doctors.create',
                'store'   => 'doctors.store',
                'show'    => 'doctors.show',
                'edit'    => 'doctors.edit',
                'update'  => 'doctors.update',
                'destroy' => 'doctors.destroy',
            ]);

        // Manajemen Jadwal Praktik
        Route::resource('doctor-schedules', DoctorScheduleController::class)
            ->names([
                'index'   => 'doctor-schedules.index',
                'create'  => 'doctor-schedules.create',
                'store'   => 'doctor-schedules.store',
                'show'    => 'doctor-schedules.show',
                'edit'    => 'doctor-schedules.edit',
                'update'  => 'doctor-schedules.update',
                'destroy' => 'doctor-schedules.destroy',
            ]);

        // Toggle status aktif dokter/jadwal/poli (AJAX)
        Route::patch('doctors/{doctor}/toggle-active',         [DoctorController::class, 'toggleActive'])->name('doctors.toggle-active');
        Route::patch('departments/{department}/toggle-active', [DepartmentController::class, 'toggleActive'])->name('departments.toggle-active');
        Route::patch('doctor-schedules/{doctorSchedule}/toggle-active', [DoctorScheduleController::class, 'toggleActive'])->name('doctor-schedules.toggle-active');
    });

    // ── Data Pasien Terdaftar (Admin + Petugas) ─────────────────────────────────
    Route::resource('patients', PatientController::class)
        ->only(['index', 'show'])
        ->names([
            'index' => 'patients.index',
            'show'  => 'patients.show',
        ]);

    // ── Pendaftaran Rawat Jalan ───────────────────────────────────────────────
    Route::get('registrations/riwayat-saya', [RegistrationController::class, 'riwayat'])
        ->name('registrations.riwayat')
        ->middleware('role:petugas');

    // Cetak antrian / tracer
    Route::get('registrations/{registration}/cetak', [RegistrationController::class, 'cetak'])
        ->name('registrations.cetak');

    Route::resource('registrations', RegistrationController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy'])
        ->names([
            'index'   => 'registrations.index',
            'create'  => 'registrations.create',
            'store'   => 'registrations.store',
            'show'    => 'registrations.show',
            'destroy' => 'registrations.destroy',
        ]);

    // ── AJAX Endpoints ────────────────────────────────────────────────────────
    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::get('doctors',     [AjaxController::class, 'getDoctors'])->name('doctors');
        Route::get('schedules',   [AjaxController::class, 'getSchedules'])->name('schedules');
        Route::get('kuota',       [AjaxController::class, 'getKuota'])->name('kuota');
        Route::get('cari-pasien', [AjaxController::class, 'cariPasien'])->name('cari-pasien');
    });

    // ══════════════════════════════════════════════════════════════════════════
    // SISTEM POIN & REWARD (PETUGAS)
    // ══════════════════════════════════════════════════════════════════════════
    Route::middleware('role:petugas')->group(function () {
        Route::get('/poin',               [PetugasPointController::class, 'index'])->name('points.index');
        Route::get('/poin/riwayat',       [PetugasPointController::class, 'riwayat'])->name('points.riwayat');
        Route::get('/poin/katalog',       [PetugasPointController::class, 'katalog'])->name('points.katalog');
        Route::post('/poin/katalog/tukar',[PetugasPointController::class, 'tukar'])->name('points.tukar');
        Route::get('/poin/penukaran',     [PetugasPointController::class, 'riwayatRedemption'])->name('points.redemptions.index');
    });

    // Cetak resi dapat diakses petugas (pemilik) & admin
    Route::get('/poin/penukaran/{redemption}/cetak', [PetugasPointController::class, 'cetakResi'])
        ->name('points.redemptions.cetak');

    // ══════════════════════════════════════════════════════════════════════════
    // SISTEM POIN & REWARD (ADMIN)
    // ══════════════════════════════════════════════════════════════════════════
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Master Merchandise
        Route::resource('merchandises', AdminMerchandiseController::class)
            ->names('merchandises');

        // Persetujuan & Manajemen Penukaran Reward
        Route::get('/penukaran',                          [AdminPointRedemptionController::class, 'index'])->name('redemptions.index');
        Route::get('/penukaran/{redemption}',             [AdminPointRedemptionController::class, 'show'])->name('redemptions.show');
        Route::patch('/penukaran/{redemption}/approve',   [AdminPointRedemptionController::class, 'approve'])->name('redemptions.approve');
        Route::patch('/penukaran/{redemption}/complete',  [AdminPointRedemptionController::class, 'complete'])->name('redemptions.complete');
        Route::patch('/penukaran/{redemption}/reject',    [AdminPointRedemptionController::class, 'reject'])->name('redemptions.reject');
        Route::patch('/penukaran/{redemption}/cancel',    [AdminPointRedemptionController::class, 'cancel'])->name('redemptions.cancel');

        // Penyesuaian / Adjustment Poin Karyawan
        Route::get('/poin/adjustment',  [AdminPointAdjustmentController::class, 'index'])->name('points.adjustment.index');
        Route::post('/poin/adjustment', [AdminPointAdjustmentController::class, 'store'])->name('points.adjustment.store');

        // Laporan Rekapitulasi Poin, Ledger & Reward
        Route::get('/poin/laporan', [AdminPointReportController::class, 'index'])->name('reports.index');
    });
});
