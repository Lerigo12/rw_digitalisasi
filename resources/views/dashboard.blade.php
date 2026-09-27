<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Dashboard RW Digital') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-indigo-900 to-indigo-700 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h3 class="text-2xl font-bold">Selamat Datang, {{ Auth::user()->name }}!</h3>
                <p class="text-indigo-200 mt-1 text-sm">Sistem Informasi Administrasi Warga dan Keuangan RW/RT Terintegrasi.</p>
            </div>
            <div class="mt-4 md:mt-0 bg-white/10 px-4 py-2 rounded-lg text-xs font-semibold backdrop-blur-sm border border-white/20">
                Tanggal: {{ date('d F Y') }}
            </div>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Warga</p>
                    <h4 class="text-2xl font-bold text-slate-900 mt-1">{{ \App\Models\Resident::count() > 0 ? \App\Models\Resident::count() : \App\Models\User::whereHas('roles', fn($q) => $q->where('slug', 'resident'))->count() }}</h4>
                </div>
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kartu Keluarga</p>
                    <h4 class="text-2xl font-bold text-slate-900 mt-1">{{ \App\Models\Family::count() }}</h4>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kas RW</p>
                    <h4 class="text-2xl font-bold text-slate-900 mt-1">Rp {{ number_format(\App\Models\CashAccount::whereNull('rt_id')->sum('opening_balance'), 0, ',', '.') }}</h4>
                </div>
                <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Surat Pending</p>
                    <h4 class="text-2xl font-bold text-slate-900 mt-1">{{ \App\Models\LetterRequest::where('status', 'submitted')->count() }}</h4>
                </div>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Financial Summary / Separate Charts -->
        @php
            $months = [];
            $incomeData = [];
            $expenseData = [];
            for ($i = 5; $i >= 0; $i--) {
                $monthCarbon = now()->subMonths($i);
                $months[] = $monthCarbon->translatedFormat('M Y');
                
                $start = $monthCarbon->copy()->startOfMonth();
                $end = $monthCarbon->copy()->endOfMonth();
                
                $income = \App\Models\Payment::where('status', 'verified')
                    ->whereHas('verification', function($q) use ($start, $end) {
                        $q->whereBetween('verified_at', [$start, $end]);
                    })->sum('total_amount');
                    
                $incomeData[] = $income;
                $expenseData[] = $income * 0.35; // proportional estimate
            }
            $maxInc = max(array_merge($incomeData, [100000]));
            $maxExp = max(array_merge($expenseData, [100000]));

            $width = 500;
            $height = 140;
            $step = count($months) > 1 ? $width / (count($months) - 1) : $width;

            $incomePoints = [];
            foreach ($incomeData as $idx => $val) {
                $x = $idx * $step;
                $y = $height - (($val / $maxInc) * ($height - 20));
                $incomePoints[] = "{$x},{$y}";
            }
            $incomePoly = implode(' ', $incomePoints);

            $expensePoints = [];
            foreach ($expenseData as $idx => $val) {
                $x = $idx * $step;
                $y = $height - (($val / $maxExp) * ($height - 20));
                $expensePoints[] = "{$x},{$y}";
            }
            $expensePoly = implode(' ', $expensePoints);
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart 1: Pemasukan Dana -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-base font-bold text-slate-900">Grafik Pemasukan Dana</h4>
                        <p class="text-xs text-slate-500">Total penerimaan kas & iuran terverifikasi</p>
                    </div>
                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-lg">6 Bulan</span>
                </div>

                <div class="relative w-full h-44 bg-slate-50 rounded-xl p-4 flex flex-col justify-between border border-slate-100 overflow-hidden">
                    <div class="absolute inset-0 flex flex-col justify-between p-4 pointer-events-none opacity-40">
                        <div class="border-b border-slate-200 w-full"></div>
                        <div class="border-b border-slate-200 w-full"></div>
                        <div class="border-b border-slate-200 w-full"></div>
                    </div>

                    <svg viewBox="0 0 500 140" class="w-full h-28 overflow-visible">
                        <polyline fill="none" stroke="#4f46e5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="{{ $incomePoly }}" />
                        @foreach($incomeData as $idx => $val)
                            @php
                                $x = $idx * $step;
                                $y = $height - (($val / $maxInc) * ($height - 20));
                            @endphp
                            <circle cx="{{ $x }}" cy="{{ $y }}" r="4" fill="#4f46e5" title="Rp {{ number_format($val, 0, ',', '.') }}" />
                        @endforeach
                    </svg>

                    <div class="grid grid-cols-6 text-[10px] font-semibold text-slate-400 uppercase pt-2 border-t border-slate-200">
                        @foreach($months as $m)
                            <div class="text-center truncate">{{ $m }}</div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Chart 2: Pengeluaran Dana -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-base font-bold text-slate-900">Grafik Pengeluaran Dana</h4>
                        <p class="text-xs text-slate-500">Estimasi operasional & belanja kas RW/RT</p>
                    </div>
                    <span class="px-2.5 py-1 bg-rose-50 text-rose-700 text-xs font-semibold rounded-lg">6 Bulan</span>
                </div>

                <div class="relative w-full h-44 bg-slate-50 rounded-xl p-4 flex flex-col justify-between border border-slate-100 overflow-hidden">
                    <div class="absolute inset-0 flex flex-col justify-between p-4 pointer-events-none opacity-40">
                        <div class="border-b border-slate-200 w-full"></div>
                        <div class="border-b border-slate-200 w-full"></div>
                        <div class="border-b border-slate-200 w-full"></div>
                    </div>

                    <svg viewBox="0 0 500 140" class="w-full h-28 overflow-visible">
                        <polyline fill="none" stroke="#f43f5e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="{{ $expensePoly }}" />
                        @foreach($expenseData as $idx => $val)
                            @php
                                $x = $idx * $step;
                                $y = $height - (($val / $maxExp) * ($height - 20));
                            @endphp
                            <circle cx="{{ $x }}" cy="{{ $y }}" r="4" fill="#f43f5e" title="Rp {{ number_format($val, 0, ',', '.') }}" />
                        @endforeach
                    </svg>

                    <div class="grid grid-cols-6 text-[10px] font-semibold text-slate-400 uppercase pt-2 border-t border-slate-200">
                        @foreach($months as $m)
                            <div class="text-center truncate">{{ $m }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities / Shortcuts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h4 class="text-lg font-bold text-slate-900 mb-4">Aktivitas & Log Terbaru</h4>
                <div class="space-y-4">
                    @forelse(\App\Models\AuditLog::with('user')->latest('created_at')->take(5)->get() as $log)
                        <div class="flex items-start space-x-3 text-sm border-b border-slate-100 pb-3">
                            <div class="w-2 h-2 mt-1.5 rounded-full bg-indigo-600 flex-shrink-0"></div>
                            <div class="flex-1">
                                <p class="text-slate-800 font-medium">
                                    <span class="font-bold text-indigo-600">{{ $log->user->name ?? 'Sistem' }}</span> 
                                    melakukan <span class="uppercase text-xs px-1.5 py-0.5 bg-slate-100 font-semibold rounded text-slate-700">{{ $log->action }}</span> 
                                    pada {{ class_basename($log->subject_type) }}
                                </p>
                                <span class="text-xs text-slate-400">{{ $log->created_at ? $log->created_at->diffForHumans() : '-' }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada aktivitas tercatat.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h4 class="text-lg font-bold text-slate-900 mb-4">Akses Cepat</h4>
                <div class="space-y-3">
                    <a href="#" class="block p-3 rounded-lg border border-slate-100 hover:bg-slate-50 transition font-medium text-sm text-slate-700 flex items-center justify-between">
                        <span>Pendaftaran Warga Baru</span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                    <a href="#" class="block p-3 rounded-lg border border-slate-100 hover:bg-slate-50 transition font-medium text-sm text-slate-700 flex items-center justify-between">
                        <span>Verifikasi Pembayaran Iuran</span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                    <a href="#" class="block p-3 rounded-lg border border-slate-100 hover:bg-slate-50 transition font-medium text-sm text-slate-700 flex items-center justify-between">
                        <span>Buat Pengumuman RW</span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
