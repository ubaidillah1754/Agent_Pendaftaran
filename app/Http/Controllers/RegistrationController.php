<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = Registration::with(['patient', 'department', 'doctor'])
            ->orderByDesc('tanggal_daftar')
            ->orderBy('urutan_antrian');

        // Filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_daftar', $request->tanggal);
        } else {
            // Default: tampilkan hari ini
            $query->whereDate('tanggal_daftar', today());
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

        return view('registrations.index', compact('registrations', 'departments'));
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
            'mode_pasien'       => ['required', 'in:lama,baru'],
            'patient_id'        => ['required_if:mode_pasien,lama', 'nullable', 'exists:patients,id'],

            // Data pasien baru (hanya jika mode_pasien=baru)
            'nik'               => ['required_if:mode_pasien,baru', 'nullable', 'digits:16'],
            'nama_pasien'       => ['required_if:mode_pasien,baru', 'nullable', 'string', 'max:100'],
            'jenis_kelamin'     => ['required_if:mode_pasien,baru', 'nullable', 'in:L,P'],
            'tanggal_lahir'     => ['required_if:mode_pasien,baru', 'nullable', 'date'],
            'alamat'            => ['required_if:mode_pasien,baru', 'nullable', 'string'],
            'jenis_pembayaran'  => ['required_if:mode_pasien,baru', 'nullable', 'in:umum,bpjs,asuransi'],

            // Data pendaftaran
            'department_id'      => ['required', 'exists:departments,id'],
            'doctor_id'          => ['required', 'exists:doctors,id'],
            'doctor_schedule_id' => ['required', 'exists:doctor_schedules,id'],
            'tanggal_daftar'     => ['required', 'date', 'after_or_equal:today'],
            'keluhan'            => ['nullable', 'string', 'max:1000'],
        ], [
            'mode_pasien.required'       => 'Mode pendaftaran wajib dipilih.',
            'patient_id.required_if'     => 'Pasien wajib dipilih untuk pendaftaran pasien lama.',
            'department_id.required'     => 'Poli wajib dipilih.',
            'doctor_id.required'         => 'Dokter wajib dipilih.',
            'doctor_schedule_id.required'=> 'Jadwal praktik wajib dipilih.',
            'tanggal_daftar.required'    => 'Tanggal kunjungan wajib diisi.',
            'tanggal_daftar.after_or_equal' => 'Tanggal kunjungan tidak boleh tanggal yang sudah lewat.',
            'nik.digits'                 => 'NIK harus 16 digit angka.',
        ]);

        return DB::transaction(function () use ($request, $validated) {

            // ── Resolve Patient ───────────────────────────────────────────────
            if ($validated['mode_pasien'] === 'baru') {
                // Cek NIK sudah terdaftar
                $existingPatient = Patient::where('nik', $validated['nik'])->first();
                if ($existingPatient) {
                    return back()->withInput()
                        ->withErrors(['nik' => 'NIK sudah terdaftar. Gunakan mode pasien lama dan cari berdasarkan NIK.']);
                }

                $patient = Patient::create([
                    'no_rm'            => Patient::generateNoRM(),
                    'nik'              => $validated['nik'],
                    'nama_pasien'      => $validated['nama_pasien'],
                    'jenis_kelamin'    => $validated['jenis_kelamin'],
                    'tanggal_lahir'    => $validated['tanggal_lahir'],
                    'alamat'           => $validated['alamat'],
                    'jenis_pembayaran' => $validated['jenis_pembayaran'],
                    'golongan_darah'   => 'Tidak Diketahui',
                ]);
            } else {
                $patient = Patient::findOrFail($validated['patient_id']);
            }

            // ── Validasi Jadwal ───────────────────────────────────────────────
            $schedule = DoctorSchedule::findOrFail($validated['doctor_schedule_id']);
            $tanggal  = $validated['tanggal_daftar'];
            $hariDaftar = DoctorSchedule::hariDariTanggal($tanggal);

            // Cek kesesuaian hari jadwal dengan tanggal yang dipilih
            if ($schedule->hari !== $hariDaftar) {
                return back()->withInput()
                    ->withErrors(['tanggal_daftar' => "Jadwal dokter ini hanya tersedia pada hari {$schedule->hari}, bukan hari {$hariDaftar}."]);
            }

            // Cek jadwal aktif (dokter tidak cuti)
            if (!$schedule->is_active) {
                return back()->withInput()
                    ->withErrors(['doctor_schedule_id' => 'Jadwal ini sedang tidak aktif (dokter cuti).']);
            }

            // Cek kuota tersedia
            if ($schedule->sisaKuota($tanggal) <= 0) {
                return back()->withInput()
                    ->withErrors(['doctor_schedule_id' => 'Kuota pendaftaran untuk jadwal ini sudah penuh.']);
            }

            // Cek pasien belum daftar ke poli yang sama hari yang sama
            $sudahDaftar = Registration::where('patient_id', $patient->id)
                ->where('department_id', $validated['department_id'])
                ->where('tanggal_daftar', $tanggal)
                ->whereNotIn('status', ['batal'])
                ->exists();

            if ($sudahDaftar) {
                return back()->withInput()
                    ->withErrors(['department_id' => 'Pasien sudah terdaftar di poli ini pada tanggal tersebut.']);
            }

            // ── Generate Nomor Antrian ────────────────────────────────────────
            $department = Department::find($validated['department_id']);
            $antrian    = $department->generateNomorAntrian($tanggal);

            // ── Simpan Pendaftaran ────────────────────────────────────────────
            $registration = Registration::create([
                'patient_id'         => $patient->id,
                'doctor_schedule_id' => $schedule->id,
                'department_id'      => $validated['department_id'],
                'doctor_id'          => $validated['doctor_id'],
                'tanggal_daftar'     => $tanggal,
                'nomor_antrian'      => $antrian['nomor_antrian'],
                'urutan_antrian'     => $antrian['urutan'],
                'keluhan'            => $validated['keluhan'] ?? null,
                'status'             => 'menunggu',
                'created_by'         => auth()->id(),
            ]);

            return redirect()->route('registrations.show', $registration)
                ->with('success', "Pendaftaran berhasil! Nomor antrian: {$antrian['nomor_antrian']}");
        });
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
}
