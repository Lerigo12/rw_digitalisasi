<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Layanan Surat Menyurat') }}
        </h2>
    </x-slot>

    <div class="space-y-6" x-data="{ tab: 'requests' }">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if($isStaff)
            <!-- Tabs Navigation -->
            <div class="border-b border-slate-200 flex space-x-6">
                <button @click="tab = 'requests'" :class="{'border-emerald-600 text-emerald-600 font-bold': tab === 'requests', 'border-transparent text-slate-500': tab !== 'requests'}" class="pb-3 border-b-2 text-sm focus:outline-none">Data Pengajuan Surat</button>
                <button @click="tab = 'types'" :class="{'border-emerald-600 text-emerald-600 font-bold': tab === 'types', 'border-transparent text-slate-500': tab !== 'types'}" class="pb-3 border-b-2 text-sm focus:outline-none">Kelola Jenis Surat</button>
                <button @click="tab = 'templates'" :class="{'border-emerald-600 text-emerald-600 font-bold': tab === 'templates', 'border-transparent text-slate-500': tab !== 'templates'}" class="pb-3 border-b-2 text-sm focus:outline-none">Kelola Template Surat</button>
            </div>
        @endif

        <!-- Tab 1: Data Pengajuan Surat -->
        <div x-show="tab === 'requests'" class="space-y-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Permohonan Surat Keterangan / Pengantar</h3>
                    <p class="text-xs text-slate-500 mt-1">Layanan administrasi persuratan resmi warga terhubung ke pengurus RT & RW.</p>
                </div>
                <a href="{{ route('letters.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Ajukan Surat Baru
                </a>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-4">No. Pengajuan</th>
                            <th class="p-4">Pemohon / RT</th>
                            <th class="p-4">Jenis Surat</th>
                            <th class="p-4">Keperluan</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($letterRequests as $request)
                            <tr>
                                <td class="p-4 font-mono font-bold text-slate-900 text-xs">{{ $request->request_number }}</td>
                                <td class="p-4">
                                    <div class="font-semibold text-slate-800">{{ $request->resident->full_name ?? 'Warga' }}</div>
                                    <div class="text-xs text-slate-400">{{ $request->resident->family->rt->name ?? '-' }}</div>
                                </td>
                                <td class="p-4 font-medium text-slate-900">{{ $request->letterType->name }}</td>
                                <td class="p-4 max-w-xs truncate text-xs">{{ $request->purpose }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $request->status === 'completed' ? 'bg-emerald-50 text-emerald-600' : ($request->status === 'rejected' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600') }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="p-4 text-right space-x-2">
                                    <a href="{{ route('letters.show', $request->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs">Detail</a>
                                    @if($isStaff && $request->status === 'submitted')
                                        <form action="{{ route('letters.approve', $request->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <input type="hidden" name="decision" value="approved">
                                            <button type="submit" class="px-2.5 py-1 bg-emerald-600 text-white rounded text-xs font-semibold">Setujui</button>
                                        </form>
                                        <form action="{{ route('letters.approve', $request->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <input type="hidden" name="decision" value="rejected">
                                            <button type="submit" class="px-2.5 py-1 bg-red-600 text-white rounded text-xs font-semibold">Tolak</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-slate-400">Belum ada permohonan surat diajukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t border-slate-100">
                    {{ $letterRequests->links() }}
                </div>
            </div>
        </div>

        @if($isStaff)
            <!-- Tab 2: Kelola Jenis Surat -->
            <div x-show="tab === 'types'" style="display: none;" class="space-y-6">
                @include('letters.types')
            </div>

            <!-- Tab 3: Kelola Template Surat -->
            <div x-show="tab === 'templates'" style="display: none;" class="space-y-6" x-data="{ selectedType: '', templateContent: '' }">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-900 text-base">Editor Template Surat</h3>
                    <p class="text-xs text-slate-500">Pilih jenis surat untuk mengubah atau menyusun template konten surat resmi.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-4 md:col-span-1 border-r pr-4">
                            <label class="block text-sm font-medium text-slate-700">Pilih Jenis Surat</label>
                            <select x-model="selectedType" @change="
                                let type = {{ Js::from($letterTypes->items()) }}.find(t => t.id == selectedType);
                                templateContent = type ? (type.template_content || '') : '';
                            " class="w-full rounded border-slate-300 text-sm">
                                <option value="">-- Pilih Jenis Surat --</option>
                                @foreach($letterTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->code }})</option>
                                @endforeach
                            </select>

                            <div class="space-y-2 mt-4">
                                <span class="block text-xs font-semibold text-slate-600 uppercase">Daftar Placeholder Tersedia:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="ph in ['@{{nama_warga}}', '@{{nik}}', '@{{nomor_kk}}', '@{{tempat_lahir}}', '@{{tanggal_lahir}}', '@{{alamat}}', '@{{rt}}', '@{{rw}}', '@{{keperluan}}', '@{{nomor_surat}}', '@{{tanggal_surat}}']">
                                        <button type="button" @click="templateContent += ' ' + ph" class="px-2 py-1 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-xs font-mono rounded border border-slate-200" x-text="ph"></button>
                                    </template>
                                </div>
                                <p class="text-[11px] text-amber-600 mt-2">* Klik placeholder untuk menyisipkan ke dalam isi template surat.</p>
                            </div>
                        </div>

                        <div class="md:col-span-2 space-y-4">
                            <template x-if="selectedType">
                                <form :action="'/letters/types/' + selectedType" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PUT')
                                    <!-- Keep required fields for update -->
                                    <input type="hidden" name="name" :value="{{ Js::from($letterTypes->keyBy('id')) }}[selectedType]?.name">
                                    <input type="hidden" name="code" :value="{{ Js::from($letterTypes->keyBy('id')) }}[selectedType]?.code">
                                    <input type="hidden" name="scope_type" :value="{{ Js::from($letterTypes->keyBy('id')) }}[selectedType]?.scope_type">
                                    <input type="hidden" name="scope_id" :value="{{ Js::from($letterTypes->keyBy('id')) }}[selectedType]?.scope_id">
                                    <input type="hidden" name="is_active" :value="{{ Js::from($letterTypes->keyBy('id')) }}[selectedType]?.is_active">

                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Isi Template Konten (HTML / Markdown)</label>
                                        <textarea name="template_content" x-model="templateContent" rows="12" class="w-full rounded border-slate-300 font-mono text-sm" placeholder="Ketik kop surat, judul, isi, dan tanda tangan di sini..."></textarea>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <button type="button" @click="$dispatch('open-modal', 'preview-modal')" class="px-4 py-2 bg-slate-800 text-white rounded text-sm font-semibold">Preview Template</button>
                                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded text-sm font-semibold hover:bg-emerald-700">Simpan Template</button>
                                    </div>
                                </form>
                            </template>
                            <template x-if="!selectedType">
                                <div class="py-16 text-center text-slate-400 border border-dashed rounded-lg">
                                    Silakan pilih jenis surat di sebelah kiri untuk mulai mengedit template.
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Preview Modal -->
                <x-modal name="preview-modal" :show="false">
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center border-b pb-3">
                            <h4 class="font-bold text-slate-800">Preview Contoh Surat (Data Dummy)</h4>
                            <button type="button" @click="$dispatch('close-modal', 'preview-modal')" class="text-slate-400 hover:text-slate-600">&times;</button>
                        </div>
                        <div class="p-4 bg-slate-50 border rounded font-serif text-sm leading-relaxed space-y-3" x-html="templateContent
                            .replace(/\{\{nama_warga\}\}/g, 'Budi Santoso')
                            .replace(/\{\{nik\}\}/g, '3271234567890001')
                            .replace(/\{\{nomor_kk\}\}/g, '3271234567890002')
                            .replace(/\{\{tempat_lahir\}\}/g, 'Bandung')
                            .replace(/\{\{tanggal_lahir\}\}/g, '17 Agustus 1985')
                            .replace(/\{\{alamat\}\}/g, 'Jl. Merdeka No. 45 RT 002/RW 005')
                            .replace(/\{\{rt\}\}/g, '002')
                            .replace(/\{\{rw\}\}/g, '005')
                            .replace(/\{\{keperluan\}\}/g, 'Pengurusan Berkas Nikah')
                            .replace(/\{\{nomor_surat\}\}/g, '[NOMOR SURAT RESMI AKAN MUNCUL SAAT DISETUJUI]')
                            .replace(/\{\{tanggal_surat\}\}/g, '28 September 2026')
                        "></div>
                        <p class="text-xs text-amber-600">* Catatan: Preview menggunakan data dummy sintetis untuk verifikasi tampilan.</p>
                        <div class="flex justify-end">
                            <button type="button" @click="$dispatch('close-modal', 'preview-modal')" class="px-4 py-2 bg-slate-200 rounded text-sm">Tutup Preview</button>
                        </div>
                    </div>
                </x-modal>
            </div>
        @endif
    </div>
</x-app-layout>
