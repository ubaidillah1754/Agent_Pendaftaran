<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel patients (Data Pasien).
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            // Nomor Rekam Medis — unik, auto-generate format: RM-YYYYMMDD-XXXX
            $table->string('no_rm', 20)->unique();
            // NIK 16 digit — unik per pasien
            $table->string('nik', 16)->unique();
            $table->string('nama_pasien', 100);
            $table->enum('jenis_kelamin', ['L', 'P']); // L = Laki-laki, P = Perempuan
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('no_telepon', 20)->nullable();
            // Data wali/penanggung jawab
            $table->string('nama_wali', 100)->nullable();
            $table->string('no_telepon_wali', 20)->nullable();
            // Informasi medis dasar
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O', 'Tidak Diketahui'])->default('Tidak Diketahui');
            // Jenis pembayaran: umum (bayar sendiri), bpjs, asuransi
            $table->enum('jenis_pembayaran', ['umum', 'bpjs', 'asuransi'])->default('umum');
            $table->string('no_bpjs', 20)->nullable(); // Nomor kartu BPJS jika ada
            $table->string('no_asuransi', 30)->nullable(); // Nomor asuransi jika ada
            $table->timestamps();

            // Index untuk pencarian cepat by NIK dan no_rm
            $table->index(['nik', 'no_rm']);
        });
    }

    /**
     * Rollback tabel patients.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
