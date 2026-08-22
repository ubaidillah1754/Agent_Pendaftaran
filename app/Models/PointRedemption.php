<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointRedemption extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'type',
        'status',
        'catatan',
    ];

    // ─── Relasi ─────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Accessor & Helper ───────────────────────────────────────────────────

    /** Label status yang ramah pengguna */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Menunggu',
            'disetujui' => 'Disetujui',
            'selesai'   => 'Selesai',
            'ditolak'   => 'Ditolak',
            default     => ucfirst($this->status),
        };
    }

    /** Kelas badge Bootstrap berdasarkan status */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'warning',
            'disetujui' => 'primary',
            'selesai'   => 'success',
            'ditolak'   => 'danger',
            default     => 'secondary',
        };
    }

    /** Label warna status (untuk inline style) */
    public function getStatusColorAttribute(): array
    {
        return match ($this->status) {
            'pending'   => ['bg' => '#FEF3C7', 'color' => '#92400E'],
            'disetujui' => ['bg' => '#DBEAFE', 'color' => '#1E40AF'],
            'selesai'   => ['bg' => '#D1FAE5', 'color' => '#065F46'],
            'ditolak'   => ['bg' => '#FEE2E2', 'color' => '#991B1B'],
            default     => ['bg' => '#F3F4F6', 'color' => '#6B7280'],
        };
    }

    /** Cek apakah penukaran masih bisa diubah (belum final) */
    public function isFinal(): bool
    {
        return in_array($this->status, ['selesai', 'ditolak']);
    }

    /** Label tipe penukaran */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'uang'        => 'Uang Tunai',
            'merchandise' => 'Merchandise',
            default       => ucfirst($this->type),
        };
    }
}
