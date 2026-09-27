<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Ajukan Peminjaman Inventaris') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('asset_loans.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Aset</label>
                    <select name="asset_id" class="w-full rounded-lg border-slate-300 text-sm" required>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->quantity }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Peminjam (Warga)</label>
                    <select name="borrower_resident_id" class="w-full rounded-lg border-slate-300 text-sm" required>
                        @foreach($residents as $resident)
                            <option value="{{ $resident->id }}">{{ $resident->full_name }} ({{ $resident->family->rt->name }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Jumlah</label>
                    <input type="number" name="quantity" min="1" class="w-full rounded-lg border-slate-300 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-slate-300 text-sm" required>
                        <option value="active">Dipinjam (aktif)</option>
                        <option value="returned">Dikembalikan</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <a href="{{ route('asset_loans.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-200">Batal</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">Simpan Peminjaman</button>
            </div>
        </form>
    </div>
</x-app-layout>
