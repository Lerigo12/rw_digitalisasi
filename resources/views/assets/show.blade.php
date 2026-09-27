<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Detail Inventaris') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-900 text-lg">{{ $asset->name }}</h3>
            <p class="mt-2 text-slate-700">{{ $asset->description ?? 'Deskripsi belum diisi.' }}</p>
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <strong class="block text-xs text-slate-500 uppercase">Kode Aset</strong>
                    {{ $asset->asset_code }}
                </div>
                <div>
                    <strong class="block text-xs text-slate-500 uppercase">Jumlah</strong>
                    {{ $asset->quantity }}
                </div>
                <div>
                    <strong class="block text-xs text-slate-500 uppercase">Kondisi</strong>
                    {{ $asset->condition }}
                </div>
                <div>
                    <strong class="block text-xs text-slate-500 uppercase">Lokasi</strong>
                    {{ $asset->storage_location ?? '-' }}
                </div>
            </div>

            @if(!$asset->loans->isEmpty())
                <div class="mt-6">
                    <h4 class="font-semibold text-slate-800 mb-2">Riwayat Peminjaman</h4>
                    <ul class="list-disc list-inside text-sm text-slate-600 space-y-1">
                        @foreach($asset->loans as $loan)
                            <li>
                                Peminjam: {{ $loan->borrowerResident->full_name ?? $loan->borrower_name }}
                                (Jumlah: {{ $loan->quantity }}) —
                                Status: {{ ucfirst($loan->status) }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-6 flex justify-end">
                <a href="{{ route('assets.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-200">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
