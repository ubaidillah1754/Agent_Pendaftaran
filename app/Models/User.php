<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Helper Role ────────────────────────────────────────────────────────

    /** Cek apakah user adalah admin */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Cek apakah user adalah petugas pendaftaran */
    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    /** Cek apakah user adalah dokter */
    public function isDokter(): bool
    {
        return $this->role === 'dokter';
    }

    // ─── Relasi ─────────────────────────────────────────────────────────────

    /** User yang merupakan dokter (opsional) */
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    /** Pendaftaran yang dibuat oleh user ini */
    public function registrations()
    {
        return $this->hasMany(Registration::class, 'created_by');
    }
    public function petugasPoints()
    {
        return $this->hasMany(PetugasPoint::class);
    }

    public function pointRedemptions()
    {
        return $this->hasMany(PointRedemption::class);
    }

    public function totalPoints(): int
    {
        $earned = $this->petugasPoints()->sum('points');
        $redeemed = $this->pointRedemptions()->sum('points');
        return $earned - $redeemed;
    }
}
