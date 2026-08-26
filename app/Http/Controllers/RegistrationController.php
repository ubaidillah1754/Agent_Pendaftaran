<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Registration;
use App\Services\PatientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function __construct(
        protected PatientService $patientService
    ) {}

    public function index(Request $request)
    {
        $tanggal = $request->filled('tanggal') ? $request->tanggal : today()->toDateString();

        // Base query for registrations
        $baseQuery = Registration::with(['patient', 'department', 'doctor'])
            ->whereDate('tanggal_daftar', $tanggal);

        if ($request->filled('department_id')) {
            $baseQuery->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $baseQuery->where('status', $request->status);
        }

        // 1. Antrian Sedang Proses (menunggu, dipanggil)
        $antrianProses = (clone $baseQuery)
            ->whereIn('status', ['menunggu', 'dipanggil'])
            ->orderBy('urutan_antrian')
            ->paginate(5, ['*'], 'page_proses')->withQueryString();

        // 2. Antrian Selesai
        $antrianSelesai = (clone $baseQuery)
            ->where('status', 'selesai')
            ->orderBy('updated_at', 'desc')
            ->paginate(5, ['*'], 'page_selesai')->withQueryString();

        // 3. Data Pasien
        $patientQuery = Patient::query();
        if ($request->filled('search')) {
            $patientQuery->where('nama_pasien', 'like', '%' . $request->search . '%')
                         ->orWhere('no_rm', 'like', '%' . $request->search . '%');
        }
        $patients = $patientQuery->orderByDesc('created_at')->paginate(5, ['*'], 'page_pasien')->withQueryString();

        // Stats
        $departments = Department::active()->orderBy('nama_poli')->get();
        $totalPendaftaran = (clone $baseQuery)->count();
        $totalMenunggu = (clone $baseQuery)->where('status', 'menunggu')->count();
        $totalSelesai = (clone $baseQuery)->where('status', 'selesai')->count();

        return view('registrations.index', compact(
            'antrianProses', 'antrianSelesai', 'patients', 'departments',
            'totalPendaftaran', 'totalMenunggu', 'totalSelesai'
        ));
    }

    public function create(Request $request)
    {
        $departments = Department::active()->orderBy('nama_poli')->get();

        // Jika ada patient_id dari redirect (pasien lama)
        $patient = null;
        if ($request->filled('patient_id')) {
            $patient = Patient::find($request->patient_id);
        }

        return view('registrations.create', compact('departments', 'patient'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Pilih antara pasien lama atau data pasien baru
            'mode_pasien'        => ['required', 'in:lama,baru'],
            'patient_id'         => ['required_if:mode_pasien,lama', 'nullable', 'exists:patients,id'],

            // Data pasien baru (hanya jika mode_pasien=baru)
            'nik'                => ['required_if:mode_pasien,baru', 'nullable', 'digits:16'],
            'nama_pasien'        => ['required_if:mode_pasien,baru', 'nullable', 'string', 'max:100'],
            'jenis_kelamin'      => ['required_if:mode_pasien,baru', 'nullable', 'in:L,P'],
            'tanggal_lahir'      => ['required_if:mode_pasien,baru', 'nullable', 'date'],
            'alamat'             => ['required_if:mode_pasien,baru', 'nullable', 'string'],
            'jenis_pembayaran'   => ['required_if:mode_pasien,baru', 'nullable', 'in:umum,bpjs,asuransi'],

            // Data pendaftaran
            'department_id'      => ['required', 'exists:departments,id'],
            'doctor_id'          => ['required', 'exists:doctors,id'],
            'doctor_schedule_id' => ['required', 'exists:doctor_schedules,id'],
            'tanggal_daftar'     => ['required', 'date', 'after_or_equal:today'],
            'keluhan'            => ['nullable', 'string', 'max:1000'],
        ], [
            'mode_pasien.required'        => 'Mode pendaftaran wajib dipilih.',
            'patient_id.required_if'      => 'Pasien wajib dipilih untuk pendaftaran pasien lama.',
            'department_id.required'      => 'Poli wajib dipilih.',
            'doctor_id.required'          => 'Dokter wajib dipilih.',
            'doctor_schedule_id.required' => 'Jadwal praktik wajib dipilih.',
            'tanggal_daftar.required'     => 'Tanggal kunjungan wajib diisi.',
            'tanggal_daftar.after_or_equal' => 'Tanggal kunjungan tidak boleh tanggal yang sudah lewat.',
            'nik.digits'                  => 'NIK harus 16 digit angka.',
        ]);

        $earnedPoints = 0;

        $registration = DB::transaction(function () use ($validated, &$earnedPoints) {
            // ── Resolve Patient ───────────────────────────────────────────────
            if ($validated['mode_pasien'] === 'baru') {
                $patient = $this->patientService->createPatient([
                    'nik'              => $validated['nik'],
                    'nama_pasien'      => $validated['nama_pasien'],
                    'jenis_kelamin'    => $validated['jenis_kelamin'],
                    'tanggal_lahir'    => $validated['tanggal_lahir'],
                    'alamat'           => $validated['alamat'],
                    'jenis_pembayaran' => $validated['jenis_pembayaran'],
                    'golongan_darah'   => 'Tidak Diketahui',
                ], Auth::user());

                $earnedPoints = (int) config('points.earn_per_new_patient', 10);
            } else {
                $patient = Patient::findOrFail($validated['patient_id']);
            }

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

            // Cek pasien belum daftar ke poli yang sama hari yang sama
            $sudahDaftar = Registration::where('patient_id', $patient->id)
                ->where('department_id', $validated['department_id'])
                ->where('tanggal_daftar', $tanggal)
                ->whereNotIn('status', ['batal'])
                ->exists();

            if ($sudahDaftar) {
                abort(back()->withInput()->withErrors(['department_id' => 'Pasien sudah terdaftar di poli ini pada tanggal tersebut.']));
            }

            // ── Generate Nomor Antrian ────────────────────────────────────────
            $department = Department::findOrFail($validated['department_id']);
            $antrian    = $department->generateNomorAntrian($tanggal);

            // ── Generate Kode Booking Unik ────────────────────────────────────
            do {
                $kodeBooking = strtoupper(\Illuminate\Support\Str::random(6));
            } while (Registration::where('kode_booking', $kodeBooking)->exists());

            // ── Simpan Pendaftaran ────────────────────────────────────────────
            return Registration::create([
                'patient_id'         => $patient->id,
                'doctor_schedule_id' => $schedule->id,
                'department_id'      => $validated['department_id'],
                'doctor_id'          => $validated['doctor_id'],
                'tanggal_daftar'     => now()->toDateString(),
                'tanggal_kunjungan'  => $tanggal,
                'nomor_antrian'      => $antrian['nomor_antrian'],
                'urutan_antrian'     => $antrian['urutan'],
                'kode_booking'       => $kodeBooking,
                'keluhan'            => $validated['keluhan'] ?? null,
                'status'             => 'menunggu',
                'created_by'         => Auth::id(),
            ]);
        });

        $message = "Pendaftaran berhasil! Nomor antrian: {$registration->nomor_antrian}.";
        
        if ($earnedPoints > 0) {
            $message .= " Anda mendapatkan +{$earnedPoints} poin untuk pendaftaran pasien baru.";
        }

        return redirect()->route('registrations.show', $registration)
            ->with('success', $message);
    }

    public function show(Registration $registration)
    {
        $registration->load(['patient', 'department', 'doctor', 'doctorSchedule', 'createdBy']);
        return view('registrations.show', compact('registration'));
    }

    public function edit(Registration $registration)
    {
        // Hanya pendaftaran yang statusnya 'menunggu' yang bisa diedit
        if ($registration->status !== 'menunggu') {
            return back()->with('error', 'Pendaftaran dengan status ' . $registration->status_label . ' tidak dapat diedit.');
        }

        $departments = Department::active()->orderBy('nama_poli')->get();
        return view('registrations.edit', compact('registration', 'departments'));
    }

    public function update(Request $request, Registration $registration)
    {
        if ($registration->status !== 'menunggu') {
            return back()->with('error', 'Pendaftaran tidak dapat diubah.');
        }

        $validated = $request->validate([
            'keluhan' => ['nullable', 'string', 'max:1000'],
        ]);

        $registration->update($validated);

        return redirect()->route('registrations.show', $registration)
            ->with('success', 'Data pendaftaran berhasil diperbarui.');
    }

    public function destroy(Registration $registration)
    {
        if (!in_array($registration->status, ['batal', 'selesai'])) {
            return back()->with('error', 'Hanya pendaftaran berstatus selesai atau batal yang dapat dihapus dari sistem.');
        }

        $registration->delete();

        return redirect()->route('registrations.index')
            ->with('success', 'Data pendaftaran berhasil dihapus.');
    }

    /** Batalkan pendaftaran */
    public function batal(Registration $registration)
    {
        if (!in_array($registration->status, ['menunggu'])) {
            return back()->with('error', 'Hanya pendaftaran berstatus menunggu yang dapat dibatalkan.');
        }

        $registration->update(['status' => 'batal']);

        return back()->with('success', "Pendaftaran {$registration->nomor_antrian} berhasil dibatalkan.");
    }

    /** Update status pendaftaran dari halaman detail */
    public function updateStatus(Request $request, Registration $registration)
    {
        $request->validate([
            'status' => ['required', 'in:menunggu,dipanggil,selesai,batal'],
        ]);

        $newStatus = $request->status;
        $currentStatus = $registration->status;

        // Admin override untuk status selesai
        if ($currentStatus === 'selesai' && !Auth::user()->isAdmin()) {
            return back()->with('error', 'Hanya admin yang dapat mengubah pendaftaran yang sudah selesai.');
        }

        $registration->update(['status' => $newStatus]);

        $statusLabel = $registration->status_label;
        return back()->with('success', "Status pendaftaran berhasil diubah menjadi {$statusLabel}.");
    }

    /** Cetak tiket antrian */
    public function cetak(Registration $registration)
    {
        $registration->load(['patient', 'department', 'doctor']);
        return view('registrations.cetak', compact('registration'));
    }

    /** Riwayat pendaftaran milik petugas yang login */
    public function riwayat(Request $request)
    {
        $user = Auth::user();

        $query = Registration::with(['patient', 'department', 'doctor'])
            ->where('created_by', $user->id)
            ->orderByDesc('tanggal_daftar')
            ->orderByDesc('created_at');

        // Filter bulan (format: YYYY-MM)
        $bulan = $request->input('bulan', today()->format('Y-m'));
        if (preg_match('/^\d{4}-\d{2}$/', $bulan)) {
            [$year, $month] = explode('-', $bulan);
            $query->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);
        }

        // Filter poli
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
