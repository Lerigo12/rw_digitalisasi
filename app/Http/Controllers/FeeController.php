<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Models\FeeBill;
use App\Models\FeeType;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\PaymentProof;
use App\Models\PaymentVerification;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $feeTypes = FeeType::all();

        // Check if user is pure resident or staff
        $isStaff = $user->hasRole('super-admin') || $user->hasRole('rw-admin') || $user->hasRole('rt-admin') || $user->hasRole('bendahara-rt');

        $rtIds = [];
        if ($isStaff) {
            // Determine RT scope for bendahara-rt or rt-admin
            if ($user->hasRole('super-admin') || $user->hasRole('rw-admin')) {
                $residents = Resident::with('family.rt')->where('status', 'active')->get();
                $bills = FeeBill::with(['feeType', 'resident.family.rt', 'payments' => function ($q) {
                    $q->latest();
                }, 'payments.proofs'])->latest()->paginate(15);
            } else {
                foreach ($user->roles as $role) {
                    if (in_array($role->slug, ['rt-admin', 'bendahara-rt']) && $role->pivot->scope_type === 'rt') {
                        $rtIds[] = $role->pivot->scope_id;
                    }
                }
                $residents = Resident::with('family.rt')->where('status', 'active')->whereHas('family', function ($q) use ($rtIds) {
                    $q->whereIn('rt_id', $rtIds);
                })->get();

                $bills = FeeBill::with(['feeType', 'resident.family.rt', 'payments' => function ($q) {
                    $q->latest();
                }, 'payments.proofs'])->whereHas('resident.family', function ($q) use ($rtIds) {
                    $q->whereIn('rt_id', $rtIds);
                })->latest()->paginate(15);
            }
        } else {
            // Only resident bills
            $bills = FeeBill::with(['feeType', 'payments' => function ($q) {
                $q->latest();
            }, 'payments.proofs'])
                ->where('resident_id', $user->resident_id)
                ->latest()
                ->paginate(15);
            $residents = collect();
        }

        $rws = Rw::all();
        $rts = Rt::all();

        return view('fees.index', compact('feeTypes', 'bills', 'isStaff', 'rws', 'rts', 'residents'));
    }

    public function storeType(Request $request)
    {
        $validated = $request->validate([
            'scope_type' => 'required|in:rw,rt',
            'scope_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'period_type' => 'required|string',
            'due_day' => 'required|integer|min:1|max:31',
            'allowed_payment_methods' => 'required|array|min:1',
            'allowed_payment_methods.*' => 'in:bank_transfer,qris,manual',
            'bank_name' => 'required_if:allowed_payment_methods,bank_transfer|nullable|string|max:255',
            'bank_account_number' => 'required_if:allowed_payment_methods,bank_transfer|nullable|string|max:50',
            'bank_account_name' => 'required_if:allowed_payment_methods,bank_transfer|nullable|string|max:255',
            'qris_image' => 'required_if:allowed_payment_methods,qris|nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $validated;
        unset($data['allowed_payment_methods']);
        $data['allowed_payment_methods'] = $validated['allowed_payment_methods'];
        unset($data['qris_image']);
        // Handle QRIS image upload
        if ($request->hasFile('qris_image')) {
            $data['qris_image_path'] = $request->file('qris_image')->store('qris_images', 'public');
        }

        // Ensure bank fields are null when not selected
        if (! in_array('bank_transfer', $validated['allowed_payment_methods'])) {
            $data['bank_name'] = null;
            $data['bank_account_number'] = null;
            $data['bank_account_name'] = null;
        }
        // Ensure QRIS path null when not selected
        if (! in_array('qris', $validated['allowed_payment_methods'])) {
            $data['qris_image_path'] = null;
        }

        FeeType::create($data);

        return redirect()->route('fees.index')->with('success', 'Jenis iuran berhasil dikonfigurasi.');
    }

    public function generateBills(Request $request)
    {
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'fee_type_id' => 'required|exists:fee_types,id',
            'period_key' => 'required|string', // e.g. "2026-09"
            'amount_due' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();
        $resident = Resident::with('family.rt')->findOrFail($validated['resident_id']);

        // Check active status
        if ($resident->status !== 'active') {
            return back()->withErrors(['resident_id' => 'Warga yang dipilih tidak aktif.']);
        }

        // Enforce RT scope limitation for bendahara-rt / rt-admin
        if (! $user->hasRole('super-admin') && ! $user->hasRole('rw-admin')) {
            $allowedRtIds = [];
            foreach ($user->roles as $role) {
                if (in_array($role->slug, ['rt-admin', 'bendahara-rt']) && $role->pivot->scope_type === 'rt') {
                    $allowedRtIds[] = $role->pivot->scope_id;
                }
            }

            if (! in_array($resident->family->rt_id, $allowedRtIds)) {
                abort(403, 'Anda tidak memiliki kewenangan membuat tagihan untuk warga RT lain.');
            }
        }

        $feeType = FeeType::findOrFail($validated['fee_type_id']);
        $amount = $validated['amount_due'] ?? $feeType->amount;

        // Check duplication
        $exists = FeeBill::where('fee_type_id', $feeType->id)
            ->where('resident_id', $resident->id)
            ->where('period_key', $validated['period_key'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['period_key' => 'Warga ini sudah memiliki tagihan untuk jenis iuran dan periode tersebut.'])->withInput();
        }

        FeeBill::create([
            'fee_type_id' => $feeType->id,
            'resident_id' => $resident->id,
            'period_key' => $validated['period_key'],
            'amount_due' => $amount,
            'due_date' => now()->setDate(now()->year, now()->month, min($feeType->due_day, 28)),
            'status' => 'unpaid',
        ]);

        return redirect()->route('fees.index')->with('success', 'Tagihan iuran berhasil diterbitkan untuk warga yang dipilih.');
    }

    public function verifications()
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole('super-admin') || $user->hasRole('rw-admin');

        $query = Payment::with(['user', 'payerResident.family.rt', 'allocations.feeBill.feeType', 'proofs', 'verification']);

        if (! $isGlobalAdmin) {
            $rtIds = [];
            foreach ($user->roles as $role) {
                if (in_array($role->slug, ['rt-admin', 'bendahara-rt']) && $role->pivot->scope_type === 'rt') {
                    $rtIds[] = $role->pivot->scope_id;
                }
            }
            $query->whereHas('payerResident.family', function ($q) use ($rtIds) {
                $q->whereIn('rt_id', $rtIds);
            });
        }

        $payments = $query->latest()->paginate(15);

        return view('fees.verifications', compact('payments'));
    }

    public function pay(Request $request)
    {
        $validated = $request->validate([
            'fee_bill_id' => 'required|exists:fee_bills,id',
            'payment_method' => 'required|in:bank_transfer,qris,manual',
            'payment_date' => 'required|date',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $bill = FeeBill::with('feeType')->findOrFail($validated['fee_bill_id']);

        // Check allowed payment methods for fee type
        $allowedMethods = $bill->feeType->allowed_payment_methods ?? ['bank_transfer', 'qris', 'manual'];
        if (! in_array($validated['payment_method'], $allowedMethods)) {
            return back()->withErrors(['payment_method' => 'Metode pembayaran yang dipilih tidak diizinkan untuk jenis iuran ini.']);
        }

        // Authorization check: bill must belong to logged-in resident
        if (! $user->hasRole('super-admin') && ! $user->hasRole('rw-admin') && ! $user->hasRole('rt-admin') && ! $user->hasRole('bendahara-rt')) {
            if ($bill->resident_id !== $user->resident_id) {
                abort(403, 'Anda tidak memiliki kewenangan membayar tagihan warga lain.');
            }
        }

        // Check if bill is already paid
        if ($bill->status === 'paid') {
            return back()->withErrors(['fee_bill_id' => 'Tagihan ini sudah lunas.']);
        }

        // Check if there is already a pending payment for this bill
        $hasPending = Payment::whereHas('allocations', function ($q) use ($bill) {
            $q->where('fee_bill_id', $bill->id);
        })->where('status', 'pending')->exists();

        if ($hasPending) {
            return back()->withErrors(['fee_bill_id' => 'Tagihan ini sudah memiliki pengajuan pembayaran yang sedang menunggu verifikasi.']);
        }

        DB::transaction(function () use ($validated, $bill, $request, $user) {
            $path = null;
            if ($request->hasFile('proof_file')) {
                $path = $request->file('proof_file')->store('payment_proofs', 'public');
            }

            $payment = Payment::create([
                'user_id' => $user->id,
                'payer_resident_id' => $bill->resident_id,
                'payment_date' => $validated['payment_date'],
                'total_amount' => $bill->amount_due,
                'method' => 'manual_transfer',
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
                'notes' => $validated['notes'],
            ]);

            PaymentAllocation::create([
                'payment_id' => $payment->id,
                'fee_bill_id' => $bill->id,
                'allocated_amount' => $bill->amount_due,
            ]);

            if ($path) {
                PaymentProof::create([
                    'payment_id' => $payment->id,
                    'file_path' => $path,
                    'original_filename' => $request->file('proof_file')->getClientOriginalName(),
                    'mime_type' => $request->file('proof_file')->getClientMimeType(),
                    'file_size' => $request->file('proof_file')->getSize(),
                    'uploaded_by' => $user->id,
                    'uploaded_at' => now(),
                ]);
            }
        });

        return redirect()->route('fees.index')->with('success', 'Pembayaran berhasil diajukan dan sedang menunggu verifikasi petugas.');
    }

    public function verifyPayment(Request $request, Payment $payment)
    {
        $user = Auth::user();
        $isGlobalAdmin = $user->hasRole('super-admin') || $user->hasRole('rw-admin');

        if (! $isGlobalAdmin) {
            $rtIds = [];
            foreach ($user->roles as $role) {
                if (in_array($role->slug, ['rt-admin', 'bendahara-rt']) && $role->pivot->scope_type === 'rt') {
                    $rtIds[] = $role->pivot->scope_id;
                }
            }

            $payment->load('payerResident.family');
            if (! $payment->payerResident || ! in_array($payment->payerResident->family->rt_id, $rtIds)) {
                abort(403, 'Anda tidak memiliki kewenangan memverifikasi pembayaran warga RT lain.');
            }
        }

        $validated = $request->validate([
            'decision' => 'required|in:verified,rejected',
            'notes' => 'required_if:decision,rejected|nullable|string|max:1000',
        ], [
            'notes.required_if' => 'Alasan penolakan wajib diisi ketika menolak pembayaran.',
        ]);

        if ($payment->status !== 'pending') {
            return back()->withErrors(['decision' => 'Pembayaran ini sudah diproses sebelumnya.']);
        }

        DB::transaction(function () use ($validated, $payment) {
            $status = $validated['decision'] === 'verified' ? 'verified' : 'rejected';
            $rejectionReason = $status === 'rejected' ? trim($validated['notes']) : null;

            $payment->update([
                'status' => $status,
                'rejection_reason' => $rejectionReason,
            ]);

            PaymentVerification::create([
                'payment_id' => $payment->id,
                'verified_by' => Auth::id(),
                'decision' => $status,
                'notes' => $validated['notes'] ?? null,
                'verified_at' => now(),
            ]);

            if ($status === 'verified') {
                foreach ($payment->allocations as $alloc) {
                    $alloc->feeBill->update([
                        'status' => 'paid',
                        'settled_at' => now(),
                    ]);
                }

                // Create CashTransaction if not already created for this payment
                $existingTrx = CashTransaction::where('description', 'like', '%Payment #'.$payment->id.'%')->first();
                if (! $existingTrx) {
                    $cashAccount = CashAccount::first();
                    if ($cashAccount) {
                        $category = CashCategory::where('transaction_type', 'income')->first();
                        if (! $category) {
                            $category = CashCategory::create([
                                'scope_type' => 'rw',
                                'scope_id' => 1,
                                'name' => 'Iuran Warga',
                                'transaction_type' => 'income',
                                'is_active' => true,
                            ]);
                        }

                        CashTransaction::create([
                            'cash_account_id' => $cashAccount->id,
                            'category_id' => $category->id,
                            'transaction_type' => 'income',
                            'amount' => $payment->total_amount,
                            'transaction_date' => now()->toDateString(),
                            'description' => 'Penerimaan Iuran Warga (Payment #'.$payment->id.')',
                            'status' => 'approved',
                            'approved_at' => now(),
                            'created_by' => Auth::id() ?? 1,
                        ]);
                    }
                }
            }

            $userToNotify = $payment->user ?? optional($payment->payerResident)->user;
            if ($userToNotify) {
                $userToNotify->notify(new GeneralNotification(
                    'Verifikasi Pembayaran Iuran',
                    $status === 'verified' ? 'Pembayaran iuran Anda telah berhasil diverifikasi.' : 'Pembayaran iuran Anda ditolak: '.($validated['notes'] ?? '-'),
                    'fee_payment',
                    route('fees.index')
                ));
            }
        });

        $msg = $validated['decision'] === 'verified' ? 'Pembayaran berhasil disetujui.' : 'Pembayaran ditolak. Alasan penolakan telah disimpan.';

        return redirect()->route('fees.verifications')->with('success', $msg);
    }
}
