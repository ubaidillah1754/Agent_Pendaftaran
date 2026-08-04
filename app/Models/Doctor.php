<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'nip',
        'nama_dokter',
        'spesialisasi',
        'no_telepon',
        'foto',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relasi ─────────────────────────────────────────────────────────────

    /** Akun user yang dimiliki dokter (opsional) */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Poli utama dokter */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /** Jadwal praktik dokter ini */
    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    /** Jadwal aktif dokter ini */
    public function activeSchedules()
    {
        return $this->hasMany(DoctorSchedule::class)->where('is_active', true);
    }

    /** Pendaftaran yang ditangani dokter ini */
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    // ─── Scope ──────────────────────────────────────────────────────────────

    /** Hanya dokter yang aktif */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Filter dokter berdasarkan poli */
    public function scopeByDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    // ─── Helper ─────────────────────────────────────────────────────────────

    /** Ambil URL foto atau placeholder default */
    public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : asset('images/default-doctor.png');
    }
}
