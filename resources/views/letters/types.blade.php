<div class="space-y-4">
    <!-- Tabs navigation (already in parent) -->
    <div class="flex space-x-4 mb-4">
        <button @click="tab='requests'" :class="{'border-b-2 border-emerald-600': tab==='requests'}" class="pb-2 text-sm font-medium">Data Pengajuan Surat</button>
        <button @click="tab='types'" :class="{'border-b-2 border-emerald-600': tab==='types'}" class="pb-2 text-sm font-medium">Kelola Jenis Surat</button>
        <button @click="tab='templates'" :class="{'border-b-2 border-emerald-600': tab==='templates'}" class="pb-2 text-sm font-medium">Kelola Template Surat</button>
    </div>

    <div x-show="tab==='types'" class="space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="font-bold text-lg text-slate-800">Kelola Jenis Surat</h3>
            <button type="button" @click="$dispatch('open-modal', 'type-create')" class="px-4 py-2 bg-emerald-600 text-white rounded text-sm font-semibold hover:bg-emerald-700">Tambah Jenis Surat</button>
        </div>

        <!-- Create Modal -->
        <x-modal name="type-create" :show="false">
            <form action="{{ route('letters.types.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <h3 class="font-bold text-lg text-slate-800">Tambah Jenis Surat Baru</h3>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Nama Jenis Surat</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded border-slate-300 text-sm" required placeholder="Contoh: Surat Pengantar KTP">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Kode Surat</label>
                    <input type="text" name="code" value="{{ old('code') }}" class="w-full rounded border-slate-300 text-sm" placeholder="Contoh: SK-KTP">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Deskripsi</label>
                    <textarea name="description" class="w-full rounded border-slate-300 text-sm" placeholder="Deskripsi singkat layanan surat...">{{ old('description') }}</textarea>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Persyaratan</label>
                    <textarea name="requirements" class="w-full rounded border-slate-300 text-sm" placeholder="Dokumen atau syarat yang diperlukan...">{{ old('requirements') }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700">Lingkup</label>
                        <select name="scope_type" class="w-full rounded border-slate-300 text-sm" required>
                            <option value="rw">RW</option>
                            <option value="rt">RT</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700">ID Lingkup (RW/RT ID)</label>
                        <input type="number" name="scope_id" value="{{ old('scope_id', 1) }}" class="w-full rounded border-slate-300 text-sm" required>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="inline-flex items-center text-sm font-medium text-slate-700">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 mr-2">
                        Status Aktif
                    </label>
                </div>
                <div class="space-y-2">
                    <input type="hidden" name="template_content" value="">
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="$dispatch('close-modal', 'type-create')" class="px-4 py-2 bg-slate-200 text-slate-700 rounded text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded text-sm font-semibold hover:bg-emerald-700">Simpan</button>
                </div>
            </form>
        </x-modal>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                <tr>
                    <th class="p-4">Nama</th>
                    <th class="p-4">Kode</th>
                    <th class="p-4">Deskripsi</th>
                    <th class="p-4">Lingkup</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($letterTypes as $type)
                    <tr>
                        <td class="p-4 font-medium">{{ $type->name }}</td>
                        <td class="p-4">{{ $type->code }}</td>
                        <td class="p-4 max-w-xs truncate">{{ $type->description }}</td>
                        <td class="p-4 capitalize">{{ $type->scope_type }}</td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $type->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                                {{ $type->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2">
                            <button @click="$dispatch('open-modal', 'type-edit-{{ $type->id }}')" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                            <form action="{{ route('letters.types.toggle', $type) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-{{ $type->is_active ? 'red' : 'emerald' }}-600 hover:text-{{ $type->is_active ? 'red' : 'emerald' }}-900 text-sm">
                                    {{ $type->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <x-modal name="type-edit-{{ $type->id }}" :show="false">
                        <form action="{{ route('letters.types.update', $type) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700">Nama</label>
                                <input type="text" name="name" value="{{ old('name', $type->name) }}" class="w-full rounded border-gray-300" required>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700">Kode</label>
                                <input type="text" name="code" value="{{ old('code', $type->code) }}" class="w-full rounded border-gray-300">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700">Deskripsi</label>
                                <textarea name="description" class="w-full rounded border-gray-300">{{ old('description', $type->description) }}</textarea>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700">Lingkup</label>
                                <select name="scope_type" class="w-full rounded border-gray-300" required>
                                    <option value="rw" {{ $type->scope_type==='rw' ? 'selected' : '' }}>RW</option>
                                    <option value="rt" {{ $type->scope_type==='rt' ? 'selected' : '' }}>RT</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="inline-flex items-center"><input type="checkbox" name="is_active" value="1" {{ $type->is_active ? 'checked' : '' }}> Aktif</label>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="$dispatch('close-modal', 'type-edit-{{ $type->id }}')" class="px-3 py-1 bg-gray-200 rounded">Batal</button>
                                <button type="submit" class="px-3 py-1 bg-emerald-600 text-white rounded">Simpan</button>
                            </div>
                        </form>
                    </x-modal>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-slate-400">Tidak ada jenis surat.</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-100">{{ $letterTypes->links() }}</div>
        </div>
    </div>
</div>
