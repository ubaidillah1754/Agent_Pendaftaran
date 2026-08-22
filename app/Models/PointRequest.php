<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointRequest extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'reason',
        'status',
        'admin_id',
        'admin_note',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'points'      => 'integer',
    ];

    // ─── Relasi ─────────────────────────────────────────────────────────────

    /** Petugas yang mengajukan */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Admin yang memproses */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // ─── Accessors & Helpers ─────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default    => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): array
    {
        return match ($this->status) {
            'pending'  => ['bg' => '#FEF3C7', 'color' => '#92400E'],
            'approved' => ['bg' => '#D1FAE5', 'color' => '#065F46'],
            'rejected' => ['bg' => '#FEE2E2', 'color' => '#991B1B'],
            default    => ['bg' => '#F3F4F6', 'color' => '#6B7280'],
        };
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
