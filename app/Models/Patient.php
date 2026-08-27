<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_rm',
        'nik',
        'nama_pasien',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_telepon',
        'nama_wali',
        'no_telepon_wali',
        'golongan_darah',
        'jenis_pembayaran',
        'no_bpjs',
        'no_asuransi',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    // ─── Relasi ─────────────────────────────────────────────────────────────

    /** Petugas/User yang pertama kali menginput pasien ini */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Semua riwayat pendaftaran/kunjungan pasien ini */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /** Pendaftaran aktif (hari ini, status menunggu/diperiksa) */
    public function pendaftaranAktif()
    {
        return $this->hasMany(Registration::class)
            ->whereDate('tanggal_kunjungan', today())
            ->whereIn('status', ['menunggu', 'diperiksa']);
    }

    /** Transaksi poin yang diperoleh dari pendaftaran pasien baru ini */
    public function pointTransaction(): MorphOne
    {
        return $this->morphOne(PointTransaction::class, 'source');
    }

    // ─── Accessor ───────────────────────────────────────────────────────────

    /** Umur pasien dihitung dari tanggal lahir */
    public function getUmurAttribute(): int
    {
        return Carbon::parse($this->tanggal_lahir)->age;
    }

    /** Label jenis kelamin lengkap */
    public function getJenisKelaminLabelAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    /** Label jenis pembayaran dengan huruf kapital pertama */
    public function getJenisPembayaranLabelAttribute(): string
    {
        return match ($this->jenis_pembayaran) {
            'bpjs'     => 'BPJS Kesehatan',
            'asuransi' => 'Asuransi',
            default    => 'Umum',
        };
    }

    // ─── Scope ──────────────────────────────────────────────────────────────

    /** Cari pasien berdasarkan NIK, No. RM, atau Nama */
    public function scopeCariPasien($query, string $keyword)
    {
        return $query->where('nik', 'like', "%{$keyword}%")
            ->orWhere('no_rm', 'like', "%{$keyword}%")
            ->orWhere('nama_pasien', 'like', "%{$keyword}%");
    }

    // ─── Static Helper ──────────────────────────────────────────────────────

    /**
     * Generate Nomor Rekam Medis baru.
     * Format: RM-YYYYMMDD-XXXX (contoh: RM-20260101-0001)
     */
    public static function generateNoRM(): string
    {
        $today  = now()->format('Ymd');
        $prefix = "RM-{$today}-";

        // Ambil nomor urut terakhir hari ini
        $last = static::where('no_rm', 'like', "{$prefix}%")
            ->orderByDesc('no_rm')
            ->value('no_rm');

        $urutan = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($urutan, 4, '0', STR_PAD_LEFT);
    }
}
