<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Kas & Pembukuan Warga') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl text-emerald-700 text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="text-xs font-semibold text-slate-500 uppercase">Total Saldo Kas Aktif</div>
                <div class="text-3xl font-extrabold text-emerald-600 mt-2">Rp {{ number_format($totalBalance ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="font-bold text-slate-900 text-base mb-4">Catat Transaksi Kas Baru</h3>
            <form action="{{ route('cash_transactions.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Akun Kas</label>
                    <select name="cash_account_id" class="w-full rounded-lg border-slate-300 text-sm" required>
                        <option value="">Pilih Akun Kas</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Kategori</label>
                    <select name="category_id" class="w-full rounded-lg border-slate-300 text-sm">
                        <option value="">Pilih Kategori (Opsional)</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Jenis Transaksi</label>
                    <select name="transaction_type" class="w-full rounded-lg border-slate-300 text-sm" required>
                        <option value="income">Pemasukan (+)</option>
                        <option value="expense">Pengeluaran (-)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Jumlah (Rp)</label>
                    <input type="number" name="amount" min="1" class="w-full rounded-lg border-slate-300 text-sm" required placeholder="Contoh: 500000">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tanggal</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-slate-300 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Keterangan</label>
                    <input type="text" name="description" class="w-full rounded-lg border-slate-300 text-sm" required placeholder="Uraian transaksi...">
                </div>
                <div class="md:col-span-3">
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg text-sm font-bold hover:bg-emerald-700 shadow-sm">Simpan Transaksi Kas</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-6">
            <h3 class="font-bold text-slate-900 text-base mb-4">Riwayat Buku Kas & Transaksi</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">Akun / Kategori</th>
                            <th class="p-3">Keterangan</th>
                            <th class="p-3 text-right">Pemasukan</th>
                            <th class="p-3 text-right">Pengeluaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transactions as $trx)
                            <tr>
                                <td class="p-3 whitespace-nowrap font-medium text-slate-900">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y') }}</td>
                                <td class="p-3">{{ $trx->cashAccount->name ?? '-' }} <span class="text-xs text-slate-400">({{ $trx->category->name ?? 'Umum' }})</span></td>
                                <td class="p-3">{{ $trx->description }}</td>
                                @if($trx->transaction_type === 'income')
                                    <td class="p-3 text-right font-semibold text-emerald-600">+Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                    <td class="p-3 text-right">-</td>
                                @else
                                    <td class="p-3 text-right">-</td>
                                    <td class="p-3 text-right font-semibold text-amber-600">-Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-400">Belum ada catatan transaksi kas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
