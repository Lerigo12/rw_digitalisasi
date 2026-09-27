<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Notifikasi Sistem') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-4">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-sm font-semibold text-slate-700">Daftar Notifikasi Anda</span>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-semibold hover:bg-indigo-100">Tandai Semua Dibaca</button>
                </form>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm divide-y divide-slate-100">
            @forelse(auth()->user()->notifications()->latest()->paginate(15) as $notification)
                <div class="p-4 flex items-start justify-between gap-4 {{ $notification->unread() ? 'bg-indigo-50/40' : '' }}">
                    <div>
                        <div class="font-semibold text-slate-900 text-sm">{{ $notification->data['title'] ?? 'Notifikasi' }}</div>
                        <p class="text-slate-600 text-sm mt-0.5">{{ $notification->data['message'] ?? '' }}</p>
                        <div class="text-[10px] text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                    @if($notification->unread())
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold whitespace-nowrap px-3 py-1 bg-indigo-50 rounded">Tandai Dibaca</button>
                        </form>
                    @elseif(!empty($notification->data['action_url']))
                        <a href="{{ $notification->data['action_url'] }}" class="text-xs text-slate-600 hover:text-indigo-600 font-semibold whitespace-nowrap">Buka</a>
                    @endif
                </div>
            @empty
                <div class="p-6 text-center text-slate-400 text-sm">Tidak ada notifikasi saat ini.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
