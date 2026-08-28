<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel departments (Poli/Departemen).
     */
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('kode_poli', 10)->unique(); // Kode unik poli, ex: PU, GG, AN
            $table->string('nama_poli', 100);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Rollback tabel departments.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
