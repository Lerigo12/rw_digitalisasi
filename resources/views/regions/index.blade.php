<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Manajemen Wilayah RW & RT') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Add RT Form -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Tambah RT Baru</h3>
                <form action="{{ route('regions.rt.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Pilih RW</label>
                        <select name="rw_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($rws as $rw)
                                <option value="{{ $rw->id }}">{{ $rw->name }} ({{ $rw->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nomor RT</label>
                        <input type="text" name="number" placeholder="Contoh: 003" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nama / Keterangan RT</label>
                        <input type="text" name="name" placeholder="Contoh: RT 003 / RW 01" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Alamat / Wilayah</label>
                        <textarea name="address" rows="2" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Wilayah RT..."></textarea>
                    </div>
                    <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm transition">
                        Simpan RT Baru
                    </button>
                </form>
            </div>

            <!-- List of Regions -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Daftar Wilayah RW dan RT</h3>
                <div class="space-y-6">
                    @foreach($rws as $rw)
                        <div class="border border-slate-200 rounded-xl p-4">
                            <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                                <div>
                                    <h4 class="font-bold text-slate-900">{{ $rw->name }} <span class="text-xs px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-full font-semibold">{{ $rw->code }}</span></h4>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $rw->address }}</p>
                                </div>
                                <span class="text-xs font-semibold text-slate-600">{{ $rw->rts->count() }} RT Terdaftar</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                                @forelse($rw->rts as $rt)
                                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 flex items-center justify-between">
                                        <div>
                                            <div class="font-semibold text-sm text-slate-800">{{ $rt->name }} (No: {{ $rt->number }})</div>
                                            <div class="text-xs text-slate-500">{{ $rt->address ?: 'Alamat belum diisi' }}</div>
                                        </div>
                                        <span class="w-2.5 h-2.5 rounded-full {{ $rt->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}" title="{{ $rt->is_active ? 'Aktif' : 'Nonaktif' }}"></span>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 italic col-span-2">Belum ada RT terdaftar pada RW ini.</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
