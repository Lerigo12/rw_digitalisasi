<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Data Barang Inventaris') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Daftar Barang Milik RW 29</h3>
                <p class="text-xs text-slate-500 mt-1">Kelola dan pantau seluruh inventaris dan aset RW 29.</p>
            </div>
            <a href="{{ route('inventories.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Barang
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-4">Kode</th>
                            <th class="p-4">Nama Barang</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4">Jumlah</th>
                            <th class="p-4">Kondisi</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($inventories as $inv)
                            <tr>
                                <td class="p-4 font-mono text-xs font-bold text-slate-900">{{ $inv->kode_barang }}</td>
                                <td class="p-4 font-semibold text-slate-900">{{ $inv->nama_barang }}</td>
                                <td class="p-4">{{ $inv->category->nama ?? '-' }}</td>
                                <td class="p-4">{{ $inv->jumlah }} {{ $inv->satuan }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $inv->kondisi === 'Baik' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                        {{ $inv->kondisi }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                        {{ $inv->status }}
                                    </span>
                                </td>
                                <td class="p-4 text-center space-x-2">
                                    <a href="{{ route('inventories.show', $inv->id) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-semibold">Detail</a>
                                    <a href="{{ route('inventories.edit', $inv->id) }}" class="text-amber-600 hover:text-amber-900 text-xs font-semibold">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-slate-400">Belum ada barang inventaris tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                {{ $inventories->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
