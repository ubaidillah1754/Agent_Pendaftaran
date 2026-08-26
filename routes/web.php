<?php

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AntrianController;
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
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// ── Public Routes (tanpa login) ──────────────────────────────────────────────
Route::get('/', function () { return redirect()->route('login'); });
Route::get('/info-pendaftaran', [PublicController::class, 'index'])->name('info.pendaftaran');
Route::get('/jadwal-dokter', [PublicController::class, 'jadwal'])->name('public.jadwal');
Route::get('/cek-pendaftaran', [PublicController::class, 'cek'])->name('public.cek');
Route::post('/cek-pendaftaran', [PublicController::class, 'prosesCek'])->name('public.cek.post');
Route::get('/tracer/{kode_booking}', [PublicController::class, 'tracer'])->name('public.tracer');

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
        Route::patch('doctors/{doctor}/toggle-active',        [DoctorController::class, 'toggleActive'])->name('doctors.toggle-active');
        Route::patch('departments/{department}/toggle-active',[DepartmentController::class, 'toggleActive'])->name('departments.toggle-active');
        Route::patch('doctor-schedules/{doctorSchedule}/toggle-active', [DoctorScheduleController::class, 'toggleActive'])->name('doctor-schedules.toggle-active');
    });

    // ── Manajemen Pasien (Admin + Petugas) ───────────────────────────────────
    Route::resource('patients', PatientController::class)
        ->names([
            'index'   => 'patients.index',
            'create'  => 'patients.create',
            'store'   => 'patients.store',
            'show'    => 'patients.show',
            'edit'    => 'patients.edit',
            'update'  => 'patients.update',
            'destroy' => 'patients.destroy',
        ]);

    // Cetak tracer pasien (standalone print page)
    Route::get('patients/{patient}/tracer', [PatientController::class, 'tracer'])
        ->name('patients.tracer');

    // ── Pendaftaran Rawat Jalan ───────────────────────────────────────────────
    Route::get('registrations/riwayat-saya', [RegistrationController::class, 'riwayat'])
        ->name('registrations.riwayat')
        ->middleware('role:petugas');

    // Batalkan pendaftaran
    Route::patch('registrations/{registration}/batal', [RegistrationController::class, 'batal'])
        ->name('registrations.batal');
        
    // Update status pendaftaran
    Route::patch('registrations/{registration}/status', [RegistrationController::class, 'updateStatus'])
        ->name('registrations.status');
        
    // Cetak antrian
    Route::get('registrations/{registration}/cetak', [RegistrationController::class, 'cetak'])
        ->name('registrations.cetak');

    Route::resource('registrations', RegistrationController::class)
        ->names([
            'index'   => 'registrations.index',
            'create'  => 'registrations.create',
            'store'   => 'registrations.store',
            'show'    => 'registrations.show',
            'edit'    => 'registrations.edit',
            'update'  => 'registrations.update',
            'destroy' => 'registrations.destroy',
        ]);

    // ── Modul Antrian ─────────────────────────────────────────────────────────
    Route::get('/antrian',                                      [AntrianController::class, 'index'])->name('antrian.index');
    Route::patch('/antrian/{registration}/panggil',             [AntrianController::class, 'panggil'])->name('antrian.panggil');
    Route::patch('/antrian/{registration}/selesai',             [AntrianController::class, 'selesai'])->name('antrian.selesai');
    Route::patch('/antrian/{registration}/status',              [AntrianController::class, 'updateStatus'])->name('antrian.update-status');
    Route::get('/antrian/display/{department}',                 [AntrianController::class, 'display'])->name('antrian.display');

    // ── AJAX Endpoints ────────────────────────────────────────────────────────
    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::get('doctors',            [AjaxController::class, 'getDoctors'])->name('doctors');
        Route::get('schedules',          [AjaxController::class, 'getSchedules'])->name('schedules');
        Route::get('kuota',              [AjaxController::class, 'getKuota'])->name('kuota');
        Route::get('cari-pasien',        [AjaxController::class, 'cariPasien'])->name('cari-pasien');
        Route::get('nomor-antrian',      [AjaxController::class, 'getNomorAntrian'])->name('nomor-antrian');
        Route::get('antrian/{department}',[AjaxController::class, 'getAntrianPoli'])->name('antrian-poli');
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

Route::get('/tools/update-kode-poli', function () {
    $map = [
        'Poli Umum' => 'UM',
        'Poli Gigi' => 'GG',
        'Poli Anak' => 'AN',
        'Poli Kandungan' => 'KD',
        'Poli Jantung' => 'JN',
        'Poli Paru' => 'PR',
        'Poli THT' => 'TH',
        'Poli Urologi' => 'UR',
        'Poli Gizi' => 'GZ',
    ];
    $count = 0;
    foreach (\App\Models\Department::all() as $dept) {
        if (isset($map[$dept->nama_poli])) {
            $dept->update(['kode_poli' => $map[$dept->nama_poli]]);
            $count++;
        }
    }
    return "Berhasil update {$count} departemen.";
});
