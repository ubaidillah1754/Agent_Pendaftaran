<?php
// app/Models/PetugasPoint.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetugasPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'registration_id',
        'department_id',
        'points',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}