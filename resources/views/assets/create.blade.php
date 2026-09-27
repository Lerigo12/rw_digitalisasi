<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Tambah Inventaris Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('assets.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">RW (opsional)</label>
                    <select name="rw_id" class="w-full rounded-lg border-slate-300 text-sm">
                        <option value="">--- Pilih RW ---</option>
                        @foreach($rws as $rw)
                            <option value="{{ $rw->id }}">{{ $rw->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">RT (opsional)</label>
                    <select name="rt_id" class="w-full rounded-lg border-slate-300 text-sm">
                        <option value="">--- Pilih RT ---</option>
                        @foreach($rts as $rt)
                            <option value="{{ $rt->id }}">{{ $rt->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Kode Aset (unik)</label>
                <input type="text" name="asset_code" class="w-full rounded-lg border-slate-300 text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nama Aset</label>
                <input type="text" name="name" class="w-full rounded-lg border-slate-300 text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Deskripsi (opsional)</label>
                <textarea name="description" rows="2" class="w-full rounded-lg border-slate-300 text-sm"></</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Jumlah</label>
                    <input type="number" name="quantity" min="1" class="w-full rounded-lg border-slate-300 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Kondisi</label>
                    <select name="condition" class="w-full rounded-lg border-slate-300 text-sm" required>
                        <option value="good">Baik</option>
                        <option value="damaged">Rusak</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Lokasi Penyimpanan (opsional)</label>
                <input type="text" name="storage_location" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <a href="{{ route('assets.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-200">Batal</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">Simpan Aset</button>
            </div>
        </form>
    </div>
</x-app-layout>
