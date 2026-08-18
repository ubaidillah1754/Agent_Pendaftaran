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
    Schema::create('petugas_points', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // petugas
        $table->foreignId('registration_id')->constrained('registrations')->onDelete('cascade');
        $table->foreignId('department_id')->constrained('departments'); // poli terkait
        $table->unsignedInteger('points')->default(1);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('petugas_points');
}
};
