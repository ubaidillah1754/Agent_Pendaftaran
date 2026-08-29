<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Registration;
use App\Services\PatientService;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function __construct(
        protected PatientService $patientService,
        protected PointService   $pointService
    ) {}

    public function index(Request $request)
    {
        $tanggal = $request->filled('tanggal') ? $request->tanggal : today()->toDateString();

        // Filter berdasarkan tanggal_daftar (tanggal pasien didaftarkan oleh petugas)
        $query = Registration::with(['patient', 'department', 'doctor'])
            ->whereDate('tanggal_daftar', $tanggal);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($q2) use ($search) {
                    $q2->where('nama_pasien', 'like', '%' . $search . '%')
                       ->orWhere('no_rm', 'like', '%' . $search . '%');
                })->orWhere('kode_booking', 'like', '%' . $search . '%')
                  ->orWhere('nomor_antrian', 'like', '%' . $search . '%');
            });
        }

        $registrations    = $query->orderBy('nomor_antrian')->paginate(15)->withQueryString();
        $departments      = Department::active()->orderBy('nama_poli')->get();
        $totalPendaftaran = Registration::whereDate('tanggal_daftar', $tanggal)->count();

        return view('registrations.index', compact(
            'registrations', 'departments', 'totalPendaftaran', 'tanggal'
        ));
    }

    public function create(Request $request)
    {
        $departments = Department::active()->orderBy('nama_poli')->get();

        // Jika ada patient_id dari redirect
        $patient = null;
        if ($request->filled('patient_id')) {
            $patient = Patient::find($request->patient_id);
        }

        return view('registrations.create', compact('departments', 'patient'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'         => ['required', 'exists:patients,id'],
            'department_id'      => ['required', 'exists:departments,id'],
            'doctor_id'          => ['required', 'exists:doctors,id'],
            'doctor_schedule_id' => ['required', 'exists:doctor_schedules,id'],
            'tanggal_daftar'     => ['required', 'date', 'after_or_equal:today'],
        ], [
            'patient_id.required'         => 'Pasien wajib dipilih.',
            'department_id.required'      => 'Poli wajib dipilih.',
            'doctor_id.required'          => 'Dokter wajib dipilih.',
            'doctor_schedule_id.required' => 'Jadwal praktik wajib dipilih.',
            'tanggal_daftar.required'     => 'Tanggal kunjungan wajib diisi.',
            'tanggal_daftar.after_or_equal' => 'Tanggal kunjungan tidak boleh tanggal yang sudah lewat.',
        ]);

        $patient = Patient::findOrFail($validated['patient_id']);

        $registration = DB::transaction(function () use ($validated, $patient) {
            // ── Validasi Jadwal ───────────────────────────────────────────────
            $schedule   = DoctorSchedule::findOrFail($validated['doctor_schedule_id']);
            $tanggal    = $validated['tanggal_daftar'];
            $hariDaftar = DoctorSchedule::hariDariTanggal($tanggal);

            if ($schedule->hari !== $hariDaftar) {
                abort(back()->withInput()->withErrors(['tanggal_daftar' => "Jadwal dokter ini hanya tersedia pada hari {$schedule->hari}, bukan hari {$hariDaftar}."]));
            }

            if (!$schedule->is_active) {
                abort(back()->withInput()->withErrors(['doctor_schedule_id' => 'Jadwal ini sedang tidak aktif (dokter cuti).']));
            }

            if ($schedule->sisaKuota($tanggal) <= 0) {
                abort(back()->withInput()->withErrors(['doctor_schedule_id' => 'Kuota pendaftaran untuk jadwal ini sudah penuh.']));
            }

            // Cek pasien belum daftar ke poli yang sama pada hari yang sama
            $sudahDaftar = Registration::where('patient_id', $patient->id)
                ->where('department_id', $validated['department_id'])
                ->where('tanggal_kunjungan', $tanggal)
                ->exists();

            if ($sudahDaftar) {
                abort(back()->withInput()->withErrors(['department_id' => 'Pasien sudah terdaftar di poli ini pada tanggal tersebut.']));
            }

            // ── Generate Nomor Antrean & Kode Booking Unik ──────────────────
            $nomorAntrian = Registration::generateNomorAntrian($validated['department_id'], $tanggal);
            $kodeBooking  = Registration::generateKodeBooking();

            // ── Simpan Pendaftaran ────────────────────────────────────────────
            return Registration::create([
                'patient_id'         => $patient->id,
                'doctor_schedule_id' => $schedule->id,
                'department_id'      => $validated['department_id'],
                'doctor_id'          => $validated['doctor_id'],
                'tanggal_daftar'     => now()->toDateString(),
                'tanggal_kunjungan'  => $tanggal,
                'nomor_antrian'      => $nomorAntrian,
                'kode_booking'       => $kodeBooking,
                'created_by'         => Auth::id(),
            ]);
        });

        // ── Berikan poin kepada petugas yang mendaftarkan ────────────────
        /** @var \App\Models\User $petugas */
        $petugas = Auth::user();
        if ($petugas->isPetugas()) {
            $registration->load(['patient', 'department']);
            $pointsToEarn = (int) config('points.earn_per_registration', 5);
            $reference    = "EARN-REG-{$registration->id}";
            $description  = "Poin pendaftaran rawat jalan: {$registration->patient->nama_pasien} ke {$registration->department->nama_poli} (Antrian: {$registration->nomor_antrian})";

            $this->pointService->earn(
                user:        $petugas,
                amount:      $pointsToEarn,
                sourceType:  Registration::class,
                sourceId:    $registration->id,
                reference:   $reference,
                description: $description,
                creator:     $petugas
            );
        }

        session()->flash('success', "Pendaftaran berhasil! Nomor Antrean: {$registration->nomor_antrian} | Kode Booking: {$registration->kode_booking}");

        // Redirect ke halaman konfirmasi (show) terlebih dahulu, bukan langsung cetak
        return redirect()->route('registrations.show', $registration);
    }

    public function show(Registration $registration)
    {
        $registration->load(['patient', 'department', 'doctor', 'doctorSchedule', 'createdBy']);
        return view('registrations.show', compact('registration'));
    }

    public function destroy(Registration $registration)
    {
        $registration->delete();

        return redirect()->route('registrations.index')
            ->with('success', 'Data pendaftaran berhasil dihapus.');
    }

    /** Cetak tiket antrian / tracer */
    public function cetak(Registration $registration)
    {
        $registration->load(['patient', 'department', 'doctor', 'doctorSchedule']);
        return view('registrations.cetak', compact('registration'));
    }

    /** Riwayat pendaftaran milik petugas yang login */
    public function riwayat(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Registration::with(['patient', 'department', 'doctor', 'pointTransaction'])
            ->where('created_by', $user->id)
            ->orderByDesc('tanggal_daftar')
            ->orderByDesc('created_at');

        // Filter bulan berdasarkan tanggal_daftar (tanggal pasien didaftarkan)
        $bulan = $request->input('bulan', today()->format('Y-m'));
        if ($bulan && preg_match('/^\d{4}-\d{2}$/', $bulan)) {
            [$year, $month] = explode('-', $bulan);
            $query->whereYear('tanggal_daftar', $year)
                  ->whereMonth('tanggal_daftar', $month);
        }

        // Filter poli
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $registrations = $query->paginate(20)->withQueryString();
        $departments   = Department::active()->orderBy('nama_poli')->get();

        // Statistik petugas sendiri
        $totalPendaftaran = Registration::where('created_by', $user->id)->count();
        $totalPoin        = $user->totalPoints();

        return view('registrations.riwayat', compact(
            'registrations', 'departments', 'totalPendaftaran', 'totalPoin'
        ));
    }
}
