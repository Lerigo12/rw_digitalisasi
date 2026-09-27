<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Audit Log Aktivitas') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Rekam Jejak Sistem</h3>
                <p class="text-xs text-slate-500 mt-1">Seluruh riwayat aktivitas dan perubahan data oleh pengguna sistem.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-4">Waktu</th>
                            <th class="p-4">Pengguna</th>
                            <th class="p-4">Aksi</th>
                            <th class="p-4">Modul / Data</th>
                            <th class="p-4">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $log)
                            <tr>
                                <td class="p-4 whitespace-nowrap text-xs">{{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}</td>
                                <td class="p-4 font-semibold text-slate-900">{{ $log->user->name ?? 'Sistem/Tamu' }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="p-4 text-xs font-mono">{{ class_basename($log->subject_type ?? '-') }} #{{ $log->subject_id ?? '-' }}</td>
                                <td class="p-4 text-xs font-mono text-slate-500">{{ $log->ip_address ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-400">Belum ada riwayat aktivitas tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
