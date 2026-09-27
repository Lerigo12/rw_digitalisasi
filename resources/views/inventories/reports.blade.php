<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Laporan Inventaris') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
            <form method="GET" action="{{ route('inventories.reports') }}" class="flex flex-wrap items-end gap-4 w-full">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Kategori</label>
                    <select name="kategori" class="rounded-lg border-slate-300 text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('kategori') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Kondisi</label>
                    <select name="kondisi" class="rounded-lg border-slate-300 text-sm">
                        <option value="">Semua Kondisi</option>
                        <option value="Baik" {{ request('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="Cukup Baik" {{ request('kondisi') == 'Cukup Baik' ? 'selected' : '' }}>Cukup Baik</option>
                        <option value="Rusak Ringan" {{ request('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="Rusak Berat" {{ request('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        <option value="Hilang" {{ request('kondisi') == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Status</label>
                    <select name="status" class="rounded-lg border-slate-300 text-sm">
                        <option value="">Semua Status</option>
                        <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="Rusak" {{ request('status') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                        <option value="Hilang" {{ request('status') == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">Filter</button>
                </div>
            </form>
            <div class="whitespace-nowrap">
                <a href="{{ route('inventories.reports.excel', request()->all()) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 flex items-center gap-2">
                    <i class="fa-solid fa-file-excel"></i> Export Excel
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-6">
            <h3 class="font-bold text-slate-900 text-base mb-4">Rekapitulasi Data Barang Inventaris</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Kode</th>
                            <th class="p-3">Nama Barang</th>
                            <th class="p-3">Kategori</th>
                            <th class="p-3">Jumlah</th>
                            <th class="p-3">Kondisi</th>
                            <th class="p-3">Tahun</th>
                            <th class="p-3">Harga</th>
                            <th class="p-3">Lokasi</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($inventories as $item)
                            <tr>
                                <td class="p-3 font-mono text-xs font-bold text-slate-900">{{ $item->kode_barang }}</td>
                                <td class="p-3 font-semibold text-slate-900">{{ $item->nama_barang }}</td>
                                <td class="p-3">{{ $item->category->nama ?? '-' }}</td>
                                <td class="p-3">{{ $item->jumlah }} {{ $item->satuan }}</td>
                                <td class="p-3">{{ $item->kondisi }}</td>
                                <td class="p-3">{{ $item->tahun_perolehan ?? '-' }}</td>
                                <td class="p-3">Rp {{ number_format($item->harga_perolehan, 0, ',', '.') }}</td>
                                <td class="p-3">{{ $item->lokasi ?? '-' }}</td>
                                <td class="p-3">{{ $item->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-6 text-center text-slate-400">Tidak ada data laporan yang sesuai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
