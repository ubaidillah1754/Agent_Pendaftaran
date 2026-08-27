<?php

namespace App\Services;

use App\Models\DoctorSchedule;
use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * AntreanService
 *
 * Menangani seluruh logika bisnis pengambilan nomor antrean pasien:
 *   1. Mencari booking berdasarkan kode
 *   2. Validasi status booking (tidak expired/used/cancelled)
 *   3. Validasi tanggal kunjungan (belum lewat / hari ini)
 *   4. Validasi jadwal dokter pada hari tersebut
 *   5. Validasi window waktu (jam_mulai - 1jam s/d jam_selesai)
 *   6. Generate nomor antrean dalam transaction (aman concurrent)
 *   7. Update status booking menjadi 'used'
 */
class AntreanService
{
    /**
     * Validasi kode booking dan ambil nomor antrean.
     *
     * @param  string $kodeBooking
     * @return array  ['registration' => Registration, 'nomor_antrian' => 'A04']
     *
     * @throws \App\Services\AntreanException  dengan pesan spesifik
     */
    public function ambilAntrean(string $kodeBooking): array
    {
        // ── 1. Cari booking ───────────────────────────────────────────────────
        $registration = Registration::with(['doctorSchedule', 'department', 'patient', 'doctor'])
            ->where('kode_booking', strtoupper(trim($kodeBooking)))
            ->first();

        if (!$registration) {
            throw new AntreanException('Kode booking tidak ditemukan. Periksa kembali kode yang Anda masukkan.');
        }

        // ── 2. Cek status booking ─────────────────────────────────────────────
        match ($registration->status_booking) {
            'used' => throw new AntreanException(
                'Kode booking sudah digunakan untuk mengambil antrean. '
                . ($registration->nomor_antrian ? "Nomor antrean Anda: {$registration->nomor_antrian}." : '')
            ),
            'expired' => throw new AntreanException(
                'Kode booking sudah expired dan tidak dapat digunakan lagi.'
            ),
            'cancelled' => throw new AntreanException(
                'Kode booking sudah dibatalkan dan tidak dapat digunakan.'
            ),
            default => null, // 'pending' = lanjut
        };

        // ── 3. Validasi tanggal kunjungan ─────────────────────────────────────
        $tanggalKunjungan = $registration->tanggal_kunjungan; // Carbon date
        $today            = today();

        if ($tanggalKunjungan->lt($today)) {
            // Tanggal sudah lewat → expired
            $registration->update(['status_booking' => 'expired']);
            throw new AntreanException(
                'Kode booking sudah expired karena tanggal kunjungan telah lewat. '
                . '(Jadwal: ' . $tanggalKunjungan->translatedFormat('d F Y') . ')'
            );
        }

        if ($tanggalKunjungan->gt($today)) {
            // Tanggal belum tiba → belum bisa digunakan
            throw new AntreanException(
                'Kode booking belum dapat digunakan. Jadwal kunjungan Anda adalah '
                . $tanggalKunjungan->translatedFormat('d F Y') . '.'
            );
        }

        // Tanggal hari ini → lanjut validasi jadwal
        $tanggalStr = $tanggalKunjungan->toDateString(); // Y-m-d

        // ── 4. Validasi jadwal dokter ─────────────────────────────────────────
        $schedule = $registration->doctorSchedule;

        if (!$schedule) {
            throw new AntreanException('Data jadwal dokter tidak ditemukan. Hubungi petugas.');
        }

        // Pastikan jadwal masih aktif
        if (!$schedule->is_active) {
            throw new AntreanException(
                "Jadwal dokter {$registration->doctor->nama_dokter} sedang tidak aktif (cuti). "
                . 'Silakan hubungi petugas.'
            );
        }

        // Pastikan jadwal memang ada pada hari kunjungan
        $hariKunjungan = DoctorSchedule::hariDariTanggal($tanggalStr);
        if ($schedule->hari !== $hariKunjungan) {
            throw new AntreanException(
                "Dokter {$registration->doctor->nama_dokter} tidak memiliki jadwal pada hari {$hariKunjungan}."
            );
        }

        // ── 5. Validasi window waktu pengambilan antrean ──────────────────────
        $windowStatus   = $schedule->statusWindowAntrean($tanggalStr);
        $jamMulaiAmbil  = $schedule->jamMulaiAmbilAntrean($tanggalStr)->format('H:i');
        $jamSelesai     = $schedule->jamSelesaiAntrean($tanggalStr)->format('H:i');
        $jamMulaiPraktik = substr($schedule->jam_mulai, 0, 5);

        match ($windowStatus) {
            'too_early' => throw new AntreanException(
                "Pengambilan nomor antrean belum dibuka. "
                . "Silakan kembali mulai pukul {$jamMulaiAmbil} WIB. "
                . "(Jadwal praktik: {$jamMulaiPraktik}–{$jamSelesai} WIB)"
            ),
            'expired' => (function () use ($registration, $schedule, $tanggalStr, $jamMulaiPraktik, $jamSelesai) {
                // Expired karena jam selesai sudah lewat
                $registration->update(['status_booking' => 'expired']);
                throw new AntreanException(
                    "Kode booking sudah expired karena jadwal praktik dokter telah berakhir. "
                    . "(Jadwal: {$jamMulaiPraktik}–{$jamSelesai} WIB)"
                );
            })(),
            default => null, // 'ok' = lanjut
        };

        // ── 6. Generate nomor antrean dalam transaction ───────────────────────
        $nomorAntrian = DB::transaction(function () use ($registration, $tanggalStr) {
            // Lock baris ini agar tidak ada request lain yang generate nomor bersamaan
            $reg = Registration::lockForUpdate()->findOrFail($registration->id);

            // Double-check: pastikan belum used (race condition)
            if ($reg->status_booking === 'used') {
                throw new AntreanException(
                    'Kode booking sudah digunakan. '
                    . ($reg->nomor_antrian ? "Nomor antrean Anda: {$reg->nomor_antrian}." : '')
                );
            }

            // Generate nomor antrean unik
            $nomor = Registration::generateNomorAntrian($reg->department_id, $tanggalStr);

            // Simpan nomor antrean & update status booking → used
            $reg->update([
                'nomor_antrian' => $nomor,
                'status_booking' => 'used',
                'status'         => 'menunggu', // pastikan status antrean menunggu
            ]);

            return $nomor;
        });

        // Refresh data
        $registration->refresh();

        return [
            'registration'  => $registration,
            'nomor_antrian' => $nomorAntrian,
        ];
    }

    /**
     * Ambil informasi booking tanpa mengeksekusi pengambilan antrean.
     * Digunakan di halaman cek untuk preview status.
     *
     * @param  string $kodeBooking
     * @return Registration|null
     */
    public function cariBooking(string $kodeBooking): ?Registration
    {
        return Registration::with(['doctorSchedule', 'department', 'patient', 'doctor'])
            ->where('kode_booking', strtoupper(trim($kodeBooking)))
            ->first();
    }
}
