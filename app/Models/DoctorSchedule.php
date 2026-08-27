<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'department_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'kuota',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relasi ─────────────────────────────────────────────────────────────

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    // ─── Scope ──────────────────────────────────────────────────────────────

    /** Hanya jadwal yang aktif */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Filter jadwal berdasarkan hari */
    public function scopeByHari($query, string $hari)
    {
        return $query->where('hari', $hari);
    }

    // ─── Helper ─────────────────────────────────────────────────────────────

    /**
     * Hitung sisa kuota untuk jadwal ini pada tanggal tertentu.
     * Hanya menghitung pendaftaran yang tidak berstatus 'batal'.
     */
    public function sisaKuota(string $tanggal): int
    {
        $terpakai = $this->registrations()
            ->where('tanggal_daftar', $tanggal)
            ->whereNotIn('status', ['batal'])
            ->count();

        return max(0, $this->kuota - $terpakai);
    }

    /**
     * Cek apakah jadwal ini masih tersedia (kuota > 0) pada tanggal tertentu.
     */
    public function tersedia(string $tanggal): bool
    {
        return $this->is_active && $this->sisaKuota($tanggal) > 0;
    }

    /**
     * Ambil nama hari dalam Bahasa Indonesia.
     * Berguna untuk mapping hari dari jadwal ke tanggal pendaftaran.
     */
    public static function hariDariTanggal(string $tanggal): string
    {
        $hariInggris = Carbon::parse($tanggal)->locale('id')->isoFormat('dddd');
        $map = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu',
        ];
        return $map[Carbon::parse($tanggal)->format('l')] ?? '';
    }

    /** Daftar hari dalam seminggu (untuk dropdown) */
    public static function daftarHari(): array
    {
        return ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    }

    /**
     * Waktu mulai boleh ambil antrean = jam_mulai - 1 jam.
     * Menggunakan Carbon agar timezone-aware.
     *
     * @param  string $tanggal  format Y-m-d
     * @return \Carbon\Carbon
     */
    public function jamMulaiAmbilAntrean(string $tanggal): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse("{$tanggal} {$this->jam_mulai}")->subHour();
    }

    /**
     * Waktu expired = jam_selesai jadwal (inklusif: >=jam_selesai = expired).
     *
     * @param  string $tanggal  format Y-m-d
     * @return \Carbon\Carbon
     */
    public function jamSelesaiAntrean(string $tanggal): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse("{$tanggal} {$this->jam_selesai}");
    }

    /**
     * Cek apakah waktu sekarang masuk dalam window pengambilan antrean.
     * Window: [jam_mulai - 1jam) s/d jam_selesai (exclusive).
     *
     * @param  string $tanggal  format Y-m-d
     * @return string  'ok' | 'too_early' | 'expired'
     */
    public function statusWindowAntrean(string $tanggal): string
    {
        $now        = now();
        $mulaiAmbil = $this->jamMulaiAmbilAntrean($tanggal);
        $selesai    = $this->jamSelesaiAntrean($tanggal);

        if ($now->lt($mulaiAmbil)) {
            return 'too_early';
        }

        if ($now->gte($selesai)) {
            return 'expired';
        }

        return 'ok';
    }
}

