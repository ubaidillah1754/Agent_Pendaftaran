<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DoctorSchedule;
use App\Models\Registration;
use App\Services\AntreanException;
use App\Services\AntreanService;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function __construct(
        protected AntreanService $antreanService
    ) {}

    /**
     * Beranda publik
     */
    public function index()
    {
        $departments = Department::active()->get();
        return view('public.index', compact('departments'));
    }

    /**
     * Jadwal praktik dokter publik
     */
    public function jadwal(Request $request)
    {
        $query = DoctorSchedule::with(['doctor', 'department'])->active();

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        $schedules   = $query->orderBy('hari')->orderBy('jam_mulai')->get();
        $departments = Department::active()->get();

        return view('public.jadwal', compact('schedules', 'departments'));
    }

    /**
     * Halaman cek pendaftaran (GET — tampilkan form)
     */
    public function cek(Request $request)
    {
        $registration = null;
        $kodeBooking  = $request->query('kode');

        // Jika ada kode di query string (dari redirect tracer), preview booking-nya
        if ($kodeBooking) {
            $registration = $this->antreanService->cariBooking($kodeBooking);
        }

        return view('public.cek', compact('registration', 'kodeBooking'));
    }

    /**
     * Proses cek pendaftaran (POST — validasi kode, arahkan ke tracer atau error)
     */
    public function prosesCek(Request $request)
    {
        $request->validate([
            'kode_booking' => 'required|string|max:20',
        ], [
            'kode_booking.required' => 'Kode booking wajib diisi.',
        ]);

        $kode         = strtoupper(trim($request->kode_booking));
        $registration = $this->antreanService->cariBooking($kode);

        if (!$registration) {
            return back()
                ->withInput()
                ->with('error', 'Kode booking tidak ditemukan. Periksa kembali kode yang Anda masukkan.');
        }

        // Arahkan ke halaman tracer dengan kode booking
        return redirect()->route('public.tracer', $kode);
    }

    /**
     * Proses pengambilan nomor antrean (POST)
     * Dipanggil dari halaman tracer ketika pasien klik "Ambil Antrean"
     */
    public function ambilAntrean(Request $request)
    {
        $request->validate([
            'kode_booking' => 'required|string|max:20',
        ]);

        $kode = strtoupper(trim($request->kode_booking));

        try {
            $result = $this->antreanService->ambilAntrean($kode);
            $reg    = $result['registration'];
            $nomor  = $result['nomor_antrian'];

            return redirect()
                ->route('public.tracer', $kode)
                ->with('success', "Nomor antrean Anda: {$nomor}. Silakan menunggu di ruang tunggu {$reg->department->nama_poli}.");
        } catch (AntreanException $e) {
            return redirect()
                ->route('public.tracer', $kode)
                ->with('error', $e->getMessage());
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('public.tracer', $kode)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman tracer publik berdasarkan kode booking.
     * Dapat diakses tanpa login.
     *
     * URL: GET /tracer/{kode_booking}
     */
    public function tracer(string $kodeBooking)
    {
        $registration = Registration::with([
            'patient:id,nama_pasien',
            'department:id,nama_poli,kode_poli',
            'doctor:id,nama_dokter',
            'doctorSchedule:id,hari,jam_mulai,jam_selesai',
        ])
        ->where('kode_booking', strtoupper(trim($kodeBooking)))
        ->first();

        if (!$registration) {
            return redirect()
                ->route('public.cek')
                ->with('error', 'Kode booking tidak ditemukan.');
        }

        // Hitung info window waktu (untuk tampil di view)
        $schedule          = $registration->doctorSchedule;
        $tanggalKunjungan  = $registration->tanggal_kunjungan->toDateString();
        $today             = today()->toDateString();
        $windowStatus      = null;
        $jamMulaiAmbil     = null;
        $bisaAmbilAntrean  = false;

        if ($schedule && $tanggalKunjungan === $today && $registration->status_booking === 'pending') {
            $windowStatus  = $schedule->statusWindowAntrean($tanggalKunjungan);
            $jamMulaiAmbil = $schedule->jamMulaiAmbilAntrean($tanggalKunjungan)->format('H:i');
            $bisaAmbilAntrean = ($windowStatus === 'ok');
        }

        return view('public.tracer', compact(
            'registration',
            'windowStatus',
            'jamMulaiAmbil',
            'bisaAmbilAntrean'
        ));
    }
}
