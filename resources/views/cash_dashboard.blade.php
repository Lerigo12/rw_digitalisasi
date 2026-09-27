<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    {{ __('Dashboard Kas RW') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Ringkasan kondisi keuangan RW dan arus kas terkini.</p>
            </div>
            <!-- Filter Periode -->
            <form method="GET" action="{{ route('cash_dashboard') }}" class="flex items-center gap-2">
                <select name="period" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-xs font-semibold py-1.5 px-3 bg-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="current_month" {{ $period === 'current_month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="3_months" {{ $period === '3_months' ? 'selected' : '' }}>3 Bulan Terakhir</option>
                    <option value="6_months" {{ $period === '6_months' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                    <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </form>
        </div>
    </x-slot>

    <div class="space-y-6 pb-12">
        <!-- 4 Card Ringkasan Keuangan -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Saldo Kas RW -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-bl-full -z-0 opacity-50"></div>
                <div>
                    <span class="text-xs font-bold tracking-wider text-slate-400 uppercase">Saldo Kas RW</span>
                    <h3 class="text-2xl font-extrabold text-emerald-600 mt-2">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h3>
                </div>
                <p class="text-xs text-slate-500 mt-4 font-medium flex items-center">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block mr-1.5"></span> Saldo saat ini
                </p>
            </div>

            <!-- Pemasukan -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-bl-full -z-0 opacity-50"></div>
                <div>
                    <span class="text-xs font-bold tracking-wider text-slate-400 uppercase">Pemasukan</span>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-2">Rp {{ number_format($periodIncome, 0, ',', '.') }}</h3>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">Total pemasukan periode</span>
                    @if(isset($incomeChange))
                        <span class="text-xs font-bold {{ $incomeChange >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $incomeChange >= 0 ? '↑' : '↓' }} {{ number_format(abs($incomeChange), 1) }}%
                        </span>
                    @endif
                </div>
            </div>

            <!-- Pengeluaran -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-bl-full -z-0 opacity-50"></div>
                <div>
                    <span class="text-xs font-bold tracking-wider text-slate-400 uppercase">Pengeluaran</span>
                    <h3 class="text-2xl font-extrabold text-amber-600 mt-2">Rp {{ number_format($periodExpense, 0, ',', '.') }}</h3>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">Total pengeluaran periode</span>
                    @if(isset($expenseChange))
                        <span class="text-xs font-bold {{ $expenseChange <= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $expenseChange <= 0 ? '↓' : '↑' }} {{ number_format(abs($expenseChange), 1) }}%
                        </span>
                    @endif
                </div>
            </div>

            <!-- Iuran Belum Lunas -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 rounded-bl-full -z-0 opacity-50"></div>
                <div>
                    <span class="text-xs font-bold tracking-wider text-slate-400 uppercase">Iuran Belum Lunas</span>
                    <h3 class="text-2xl font-extrabold text-rose-600 mt-2">Rp {{ number_format($unpaidBillAmount, 0, ',', '.') }}</h3>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">Total tagihan tertunggak</span>
                    <a href="{{ route('fees.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">Lihat Tagihan →</a>
                </div>
            </div>
        </div>

        <!-- Grafik Arus Kas -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h3 class="font-bold text-base text-slate-900">Grafik Arus Kas</h3>
                    <p class="text-xs text-slate-500">Pemasukan dan pengeluaran RW berdasarkan periode.</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold mt-2 md:mt-0">
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Pemasukan</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-500"></span> Pengeluaran</div>
                </div>
            </div>
            
            <div class="h-64 w-full">
                <canvas id="cashFlowChart"></canvas>
            </div>
        </div>

        <!-- Grid Tengah: Ringkasan Iuran & Pengeluaran Berdasarkan Kategori -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Ringkasan Iuran -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-base text-slate-900">Ringkasan Iuran</h3>
                        <a href="{{ route('fees.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">Lihat Tagihan →</a>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 font-medium">Total Tagihan</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($totalBillAmount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 font-medium">Sudah Dibayar</span>
                            <span class="font-bold text-emerald-600">Rp {{ number_format($paidBillAmount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 font-medium">Belum Dibayar</span>
                            <span class="font-bold text-rose-600">Rp {{ number_format($unpaidBillAmount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between text-xs font-semibold">
                            <span class="text-slate-600">Persentase Pelunasan</span>
                            <span class="text-indigo-600">{{ $collectionPercentage }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                            <div class="bg-indigo-600 h-3 rounded-full" style="width: {{ $collectionPercentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengeluaran Berdasarkan Kategori -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-base text-slate-900 mb-4">Pengeluaran Berdasarkan Kategori</h3>
                    
                    <div class="space-y-3">
                        @forelse($expenseByCategory as $item)
                            <div>
                                <div class="flex justify-between text-xs font-semibold mb-1">
                                    <span class="text-slate-700">{{ $item->category->name ?? 'Lainnya' }}</span>
                                    <span class="text-slate-900">Rp {{ number_format($item->total, 0, ',', '.') }}</span>
                                </div>
                                @php
                                    $maxCat = $expenseByCategory->max('total') ?: 1;
                                    $catPercent = ($item->total / $maxCat) * 100;
                                @endphp
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $catPercent }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-slate-400 text-xs">Belum ada data pengeluaran pada periode ini.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Iuran per RT -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6">
            <h3 class="font-bold text-base text-slate-900 mb-4">Status Iuran per RT</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">RT</th>
                            <th class="p-3 text-right">Total Tagihan</th>
                            <th class="p-3 text-right">Sudah Dibayar</th>
                            <th class="p-3 text-right">Belum Dibayar</th>
                            <th class="p-3 text-right">Persentase Lunas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rtSummary as $rt)
                            <tr>
                                <td class="p-3 font-bold text-slate-900">{{ $rt['rt_name'] }}</td>
                                <td class="p-3 text-right font-semibold">Rp {{ number_format($rt['total'], 0, ',', '.') }}</td>
                                <td class="p-3 text-right font-semibold text-emerald-600">Rp {{ number_format($rt['paid'], 0, ',', '.') }}</td>
                                <td class="p-3 text-right font-semibold text-rose-600">Rp {{ number_format($rt['unpaid'], 0, ',', '.') }}</td>
                                <td class="p-3 text-right font-bold text-indigo-600">{{ $rt['percentage'] }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-400">Belum ada data RT.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transaksi Kas Terbaru -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-base text-slate-900">Transaksi Kas Terbaru</h3>
                <a href="{{ route('cash_transactions.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">Lihat Semua →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">Keterangan / Kategori</th>
                            <th class="p-3">Akun</th>
                            <th class="p-3 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentTransactions as $trx)
                            <tr>
                                <td class="p-3 whitespace-nowrap text-xs font-medium text-slate-500">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y') }}</td>
                                <td class="p-3">
                                    <div class="font-bold text-slate-900">{{ $trx->description }}</div>
                                    <div class="text-xs text-slate-400">{{ $trx->category->name ?? 'Umum' }}</div>
                                </td>
                                <td class="p-3 text-xs">{{ $trx->cashAccount->name ?? '-' }}</td>
                                @if($trx->transaction_type === 'income')
                                    <td class="p-3 text-right font-extrabold text-emerald-600">+Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                @else
                                    <td class="p-3 text-right font-extrabold text-amber-600">-Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-400">Belum ada transaksi terbaru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Akses Cepat -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-base text-slate-900 mb-4">Akses Cepat Keuangan</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <a href="{{ route('cash_transactions.index') }}" class="p-4 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 rounded-xl text-center transition group">
                    <div class="text-indigo-600 font-bold text-sm group-hover:scale-105 transition">+ Catat Pemasukan</div>
                </a>
                <a href="{{ route('cash_transactions.index') }}" class="p-4 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 rounded-xl text-center transition group">
                    <div class="text-indigo-600 font-bold text-sm group-hover:scale-105 transition">+ Catat Pengeluaran</div>
                </a>
                <a href="{{ route('fees.index') }}" class="p-4 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 rounded-xl text-center transition group">
                    <div class="text-indigo-600 font-bold text-sm group-hover:scale-105 transition">Buat Tagihan Iuran</div>
                </a>
                @if(Route::has('fees.verifications'))
                <a href="{{ route('fees.verifications') }}" class="p-4 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 rounded-xl text-center transition group">
                    <div class="text-indigo-600 font-bold text-sm group-hover:scale-105 transition">Verifikasi Pembayaran</div>
                </a>
                @endif
                @if(Route::has('reports.index'))
                <a href="{{ route('reports.index') }}" class="p-4 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 rounded-xl text-center transition group">
                    <div class="text-indigo-600 font-bold text-sm group-hover:scale-105 transition">Laporan Keuangan</div>
                </a>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('cashFlowChart').getContext('2d');
            const labels = @json($chartLabels);
            const incomeData = @json($chartIncome);
            const expenseData = @json($chartExpense);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: incomeData,
                            backgroundColor: 'rgba(16, 185, 129, 0.8)',
                            borderRadius: 6,
                        },
                        {
                            label: 'Pengeluaran',
                            data: expenseData,
                            backgroundColor: 'rgba(245, 158, 11, 0.8)',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw || 0;
                                    return context.dataset.label + ': Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value >= 1000000 ? (value / 1000000) + 'jt' : (value >= 1000 ? (value / 1000) + 'rb' : value));
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
