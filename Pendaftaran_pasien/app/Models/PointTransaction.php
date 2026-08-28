<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'source_type',
        'source_id',
        'reference',
        'description',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount'         => 'integer',
            'balance_before' => 'integer',
            'balance_after'  => 'integer',
        ];
    }

    // ─── Relasi ─────────────────────────────────────────────────────────────

    /** Pemilik transaksi poin (karyawan/petugas) */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Admin atau user pembuat transaksi (opsional, misal admin adjustment) */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Sumber transaksi (Patient, PointRedemption, dll) */
    public function source(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'source_type', 'source_id');
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────

    public function scopeEarn($query)
    {
        return $query->where('type', 'earn');
    }

    public function scopeRedeem($query)
    {
        return $query->where('type', 'redeem');
    }

    public function scopeAdjustment($query)
    {
        return $query->where('type', 'adjustment');
    }

    public function scopeReversal($query)
    {
        return $query->where('type', 'reversal');
    }

    // ─── Accessors ──────────────────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'earn'       => 'Poin Masuk',
            'redeem'     => 'Tukar Reward',
            'adjustment' => 'Penyesuaian',
            'reversal'   => 'Pengembalian',
            default      => ucfirst($this->type),
        };
    }

    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'earn'       => 'success',
            'redeem'     => 'warning',
            'adjustment' => 'info',
            'reversal'   => 'secondary',
            default      => 'light',
        };
    }
}
