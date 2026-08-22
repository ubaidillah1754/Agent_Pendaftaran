<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PatientService
{
    public function __construct(
        protected PointService $pointService,
        protected AuditService $auditService
    ) {}

    /**
     * Daftarkan pasien baru ke database dan berikan reward poin kepada petugas.
     */
    public function createPatient(array $data, User $creator): Patient
    {
        return DB::transaction(function () use ($data, $creator) {
            // Cek apakah NIK sudah terdaftar (dengan row lock)
            $existing = Patient::where('nik', $data['nik'])->lockForUpdate()->first();
            if ($existing) {
                throw new RuntimeException("Pasien dengan NIK {$data['nik']} sudah terdaftar sebelumnya (No. RM: {$existing->no_rm}).");
            }

            // Generate No. RM unik
            $noRM = Patient::generateNoRM();

            $patient = Patient::create([
                'no_rm'            => $noRM,
                'nik'              => $data['nik'],
                'nama_pasien'      => $data['nama_pasien'],
                'jenis_kelamin'    => $data['jenis_kelamin'],
                'tempat_lahir'     => $data['tempat_lahir'] ?? null,
                'tanggal_lahir'    => $data['tanggal_lahir'],
                'alamat'           => $data['alamat'],
                'no_telepon'       => $data['no_telepon'] ?? null,
                'nama_wali'        => $data['nama_wali'] ?? null,
                'no_telepon_wali'  => $data['no_telepon_wali'] ?? null,
                'golongan_darah'   => $data['golongan_darah'] ?? 'Tidak Diketahui',
                'jenis_pembayaran' => $data['jenis_pembayaran'] ?? 'umum',
                'no_bpjs'          => $data['no_bpjs'] ?? null,
                'no_asuransi'      => $data['no_asuransi'] ?? null,
                'created_by'       => $creator->id,
            ]);

            // Ambil konfigurasi jumlah poin per pasien baru
            $pointsToEarn = (int) config('points.earn_per_new_patient', 10);

            // Berikan poin ke akun petugas yang menginput
            $reference = "EARN-PATIENT-{$patient->id}";
            $description = "Poin pendaftaran pasien baru: {$patient->nama_pasien} (No. RM: {$patient->no_rm})";

            $this->pointService->earn(
                user: $creator,
                amount: $pointsToEarn,
                sourceType: Patient::class,
                sourceId: $patient->id,
                reference: $reference,
                description: $description,
                creator: $creator
            );

            $this->auditService->log(
                actor: $creator,
                action: 'patient_created',
                target: $patient,
                oldValues: null,
                newValues: ['no_rm' => $patient->no_rm, 'nama_pasien' => $patient->nama_pasien, 'points_earned' => $pointsToEarn],
                description: "Input pasien baru {$patient->nama_pasien} (No. RM: {$patient->no_rm}) oleh {$creator->name} (+{$pointsToEarn} poin)"
            );

            return $patient;
        });
    }
}
