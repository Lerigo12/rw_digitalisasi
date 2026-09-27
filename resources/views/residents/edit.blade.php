<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Edit Data Warga') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('residents.update', $resident->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Pilih Kartu Keluarga (KK)</label>
                <select name="family_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    @foreach($families as $family)
                        <option value="{{ $family->id }}" {{ $resident->family_id == $family->id ? 'selected' : '' }}>
                            KK #{{ $family->id }} (RT: {{ $family->rt->name }}) - {{ $family->address }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nama Lengkap</label>
                <input type="text" name="full_name" value="{{ $resident->full_name }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tempat Lahir</label>
                    <input type="text" name="birth_place" value="{{ $resident->birth_place }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="{{ $resident->birth_date }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Jenis Kelamin</label>
                    <select name="gender" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="L" {{ $resident->gender == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $resident->gender == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Agama</label>
                    <input type="text" name="religion" value="{{ $resident->religion }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Pekerjaan</label>
                    <input type="text" name="occupation" value="{{ $resident->occupation }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ $resident->phone }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Status Warga</label>
                    <select name="status" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active" {{ $resident->status == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="moved" {{ $resident->status == 'moved' ? 'selected' : '' }}>Pindah</option>
                        <option value="deceased" {{ $resident->status == 'deceased' ? 'selected' : '' }}>Meninggal</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Status Verifikasi</label>
                    <select name="verification_status" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="pending" {{ $resident->verification_status == 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="verified" {{ $resident->verification_status == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('residents.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-200">Batal</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">Perbarui Warga</button>
            </div>
        </form>
    </div>
</x-app-layout>
