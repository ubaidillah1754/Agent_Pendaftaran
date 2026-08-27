<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_poli',
        'nama_poli',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relasi ─────────────────────────────────────────────────────────────

    /** Dokter yang tergabung di poli ini */
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    /** Jadwal praktik di poli ini */
    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    /** Pendaftaran yang masuk ke poli ini */
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    // ─── Scope ──────────────────────────────────────────────────────────────

    /** Hanya poli yang aktif */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Helper ─────────────────────────────────────────────────────────────

}
