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
            $table->unsignedInteger('cash_amount')->nullable()->after('total_points');
            $table->string('bank_name')->nullable()->after('cash_amount');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('point_redemptions', function (Blueprint $table) {
            $table->dropColumn(['cash_amount', 'bank_name', 'bank_account_number', 'bank_account_name']);
        });
    }
};
