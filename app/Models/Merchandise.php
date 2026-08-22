<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Merchandise extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'points_required',
        'stock',
        'is_active',
        'image',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'points_required' => 'integer',
            'stock'           => 'integer',
            'is_active'       => 'boolean',
        ];
    }

    // ─── Relasi ─────────────────────────────────────────────────────────────

    /** Riwayat penukaran merchandise ini */
    public function redemptions(): HasMany
    {
        return $this->hasMany(PointRedemption::class);
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    /** URL gambar merchandise */
    public function getImageUrlAttribute(): string
    {
        if ($this->image && file_exists(public_path('images/merchandise/' . $this->image))) {
            return asset('images/merchandise/' . $this->image);
        }
        if ($this->image && file_exists(public_path($this->image))) {
            return asset($this->image);
        }
        return asset('images/merchandise/tumbler_hadiah_1787316639461.jpg');
    }

    /** Cek ketersediaan stok */
    public function isAvailable(int $quantity = 1): bool
    {
        return $this->is_active && $this->stock >= $quantity;
    }
}
