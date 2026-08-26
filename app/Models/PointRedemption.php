<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PointRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_code',
        'user_id',
        'merchandise_id',
        'merchandise_name',
        'points_required',
        'quantity',
        'total_points',
        'type',
        'cash_amount',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'status',
        'approved_by',
        'approved_at',
        'completed_at',
        'rejected_at',
        'cancelled_at',
        'notes',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'points_required' => 'integer',
            'quantity'        => 'integer',
            'total_points'    => 'integer',
            'approved_at'     => 'datetime',
            'completed_at'    => 'datetime',
            'rejected_at'     => 'datetime',
            'cancelled_at'    => 'datetime',
        ];
    }

    // ─── Relasi ─────────────────────────────────────────────────────────────

    /** Petugas/Karyawan yang mengajukan penukaran */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Merchandise yang ditukar */
    public function merchandise(): BelongsTo
    {
        return $this->belongsTo(Merchandise::class, 'merchandise_id')->withTrashed();
    }

    /** Admin yang memproses approval / rejection */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /** Transaksi poin terkait penukaran ini (redeem & reversal) */
    public function pointTransactions(): MorphMany
    {
        return $this->morphMany(PointTransaction::class, 'source');
    }

    // ─── Status Helpers ─────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved' || $this->status === 'disetujui';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed' || $this->status === 'selesai';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected' || $this->status === 'ditolak';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /** Cek apakah status sudah final dan tidak dapat diubah lagi */
    public function isFinal(): bool
    {
        return in_array($this->status, ['completed', 'selesai', 'rejected', 'ditolak', 'cancelled']);
    }

    // ─── Accessors ──────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'               => 'Menunggu Persetujuan',
            'approved', 'disetujui' => 'Disetujui',
            'completed', 'selesai'  => 'Selesai / Diterima',
            'rejected', 'ditolak'   => 'Ditolak',
            'cancelled'             => 'Dibatalkan',
            default                 => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'               => 'warning',
            'approved', 'disetujui' => 'primary',
            'completed', 'selesai'  => 'success',
            'rejected', 'ditolak'   => 'danger',
            'cancelled'             => 'secondary',
            default                 => 'light',
        };
    }

    // ─── Static Helpers ─────────────────────────────────────────────────────

    /**
     * Generate reference code unik untuk redemption.
     * Format: RED-YYYYMMDD-XXXXXX (contoh: RED-20260822-000001)
     */
    public static function generateReferenceCode(): string
    {
        $today = now()->format('Ymd');
        $prefix = "RED-{$today}-";

        $last = static::where('reference_code', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('reference_code');

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last);
            $seq = ((int) end($parts)) + 1;
        }

        return $prefix . str_pad($seq, 6, '0', STR_PAD_LEFT);
    }
}
