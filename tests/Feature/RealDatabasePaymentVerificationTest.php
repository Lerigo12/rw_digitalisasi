<?php

namespace Tests\Feature;

use App\Models\FeeBill;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RealDatabasePaymentVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_bank_transfer_and_qris_processing_and_verification(): void
    {
        Storage::fake('public');

        $userAchmad = User::where('email', 'achmad@test.com')->first();
        if (! $userAchmad) {
            $this->markTestSkipped('No achmad@test.com user found for real database test.');
        }

        $bill = FeeBill::where('resident_id', $userAchmad->resident_id)->first();
        if (! $bill) {
            $this->markTestSkipped('No bill found for test.');
        }

        $bill->update(['status' => 'unpaid']);
        Payment::whereHas('allocations', fn ($q) => $q->where('fee_bill_id', $bill->id))->delete();

        // 1. Submit Bank Transfer
        $fileBank = UploadedFile::fake()->create('transfer_bca.jpg', 300, 'image/jpeg');
        $responseBank = $this->actingAs($userAchmad)->post(route('fees.pay'), [
            'fee_bill_id' => $bill->id,
            'payment_method' => 'bank_transfer',
            'payment_date' => '2026-09-27',
            'proof_file' => $fileBank,
            'notes' => 'Transfer Bank BCA',
        ]);

        $responseBank->assertRedirect(route('fees.index'));

        $paymentBank = Payment::whereHas('allocations', fn ($q) => $q->where('fee_bill_id', $bill->id))
            ->where('status', 'pending')
            ->latest()
            ->first();

        $this->assertNotNull($paymentBank);
        $this->assertEquals('bank_transfer', $paymentBank->payment_method);
        $this->assertDatabaseHas('payment_proofs', ['payment_id' => $paymentBank->id]);

        // Check if treasurer (scope RT 01 or global) can see it
        $treasurer = User::whereHas('roles', fn ($q) => $q->where('slug', 'bendahara-rt'))->first() ?? User::find(1);
        $verifPage = $this->actingAs($treasurer)->get(route('fees.verifications'));
        $verifPage->assertStatus(200);

        // Treasurer approves bank transfer
        $approveRes = $this->actingAs($treasurer)->post(route('fees.verify', $paymentBank->id), [
            'decision' => 'verified',
        ]);
        $approveRes->assertRedirect(route('fees.verifications'));

        $bill->refresh();
        $this->assertEquals('paid', $bill->status);

        // Reset for QRIS test
        $bill->update(['status' => 'unpaid']);

        // 2. Submit QRIS
        $fileQris = UploadedFile::fake()->create('qris_scan.png', 400, 'image/png');
        $responseQris = $this->actingAs($userAchmad)->post(route('fees.pay'), [
            'fee_bill_id' => $bill->id,
            'payment_method' => 'qris',
            'payment_date' => '2026-09-27',
            'proof_file' => $fileQris,
            'notes' => 'Pembayaran via QRIS',
        ]);

        $responseQris->assertRedirect(route('fees.index'));

        $paymentQris = Payment::whereHas('allocations', fn ($q) => $q->where('fee_bill_id', $bill->id))
            ->where('status', 'pending')
            ->latest()
            ->first();

        $this->assertNotNull($paymentQris);
        $this->assertEquals('qris', $paymentQris->payment_method);
        $this->assertDatabaseHas('payment_proofs', ['payment_id' => $paymentQris->id]);

        // Treasurer rejects QRIS with reason
        $rejectRes = $this->actingAs($treasurer)->post(route('fees.verify', $paymentQris->id), [
            'decision' => 'rejected',
            'notes' => 'QRIS screenshot buram dan tidak terbaca',
        ]);
        $rejectRes->assertRedirect(route('fees.verifications'));

        $paymentQris->refresh();
        $this->assertEquals('rejected', $paymentQris->status);
        $this->assertEquals('QRIS screenshot buram dan tidak terbaca', $paymentQris->rejection_reason);

        // Resident sees rejection on index page
        $residentView = $this->actingAs($userAchmad)->get(route('fees.index'));
        $residentView->assertStatus(200);
        $residentView->assertSee('Ditolak');
        $residentView->assertSee('QRIS screenshot buram dan tidak terbaca');
        $residentView->assertSee('Kirim Ulang Bukti');
    }
}
