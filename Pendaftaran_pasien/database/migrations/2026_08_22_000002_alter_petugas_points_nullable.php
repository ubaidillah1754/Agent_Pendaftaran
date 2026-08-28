<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('petugas_points', function (Blueprint $table) {
            // Jadikan nullable agar bisa diisi tanpa registration dan department
            $table->foreignId('registration_id')->nullable()->change();
            $table->foreignId('department_id')->nullable()->change();

            // Relasi ke pengajuan poin (opsional)
            $table->foreignId('point_request_id')
                ->nullable()
                ->after('department_id')
                ->constrained('point_requests')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('petugas_points', function (Blueprint $table) {
            $table->dropForeign(['point_request_id']);
            $table->dropColumn('point_request_id');
            $table->foreignId('registration_id')->nullable(false)->change();
            $table->foreignId('department_id')->nullable(false)->change();
        });
    }
};
