<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(10);
        $roles = Role::all();
        $rws = Rw::all();
        $rts = Rt::with('rw')->get();

        return view('accounts.index', compact('users', 'roles', 'rws', 'rts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'scope_type' => 'required|in:global,rw,rt',
            'scope_id' => 'nullable|required_if:scope_type,rw,rt|integer',
        ]);

        if (in_array($validated['scope_type'], ['rw', 'rt'])) {
            $request->validate(['scope_id' => Rule::exists($validated['scope_type'].'s', 'id')]);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $user->roles()->attach($validated['role_id'], [
            'scope_type' => $validated['scope_type'],
            'scope_id' => $validated['scope_id'] ?? null,
        ]);

        return redirect()->route('accounts.index')->with('success', 'Akun pengguna berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $this->authorizeSuperAdmin();

        $roles = Role::all();
        $rws = Rw::all();
        $rts = Rt::with('rw')->get();

        return view('accounts.edit', compact('user', 'roles', 'rws', 'rts'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'scope_type' => 'required|in:global,rw,rt',
            'scope_id' => 'nullable|required_if:scope_type,rw,rt|integer',
        ]);

        if (in_array($validated['scope_type'], ['rw', 'rt'])) {
            $request->validate(['scope_id' => Rule::exists($validated['scope_type'].'s', 'id')]);
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            ...(isset($validated['password']) ? ['password' => Hash::make($validated['password'])] : []),
        ]);

        $user->roles()->sync([
            $validated['role_id'] => [
                'scope_type' => $validated['scope_type'],
                'scope_id' => $validated['scope_id'] ?? null,
            ],
        ]);

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorizeSuperAdmin();

        if ($user->id === auth()->id()) {
            return redirect()->route('accounts.index')->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil dihapus.');
    }

    private function authorizeSuperAdmin(): void
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);
    }
}
