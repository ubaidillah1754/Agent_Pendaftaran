<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_schedule_id',
        'department_id',
        'doctor_id',
        'tanggal_daftar',
        'nomor_antrian',
        'urutan_antrian',
        'keluhan',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_daftar' => 'date',
        ];
    }

    // ─── Relasi ─────────────────────────────────────────────────────────────

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctorSchedule()
    {
        return $this->belongsTo(DoctorSchedule::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /** Petugas yang mendaftarkan */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Scope ──────────────────────────────────────────────────────────────

    /** Filter pendaftaran hari ini */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_daftar', today());
    }

    /** Filter berdasarkan status */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /** Filter berdasarkan poli */
    public function scopeByDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /** Hanya yang menunggu atau sedang dipanggil (antrian aktif) */
    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['menunggu', 'dipanggil']);
    }

    // ─── Accessor ───────────────────────────────────────────────────────────

    /** Label status dengan badge warna */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu'  => 'Menunggu',
            'dipanggil' => 'Dipanggil',
            'selesai'   => 'Selesai',
            'batal'     => 'Batal',
            default     => ucfirst($this->status),
        };
    }

    /** Kelas badge Bootstrap berdasarkan status */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'menunggu'  => 'warning',
            'dipanggil' => 'primary',
            'selesai'   => 'success',
            'batal'     => 'danger',
            default     => 'secondary',
        };
    }

    // ─── Static Helper ──────────────────────────────────────────────────────

    /**
     * Daftar transisi status yang valid.
     * Digunakan untuk validasi update status antrian.
     */
    public static function transisiStatusValid(): array
    {
        return [
            'menunggu'  => ['dipanggil', 'batal'],
            'dipanggil' => ['selesai', 'menunggu'],
            'selesai'   => [],
            'batal'     => [],
        ];
    }
}
