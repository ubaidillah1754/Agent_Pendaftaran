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

    /**
     * Hitung antrian berikutnya untuk poli ini pada tanggal tertentu.
     * Format: {KODE_POLI}{3 digit urutan} → contoh: PU001
     */
    public function generateNomorAntrian(string $tanggal): array
    {
        // Ambil urutan terakhir hari ini untuk poli ini
        $lastUrutan = Registration::where('department_id', $this->id)
            ->where('tanggal_daftar', $tanggal)
            ->whereNotIn('status', ['batal'])
            ->max('urutan_antrian') ?? 0;

        $urutan       = $lastUrutan + 1;
        $nomorAntrian = strtoupper($this->kode_poli) . str_pad($urutan, 3, '0', STR_PAD_LEFT);

        return [
            'urutan'        => $urutan,
            'nomor_antrian' => $nomorAntrian,
        ];
    }
}
