<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_schedule_id',
        'department_id',
        'doctor_id',
        'tanggal_daftar',
        'tanggal_kunjungan',
        'kode_booking',
        'nomor_antrian',
        'urutan_antrian',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_daftar'    => 'date',
            'tanggal_kunjungan' => 'date',
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

    /** Poin yang didapat dari pendaftaran ini */
    public function pointTransaction()
    {
        return $this->morphOne(PointTransaction::class, 'source');
    }

    // ─── Scope ──────────────────────────────────────────────────────────────

    /** Filter pendaftaran hari ini */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_daftar', today());
    }

    /** Filter berdasarkan poli */
    public function scopeByDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /** Antrean hari ini untuk department tertentu yang sudah punya nomor antrean */
    public function scopeAntreanHariIni($query, int $departmentId, string $tanggal)
    {
        return $query
            ->where('department_id', $departmentId)
            ->where('tanggal_kunjungan', $tanggal)
            ->whereNotNull('nomor_antrian');
    }

    // ─── Static Helper ──────────────────────────────────────────────────────

    /**
     * Generate kode booking unik format BK-XXXX.
     * Menggunakan karakter yang tidak membingungkan (tanpa 0,1,I,O).
     * Loop hingga menemukan kode yang belum ada di database.
     */
    public static function generateKodeBooking(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        do {
            $suffix = '';
            for ($i = 0; $i < 4; $i++) {
                $suffix .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $kode = 'BK-' . $suffix;
        } while (static::where('kode_booking', $kode)->exists());

        return $kode;
    }

    /**
     * Generate nomor antrean berikutnya untuk department + tanggal tertentu.
     * Format: PAR-001, JAN-001, dll. (3 huruf pertama nama poli tanpa kata "Poli").
     * Mengambil MAX nomor terakhir kemudian +1 (bukan count).
     * Thread-safe: harus dipanggil dalam DB::transaction dengan LOCK.
     *
     * @param int    $departmentId
     * @param string $tanggal      format Y-m-d
     * @return string  misal "PAR-004"
     * @throws \RuntimeException jika nomor antrean melebihi batas 999
     */
    public static function generateNomorAntrian(int $departmentId, string $tanggal): string
    {
        $department = DB::table('departments')->where('id', $departmentId)->first();
        $namaPoli = $department ? $department->nama_poli : 'Poli';
        $namaTanpaPoli = trim(str_ireplace('Poli ', '', $namaPoli));
        $prefix = strtoupper(substr($namaTanpaPoli, 0, 3));

        // Ambil nomor terbesar yang sudah ada hari ini di poli ini (untuk tanggal kunjungan)
        $lastNomor = DB::table('registrations')
            ->where('department_id', $departmentId)
            ->where('tanggal_kunjungan', $tanggal)
            ->whereNotNull('nomor_antrian')
            ->where('nomor_antrian', 'like', $prefix . '-%')
            ->lockForUpdate()  // pessimistic lock untuk request bersamaan
            ->max(DB::raw("CAST(SUBSTRING(nomor_antrian, 5) AS UNSIGNED)"));

        $urutan = ($lastNomor ?? 0) + 1;

        if ($urutan > 999) {
            throw new \RuntimeException("Nomor antrean sudah mencapai batas maksimum ({$prefix}-999) untuk hari ini.");
        }

        return $prefix . '-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);
    }
}
