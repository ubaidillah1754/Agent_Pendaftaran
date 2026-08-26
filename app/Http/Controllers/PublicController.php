<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;

class PublicController extends Controller
{
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
