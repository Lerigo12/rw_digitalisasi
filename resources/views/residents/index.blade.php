<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Manajemen Data Warga') }}
        </h2>
    </x-slot>

    <div x-data="{ openCreateFamily: false, editFamilyData: null }" class="space-y-6">
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
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
            <form method="GET" action="{{ route('residents.index') }}" class="flex items-center space-x-3 w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama warga..." class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <select name="rt_id" class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua RT</option>
                    @foreach($rts as $rt)
                        <option value="{{ $rt->id }}" {{ request('rt_id') == $rt->id ? 'selected' : '' }}>{{ $rt->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold hover:bg-slate-700">Filter</button>
            </form>

            <div class="flex items-center gap-2">
                <button type="button" @click="openCreateFamily = true" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah KK
                </button>
                <a href="{{ route('residents.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Warga
                </a>
            </div>
        </div>

        <!-- Modal Tambah KK -->
        <div x-show="openCreateFamily" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;">
            <div class="bg-white rounded-xl max-w-lg w-full p-6 space-y-4 shadow-xl">
                <h3 class="font-bold text-lg text-slate-900">Tambah Kartu Keluarga (KK)</h3>
                <form action="{{ route('families.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nomor KK</label>
                        <input type="text" name="family_number" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Wilayah RT</label>
                        <select name="rt_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Pilih RT</option>
                            @foreach($rts as $rt)
                                <option value="{{ $rt->id }}">{{ $rt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Alamat</label>
                        <textarea name="address" rows="2" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="openCreateFamily = false" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs">Batal</button>
                        <button type="submit" class="py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg text-xs">Simpan KK</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit KK -->
        <div x-show="editFamilyData" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;">
            <div class="bg-white rounded-xl max-w-lg w-full p-6 space-y-4 shadow-xl" x-data="{ form: { id: '', family_number: '', rt_id: '', address: '', status: 'active' } }" @open-edit-family.window="form = $event.detail">
                <h3 class="font-bold text-lg text-slate-900">Edit Kartu Keluarga (KK)</h3>
                <form :action="'/families/' + form.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nomor KK</label>
                        <input type="text" name="family_number" x-model="form.family_number" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Wilayah RT</label>
                        <select name="rt_id" x-model="form.rt_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Pilih RT</option>
                            @foreach($rts as $rt)
                                <option value="{{ $rt->id }}">{{ $rt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Alamat</label>
                        <textarea name="address" x-model="form.address" rows="2" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Status</label>
                        <select name="status" x-model="form.status" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="editFamilyData = null" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs">Batal</button>
                        <button type="submit" class="py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-xs">Simpan Perubahan</>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Daftar KK -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-slate-900 text-base">Daftar Kartu Keluarga (KK)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Nomor KK</th>
                            <th class="p-3">RT</th>
                            <th class="p-3">Alamat</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-right">Aksi KK</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($families as $family)
                            <tr>
                                <td class="p-3 font-mono font-bold text-slate-900">{{ $family->family_number }}</td>
                                <td class="p-3">{{ $family->rt->name ?? '-' }}</td>
                                <td class="p-3">{{ $family->address }}</td>
                                <td class="p-3"><span class="px-2 py-0.5 rounded text-xs font-semibold {{ $family->status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">{{ ucfirst($family->status) }}</span></td>
                                <td class="p-3 text-right space-x-2">
                                    <button type="button" @click="editFamilyData = { id: '{{ $family->id }}', family_number: '{{ $family->family_number }}', rt_id: '{{ $family->rt_id }}', address: '{{ addslashes($family->address) }}', status: '{{ $family->status }}' }" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs">Edit KK</button>
                                    <form action="{{ route('families.destroy', $family->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus Kartu Keluarga ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-xs">Hapus KK</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-slate-400">Belum ada data Kartu Keluarga tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="p-4">Nama Lengkap / NIK</th>
                        <th class="p-4">RT / KK</th>
                        <th class="p-4">JK / TTL</th>
                        <th class="p-4">Status & Verifikasi</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($residents as $resident)
                        <tr>
                            <td class="p-4">
                                <div class="font-semibold text-slate-900">{{ $resident->full_name }}</div>
                                <div class="text-xs text-slate-400">NIK: {{ substr($resident->nik, 0, 6) }}********</div>
                            </td>
                            <td class="p-4">
                                <div class="text-slate-800">{{ $resident->family->rt->name ?? '-' }}</div>
                                <div class="text-xs text-slate-400">KK ID: {{ $resident->family_id }}</div>
                            </td>
                            <td class="p-4">
                                <div>{{ $resident->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                <div class="text-xs text-slate-400">{{ $resident->birth_place }}, {{ $resident->birth_date }}</div>
                            </td>
                            <td class="p-4">
                                <div class="space-y-1">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $resident->status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                        {{ ucfirst($resident->status) }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $resident->verification_status === 'verified' ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600' }}">
                                        {{ ucfirst($resident->verification_status) }}
                                    </span>
                                </div>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <a href="{{ route('residents.edit', $resident->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs">Edit</a>
                                <form action="{{ route('residents.destroy', $resident->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus data warga ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-xs">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400">Tidak ada data warga ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-100">
                {{ $residents->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
