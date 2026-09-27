<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Detail Kegiatan') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-900 text-lg">{{ $event->title }}</h3>
            <p class="mt-2 text-slate-700">{{ $event->description ?? 'Deskripsi belum diisi.' }}</p>
            <div class="mt-4">
                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $event->status === 'upcoming' ? 'bg-indigo-50 text-indigo-600' : 'bg-emerald-50 text-emerald-600' }}">{{ ucfirst($event->status) }}</span>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <strong class="block text-xs text-slate-500 uppercase">Mulai</strong>
                    {{ \Carbon\Carbon::parse($event->starts_at)->format('d/m/Y H:i') }}
                </div>
                <div>
                    <strong class="block text-xs text-slate-500 uppercase">Selesai</strong>
                    {{ \Carbon\Carbon::parse($event->ends_at)->format('d/m/Y H:i') }}
                </div>
                <div>
                    <strong class="block text-xs text-slate-500 uppercase">Lokasi</strong>
                    {{ $event->location ?? 'Lokasi belum diisi' }}
                </div>
                <div>
                    <strong class="block text-xs text-slate-500 uppercase">Pendaftaran</strong>
                    {{ $event->registration_enabled ? 'Aktif' : 'Nonaktif' }}
                </div>
            </div>

            @if($event->registrations->count() > 0)
                <div class="mt-6">
                    <h4 class="font-semibold text-slate-800 mb-2">Daftar Peserta</h4>
                    <ul class="list-disc list-inside text-sm text-slate-600">
                        @foreach($event->registrations as $reg)
                            <li>{{ $reg->resident->full_name }} ({{ $reg->resident->family->rt->name }})</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!$event->attendanceSessions->isEmpty())
                <div class="mt-6">
                    <h4 class="font-semibold text-slate-800 mb-2">Sesi QR Aktif</h4>
                    <ul class="list-disc list-inside text-sm text-slate-600">
                        @foreach($event->attendanceSessions as $session)
                            <li>Validitas: {{ \Carbon\Carbon::parse($session->valid_from)->format('d/m/Y H:i') }} – {{ \Carbon\Carbon::parse($session->valid_until)->format('d/m/Y H:i') }}
                                <br/>Token (hash) : {{ $session->token_hash }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-6 flex items-center justify-between">
                <div>
                    @php
                        $user = Auth::user();
                        $isResident = $user->hasRole('resident') && ! $user->hasRole('super-admin') && ! $user->hasRole('rw-admin') && ! $user->hasRole('rt-admin');
                    @endphp
                    @if($isResident && $event->registration_enabled)
                        @if($isRegistered)
                            <div class="flex items-center space-x-3">
                                <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-sm font-bold">Terdaftar</span>
                                @if(now()->lessThan($event->starts_at))
                                    <form action="{{ route('events.cancel', $event->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Batalkan pendaftaran kegiatan ini?')" class="text-xs text-red-600 hover:text-red-800 font-semibold underline">Batalkan Pendaftaran</button>
                                    </form>
                                @endif
                            </div>
                        @else
                            <form action="{{ route('events.register', $event->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold">Daftar Kegiatan</button>
                            </form>
                        @endif
                    @endif
                </div>
                <a href="{{ route('events.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-200">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
