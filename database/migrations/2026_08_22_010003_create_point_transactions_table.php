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
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['earn', 'redeem', 'adjustment', 'reversal'])->index();
            $table->integer('amount'); // Positif untuk penambahan (+), Negatif untuk pengurangan (-)
            $table->unsignedInteger('balance_before');
            $table->unsignedInteger('balance_after');
            $table->string('source_type')->nullable(); // Polymorphic / custom source
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('reference', 64)->unique(); // Unik untuk idempotency & pelacakan
            $table->text('description');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // Admin / System
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index(['source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
