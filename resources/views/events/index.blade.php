<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Kegiatan Warga & Absensi QR') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Agenda Kegiatan Warga</h3>
                <p class="text-xs text-slate-500 mt-1">Kelola acara, pendaftaran peserta, dan sesi presensi QR dinamis.</p>
            </div>
            @php
                $user = Auth::user();
                $isStaff = !($user->hasRole('resident') && ! $user->hasRole('super-admin') && ! $user->hasRole('rw-admin') && ! $user->hasRole('rt-admin'));
            @endphp
            @if($isStaff)
                <a href="{{ route('events.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Buat Kegiatan Baru
                </a>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="p-4">Nama Kegiatan</th>
                        <th class="p-4">Waktu & Lokasi</th>
                        <th class="p-4">Pendaftar</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($events as $event)
                        @php
                            $isRegistered = in_array($event->id, $registeredEventIds ?? []);
                        @endphp
                        <tr>
                            <td class="p-4">
                                <div class="font-semibold text-slate-900">{{ $event->title }}</div>
                                <div class="text-xs text-slate-400">{{ Str::limit($event->description, 50) }}</div>
                            </td>
                            <td class="p-4">
                                <div>{{ \Carbon\Carbon::parse($event->starts_at)->format('d/m/Y H:i') }}</div>
                                <div class="text-xs text-slate-400">{{ $event->location ?? 'Lokasi belum diisi' }}</div>
                            </td>
                            <td class="p-4 font-semibold text-slate-700">{{ $event->registrations_count ?? 0 }} Warga</td>
                            <td class="p-4">
                                @php
                                    $statusClasses = 'bg-indigo-50 text-indigo-600';
                                    if ($event->status === 'completed') {
                                        $statusClasses = 'bg-slate-100 text-slate-600';
                                    } elseif ($event->status === 'ongoing') {
                                        $statusClasses = 'bg-emerald-50 text-emerald-600';
                                    }
                                    $statusLabel = $event->status === 'completed' ? 'Selesai' : ucfirst($event->status);
                                @endphp
                                <span class="px-2.5 py-0.5 rounded text-xs font-semibold {{ $statusClasses }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <a href="{{ route('events.show', $event->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs">Detail</a>
                                @if($isStaff)
                                    @if($event->status !== 'completed')
                                        <form action="{{ route('events.complete', $event->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin kegiatan ini sudah selesai?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded text-xs font-semibold">Selesai</button>
                                        </form>
                                    @endif
                                @endif
                                @if($isRegistered)
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded text-xs font-bold">Terdaftar</span>
                                @else
                                    <form action="{{ route('events.register', $event->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-semibold">Daftar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400">Belum ada kegiatan yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-100">
                {{ $events->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
