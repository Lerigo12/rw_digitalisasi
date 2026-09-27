<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\CashCategory;
use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashTransactionController extends Controller
{
    public function index()
    {
        $accounts = CashAccount::where('is_active', true)->get();
        $categories = CashCategory::where('is_active', true)->get();
        $transactions = CashTransaction::with(['cashAccount', 'category'])
            ->latest('transaction_date')
            ->paginate(15);

        $totalBalance = CashTransaction::where('status', 'approved')
            ->selectRaw('sum(case when transaction_type = "income" then amount else -amount end) as total')
            ->value('total') ?? 0;

        return view('cash_transactions.index', compact('accounts', 'categories', 'transactions', 'totalBalance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'category_id' => 'nullable|exists:cash_categories,id',
            'transaction_type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
        ]);

        $categoryId = $validated['category_id'] ?? null;
        if (! $categoryId) {
            $defaultCat = CashCategory::first();
            if (! $defaultCat) {
                $defaultCat = CashCategory::create([
                    'name' => 'Umum',
                    'transaction_type' => $validated['transaction_type'],
                    'scope_type' => 'rw',
                    'scope_id' => 1,
                    'is_active' => true,
                ]);
            }
            $categoryId = $defaultCat->id;
        }

        CashTransaction::create([
            'cash_account_id' => $validated['cash_account_id'],
            'category_id' => $categoryId,
            'transaction_type' => $validated['transaction_type'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'description' => $validated['description'],
            'status' => 'approved',
            'approved_at' => now(),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('cash_transactions.index')->with('success', 'Transaksi kas berhasil dicatat.');
    }
}
