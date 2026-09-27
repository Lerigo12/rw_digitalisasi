<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Dashboard Inventaris') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Stat cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white p-4 rounded-xl shadow-sm flex flex-col items-center">
                <div class="text-xs font-semibold text-slate-500 uppercase">Total Jenis Barang</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $totalJenis }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm flex flex-col items-center">
                <div class="text-xs font-semibold text-slate-500 uppercase">Total Barang</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $totalBarang }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm flex flex-col items-center">
                <div class="text-xs font-semibold text-slate-500 uppercase">Barang Tersedia</div>
                <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $barangTersedia }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm flex flex-col items-center">
                <div class="text-xs font-semibold text-slate-500 uppercase">Sedang Dipinjam</div>
                <div class="text-2xl font-bold text-amber-600 mt-1">{{ $sedangDipinjam }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm flex flex-col items-center">
                <div class="text-xs font-semibold text-slate-500 uppercase">Barang Rusak</div>
                <div class="text-2xl font-bold text-red-600 mt-1">{{ $barangRusak }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm flex flex-col items-center">
                <div class="text-xs font-semibold text-slate-500 uppercase">Barang Hilang</div>
                <div class="text-2xl font-bold text-gray-600 mt-1">{{ $barangHilang }}</div>
            </div>
        </div>

        <!-- Latest loans table -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-bold text-slate-900 mb-4">Peminjaman Terbaru</h3>
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="p-3">Kode</th>
                        <th class="p-3">Peminjam</th>
                        <th class="p-3">Barang</th>
                        <th class="p-3">Jumlah</th>
                        <th class="p-3">Tgl Pinjam</th>
                        <th class="p-3">Batas Kembali</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($latestLoans as $loan)
                        <tr>
                            <td class="p-3">{{ $loan->kode_peminjaman }}</td>
                            <td class="p-3">{{ $loan->nama_peminjam }}</td>
                            <td class="p-3">{{ $loan->inventory->nama_barang ?? '-' }}</td>
                            <td class="p-3">{{ $loan->jumlah }}</td>
                            <td class="p-3">{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d/m/Y') }}</td>
                            <td class="p-3">{{ \Carbon\Carbon::parse($loan->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                            <td class="p-3">{{ $loan->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-slate-400">Tidak ada peminjaman terbaru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
