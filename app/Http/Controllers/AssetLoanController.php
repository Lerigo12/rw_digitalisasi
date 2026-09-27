<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\AssetLoanReturn;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetLoanController extends Controller
{
    public function index()
    {
        $loans = AssetLoan::with(['asset', 'borrowerResident', 'approver', 'returnRecord'])->latest()->paginate(15);

        return view('asset_loans.index', compact('loans'));
    }

    public function create()
    {
        $assets = Asset::all();
        $residents = Resident::where('status', 'active')->limit(200)->get(); // demo limit

        return view('asset_loans.create', compact('assets', 'residents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'borrower_resident_id' => 'required|exists:residents,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:active,returned',
        ]);

        DB::transaction(function () use ($validated) {
            $loan = AssetLoan::create([
                'asset_id' => $validated['asset_id'],
                'borrower_resident_id' => $validated['borrower_resident_id'],
                'quantity' => $validated['quantity'],
                'status' => $validated['status'],
                'approved_by' => Auth::id(),
            ]);

            if ($validated['status'] === 'returned') {
                AssetLoanReturn::create([
                    'asset_loan_id' => $loan->id,
                    'returned_quantity' => $validated['quantity'],
                    'condition_after' => 'good',
                    'received_by' => Auth::id(),
                    'returned_at' => now(),
                ]);
            }
        });

        return redirect()->route('asset_loans.index')->with('success', 'Peminjaman inventaris diproses.');
    }
}
