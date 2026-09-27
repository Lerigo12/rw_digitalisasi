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

class FeeBillManagementTest extends TestCase
{
    use RefreshDatabase;

    private Rw $rw;

    private Rt $rt1;

    private Rt $rt2;

    private Role $roleBendahara;

    private Role $roleResident;

    private Role $roleSuperAdmin;

    private User $bendaharaRt1;

    private Resident $residentRt1A;

    private Resident $residentRt1B;

    private Resident $residentRt2;

    private FeeType $feeType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rw = Rw::create(['name' => 'RW 01', 'code' => 'RW01', 'is_active' => true]);
        $this->rt1 = Rt::create(['rw_id' => $this->rw->id, 'number' => '001', 'name' => 'RT 001', 'is_active' => true]);
        $this->rt2 = Rt::create(['rw_id' => $this->rw->id, 'number' => '002', 'name' => 'RT 002', 'is_active' => true]);

        $this->roleBendahara = Role::create(['name' => 'Bendahara RT', 'slug' => 'bendahara-rt']);
        $this->roleResident = Role::create(['name' => 'Warga', 'slug' => 'resident']);
        $this->roleSuperAdmin = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);

        // Families
        $fam1 = Family::create(['rt_id' => $this->rt1->id, 'family_number' => '3201010101010001', 'address' => 'Jl. RT 1']);
        $fam2 = Family::create(['rt_id' => $this->rt1->id, 'family_number' => '3201010101010002', 'address' => 'Jl. RT 1']);
        $fam3 = Family::create(['rt_id' => $this->rt2->id, 'family_number' => '3201010101010003', 'address' => 'Jl. RT 2']);

        // Residents
        $this->residentRt1A = Resident::create([
            'family_id' => $fam1->id,
            'full_name' => 'Ahmad Fauzi',
            'nik' => '3201010101010001',
            'gender' => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '1990-01-01',
            'status' => 'active',
        ]);

        $this->residentRt1B = Resident::create([
            'family_id' => $fam2->id,
            'full_name' => 'Budi Santoso',
            'nik' => '3201010101010002',
            'gender' => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '1992-01-01',
            'status' => 'active',
        ]);

        $this->residentRt2 = Resident::create([
            'family_id' => $fam3->id,
            'full_name' => 'Citra Lestari',
            'nik' => '3201010101010003',
            'gender' => 'P',
            'birth_place' => 'Jakarta',
            'birth_date' => '1995-01-01',
            'status' => 'active',
        ]);

        // Bendahara RT 01
        $this->bendaharaRt1 = User::create([
            'name' => 'Bendahara RT 01',
            'email' => 'bendahara.rt1@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->bendaharaRt1->roles()->attach($this->roleBendahara->id, ['scope_type' => 'rt', 'scope_id' => $this->rt1->id]);

        // Fee Type
        $this->feeType = FeeType::create([
            'scope_type' => 'rt',
            'scope_id' => $this->rt1->id,
            'name' => 'Iuran Keamanan',
            'description' => 'Iuran Keamanan RT 01',
            'amount' => 250000,
            'period_type' => 'monthly',
            'due_day' => 10,
            'is_active' => true,
        ]);
    }

    public function test_bendahara_rt_only_sees_residents_in_their_rt_in_dropdown(): void
    {
        $response = $this->actingAs($this->bendaharaRt1)->get(route('fees.index'));

        $response->assertStatus(200);
        $response->assertSee('Ahmad Fauzi');
        $response->assertSee('Budi Santoso');
        $response->assertDontSee('Citra Lestari');
    }

    public function test_bendahara_rt_can_create_bill_for_single_resident(): void
    {
        $response = $this->actingAs($this->bendaharaRt1)->post(route('fees.bills.generate'), [
            'resident_id' => $this->residentRt1A->id,
            'fee_type_id' => $this->feeType->id,
            'period_key' => '2026-09',
            'amount_due' => 250000,
        ]);

        $response->assertRedirect(route('fees.index'));
        $response->assertSessionHas('success');

        // Verify only 1 bill is created for residentRt1A
        $this->assertDatabaseCount('fee_bills', 1);
        $this->assertDatabaseHas('fee_bills', [
            'fee_type_id' => $this->feeType->id,
            'resident_id' => $this->residentRt1A->id,
            'period_key' => '2026-09',
            'amount_due' => 250000,
            'status' => 'unpaid',
        ]);

        // Verify other residents do not have bills
        $this->assertDatabaseMissing('fee_bills', [
            'resident_id' => $this->residentRt1B->id,
        ]);
        $this->assertDatabaseMissing('fee_bills', [
            'resident_id' => $this->residentRt2->id,
        ]);
    }

    public function test_bendahara_rt_cannot_create_bill_for_resident_in_different_rt(): void
    {
        $response = $this->actingAs($this->bendaharaRt1)->post(route('fees.bills.generate'), [
            'resident_id' => $this->residentRt2->id,
            'fee_type_id' => $this->feeType->id,
            'period_key' => '2026-09',
            'amount_due' => 250000,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('fee_bills', 0);
    }

    public function test_cannot_create_duplicate_bill_for_same_resident_fee_type_and_period(): void
    {
        FeeBill::create([
            'fee_type_id' => $this->feeType->id,
            'resident_id' => $this->residentRt1A->id,
            'period_key' => '2026-09',
            'amount_due' => 250000,
            'due_date' => now()->addDays(10),
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->bendaharaRt1)->post(route('fees.bills.generate'), [
            'resident_id' => $this->residentRt1A->id,
            'fee_type_id' => $this->feeType->id,
            'period_key' => '2026-09',
            'amount_due' => 250000,
        ]);

        $response->assertSessionHasErrors('period_key');
        $this->assertDatabaseCount('fee_bills', 1);
    }

    public function test_resident_can_view_own_bill_and_submit_payment(): void
    {
        Storage::fake('public');

        $bill = FeeBill::create([
            'fee_type_id' => $this->feeType->id,
            'resident_id' => $this->residentRt1A->id,
            'period_key' => '2026-09',
            'amount_due' => 250000,
            'due_date' => now()->addDays(10),
            'status' => 'unpaid',
        ]);

        $userResident = User::create([
            'resident_id' => $this->residentRt1A->id,
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $userResident->roles()->attach($this->roleResident->id, ['scope_type' => 'rt', 'scope_id' => $this->rt1->id]);

        // Resident views index
        $viewResponse = $this->actingAs($userResident)->get(route('fees.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Iuran Keamanan');
        $viewResponse->assertSee('2026-09');

        // Resident submits payment
        $file = UploadedFile::fake()->create('proof.jpg', 500, 'image/jpeg');
        $payResponse = $this->actingAs($userResident)->post(route('fees.pay'), [
            'fee_bill_id' => $bill->id,
            'payment_method' => 'bank_transfer',
            'payment_date' => '2026-09-28',
            'proof_file' => $file,
            'notes' => 'Transfer via BCA',
        ]);

        $payResponse->assertRedirect(route('fees.index'));
        $this->assertDatabaseHas('payments', [
            'payer_resident_id' => $this->residentRt1A->id,
            'status' => 'pending',
            'total_amount' => 250000,
        ]);
        $this->assertDatabaseHas('payment_allocations', [
            'fee_bill_id' => $bill->id,
            'allocated_amount' => 250000,
        ]);
    }
}
