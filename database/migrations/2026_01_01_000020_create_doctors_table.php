<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel doctors (Data Dokter).
     */
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            // user_id opsional — hanya jika dokter punya akun login
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            // department_id = poli utama dokter
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->string('nip', 30)->unique()->nullable(); // Nomor Induk Pegawai
            $table->string('nama_dokter', 100);
            $table->string('spesialisasi', 100)->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->string('foto')->nullable(); // path foto profil dokter
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Rollback tabel doctors.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
