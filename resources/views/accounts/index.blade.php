<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Manajemen Akun & Hak Akses') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-sm">
                <p class="font-semibold mb-1">Gagal menyimpan akun:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Add User Form -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Buat Akun Petugas/Warga</h3>
                <form action="{{ route('accounts.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nama Lengkap</label>
                        <input type="text" name="name" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Email</label>
                        <input type="email" name="email" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Password</label>
                        <input type="password" name="password" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div x-data="{ type: '{{ old('scope_type', 'global') }}' }" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Peran (Role)</label>
                            <select name="role_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('role_id') ? 'border-rose-300' : '' }}">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tipe Cakupan (Scope)</label>
                            <select name="scope_type" x-model="type" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('scope_type') ? 'border-rose-300' : '' }}">
                                <option value="global">Global (Super Admin)</option>
                                <option value="rw">Tingkat RW</option>
                                <option value="rt">Tingkat RT</option>
                            </select>
                            @error('scope_type')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <template x-if="type === 'rw'">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Wilayah RW</label>
                                <select name="scope_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('scope_id') ? 'border-rose-300' : '' }}">
                                    @foreach($rws as $rw)
                                        <option value="{{ $rw->id }}" @selected(old('scope_id') == $rw->id)>{{ $rw->name ?? 'RW #'.$rw->id }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </template>
                        <template x-if="type === 'rt'">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Wilayah RT</label>
                                <select name="scope_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('scope_id') ? 'border-rose-300' : '' }}">
                                    @foreach($rts as $rt)
                                        <option value="{{ $rt->id }}" @selected(old('scope_id') == $rt->id)>{{ ($rt->name ?? 'RT #'.$rt->id).' — '.($rt->rw->name ?? 'RW #'.($rt->rw_id ?? '-')) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </template>
                        @error('scope_id')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm transition">
                        Buat Akun
                    </button>
                </form>
            </div>

            <!-- List Users -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Daftar Pengguna Terdaftar</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="p-3">Nama / Email</th>
                                <th class="p-3">Role & Scope</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($users as $user)
                                <tr>
                                    <td class="p-3">
                                        <div class="font-semibold text-slate-900">{{ $user->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $user->email }}</div>
                                    </td>
                                    <td class="p-3">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($user->roles as $role)
                                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-xs font-medium border border-indigo-100">
                                                    {{ $role->name }} ({{ strtoupper($role->pivot->scope_type) }} #{{ $role->pivot->scope_id ?? '-' }})
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $user->status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('accounts.edit', $user) }}" class="px-2 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded text-xs font-semibold border border-indigo-100">
                                                Edit
                                            </a>
                                            <form action="{{ route('accounts.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus akun {{ $user->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded text-xs font-semibold border border-rose-100">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
