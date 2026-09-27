<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Detail Barang: ' . $inventory->nama_barang) }}
        </h2>
    </x-slot>

    <div class="space-y-6 max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-mono font-bold px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg border border-indigo-100">
                        {{ $inventory->kode_barang }}
                    </span>
                    <h3 class="text-xl font-bold text-slate-900 mt-2">{{ $inventory->nama_barang }}</h3>
                    <p class="text-xs text-slate-500">Kategori: <span class="font-semibold text-slate-700">{{ $inventory->category->nama ?? '-' }}</span></p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('inventories.edit', $inventory) }}" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-semibold border border-amber-200">
                        Edit Barang
                    </a>
                    <a href="{{ route('inventories.index') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold">
                        Kembali
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($inventory->foto)
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-2">Foto Barang</label>
                        <img src="{{ asset('storage/' . $inventory->foto) }}" alt="Foto Barang" class="w-full h-64 object-cover rounded-xl border border-slate-200 shadow-sm">
                    </div>
                @endif
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase">Jumlah & Satuan</span>
                            <span class="font-bold text-slate-800">{{ $inventory->jumlah }} {{ $inventory->satuan }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase">Kondisi</span>
                            <span class="font-bold text-slate-800">{{ $inventory->kondisi }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase">Status</span>
                            <span class="font-bold text-slate-800">{{ $inventory->status }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase">Merek / Tipe</span>
                            <span class="font-bold text-slate-800">{{ $inventory->merek ?? '-' }} {{ $inventory->tipe ? '('.$inventory->tipe.')' : '' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase">Tahun Perolehan</span>
                            <span class="font-bold text-slate-800">{{ $inventory->tahun_perolehan ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase">Sumber Dana</span>
                            <span class="font-bold text-slate-800">{{ $inventory->sumber_dana ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase">Harga Perolehan</span>
                            <span class="font-bold text-slate-800">{{ $inventory->harga_perolehan ? 'Rp ' . number_format($inventory->harga_perolehan, 0, ',', '.') : '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase">Lokasi</span>
                            <span class="font-bold text-slate-800">{{ $inventory->lokasi ?? '-' }}</span>
                        </div>
                    </div>
                    @if($inventory->deskripsi)
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase mb-1">Deskripsi</span>
                            <p class="text-sm text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-100">{{ $inventory->deskripsi }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Riwayat Barang -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
            <h4 class="font-bold text-slate-900 text-base">Riwayat Aktivitas Barang</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Waktu</th>
                            <th class="p-3">Aktivitas</th>
                            <th class="p-3">Pengguna</th>
                            <th class="p-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($inventory->histories as $history)
                            <tr>
                                <td class="p-3 text-xs text-slate-500">{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-3 font-semibold text-slate-800">{{ $history->aktivitas }}</td>
                                <td class="p-3">{{ $history->user->name ?? 'Sistem' }}</td>
                                <td class="p-3 text-xs text-slate-500">{{ $history->keterangan }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-slate-400">Belum ada riwayat tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
