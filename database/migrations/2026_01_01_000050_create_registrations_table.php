<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel registrations (Pendaftaran Rawat Jalan).
     * Ini adalah tabel transaksi utama sistem.
     */
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_schedule_id')->constrained('doctor_schedules')->cascadeOnDelete();
            // Kolom redundan untuk kemudahan query (denormalisasi ringan)
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('doctor_id')->constrained('doctors');
            // Tanggal berobat (bisa berbeda dari tanggal pendaftaran)
            $table->date('tanggal_daftar');
            // Nomor antrian: format {KODE_POLI}{urutan 3 digit} → contoh PU001
            $table->string('nomor_antrian', 10);
            // Urutan angka antrian — reset setiap hari per poli
            $table->unsignedSmallInteger('urutan_antrian');
            $table->text('keluhan')->nullable(); // Keluhan/alasan kunjungan
            // Status alur: menunggu → dipanggil → selesai / batal
            $table->enum('status', ['menunggu', 'dipanggil', 'selesai', 'batal'])->default('menunggu');
            // Petugas yang mendaftarkan pasien
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            // Satu pasien tidak bisa daftar ke poli yang sama di hari yang sama
            $table->unique(['patient_id', 'department_id', 'tanggal_daftar'], 'unique_pendaftaran_harian');
            // Nomor antrian unik per poli per hari
            $table->unique(['department_id', 'tanggal_daftar', 'nomor_antrian'], 'unique_antrian_harian');

            // Index untuk query dashboard dan filter antrian
            $table->index(['tanggal_daftar', 'department_id', 'status']);
        });
    }

    /**
     * Rollback tabel registrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
