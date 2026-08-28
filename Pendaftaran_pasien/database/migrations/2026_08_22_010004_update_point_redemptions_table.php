<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('point_redemptions', function (Blueprint $table) {
            if (!Schema::hasColumn('point_redemptions', 'reference_code')) {
                $table->string('reference_code', 64)->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('point_redemptions', 'merchandise_id')) {
                $table->foreignId('merchandise_id')->nullable()->after('user_id')->constrained('merchandises')->nullOnDelete();
            }
            if (!Schema::hasColumn('point_redemptions', 'merchandise_name')) {
                $table->string('merchandise_name')->nullable()->after('merchandise_id');
            }
            if (!Schema::hasColumn('point_redemptions', 'points_required')) {
                $table->unsignedInteger('points_required')->default(0)->after('merchandise_name');
            }
            if (!Schema::hasColumn('point_redemptions', 'quantity')) {
                $table->unsignedInteger('quantity')->default(1)->after('points_required');
            }
            if (!Schema::hasColumn('point_redemptions', 'total_points')) {
                $table->unsignedInteger('total_points')->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('point_redemptions', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('point_redemptions', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('point_redemptions', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('point_redemptions', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('completed_at');
            }
            if (!Schema::hasColumn('point_redemptions', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('rejected_at');
            }
            if (!Schema::hasColumn('point_redemptions', 'notes')) {
                $table->text('notes')->nullable()->after('cancelled_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('point_redemptions', function (Blueprint $table) {
            $columns = [
                'reference_code', 'merchandise_id', 'merchandise_name',
                'points_required', 'quantity', 'total_points',
                'approved_by', 'approved_at', 'completed_at',
                'rejected_at', 'cancelled_at', 'notes'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('point_redemptions', $column)) {
                    if (in_array($column, ['merchandise_id', 'approved_by'])) {
                        $table->dropForeign([$column]);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
