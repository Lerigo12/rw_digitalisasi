<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Laporan & Rekapitulasi') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col md:flex-row items-end gap-4 w-full">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-lg border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-lg border-slate-300 text-sm">
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">Filter Laporan</button>
                </div>
            </form>
            <div>
                <a href="{{ route('reports.export-excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 flex items-center gap-2 whitespace-nowrap">
                    <i class="fa-solid fa-file-excel"></i> Download Excel Kas
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                <div class="text-xs font-semibold text-slate-500 uppercase">Total Pemasukan</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                <div class="text-xs font-semibold text-slate-500 uppercase">Total Warga</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $residentsCount }}</div>
            </div>
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                <div class="text-xs font-semibold text-slate-500 uppercase">Jumlah Aset</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $assetsCount }}</div>
            </div>
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                <div class="text-xs font-semibold text-slate-500 uppercase">Surat Dibuat (Periode Ini)</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $lettersCount }}</div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-6">
            <h3 class="font-bold text-slate-900 text-base mb-4">Rincian Pembayaran Masuk (Terverifikasi)</h3>
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="p-3">Tanggal Verifikasi</th>
                        <th class="p-3">Warga</th>
                        <th class="p-3">Jenis Iuran</th>
                        <th class="p-3 text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $payment)
                        <tr>
                        <td class="p-3">{{ $payment->verification?->verified_at ? \Carbon\Carbon::parse($payment->verification->verified_at)->format('d/m/Y H:i') : '-' }}</td>
                        <td class="p-3 font-semibold text-slate-900">{{ $payment->payerResident->full_name ?? '-' }}</td>
                        <td class="p-3">{{ optional($payment->allocations->first()?->feeBill?->feeType)->name ?? '-' }}</td>
                        <td class="p-3 text-right font-medium text-emerald-600">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-slate-400">Tidak ada transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
