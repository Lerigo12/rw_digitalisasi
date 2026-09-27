<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Pengumuman & Informasi') }}
        </h2>
    </x-slot>

    <div class="space-y-6"
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
    </div>

    @php
        $user = auth()->user();
        $canCreate = $user && ($user->hasRole('super-admin') || $user->hasRole('rw-admin') || $user->hasRole('rt-admin') || $user->hasRole('sekretaris-rw'));
    @endphp

    @if($canCreate)
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Buat Pengumuman Baru</h3>
                <p class="text-xs text-slate-500 mt-1">Pengumuman dapat ditargetkan ke RW, RT, atau semua warga.</p>
            </div>
            <a href="{{ route('announcements.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Pengumuman
            </a>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                <tr>
                    <th class="p-4">Judul</th>
                    <th class="p-4">Penulis</th>
                    <th class="p-4">Target</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($announcements as $ann)
                    <tr>
                        <td class="p-4 font-medium text-slate-900">{{ $ann->title }}</td>
                        <td class="p-4">{{ $ann->creator->name }}</td>
                        <td class="p-4">
                            @if($ann->targets->isEmpty())
                                Semua
                            @else
                                @foreach($ann->targets as $t)
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded text-xs font-medium mr-1">{{ ucfirst($t->target_type) }}#{{ $t->target_id }}</span>
                                @endforeach
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $ann->status === 'published' ? 'bg-emerald-50 text-emerald-600' : ($ann->status === 'archived' ? 'bg-amber-50 text-amber-600' : 'bg-slate-50 text-slate-600') }}">{{ ucfirst($ann->status) }}</span>
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('announcements.show', $ann->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-slate-400">Tidak ada pengumuman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">
            {{ $announcements->links() }}
        </div>
    </div>
</x-app-layout>
