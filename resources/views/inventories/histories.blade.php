<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Riwayat Inventaris') }}
        </h2>
    </x-slot>

    <div class="space-y-6"
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Waktu</th>
                            <th class="p-3">Pengguna</th>
                            <th class="p-3">Aktivitas</th>
                            <th class="p-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($histories as $h)
                            <tr>
                                <td class="p-3 whitespace-nowrap text-xs">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-3 font-semibold text-slate-900">{{ $h->user->name ?? 'Sistem' }}</td>
                                <td class="p-3">{{ $h->aktivitas }}</td>
                                <td class="p-3">{{ $h->keterangan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-slate-400">Tidak ada riwayat inventaris.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                {{ $histories->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
