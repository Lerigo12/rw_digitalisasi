<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with(['loans', 'rt', 'rw'])->latest()->paginate(15);

        return view('assets.index', compact('assets'));
    }

    public function create()
    {
        $rws = Rw::all();
        $rts = Rt::all();

        return view('assets.create', compact('rws', 'rts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rw_id' => 'nullable|exists:rws,id',
            'rt_id' => 'nullable|exists:rts,id',
            'asset_code' => 'required|string|unique:assets,asset_code',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|string',
            'storage_location' => 'nullable|string',
        ]);

        Asset::create($validated);

        return redirect()->route('assets.index')->with('success', 'Inventaris baru berhasil ditambahkan.');
    }

    public function show(Asset $asset)
    {
        $asset->load(['loans.borrowerResident', 'loans.approver', 'loans.returnRecord']);

        return view('assets.show', compact('asset'));
    }
}
