<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'point_balance',
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
            'point_balance'     => 'integer',
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
    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    /** Pasien yang pertama kali diinput oleh user ini */
    public function createdPatients(): HasMany
    {
        return $this->hasMany(Patient::class, 'created_by');
    }

    /** Pendaftaran yang dibuat oleh user ini */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'created_by');
    }

    /** Buku besar transaksi poin */
    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class, 'user_id');
    }

    /** Transaksi penukaran reward */
    public function pointRedemptions(): HasMany
    {
        return $this->hasMany(PointRedemption::class, 'user_id');
    }

    /** Data poin legacy (petugas_points) — dipertahankan untuk kompatibilitas backward */
    public function petugasPoints(): HasMany
    {
        return $this->hasMany(PetugasPoint::class, 'user_id');
    }

    /** Log audit yang dilakukan oleh user ini */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'actor_id');
    }

    // ─── Poin Helpers ───────────────────────────────────────────────────────

    /** Total saldo poin saat ini */
    public function totalPoints(): int
    {
        return (int) $this->point_balance;
    }

    /** Total perolehan poin masuk sepanjang waktu (lifetime earned) */
    public function totalEarnedPoints(): int
    {
        return (int) $this->pointTransactions()->where('type', 'earn')->sum('amount');
    }

    /** Total poin yang telah ditukarkan (lifetime redeemed) */
    public function totalRedeemedPoints(): int
    {
        return (int) abs($this->pointTransactions()->where('type', 'redeem')->sum('amount'));
    }
}
