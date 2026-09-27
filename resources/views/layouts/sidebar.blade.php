<aside class="w-64 bg-slate-900 text-slate-100 min-h-screen flex flex-col flex-shrink-0 transition-all duration-200">
    <!-- Brand -->
    <div class="h-16 flex items-center px-6 bg-slate-950 font-bold text-lg tracking-wider border-b border-slate-800 text-indigo-400">
        <svg class="w-6 h-6 mr-2 inline-block text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
        RW DIGITAL
    </div>

    <!-- Navigation List -->
    <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            Dashboard
        </a>

        @php
            $user = Auth::user();
            $isSuperAdmin = $user && $user->hasRole('super-admin');
            $isRwAdmin = $user && ($user->hasRole('rw-admin') || $isSuperAdmin);
            $isRtAdmin = $user && ($user->hasRole('rt-admin') || $isRwAdmin);
            $isBendahara = $user && ($user->hasRole('bendahara-rt') || $isRtAdmin);
            $isSekretaris = $user && ($user->hasRole('sekretaris-rw') || $isRwAdmin);
            $isResident = $user && $user->hasRole('resident') && ! $isRtAdmin;
        @endphp

        <!-- Group: Administrasi & Wilayah -->
        @if($isRtAdmin)
            <div class="pt-4 pb-1 px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Kependudukan & Wilayah
            </div>
            @if($isRwAdmin)
                <a href="{{ route('regions.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Master Wilayah (RW/RT)
                </a>
            @endif
            @if($isSuperAdmin)
                <a href="{{ route('accounts.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Akun & Hak Akses
                </a>
            @endif
            <a href="{{ route('residents.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Data Warga & KK
            </a>
        @endif

        <!-- Group: Keuangan -->
        <div class="pt-4 pb-1 px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Keuangan & Iuran
        </div>
        <a href="{{ route('fees.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
            <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Tagihan & Pembayaran
        </a>
        @if($isBendahara)
            <a href="{{ route('fees.verifications') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                Verifikasi Pembayaran
            </a>
            <a href="{{ route('cash_dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('cash_dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Kas & Pembukuan
            </a>
        @endif

        <!-- Group: Layanan Publik -->
        <div class="pt-4 pb-1 px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Layanan & Kegiatan
        </div>
        <a href="{{ route('letters.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
            <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Surat Menyurat
        </a>
        <a href="{{ route('announcements.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
            <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            Pengumuman
        </a>
        <a href="{{ route('events.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
            <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21h8a2 2 0 002-2v-5a2 2 0 00-2-2h-3l-4 5V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            Kegiatan & QR Absensi
        </a>
        <!-- Removed duplicate Peminjaman Inventaris link -->

        <!-- Group: Inventaris RW -->
        <div class="pt-4 pb-1 px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Inventaris RW 29
        </div>
        <a href="{{ route('inventories.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('inventories.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            Dashboard Inventaris
        </a>
        @if(!$isResident)
            <a href="{{ route('inventories.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('inventories.index') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                Data Barang
            </a>
        @endif
        <a href="{{ route('inventories.loans') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('inventories.loans') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Peminjaman
        </a>
        <a href="{{ route('inventories.returns') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('inventories.returns') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Pengembalian
        </a>
        <a href="{{ route('inventories.histories') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('inventories.histories') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Riwayat
        </a>
        <a href="{{ route('inventories.reports') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('inventories.reports') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Laporan
        </a>

        <!-- Group: Sistem & Laporan -->
        @if($isSuperAdmin || $isRwAdmin)
            <div class="pt-4 pb-1 px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Sistem & Pengaturan
            </div>
            <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Laporan & Rekapitulasi
            </a>
            <a href="{{ route('audit_logs.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Audit Log Aktivitas
            </a>
        @endif
    </div>

    <!-- User Mini Profile / Scope Indicator -->
    <div class="p-4 border-t border-slate-800 bg-slate-950 flex items-center justify-between">
        <div class="flex items-center space-x-3 truncate">
            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center font-bold text-white text-xs flex-shrink-0">
                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
            </div>
            <div class="truncate">
                <div class="text-xs font-medium text-white truncate">{{ Auth::user()->name ?? 'Pengguna' }}</div>
                <div class="text-[10px] text-slate-400 truncate">
                    @if($isSuperAdmin) Super Admin @elseif($isRwAdmin) Pengurus RW @elseif($isRtAdmin) Pengurus RT @else Warga @endif
                </div>
            </div>
        </div>
    </div>
</aside>
