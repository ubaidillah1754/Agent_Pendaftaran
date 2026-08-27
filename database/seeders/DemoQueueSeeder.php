<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoQueueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');
        $tanggalHariIni = \Carbon\Carbon::now()->toDateString();
        $hariIni = \App\Models\DoctorSchedule::hariDariTanggal($tanggalHariIni);
        $user = \App\Models\User::where('role', 'petugas')->first() ?? \App\Models\User::first();

        // Cari departemen yang punya dokter dengan jadwal hari ini
        $schedule = \App\Models\DoctorSchedule::where('hari', $hariIni)
            ->where('is_active', true)
            ->whereHas('doctor', function ($q) {
                $q->where('is_active', true);
            })
            ->first();

        if (!$schedule) {
            echo "Tidak ada jadwal dokter untuk hari ini ({$hariIni}). Silakan buat jadwal dokter di admin panel terlebih dahulu.\n";
            return;
        }

        $doctor = $schedule->doctor;
        $department = $doctor->department;

        $namas = ['Andi', 'Budi', 'Citra', 'Deni', 'Eka'];

        foreach ($namas as $nama) {
            // 1. Buat Pasien Dummy
            $patient = \App\Models\Patient::create([
                'no_rm' => \App\Models\Patient::generateNoRM(),
                'nik' => $faker->nik(),
                'nama_pasien' => $nama,
                'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                'tanggal_lahir' => $faker->date('Y-m-d', '-20 years'),
                'alamat' => $faker->address,
                'jenis_pembayaran' => 'umum',
                'golongan_darah' => 'Tidak Diketahui',
                'created_by' => $user->id,
            ]);

            // 2. Buat Registrasi Dummy
            $kodeBooking = \App\Models\Registration::generateKodeBooking();
            
            \Illuminate\Support\Facades\DB::transaction(function () use ($patient, $schedule, $department, $doctor, $tanggalHariIni, $kodeBooking, $user) {
                $nomorAntrian = \App\Models\Registration::generateNomorAntrian($department->id, $tanggalHariIni);
                
                \App\Models\Registration::create([
                    'patient_id' => $patient->id,
                    'doctor_schedule_id' => $schedule->id,
                    'department_id' => $department->id,
                    'doctor_id' => $doctor->id,
                    'tanggal_daftar' => $tanggalHariIni,
                    'tanggal_kunjungan' => $tanggalHariIni,
                    'kode_booking' => $kodeBooking,
                    'status_booking' => 'used',
                    'nomor_antrian' => $nomorAntrian,
                    'status' => 'menunggu',
                    'created_by' => $user->id,
                ]);
            });
        }
        
        echo "Berhasil membuat 5 data antrean dummy untuk {$department->nama_poli} hari ini.\n";
    }
}
