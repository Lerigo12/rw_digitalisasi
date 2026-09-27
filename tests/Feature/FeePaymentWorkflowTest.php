<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\FeeBill;
use App\Models\FeeType;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FeePaymentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $resident1;

    private User $resident2;

    private User $treasurerRt1;

    private User $treasurerRt2;

    private Resident $res1;

    private Resident $res2;

    private FeeBill $bill1;

    private FeeBill $bill2;

    private Rt $rt1;

    private Rt $rt2;

    protected function setUp(): void
    {
        parent::setUp();

        $rw = Rw::create(['name' => 'RW 01', 'code' => 'RW01', 'is_active' => true]);
        $this->rt1 = Rt::create(['rw_id' => $rw->id, 'number' => '001', 'name' => 'RT 001', 'is_active' => true]);
        $this->rt2 = Rt::create(['rw_id' => $rw->id, 'number' => '002', 'name' => 'RT 002', 'is_active' => true]);

        $family1 = Family::create(['rt_id' => $this->rt1->id, 'family_number' => '3201010101010001', 'address' => 'Jl. Mawar 1']);
        $family2 = Family::create(['rt_id' => $this->rt2->id, 'family_number' => '3201010101010002', 'address' => 'Jl. Melati 2']);

        $this->res1 = Resident::create([
            'family_id' => $family1->id,
            'full_name' => 'Warga RT 01',
            'nik' => '3201010101010001',
            'gender' => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '1990-01-01',
            'status' => 'active',
        ]);

        $this->res2 = Resident::create([
            'family_id' => $family2->id,
            'full_name' => 'Warga RT 02',
            'nik' => '3201010101010002',
            'gender' => 'P',
            'birth_place' => 'Bandung',
            'birth_date' => '1992-02-02',
            'status' => 'active',
        ]);

        $roleResident = Role::create(['name' => 'Warga', 'slug' => 'resident']);
        $roleTreasurer = Role::create(['name' => 'Bendahara RT', 'slug' => 'bendahara-rt']);

        $this->resident1 = User::create([
            'resident_id' => $this->res1->id,
            'name' => 'Warga RT 01',
            'email' => 'warga1@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->resident1->roles()->attach($roleResident->id, ['scope_type' => 'rt', 'scope_id' => $this->rt1->id]);

        $this->resident2 = User::create([
            'resident_id' => $this->res2->id,
            'name' => 'Warga RT 02',
            'email' => 'warga2@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->resident2->roles()->attach($roleResident->id, ['scope_type' => 'rt', 'scope_id' => $this->rt2->id]);

        $this->treasurerRt1 = User::create([
            'name' => 'Bendahara RT 01',
            'email' => 'bendahara1@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->treasurerRt1->roles()->attach($roleTreasurer->id, ['scope_type' => 'rt', 'scope_id' => $this->rt1->id]);

        $this->treasurerRt2 = User::create([
            'name' => 'Bendahara RT 02',
            'email' => 'bendahara2@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->treasurerRt2->roles()->attach($roleTreasurer->id, ['scope_type' => 'rt', 'scope_id' => $this->rt2->id]);

        $feeType = FeeType::create([
            'scope_type' => 'rt',
            'scope_id' => $this->rt1->id,
            'name' => 'Iuran Keamanan',
            'amount' => 50000,
            'period_type' => 'monthly',
            'due_day' => 10,
            'is_active' => true,
        ]);

        $this->bill1 = FeeBill::create([
            'fee_type_id' => $feeType->id,
            'resident_id' => $this->res1->id,
            'period_key' => '2026-09',
            'amount_due' => 50000,
            'due_date' => now()->addDays(5),
            'status' => 'unpaid',
        ]);

        $this->bill2 = FeeBill::create([
            'fee_type_id' => $feeType->id,
            'resident_id' => $this->res2->id,
            'period_key' => '2026-09',
            'amount_due' => 50000,
            'due_date' => now()->addDays(5),
            'status' => 'unpaid',
        ]);
    }

    public function test_resident_cannot_pay_another_residents_bill(): void
    {
        $response = $this->actingAs($this->resident1)->post(route('fees.pay'), [
            'fee_bill_id' => $this->bill2->id,
            'payment_method' => 'bank_transfer',
            'payment_date' => '2026-09-28',
            'proof_file' => UploadedFile::fake()->create('proof.jpg', 200, 'image/jpeg'),
        ]);

        $response->assertStatus(403);
    }

    public function test_resident_submits_payment_and_cannot_submit_duplicate_while_pending(): void
    {
        Storage::fake('public');

        // Initial view shows Bayar button
        $viewRes = $this->actingAs($this->resident1)->get(route('fees.index'));
        $viewRes->assertStatus(200);
        $viewRes->assertSee('Bayar');

        // Submit first payment
        $response = $this->actingAs($this->resident1)->post(route('fees.pay'), [
            'fee_bill_id' => $this->bill1->id,
            'payment_method' => 'bank_transfer',
            'payment_date' => '2026-09-28',
            'proof_file' => UploadedFile::fake()->create('proof.jpg', 200, 'image/jpeg'),
            'notes' => 'Pembayaran pertama',
        ]);
        $response->assertRedirect(route('fees.index'));
        $response->assertSessionHas('success');

        // Now view shows Menunggu Verifikasi, no Bayar button
        $viewResAfter = $this->actingAs($this->resident1)->get(route('fees.index'));
        $viewResAfter->assertSee('Menunggu Verifikasi');
        $viewResAfter->assertDontSee('modal-pay-'.$this->bill1->id);

        // Attempting to submit again for same bill is rejected by backend
        $dupResponse = $this->actingAs($this->resident1)->post(route('fees.pay'), [
            'fee_bill_id' => $this->bill1->id,
            'payment_method' => 'bank_transfer',
            'payment_date' => '2026-09-28',
            'proof_file' => UploadedFile::fake()->create('proof2.jpg', 200, 'image/jpeg'),
        ]);
        $dupResponse->assertSessionHasErrors(['fee_bill_id']);
    }

    public function test_treasurer_cross_rt_authorization(): void
    {
        $payment = Payment::create([
            'user_id' => $this->resident1->id,
            'payer_resident_id' => $this->res1->id,
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

        // Treasurer of RT 02 attempts to verify RT 01 payment -> 403 Forbidden
        $response = $this->actingAs($this->treasurerRt2)->post(route('fees.verify', $payment->id), [
            'decision' => 'verified',
        ]);
        $response->assertStatus(403);

        // Treasurer of RT 01 approves
        $approveRes = $this->actingAs($this->treasurerRt1)->post(route('fees.verify', $payment->id), [
            'decision' => 'verified',
        ]);
        $approveRes->assertRedirect(route('fees.verifications'));
        $approveRes->assertSessionHas('success');

        $this->bill1->refresh();
        $this->assertEquals('paid', $this->bill1->status);
    }

    public function test_treasurer_rejection_allows_resident_to_resubmit(): void
    {
        $payment = Payment::create([
            'user_id' => $this->resident1->id,
            'payer_resident_id' => $this->res1->id,
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

        // Treasurer rejects with reason
        $rejectRes = $this->actingAs($this->treasurerRt1)->post(route('fees.verify', $payment->id), [
            'decision' => 'rejected',
            'notes' => 'Nominal transfer kurang Rp 5.000',
        ]);
        $rejectRes->assertRedirect(route('fees.verifications'));

        // Resident sees rejection reason and 'Kirim Ulang Bukti' button
        $viewRes = $this->actingAs($this->resident1)->get(route('fees.index'));
        $viewRes->assertSee('Ditolak');
        $viewRes->assertSee('Nominal transfer kurang Rp 5.000');
        $viewRes->assertSee('Kirim Ulang Bukti');

        // Resident can resubmit
        $resubmitResponse = $this->actingAs($this->resident1)->post(route('fees.pay'), [
            'fee_bill_id' => $this->bill1->id,
            'payment_method' => 'bank_transfer',
            'payment_date' => '2026-09-29',
            'proof_file' => UploadedFile::fake()->create('correct_proof.jpg', 200, 'image/jpeg'),
            'notes' => 'Sudah ditransfer pas Rp 50.000',
        ]);
        $resubmitResponse->assertRedirect(route('fees.index'));
        $resubmitResponse->assertSessionHas('success');
    }
}
