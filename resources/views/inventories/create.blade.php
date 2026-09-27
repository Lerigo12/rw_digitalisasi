<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Tambah Barang Inventaris') }}
        </h2>
    </x-slot>

    <div x-data="{ openCategoryModal: false }" class="space-y-6 max-w-4xl mx-auto">
        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-sm">
                <p class="font-semibold mb-1">Terjadi kesalahan:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Modal Tambah Kategori -->
        <div x-show="openCategoryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;">
            <div class="bg-white rounded-xl max-w-md w-full p-6 space-y-4 shadow-xl">
                <h3 class="font-bold text-lg text-slate-900">Tambah Kategori Baru</h3>
                <form action="{{ route('inventories.categories.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nama Kategori</label>
                        <input type="text" name="nama" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" rows="2" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="openCategoryModal = false" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs">Batal</button>
                        <button type="submit" class="py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-xs">Simpan Kategori</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <form action="{{ route('inventories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Kode Barang</label>
                        <input type="text" name="kode_barang" value="{{ old('kode_barang', $nextKode) }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nama Barang</label>
                        <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-xs font-semibold text-slate-600 uppercase">Kategori</label>
                            <button type="button" @click="openCategoryModal = true" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 underline">+ Kategori Baru</button>
                        </div>
                        <select name="kategori_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('kategori_id') == $cat->id)>{{ $cat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Merek</label>
                        <input type="text" name="merek" value="{{ old('merek') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tipe / Model</label>
                        <input type="text" name="tipe" value="{{ old('tipe') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Jumlah</label>
                            <input type="number" name="jumlah" value="{{ old('jumlah', 1) }}" min="0" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Satuan</label>
                            <input type="text" name="satuan" value="{{ old('satuan', 'Unit') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Kondisi</label>
                        <select name="kondisi" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @foreach(['Baik', 'Cukup Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang'] as $cond)
                                <option value="{{ $cond }}" @selected(old('kondisi') == $cond)>{{ $cond }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @foreach(['Tersedia', 'Dipinjam', 'Rusak', 'Hilang', 'Tidak Aktif'] as $st)
                                <option value="{{ $st }}" @selected(old('status') == $st)>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tahun Perolehan</label>
                        <input type="number" name="tahun_perolehan" value="{{ old('tahun_perolehan', date('Y')) }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Sumber Dana</label>
                        <input type="text" name="sumber_dana" value="{{ old('sumber_dana') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Harga Perolehan (Rp)</label>
                        <input type="number" name="harga_perolehan" value="{{ old('harga_perolehan') }}" min="0" step="0.01" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Lokasi Penyimpanan</label>
                        <input type="text" name="lokasi" value="{{ old('lokasi') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Penanggung Jawab</label>
                        <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Foto Barang</label>
                        <input type="file" name="foto" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Deskripsi / Keterangan</label>
                    <textarea name="deskripsi" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('deskripsi') }}</textarea>
                </div>
                <div class="flex gap-2 pt-4">
                    <button type="submit" class="py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm transition">
                        Simpan Barang
                    </button>
                    <a href="{{ route('inventories.index') }}" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-sm transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
