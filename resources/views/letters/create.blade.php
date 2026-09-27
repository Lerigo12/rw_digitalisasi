<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Buat Permohonan Surat') }}
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('letters.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Pilih Jenis Surat</label>
                <select name="letter_type_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    @foreach($letterTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }} - {{ $type->description }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Keperluan / Keterangan</label>
                <textarea name="purpose" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tuliskan keperluan pengajuan surat secara jelas..." required></textarea>
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <a href="{{ route('letters.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-200">Batal</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">Kirim Permohonan</button>
            </div>
        </form>
    </div>
</x-app-layout>
