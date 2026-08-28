<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function __construct(
        protected PatientService $patientService
    ) {}

    public function index(Request $request)
    {
        $query = Patient::withCount('registrations')->orderBy('nama_pasien');

        // Filter pencarian
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($query) use ($q) {
                $query->where('nama_pasien', 'like', "%{$q}%")
                      ->orWhere('nik', 'like', "%{$q}%")
                      ->orWhere('no_rm', 'like', "%{$q}%");
            });
        }

        $patients = $query->paginate(20)->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        // Generate No. RM baru untuk ditampilkan di form preview
        $noRM = Patient::generateNoRM();
        return view('patients.create', compact('noRM'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik'               => ['required', 'digits:16', 'unique:patients,nik'],
            'nama_pasien'       => ['required', 'string', 'max:100'],
            'jenis_kelamin'     => ['required', 'in:L,P'],
            'tempat_lahir'      => ['nullable', 'string', 'max:50'],
            'tanggal_lahir'     => ['required', 'date', 'before:today'],
            'alamat'            => ['required', 'string', 'max:500'],
            'no_telepon'        => ['nullable', 'string', 'max:20'],
            'nama_wali'         => ['nullable', 'string', 'max:100'],
            'no_telepon_wali'   => ['nullable', 'string', 'max:20'],
            'golongan_darah'    => ['nullable', 'in:A,B,AB,O,Tidak Diketahui'],
            'jenis_pembayaran'  => ['required', 'in:umum,bpjs,asuransi'],
            'no_bpjs'           => ['nullable', 'string', 'max:20', 'required_if:jenis_pembayaran,bpjs'],
            'no_asuransi'       => ['nullable', 'string', 'max:30', 'required_if:jenis_pembayaran,asuransi'],
        ], [
            'nik.required'           => 'NIK wajib diisi.',
            'nik.digits'             => 'NIK harus 16 digit angka.',
            'nik.unique'             => 'NIK sudah terdaftar di sistem.',
            'nama_pasien.required'   => 'Nama pasien wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.before'   => 'Tanggal lahir tidak valid.',
            'alamat.required'        => 'Alamat wajib diisi.',
            'jenis_pembayaran.required' => 'Jenis pembayaran wajib dipilih.',
            'no_bpjs.required_if'    => 'Nomor BPJS wajib diisi jika pembayaran BPJS.',
            'no_asuransi.required_if'=> 'Nomor asuransi wajib diisi jika pembayaran asuransi.',
        ]);

        $validated['golongan_darah'] = $request->input('golongan_darah', 'Tidak Diketahui');

        // Buat pasien baru dan tambahkan poin ke akun petugas via PatientService
        $patient = $this->patientService->createPatient($validated, Auth::user());

        $earnedPoints = config('points.earn_per_new_patient', 10);

        return redirect()->route('patients.show', $patient)
            ->with('success', "Pasien {$patient->nama_pasien} berhasil didaftarkan (No. RM: {$patient->no_rm}). Anda mendapatkan +{$earnedPoints} poin!")
            ->with('show_print_tracer', $patient->id);
    }

    public function show(Patient $patient)
    {
        $patient->load(['registrations' => function ($q) {
            $q->with(['department', 'doctor'])->orderByDesc('tanggal_daftar');
        }, 'creator']);

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'nik'               => ['required', 'digits:16', 'unique:patients,nik,' . $patient->id],
            'nama_pasien'       => ['required', 'string', 'max:100'],
            'jenis_kelamin'     => ['required', 'in:L,P'],
            'tempat_lahir'      => ['nullable', 'string', 'max:50'],
            'tanggal_lahir'     => ['required', 'date', 'before:today'],
            'alamat'            => ['required', 'string', 'max:500'],
            'no_telepon'        => ['nullable', 'string', 'max:20'],
            'nama_wali'         => ['nullable', 'string', 'max:100'],
            'no_telepon_wali'   => ['nullable', 'string', 'max:20'],
            'golongan_darah'    => ['nullable', 'in:A,B,AB,O,Tidak Diketahui'],
            'jenis_pembayaran'  => ['required', 'in:umum,bpjs,asuransi'],
            'no_bpjs'           => ['nullable', 'string', 'max:20', 'required_if:jenis_pembayaran,bpjs'],
            'no_asuransi'       => ['nullable', 'string', 'max:30', 'required_if:jenis_pembayaran,asuransi'],
        ], [
            'nik.digits' => 'NIK harus 16 digit angka.',
            'nik.unique' => 'NIK sudah digunakan pasien lain.',
        ]);

        $patient->update($validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Data pasien ' . $patient->nama_pasien . ' berhasil diperbarui.');
    }

    public function destroy(Patient $patient)
    {
        // Cegah hapus jika masih ada antrian aktif
        $aktif = $patient->pendaftaranHariIni()->exists();

        if ($aktif) {
            return back()->with('error', 'Pasien tidak dapat dihapus karena masih memiliki antrian aktif hari ini.');
        }

        $nama = $patient->nama_pasien;
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Data pasien ' . $nama . ' berhasil dihapus.');
    }

    /** Cetak tracer pasien (halaman standalone print-friendly) */
    public function tracer(Patient $patient)
    {
        $patient->load(['registrations' => function ($q) {
            $q->with(['department', 'doctor'])->orderByDesc('tanggal_daftar')->limit(1);
        }]);

        return view('patients.tracer', compact('patient'));
    }
}
