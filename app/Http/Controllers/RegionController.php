<?php

namespace App\Http\Controllers;

use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        $rws = Rw::with('rts')->get();

        return view('regions.index', compact('rws'));
    }

    public function storeRt(Request $request)
    {
        $validated = $request->validate([
            'rw_id' => 'required|exists:rws,id',
            'number' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        Rt::create($validated);

        return redirect()->route('regions.index')->with('success', 'RT berhasil ditambahkan.');
    }
}
