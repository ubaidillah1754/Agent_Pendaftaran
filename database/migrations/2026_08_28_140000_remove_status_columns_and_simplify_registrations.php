<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop index yang mengandung kolom status jika ada
        Schema::table('registrations', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                try {
                    $table->dropIndex('registrations_tanggal_daftar_department_id_status_index');
                } catch (\Exception $e) {
                    // Index tidak ada atau sudah di-drop
                }
            }
        });

        // 2. Drop kolom status & status_booking jika ada
        Schema::table('registrations', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('registrations', 'status')) {
                $columnsToDrop[] = 'status';
            }
            if (Schema::hasColumn('registrations', 'status_booking')) {
                $columnsToDrop[] = 'status_booking';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('registrations', 'status')) {
                $table->string('status')->default('menunggu');
            }
            if (!Schema::hasColumn('registrations', 'status_booking')) {
                $table->string('status_booking')->default('pending');
            }
        });
    }
};
