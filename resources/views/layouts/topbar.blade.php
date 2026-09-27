<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-10">
    <div class="flex items-center space-x-4">
        <!-- Mobile Sidebar Toggle -->
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 md:hidden focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <span class="text-xs md:text-sm font-medium text-gray-500">
            Sistem Informasi Administrasi RW Digitalisasi
        </span>
    </div>

    <!-- Right Actions -->
    <div class="flex items-center space-x-4">
        <!-- Notification Bell Dropdown -->
        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
            <button @click="open = !open" class="relative p-1.5 text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                    <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </button>

            <div x-show="open"
                 style="display: none;"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50">
                <div class="px-4 py-2 border-b border-slate-100 flex justify-between items-center">
                    <span class="font-bold text-sm text-slate-800">Notifikasi</span>
                    <div class="flex items-center gap-2">
                        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                            <form action="{{ route('notifications.read-all') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-[11px] text-indigo-600 hover:text-indigo-800 font-semibold">Tandai Semua Dibaca</button>
                            </form>
                        @endif
                        <a href="{{ route('notifications.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">Semua</a>
                    </div>
                </div>

                <div class="max-h-72 overflow-y-auto divide-y divide-slate-100">
                    @forelse(auth()->user()->notifications()->take(5)->get() as $notification)
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left p-3 hover:bg-slate-50 text-xs space-y-1 block {{ $notification->read_at ? 'opacity-75' : 'bg-indigo-50/20 font-medium' }}">
                                <div class="font-semibold text-slate-900">{{ $notification->data['title'] ?? 'Notifikasi Sistem' }}</div>
                                <p class="text-slate-600 line-clamp-2">{{ $notification->data['message'] ?? 'Ada aktivitas baru.' }}</p>
                                <div class="text-[10px] text-slate-400">{{ $notification->created_at->diffForHumans() }}</div>
                            </button>
                        </form>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400">Belum ada notifikasi.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- User Dropdown -->
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none">
                    <span class="mr-2">{{ Auth::user()->name }}</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Profil Pengguna') }}
                </x-dropdown-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Keluar') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>
