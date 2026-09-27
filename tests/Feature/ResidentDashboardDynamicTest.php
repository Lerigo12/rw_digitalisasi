<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\FeeBill;
use App\Models\FeeType;
use App\Models\LetterRequest;
use App\Models\LetterType;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResidentDashboardDynamicTest extends TestCase
{
    use RefreshDatabase;

    private User $residentUser;

    private Resident $resident;

    private FeeBill $bill1;

    private FeeBill $bill2;

    private User $treasurerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $rw = Rw::create(['name' => 'RW 01', 'code' => 'RW01', 'is_active' => true]);
        $rt = Rt::create(['rw_id' => $rw->id, 'number' => '001', 'name' => 'RT 001', 'is_active' => true]);
        $family = Family::create(['rt_id' => $rt->id, 'family_number' => '3201010101010001', 'address' => 'Jl. Mawar']);

        $this->resident = Resident::create([
            'family_id' => $family->id,
            'full_name' => 'Achmad Fauzi',
            'nik' => '3201010101010001',
            'gender' => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '1990-01-01',
            'status' => 'active',
        ]);

        $roleResident = Role::create(['name' => 'Warga', 'slug' => 'resident']);
        $roleBendahara = Role::create(['name' => 'Bendahara RT', 'slug' => 'bendahara-rt']);

        $this->residentUser = User::create([
            'resident_id' => $this->resident->id,
            'name' => 'Achmad Fauzi',
            'email' => 'achmad@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->residentUser->roles()->attach($roleResident->id, ['scope_type' => 'rt', 'scope_id' => $rt->id]);

        $this->treasurerUser = User::create([
            'name' => 'Bendahara RT',
            'email' => 'bendahara@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->treasurerUser->roles()->attach($roleBendahara->id, ['scope_type' => 'rt', 'scope_id' => $rt->id]);

        $feeType = FeeType::create([
            'scope_type' => 'rt',
            'scope_id' => $rt->id,
            'name' => 'Iuran Keamanan',
            'amount' => 50000,
            'period_type' => 'monthly',
            'due_day' => 10,
            'is_active' => true,
        ]);

        $this->bill1 = FeeBill::create([
            'fee_type_id' => $feeType->id,
            'resident_id' => $this->resident->id,
            'period_key' => '2026-09',
            'amount_due' => 50000,
            'due_date' => now()->addDays(5),
            'status' => 'unpaid',
        ]);

        $this->bill2 = FeeBill::create([
            'fee_type_id' => $feeType->id,
            'resident_id' => $this->resident->id,
            'period_key' => '2026-10',
            'amount_due' => 50000,
            'due_date' => now()->addDays(35),
            'status' => 'unpaid',
        ]);

        $letterType = LetterType::create([
            'scope_type' => 'rt',
            'scope_id' => $rt->id,
            'name' => 'Surat Pengantar',
            'is_active' => true,
        ]);

        LetterRequest::create([
            'letter_type_id' => $letterType->id,
            'resident_id' => $this->resident->id,
            'request_number' => 'REQ-TEST-001',
            'purpose' => 'KTP baru',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function test_dashboard_shows_accurate_unpaid_bills_and_updates_on_verification(): void
    {
        // Initially 2 unpaid bills
        $res = $this->actingAs($this->residentUser)->get(route('dashboard'));
        $res->assertStatus(200);
        $res->assertSee('Achmad Fauzi');
        $res->assertSee('2'); // unpaid bills count
        $res->assertSee('1'); // active letters count

        // Resident pays bill 1
        $payment = Payment::create([
            'user_id' => $this->residentUser->id,
            'payer_resident_id' => $this->resident->id,
            'payment_date' => '2026-09-28',
            'total_amount' => 50000,
            'method' => 'manual_transfer',
            'payment_method' => 'bank_transfer',
            'status' => 'pending',
        ]);

        PaymentAllocation::create([
            'payment_id' => $payment->id,
            'fee_bill_id' => $this->bill1->id,
            'allocated_amount' => 50000,
        ]);

        // While pending, bill is still unpaid (status = unpaid)
        $res = $this->actingAs($this->residentUser)->get(route('dashboard'));
        $res->assertSee('2');

        // Treasurer approves payment
        $this->actingAs($this->treasurerUser)->post(route('fees.verify', $payment->id), [
            'decision' => 'verified',
        ]);

        // Dashboard now reflects 1 unpaid bill
        $this->bill1->refresh();
        $this->assertEquals('paid', $this->bill1->status);

        $res = $this->actingAs($this->residentUser)->get(route('dashboard'));
        $res->assertSee('1');
    }
}
