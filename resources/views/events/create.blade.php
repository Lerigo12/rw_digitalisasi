<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Buat Kegiatan Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('events.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nama Kegiatan</label>
                <input type="text" name="title" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Lokasi</label>
                <input type="text" name="location" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Mulai</label>
                    <input type="datetime-local" name="starts_at" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Selesai</label>
                    <input type="datetime-local" name="ends_at" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Buka Pendaftaran Peserta?</label>
                <select name="registration_enabled" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="1">Ya</option>
                    <option value="0">Tidak</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <a href="{{ route('events.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-200">Batal</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">Simpan Kegiatan</button>
            </div>
        </form>
    </div>
</x-app-layout>
