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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isPetugas()) {
            return $this->create($request);
        }

        $tanggal = $request->filled('tanggal') ? $request->tanggal : today()->toDateString();

        // Base query: pendaftaran HARI INI yang sudah ambil antrean (punya nomor_antrian)
        $baseQuery = Registration::with(['patient', 'department', 'doctor'])
            ->whereDate('tanggal_kunjungan', $tanggal)
            ->whereNotNull('nomor_antrian');

        if ($request->filled('department_id')) {
            $baseQuery->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $baseQuery->where('status', $request->status);
        }

        // 1. Antrean Aktif (menunggu / diperiksa) — sudah ambil nomor antrean
        $pendaftaranProses = (clone $baseQuery)
            ->whereIn('status', ['menunggu', 'diperiksa'])
            ->orderBy('nomor_antrian')
            ->paginate(10, ['*'], 'page_proses')->withQueryString();

        // 2. Antrean Selesai hari ini — sudah selesai dilayani
        $pendaftaranSelesai = (clone $baseQuery)
            ->where('status', 'selesai')
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'page_selesai')->withQueryString();

        // 3. Seluruh Data Pendaftaran (semua record dari tabel registrations)
        $allRegistrationsQuery = Registration::with(['patient', 'department', 'doctor']);
        if ($request->filled('search')) {
            $search = $request->search;
            $allRegistrationsQuery->where(function($q) use ($search) {
                $q->whereHas('patient', function($q2) use ($search) {
                    $q2->where('nama_pasien', 'like', '%' . $search . '%')
                       ->orWhere('no_rm', 'like', '%' . $search . '%');
                })->orWhere('kode_booking', 'like', '%' . $search . '%');
            });
        }
        $allRegistrations = $allRegistrationsQuery->orderByDesc('created_at')->paginate(10, ['*'], 'page_all')->withQueryString();

        // 4. Panel Antrean (Pasien Aktif & Pasien Berikutnya)
        $pasienAktif = null;
        $pasienBerikutnya = null;

        if ($request->filled('department_id')) {
            // Cari pasien yang sedang diproses di poli ini pada hari ini
            $pasienAktif = Registration::with(['patient', 'department'])
                ->whereDate('tanggal_kunjungan', $tanggal)
                ->where('department_id', $request->department_id)
                ->where('status', 'diperiksa')
                ->whereNotNull('nomor_antrian')
                ->first();

            // Cari pasien menunggu berikutnya di poli ini pada hari ini
            $pasienBerikutnya = Registration::with(['patient', 'department'])
                ->whereDate('tanggal_kunjungan', $tanggal)
                ->where('department_id', $request->department_id)
                ->where('status', 'menunggu')
                ->whereNotNull('nomor_antrian')
                // Urutkan berdasarkan urutan nomor antrean numerik
                ->orderByRaw("CAST(SUBSTRING(nomor_antrian, 5) AS UNSIGNED) ASC")
                ->first();
        }

        // Stats — hitung dari antrean hari ini
        $departments    = Department::active()->orderBy('nama_poli')->get();
        $totalAntrean   = (clone $baseQuery)->count();
        $totalMenunggu  = (clone $baseQuery)->where('status', 'menunggu')->count();
        $totalSelesai   = (clone $baseQuery)->where('status', 'selesai')->count();

        return view('registrations.index', compact(
            'pendaftaranProses', 'pendaftaranSelesai', 'allRegistrations', 'departments',
            'totalAntrean', 'totalMenunggu', 'totalSelesai',
            'pasienAktif', 'pasienBerikutnya'
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
                /** @var \App\Models\User $user */
                $user = Auth::user();
                $patient = $this->patientService->createPatient([
                    'nik'              => $validated['nik'],
                    'nama_pasien'      => $validated['nama_pasien'],
                    'jenis_kelamin'    => $validated['jenis_kelamin'],
                    'tanggal_lahir'    => $validated['tanggal_lahir'],
                    'alamat'           => $validated['alamat'],
                    'jenis_pembayaran' => $validated['jenis_pembayaran'],
                    'golongan_darah'   => 'Tidak Diketahui',
                ], $user);

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

            // ── Generate Kode Booking Unik BK-XXXX ────────────────────────────
            $kodeBooking = Registration::generateKodeBooking();

            // ── Simpan Pendaftaran ────────────────────────────────────────────
            return Registration::create([
                'patient_id'         => $patient->id,
                'doctor_schedule_id' => $schedule->id,
                'department_id'      => $validated['department_id'],
                'doctor_id'          => $validated['doctor_id'],
                'tanggal_daftar'     => now()->toDateString(),
                'tanggal_kunjungan'  => $tanggal,
                'kode_booking'       => $kodeBooking,
                'status'             => 'menunggu',
                'created_by'         => Auth::id(),
            ]);
        });

        $message = "Pendaftaran berhasil! Kode booking: {$registration->kode_booking}.";
        
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

        // Jika tidak ada data lain yang bisa diubah, kita biarkan kosong atau update field lain jika ada
        // (contoh: di sini validasi keluhan dihapus, maka tidak ada yang diupdate dari form ini, 
        // tapi kita biarkan blok validasi kosong atau kembalikan response sukses)
        
        // $registration->update($validated);

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

        return back()->with('success', "Pendaftaran {$registration->kode_booking} berhasil dibatalkan.");
    }

    /** Update status pendaftaran dari halaman detail */
    public function updateStatus(Request $request, Registration $registration)
    {
        $request->validate([
            'status' => ['required', 'in:menunggu,diperiksa,selesai,batal'],
        ]);

        $newStatus = $request->status;
        $currentStatus = $registration->status;

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Admin override untuk status selesai
        if ($currentStatus === 'selesai' && !$user->isAdmin()) {
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
        /** @var \App\Models\User $user */
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

    /** 
     * Panggil Pasien Berikutnya secara atomik.
     * Mengubah pasien 'diperiksa' menjadi 'selesai' dan
     * pasien 'menunggu' urutan berikutnya menjadi 'diperiksa'.
     */
    public function panggilBerikutnya(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'tanggal'       => 'required|date',
        ]);

        $departmentId = $request->department_id;
        $tanggal      = $request->tanggal;

        try {
            DB::transaction(function () use ($departmentId, $tanggal) {
                // 1. Ambil pasien yang SEDANG DIPROSES dan kunci (lock for update)
                $pasienAktif = Registration::whereDate('tanggal_kunjungan', $tanggal)
                    ->where('department_id', $departmentId)
                    ->where('status', 'diperiksa')
                    ->whereNotNull('nomor_antrian')
                    ->lockForUpdate()
                    ->first();

                // 2. Jika ada, selesaikan
                if ($pasienAktif) {
                    $pasienAktif->update(['status' => 'selesai']);
                }

                // 3. Cari pasien MENUNGGU berikutnya
                $pasienBerikutnya = Registration::whereDate('tanggal_kunjungan', $tanggal)
                    ->where('department_id', $departmentId)
                    ->where('status', 'menunggu')
                    ->whereNotNull('nomor_antrian')
                    ->orderByRaw("CAST(SUBSTRING(nomor_antrian, 5) AS UNSIGNED) ASC")
                    ->lockForUpdate()
                    ->first();

                // 4. Jika ada, ubah jadi DIPROSES
                if ($pasienBerikutnya) {
                    $pasienBerikutnya->update(['status' => 'diperiksa']);
                }
            });

            return back()->with('success', 'Berhasil memanggil pasien berikutnya.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memanggil pasien berikutnya. Silakan coba lagi.');
        }
    }
}
