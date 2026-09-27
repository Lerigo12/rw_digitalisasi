<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Pengembalian Inventaris') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm"
                <ul class="list-disc pl-5"
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Active loans needing return -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="font-bold text-slate-900 mb-4">Pinjaman Aktif (Belum Dikembalikan)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Kode</th>
                            <th class="p-3">Peminjam</th>
                            <th class="p-3">Barang</th>
                            <th class="p-3">Jumlah</th>
                            <th class="p-3">Pinjam</th>
                            <th class="p-3">Batas Kembali</th>
                            <th class="p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($activeLoans as $loan)
                            <tr>
                                <td class="p-3 font-mono text-xs font-bold text-slate-900">{{ $loan->kode_peminjaman }}</td>
                                <td class="p-3 font-semibold text-slate-900">{{ $loan->nama_peminjam }}</br><span class="text-xs text-slate-400">{{ $loan->nomor_hp }}</span></td>
                                <td class="p-3">{{ $loan->inventory->nama_barang ?? '-' }}</td>
                                <td class="p-3">{{ $loan->jumlah }} {{ $loan->inventory->satuan ?? 'Unit' }}</td>
                                <td class="p-3 text-xs">{{ $loan->tanggal_pinjam }}</td>
                                <td class="p-3 text-xs">{{ $loan->tanggal_kembali_rencana }}</td>
                                <td class="p-3">
                                    @php
                                        $currentUser = auth()->user();
                                    @endphp
                                    @if($currentUser && $loan->user_id === $currentUser->id && in_array($loan->status, ['Disetujui', 'Dipinjam', 'Terlambat']))
                                        <form action="{{ route('inventories.returns.store', $loan->id) }}" method="POST" class="space-y-2">
                                            @csrf
                                            <div class="flex items-center space-x-2">
                                                <label class="text-xs font-semibold text-slate-600">Kondisi Akhir</label>
                                                <select name="kondisi_sesudah" class="rounded-lg border-slate-300 text-sm" required>
                                                    <option value="Baik">Baik</option>
                                                    <option value="Cukup Baik">Cukup Baik</option>
                                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                                    <option value="Rusak Berat">Rusak Berat</option>
                                                    <option value="Hilang">Hilang</option>
                                                </select>
                                                <button type="submit" class="px-3 py-1 bg-emerald-600 text-white text-xs rounded hover:bg-emerald-700">Kembalikan</button>
                                            </div>
                                        </form>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $loan->status === 'Dikembalikan' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">
                                            {{ $loan->status === 'Dikembalikan' ? 'Sudah Dikembalikan' : 'Sedang Dipinjam' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-4 text-center text-slate-400">Tidak ada peminjaman aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- History of returned loans -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mt-6">
            <h3 class="font-bold text-slate-900 mb-4">Riwayat Pengembalian</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Kode</th>
                            <th class="p-3">Peminjam</th>
                            <th class="p-3">Barang</th>
                            <th class="p-3">Jumlah</th>
                            <th class="p-3">Pinjam</th>
                            <th class="p-3">Kembali</th>
                            <th class="p-3">Kondisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($returnedLoans as $loan)
                            <tr>
                                <td class="p-3 font-mono text-xs font-bold text-slate-900">{{ $loan->kode_peminjaman }}</td>
                                <td class="p-3 font-semibold text-slate-900">{{ $loan->nama_peminjam }}<br><span class="text-xs text-slate-400">{{ $loan->nomor_hp }}</span></td>
                                <td class="p-3">{{ $loan->inventory->nama_barang ?? '-' }}</td>
                                <td class="p-3">{{ $loan->jumlah }} {{ $loan->inventory->satuan ?? '' }}</td>
                                <td class="p-3 text-xs">{{ $loan->tanggal_pinjam }}</td>
                                <td class="p-3 text-xs">{{ $loan->tanggal_kembali_actual }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $loan->kondisi_sesudah === 'Baik' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                        {{ $loan->kondisi_sesudah ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-4 text-center text-slate-400">Tidak ada riwayat pengembalian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $returnedLoans->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
