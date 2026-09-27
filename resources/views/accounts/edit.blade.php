<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Edit Akun') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-sm">
                <p class="font-semibold mb-1">Gagal memperbarui akun:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="max-w-xl bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Ubah Akun: {{ $user->name }}</h3>
            <form action="{{ route('accounts.update', $user) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('name') ? 'border-rose-300' : '' }}" required>
                    @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('email') ? 'border-rose-300' : '' }}" required>
                    @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Password (kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('password') ? 'border-rose-300' : '' }}">
                    @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div x-data="{ type: '{{ old('scope_type', $user->roles->first()?->pivot->scope_type ?? 'global') }}' }" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Peran (Role)</label>
                        <select name="role_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('role_id') ? 'border-rose-300' : '' }}">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @selected(old('role_id', $user->roles->first()?->id) == $role->id)>{{ $role->name }}</option>
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
                                    <option value="{{ $rw->id }}" @selected(old('scope_id', $user->roles->first()?->pivot->scope_id) == $rw->id)>{{ $rw->name ?? 'RW #'.$rw->id }}</option>
                                @endforeach
                            </select>
                        </div>
                    </template>
                    <template x-if="type === 'rt'">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Wilayah RT</label>
                            <select name="scope_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('scope_id') ? 'border-rose-300' : '' }}">
                                @foreach($rts as $rt)
                                    <option value="{{ $rt->id }}" @selected(old('scope_id', $user->roles->first()?->pivot->scope_id) == $rt->id)>{{ ($rt->name ?? 'RT #'.$rt->id).' — '.($rt->rw->name ?? 'RW #'.($rt->rw_id ?? '-')) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </template>
                    @error('scope_id')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm transition">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('accounts.index') }}" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-sm transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
