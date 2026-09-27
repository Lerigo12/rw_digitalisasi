<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Dashboard Warga') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-gradient-to-r from-emerald-900 to-emerald-700 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h3 class="text-2xl font-bold">Halo, Warga Setia {{ $resident->full_name ?? Auth::user()->name }}!</h3>
                <p class="text-emerald-200 mt-1 text-sm">
                    Status akun: 
                    <span class="font-bold underline">
                        @if($resident && $resident->status === 'active')
                            Terverifikasi / Warga Aktif
                        @elseif($resident)
                            {{ ucfirst($resident->status) }}
                        @else
                            Belum Terhubung Warga
                        @endif
                    </span>
                </p>
            </div>
            <div class="mt-4 md:mt-0 bg-white/10 px-4 py-2 rounded-lg text-xs font-semibold backdrop-blur-sm border border-white/20">
                {{ $resident && $resident->family && $resident->family->rt ? $resident->family->rt->name : 'Warga RT/RW Terdaftar' }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tagihan Iuran Belum Lunas</p>
                <h4 class="text-3xl font-bold text-slate-900 mt-2">
                    {{ $unpaidBillsCount }}
                </h4>
                <a href="{{ route('fees.index') }}" class="mt-4 inline-block text-xs font-semibold text-emerald-600 hover:text-emerald-800">Bayar Tagihan &rarr;</a>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Permohonan Surat Aktif</p>
                <h4 class="text-3xl font-bold text-slate-900 mt-2">
                    {{ $activeLettersCount }}
                </h4>
                <a href="{{ route('letters.index') }}" class="mt-4 inline-block text-xs font-semibold text-emerald-600 hover:text-emerald-800">Ajukan Surat &rarr;</a>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengumuman Terbaru</p>
                <h4 class="text-3xl font-bold text-slate-900 mt-2">
                    {{ $latestAnnouncementsCount }}
                </h4>
                <a href="{{ route('announcements.index') }}" class="mt-4 inline-block text-xs font-semibold text-emerald-600 hover:text-emerald-800">Lihat Pengumuman &rarr;</a>
            </div>
        </div>
    </div>
</x-app-layout>
