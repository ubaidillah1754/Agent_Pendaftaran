<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel doctor_schedules (Jadwal Praktik Dokter).
     * Bersifat rekuren mingguan — satu row = satu slot jadwal per hari.
     */
    public function up(): void
    {
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            // Hari praktik dalam seminggu
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            // Kuota maksimal pasien per sesi jadwal ini
            $table->unsignedSmallInteger('kuota')->default(20);
            // is_active = false berarti dokter cuti / jadwal ditangguhkan
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Pastikan tidak ada duplikasi jadwal dokter di hari yang sama di poli yang sama
            $table->unique(['doctor_id', 'department_id', 'hari'], 'unique_jadwal_dokter');
        });
    }

    /**
     * Rollback tabel doctor_schedules.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
    }
};
