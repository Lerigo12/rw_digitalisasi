<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\FeeBill;
use App\Models\FeeType;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentMethodsTest extends TestCase
{
    use RefreshDatabase;

    private Rw $rw;

    private Rt $rt;

    private Family $family;

    private Resident $resident;

    private User $residentUser;

    private FeeType $feeType;

    private FeeBill $bill;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rw = Rw::create(['name' => 'RW 01', 'code' => 'RW01', 'is_active' => true]);
        $this->rt = Rt::create(['rw_id' => $this->rw->id, 'number' => '001', 'name' => 'RT 001', 'is_active' => true]);
        $this->family = Family::create(['rt_id' => $this->rt->id, 'family_number' => '3201010101010001', 'address' => 'Jl. Test']);

        $this->resident = Resident::create([
            'family_id' => $this->family->id,
            'full_name' => 'Warga Test',
            'nik' => '3201010101010001',
            'gender' => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '1990-01-01',
            'status' => 'active',
        ]);

        $roleResident = Role::create(['name' => 'Warga', 'slug' => 'resident']);

        $this->residentUser = User::create([
            'resident_id' => $this->resident->id,
            'name' => 'Warga Test',
            'email' => 'warga@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->residentUser->roles()->attach($roleResident->id, ['scope_type' => 'rt', 'scope_id' => $this->rt->id]);

        $this->feeType = FeeType::create([
            'scope_type' => 'rt',
            'scope_id' => $this->rt->id,
            'name' => 'Iuran Wajib',
            'amount' => 50000,
            'period_type' => 'monthly',
            'due_day' => 10,
            'is_active' => true,
        ]);

        $this->bill = FeeBill::create([
            'fee_type_id' => $this->feeType->id,
            'resident_id' => $this->resident->id,
            'period_key' => '2026-09',
            'amount_due' => 50000,
            'due_date' => now()->addDays(10),
            'status' => 'unpaid',
        ]);
    }

    public function test_resident_can_submit_payment_with_qris_method(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('qris_proof.jpg', 400, 'image/jpeg');

        $response = $this->actingAs($this->residentUser)->post(route('fees.pay'), [
            'fee_bill_id' => $this->bill->id,
            'payment_method' => 'qris',
            'payment_date' => '2026-09-28',
            'proof_file' => $file,
            'notes' => 'Bayar via QRIS',
        ]);

        $response->assertRedirect(route('fees.index'));
        $this->assertDatabaseHas('payments', [
            'payer_resident_id' => $this->resident->id,
            'payment_method' => 'qris',
            'status' => 'pending',
            'total_amount' => 50000,
        ]);
    }

    public function test_resident_can_submit_payment_with_manual_method_without_proof(): void
    {
        $response = $this->actingAs($this->residentUser)->post(route('fees.pay'), [
            'fee_bill_id' => $this->bill->id,
            'payment_method' => 'manual',
            'payment_date' => '2026-09-28',
            'notes' => 'Bayar langsung ke bendahara',
        ]);

        $response->assertRedirect(route('fees.index'));
        $this->assertDatabaseHas('payments', [
            'payer_resident_id' => $this->resident->id,
            'payment_method' => 'manual',
            'status' => 'pending',
            'total_amount' => 50000,
        ]);
    }
}
