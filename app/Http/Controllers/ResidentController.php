<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Rt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isRtAdmin = false;
        $rtIdScope = null;

        foreach ($user->roles as $role) {
            if ($role->slug === 'rt-admin' && $role->pivot->scope_type === 'rt') {
                $isRtAdmin = true;
                $rtIdScope = $role->pivot->scope_id;
                break;
            }
        }

        $residentQuery = Resident::with(['family.rt', 'user']);
        $familyQuery = Family::with('rt');

        if ($isRtAdmin && $rtIdScope) {
            $residentQuery->whereHas('family', function ($q) use ($rtIdScope) {
                $q->where('rt_id', $rtIdScope);
            });
            $familyQuery->where('rt_id', $rtIdScope);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $residentQuery->where('full_name', 'like', "%{$search}%");
        }

        if ($request->filled('rt_id') && ! $isRtAdmin) {
            $rtId = $request->input('rt_id');
            $residentQuery->whereHas('family', function ($q) use ($rtId) {
                $q->where('rt_id', $rtId);
            });
            $familyQuery->where('rt_id', $rtId);
        }

        $residents = $residentQuery->latest()->paginate(15);
        $families = $familyQuery->get();
        $rts = $isRtAdmin && $rtIdScope ? Rt::where('id', $rtIdScope)->get() : Rt::all();

        return view('residents.index', compact('residents', 'families', 'rts', 'isRtAdmin', 'rtIdScope'));
    }

    public function storeFamily(Request $request)
    {
        $validated = $request->validate([
            'family_number' => 'required|string|unique:families,family_number_hash',
            'rt_id' => 'required|exists:rts,id',
            'address' => 'required|string',
            'status' => 'required|string',
        ]);

        Family::create($validated);

        return redirect()->route('residents.index')->with('success', 'Kartu Keluarga berhasil ditambahkan.');
    }

    private function authorizeRtScope($familyId = null, ?Resident $resident = null, ?Family $family = null)
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $isRtAdmin = false;
        $rtIdScope = null;
        foreach ($user->roles as $role) {
            if ($role->slug === 'rt-admin' && $role->pivot->scope_type === 'rt') {
                $isRtAdmin = true;
                $rtIdScope = $role->pivot->scope_id;
                break;
            }
        }

        if (! $isRtAdmin || ! $rtIdScope) {
            return;
        }

        if ($familyId) {
            $targetFamily = Family::find($familyId);
            if ($targetFamily && $targetFamily->rt_id !== $rtIdScope) {
                abort(403, 'Anda tidak memiliki akses ke Kartu Keluarga wilayah RT lain.');
            }
        }

        if ($family) {
            if ($family->rt_id !== $rtIdScope) {
                abort(403, 'Anda tidak memiliki akses ke Kartu Keluarga wilayah RT lain.');
            }
        }

        if ($resident) {
            $resident->load('family');
            if (! $resident->family || $resident->family->rt_id !== $rtIdScope) {
                abort(403, 'Anda tidak memiliki akses ke data warga wilayah RT lain.');
            }
        }
    }

    public function updateFamily(Request $request, Family $family)
    {
        $this->authorizeRtScope(null, null, $family);

        $validated = $request->validate([
            'family_number' => ['required', 'string', Rule::unique('families', 'family_number_hash')->ignore($family->id)],
            'rt_id' => 'required|exists:rts,id',
            'address' => 'required|string',
            'status' => 'required|string',
        ]);

        if (auth()->user()->roles()->where('slug', 'rt-admin')->where('pivot.scope_type', 'rt')->exists()) {
            $rtIdScope = auth()->user()->roles()->where('slug', 'rt-admin')->where('pivot.scope_type', 'rt')->first()->pivot->scope_id;
            if ($validated['rt_id'] != $rtIdScope) {
                abort(403, 'Anda tidak dapat memindahkan KK ke RT lain.');
            }
        }

        $family->update($validated);

        return redirect()->route('residents.index')->with('success', 'Kartu Keluarga berhasil diperbarui.');
    }

    public function destroyFamily(Family $family)
    {
        $this->authorizeRtScope(null, null, $family);

        if ($family->residents()->count() > 0) {
            return redirect()->route('residents.index')->with('error', 'Tidak dapat menghapus KK yang masih memiliki anggota warga.');
        }

        $family->delete();

        return redirect()->route('residents.index')->with('success', 'Kartu Keluarga berhasil dihapus.');
    }

    public function create()
    {
        return view('residents.create');
    }

    public function searchFamily(Request $request)
    {
        $query = $request->input('q');
        if (empty($query)) {
            return response()->json([]);
        }

        $families = Family::with('rt')->get();
        $matched = [];

        foreach ($families as $family) {
            if ($family->family_number && str_contains($family->family_number, $query)) {
                $matched[] = [
                    'id' => $family->id,
                    'family_number' => $family->family_number,
                    'rt_name' => $family->rt->name ?? '-',
                    'address' => $family->address,
                ];
            }
        }

        return response()->json(array_slice($matched, 0, 10));
    }

    public function store(Request $request)
    {
        $nikHash = hash('sha256', $request->input('nik'));
        $request->merge(['nik_hash' => $nikHash]);

        $validated = $request->validate([
            'family_id' => 'required|exists:families,id',
            'nik' => 'required|string|size:16',
            'nik_hash' => 'required|unique:residents,nik_hash',
            'full_name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:L,P',
            'religion' => 'nullable|string|max:50',
            'occupation' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|string',
            'verification_status' => 'required|string',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ], [
            'family_id.required' => 'Nomor KK tidak ditemukan. Silakan pilih KK yang sudah terdaftar atau tambahkan data KK terlebih dahulu melalui menu Data Warga & KK.',
            'family_id.exists' => 'Nomor KK tidak ditemukan. Silakan pilih KK yang sudah terdaftar atau tambahkan data KK terlebih dahulu melalui menu Data Warga & KK.',
            'nik_hash.unique' => 'NIK sudah terdaftar dalam sistem.',
            'nik.size' => 'NIK harus berjumlah 16 digit.',
            'email.unique' => 'Email akun login sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        DB::transaction(function () use ($validated) {
            $resident = Resident::create([
                'family_id' => $validated['family_id'],
                'nik' => $validated['nik'],
                'nik_hash' => $validated['nik_hash'],
                'full_name' => $validated['full_name'],
                'birth_place' => $validated['birth_place'],
                'birth_date' => $validated['birth_date'],
                'gender' => $validated['gender'],
                'religion' => $validated['religion'] ?? null,
                'occupation' => $validated['occupation'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'status' => $validated['status'],
                'verification_status' => $validated['verification_status'],
            ]);

            $user = User::create([
                'resident_id' => $resident->id,
                'name' => $resident->full_name,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $resident->phone,
                'status' => 'active',
            ]);

            $roleResident = Role::where('slug', 'resident')->first();
            if ($roleResident) {
                $family = Family::with('rt')->find($resident->family_id);
                $rtId = $family ? $family->rt_id : null;
                $user->roles()->attach($roleResident->id, [
                    'scope_type' => $rtId ? 'rt' : 'global',
                    'scope_id' => $rtId,
                ]);
            }
        });

        return redirect()->route('residents.index')->with('success', 'Data warga dan akun login berhasil ditambahkan.');
    }

    public function show(Resident $resident)
    {
        $this->authorizeRtScope(null, $resident);

        return view('residents.show', compact('resident'));
    }

    public function edit(Resident $resident)
    {
        $this->authorizeRtScope(null, $resident);

        $families = Family::with('rt')->get();

        return view('residents.edit', compact('resident', 'families'));
    }

    public function update(Request $request, Resident $resident)
    {
        $this->authorizeRtScope($request->input('family_id'), $resident);

        $validated = $request->validate([
            'family_id' => 'required|exists:families,id',
            'full_name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:L,P',
            'religion' => 'nullable|string|max:50',
            'occupation' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|string',
            'verification_status' => 'required|string',
        ]);

        $resident->update($validated);

        return redirect()->route('residents.index')->with('success', 'Data warga berhasil diperbarui.');
    }

    public function destroy(Resident $resident)
    {
        $this->authorizeRtScope(null, $resident);

        $resident->delete();

        return redirect()->route('residents.index')->with('success', 'Data warga berhasil dihapus.');
    }
}
