<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Daftar Inventaris & Aset') }}
        </h2>
    </x-slot>

    <div class="space-y-6"
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Inventaris & Aset</h3>
                <p class="text-xs text-slate-500 mt-1">Data barang tetap, perlengkapan, dan aset milik RW/RT.</p>
            </div>
            <a href="{{ route('assets.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Inventaris
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="p-4">Kode Aset</th>
                        <th class="p-4">Nama / Jumlah</th>
                        <th class="p-4">Lokasi / Kondisi</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($assets as $asset)
                        <tr>
                            <td class="p-4 font-mono text-xs">{{ $asset->asset_code }}</td>
                            <td class="p-4">
                                <div class="font-semibold text-slate-900">{{ $asset->name }}</div>
                                <div class="text-xs text-slate-500">Qty: {{ $asset->quantity }}</div>
                            </td>
                            <td class="p-4">
                                <div class="text-slate-800">{{ $asset->location ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $asset->condition }}</div>
                            </td>
                            <td class="p-4"
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $asset->status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">{{ ucfirst($asset->status) }}</span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <a href="{{ route('assets.show', $asset->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400">Belum ada inventaris tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-100">
                {{ $assets->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
