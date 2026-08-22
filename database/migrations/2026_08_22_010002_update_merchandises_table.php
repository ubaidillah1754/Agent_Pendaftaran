<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('merchandises', function (Blueprint $table) {
            if (!Schema::hasColumn('merchandises', 'points_required')) {
                $table->unsignedInteger('points_required')->default(0)->after('name');
            }
            if (!Schema::hasColumn('merchandises', 'stock')) {
                $table->integer('stock')->default(50)->after('points_required');
            }
            if (!Schema::hasColumn('merchandises', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('stock')->index();
            }
            if (!Schema::hasColumn('merchandises', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });

        // Copy points value if column points exists
        if (Schema::hasColumn('merchandises', 'points')) {
            DB::statement('UPDATE merchandises SET points_required = points WHERE points_required = 0');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchandises', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('merchandises', 'points_required')) {
                $columnsToDrop[] = 'points_required';
            }
            if (Schema::hasColumn('merchandises', 'stock')) {
                $columnsToDrop[] = 'stock';
            }
            if (Schema::hasColumn('merchandises', 'is_active')) {
                $columnsToDrop[] = 'is_active';
            }
            if (Schema::hasColumn('merchandises', 'deleted_at')) {
                $columnsToDrop[] = 'deleted_at';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
