<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Verifikasi Pembayaran Iuran') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="p-4">Pembayar / Akun</th>
                        <th class="p-4">Tgl & Metode</th>
                        <th class="p-4">Total & Rincian Tagihan</th>
                        <th class="p-4">Bukti Transfer</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Keputusan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $payment)
                        <tr>
                            <td class="p-4">
                                <div class="font-semibold text-slate-900">{{ $payment->payerResident->full_name ?? $payment->user->name }}</div>
                                <div class="text-xs text-slate-400">Akun: {{ $payment->user->email }}</div>
                            </td>
                            <td class="p-4">
                                <div>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</div>
                                <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-[10px] font-bold uppercase tracking-wider {{ $payment->payment_method === 'qris' ? 'bg-purple-100 text-purple-700' : ($payment->payment_method === 'manual' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ $payment->payment_method === 'qris' ? 'QRIS' : ($payment->payment_method === 'manual' ? 'Manual' : 'Transfer Bank') }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</div>
                                @foreach($payment->allocations as $alloc)
                                    <div class="text-xs text-slate-500">{{ $alloc->feeBill->feeType->name }} ({{ $alloc->feeBill->period_key }})</div>
                                @endforeach
                            </td>
                            <td class="p-4">
                                @forelse($payment->proofs as $proof)
                                    <a href="{{ asset('storage/' . $proof->file_path) }}" target="_blank" class="text-xs text-indigo-600 underline font-medium">
                                        Lihat Bukti
                                    </a>
                                @empty
                                    <span class="text-xs text-slate-400">Tidak Ada File</span>
                                @endforelse
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $payment->status === 'verified' ? 'bg-emerald-50 text-emerald-600' : ($payment->status === 'rejected' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-1">
                                @if($payment->status === 'pending')
                                    <form action="{{ route('fees.verify', $payment->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="decision" value="verified">
                                        <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-semibold">
                                            Setujui
                                        </button>
                                    </form>
                                    <form action="{{ route('fees.verify', $payment->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="decision" value="rejected">
                                        <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-semibold">
                                            Tolak
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400">Belum ada pembayaran diajukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-100">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
