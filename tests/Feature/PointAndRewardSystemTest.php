<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Merchandise;
use App\Models\Patient;
use App\Models\PointRedemption;
use App\Models\PointTransaction;
use App\Models\Registration;
use App\Models\User;
use App\Services\PatientService;
use App\Services\PointService;
use App\Services\RedemptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointAndRewardSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_patient_creation_awards_points_and_records_ledger(): void
    {
        $petugas = User::factory()->create([
            'role'          => 'petugas',
            'point_balance' => 0,
        ]);

        $this->actingAs($petugas);

        $nik = '350101' . rand(1000000000, 9999999999);
        $patientData = [
            'nik'              => (string) $nik,
            'nama_pasien'      => 'Budi Santoso',
            'jenis_kelamin'    => 'L',
            'tanggal_lahir'    => '1995-05-10',
            'alamat'           => 'Jl. Merdeka No. 10',
            'jenis_pembayaran' => 'umum',
            'golongan_darah'   => 'O',
        ];

        $response = $this->post(route('patients.store'), $patientData);
        $response->assertSessionHas('success');

        $petugas->refresh();
        $this->assertEquals(10, $petugas->point_balance);

        $patient = Patient::where('nik', $nik)->first();
        $this->assertNotNull($patient);
        $this->assertEquals($petugas->id, $patient->created_by);

        $tx = PointTransaction::where('source_type', Patient::class)
            ->where('source_id', $patient->id)
            ->first();

        $this->assertNotNull($tx);
        $this->assertEquals('earn', $tx->type);
        $this->assertEquals(10, $tx->amount);
        $this->assertEquals(0, $tx->balance_before);
        $this->assertEquals(10, $tx->balance_after);
    }

    public function test_registration_with_existing_patient_does_not_award_points(): void
    {
        $petugas = User::factory()->create([
            'role'          => 'petugas',
            'point_balance' => 50,
        ]);

        $existingPatient = Patient::create([
            'no_rm'            => Patient::generateNoRM(),
            'nik'              => '350101' . rand(1000000000, 9999999999),
            'nama_pasien'      => 'Pasien Lama',
            'jenis_kelamin'    => 'P',
            'tanggal_lahir'    => '1990-01-01',
            'alamat'           => 'Jl. Mawar No. 1',
            'jenis_pembayaran' => 'umum',
            'created_by'       => $petugas->id,
        ]);

        $dept = Department::create(['nama_poli' => 'Poli Penyakit Dalam', 'kode_poli' => 'PPD', 'is_active' => true]);
        $doctor = Doctor::create([
            'department_id' => $dept->id,
            'nama_dokter'   => 'dr. Sarah Sp.PD',
            'spesialisasi'  => 'Penyakit Dalam',
            'is_active'     => true
        ]);
        
        $todayName = DoctorSchedule::hariDariTanggal(today()->format('Y-m-d'));
        $schedule = DoctorSchedule::create([
            'doctor_id'     => $doctor->id,
            'department_id' => $dept->id,
            'hari'          => $todayName,
            'jam_mulai'     => '08:00',
            'jam_selesai'   => '14:00',
            'kuota'         => 30,
            'is_active'     => true,
        ]);

        $this->actingAs($petugas);

        $response = $this->post(route('registrations.store'), [
            'mode_pasien'        => 'lama',
            'patient_id'         => $existingPatient->id,
            'department_id'      => $dept->id,
            'doctor_id'          => $doctor->id,
            'doctor_schedule_id' => $schedule->id,
            'tanggal_daftar'     => today()->format('Y-m-d'),
        ]);

        $response->assertSessionHas('success');

        $petugas->refresh();
        $this->assertEquals(50, $petugas->point_balance); // Saldo tetap 50, tidak bertambah
    }

    public function test_redemption_creation_deducts_points_and_reserves_stock(): void
    {
        $petugas = User::factory()->create([
            'role'          => 'petugas',
            'point_balance' => 200,
        ]);

        $merchandise = Merchandise::create([
            'name'            => 'Tumbler Uji',
            'points'          => 50,
            'points_required' => 50,
            'stock'           => 10,
            'is_active'       => true,
        ]);

        $this->actingAs($petugas);

        $response = $this->post(route('points.tukar'), [
            'merchandise_id' => $merchandise->id,
            'quantity'       => 2,
            'notes'          => 'Warna biru',
        ]);

        $response->assertRedirect(route('points.redemptions.index'));

        $petugas->refresh();
        $merchandise->refresh();

        $this->assertEquals(100, $petugas->point_balance); // 200 - (50 * 2) = 100
        $this->assertEquals(8, $merchandise->stock);        // 10 - 2 = 8

        $redemption = PointRedemption::where('user_id', $petugas->id)->latest()->first();
        $this->assertNotNull($redemption);
        $this->assertEquals('pending', $redemption->status);
        $this->assertEquals(100, $redemption->total_points);

        $tx = PointTransaction::where('source_type', PointRedemption::class)
            ->where('source_id', $redemption->id)
            ->first();

        $this->assertNotNull($tx);
        $this->assertEquals('redeem', $tx->type);
        $this->assertEquals(-100, $tx->amount);
    }

    public function test_redemption_rejected_refunds_points_and_stock(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $petugas = User::factory()->create([
            'role'          => 'petugas',
            'point_balance' => 300,
        ]);

        $merchandise = Merchandise::create([
            'name'            => 'Payung Uji',
            'points'          => 100,
            'points_required' => 100,
            'stock'           => 5,
            'is_active'       => true,
        ]);

        /** @var RedemptionService $redemptionService */
        $redemptionService = app(RedemptionService::class);
        $redemption = $redemptionService->createRedemption($petugas, $merchandise->id, 1);

        $petugas->refresh();
        $merchandise->refresh();
        $this->assertEquals(200, $petugas->point_balance);
        $this->assertEquals(4, $merchandise->stock);

        // Admin tolak penukaran
        $this->actingAs($admin);
        $response = $this->patch(route('admin.redemptions.reject', $redemption), [
            'reason' => 'Stok rusak',
        ]);

        $response->assertSessionHas('success');

        $petugas->refresh();
        $merchandise->refresh();
        $redemption->refresh();

        $this->assertEquals('rejected', $redemption->status);
        $this->assertEquals(300, $petugas->point_balance); // Poin kembali utuh
        $this->assertEquals(5, $merchandise->stock);       // Stok kembali utuh

        $reversalTx = PointTransaction::where('type', 'reversal')
            ->where('source_id', $redemption->id)
            ->first();

        $this->assertNotNull($reversalTx);
        $this->assertEquals(100, $reversalTx->amount);
    }

    public function test_admin_point_adjustment_and_negative_balance_prevention(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $petugas = User::factory()->create([
            'role'          => 'petugas',
            'point_balance' => 20,
        ]);

        $this->actingAs($admin);

        // Tambah poin +50
        $response = $this->post(route('admin.points.adjustment.store'), [
            'user_id' => $petugas->id,
            'action'  => 'add',
            'amount'  => 50,
            'reason'  => 'Bonus prestasi',
        ]);
        $response->assertRedirect(route('admin.points.adjustment.index'));

        $petugas->refresh();
        $this->assertEquals(70, $petugas->point_balance);

        // Coba kurangi poin melebihi saldo (-100 padahal saldo 70)
        $response = $this->post(route('admin.points.adjustment.store'), [
            'user_id' => $petugas->id,
            'action'  => 'subtract',
            'amount'  => 100,
            'reason'  => 'Koreksi kelebihan',
        ]);

        $response->assertSessionHas('error');

        $petugas->refresh();
        $this->assertEquals(70, $petugas->point_balance); // Saldo tetap 70 dan tidak negatif
    }
}
