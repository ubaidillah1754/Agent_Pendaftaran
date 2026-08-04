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
use Illuminate\Support\Facades\Route;

// ── Redirect root ke dashboard atau login ────────────────────────────────────
Route::get('/', fn() => redirect()->route('dashboard'));

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

    // ── Pendaftaran Rawat Jalan ───────────────────────────────────────────────
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

    // Batalkan pendaftaran
    Route::patch('registrations/{registration}/batal', [RegistrationController::class, 'batal'])
        ->name('registrations.batal');

    // ── Modul Antrian ─────────────────────────────────────────────────────────
    Route::get('/antrian',                                      [AntrianController::class, 'index'])->name('antrian.index');
    Route::patch('/antrian/{registration}/panggil',             [AntrianController::class, 'panggil'])->name('antrian.panggil');
    Route::patch('/antrian/{registration}/selesai',             [AntrianController::class, 'selesai'])->name('antrian.selesai');
    Route::patch('/antrian/{registration}/status',              [AntrianController::class, 'updateStatus'])->name('antrian.update-status');
    Route::get('/antrian/display/{department}',                 [AntrianController::class, 'display'])->name('antrian.display');

    // ── AJAX Endpoints ────────────────────────────────────────────────────────
    Route::prefix('ajax')->name('ajax.')->group(function () {
        // Cascading dropdown: Poli → Dokter
        Route::get('doctors',            [AjaxController::class, 'getDoctors'])->name('doctors');
        // Cascading dropdown: Dokter → Jadwal (hari + jam)
        Route::get('schedules',          [AjaxController::class, 'getSchedules'])->name('schedules');
        // Cek sisa kuota & nomor antrian berikutnya
        Route::get('kuota',              [AjaxController::class, 'getKuota'])->name('kuota');
        // Cari pasien by NIK / No. RM (live search)
        Route::get('cari-pasien',        [AjaxController::class, 'cariPasien'])->name('cari-pasien');
        // Ambil nomor antrian berikutnya (preview sebelum submit)
        Route::get('nomor-antrian',      [AjaxController::class, 'getNomorAntrian'])->name('nomor-antrian');
        // Data antrian real-time per poli (untuk display antrian)
        Route::get('antrian/{department}',[AjaxController::class, 'getAntrianPoli'])->name('antrian-poli');
    });
});
