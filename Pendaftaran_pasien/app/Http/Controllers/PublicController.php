<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DoctorSchedule;
use App\Models\Registration;
use Illuminate\Http\Request;

class PublicController extends Controller
{
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
        
        $schedules = $query->orderBy('hari')->orderBy('jam_mulai')->get();
        $departments = Department::active()->get();
        
        return view('public.jadwal', compact('schedules', 'departments'));
    }

    /**
     * Halaman cek pendaftaran (form)
     */
    public function cek()
    {
        return view('public.cek');
    }

    /**
     * Proses cek pendaftaran
     */
    public function prosesCek(Request $request)
    {
        $request->validate([
            'kode_booking' => 'required|string',
        ]);

        return redirect()->route('public.tracer', $request->kode_booking);
    }

    /**
     * Menampilkan halaman tracer publik berdasarkan kode booking.
     * Dapat diakses tanpa login — tidak ada middleware auth di route ini.
     *
     * URL: GET /tracer/{kode_booking}
     *
     * CATATAN: kode_booking adalah identifier publik yang aman.
     * Data sensitif (NIK, alamat lengkap) tidak ditampilkan di halaman ini.
     */
    public function tracer(string $kodeBooking)
    {
        // Pastikan kode booking tidak kosong/invalid
        $registration = Registration::with([
                'patient:id,nama_pasien',
                'department:id,nama_poli,kode_poli',
                'doctor:id,nama_dokter',
                'doctorSchedule:id,hari,jam_mulai,jam_selesai',
            ])
            ->where('kode_booking', $kodeBooking)
            ->first();

        if (!$registration) {
            abort(404, 'Kode booking tidak ditemukan. Pastikan Anda memindai QR Code dari tracer resmi RSI Sakinah.');
        }

        return view('public.tracer', compact('registration'));
    }
}
