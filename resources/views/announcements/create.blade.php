<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Buat Pengumuman Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('announcements.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Judul Pengumuman</label>
                <input type="text" name="title" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Deskripsi (opsional)</label>
                <textarea name="body" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            <!-- Targets (optional) -->
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Target Pengumuman (opsional)</label>
                <!-- Repeatable target rows – simple JSON‑style input for demo –>
                <div id="targets-container" class="space-y-2">
                    <!-- Initial empty row -->
                </div>
                <button type="button" id="add-target" class="mt-2 px-3 py-1 bg-slate-100 text-slate-600 rounded text-xs">Tambah Target</button>
            </div>
            
            <div class="flex justify-end space-x-3 pt-2">
                <a href="{{ route('announcements.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-200">Batal</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">Simpan Pengumuman</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('add-target').addEventListener('click', function () {
            const container = document.getElementById('targets-container');
            const index = container.children.length;
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2';
            row.innerHTML = `
                <select name="targets[${index}][target_type]" class="rounded-lg border-slate-300 text-sm" required>
                    <option value="rw">RW</option>
                    <option value="rt">RT</option>
                </select>
                <input type="number" name="targets[${index}][target_id]" placeholder="ID" class="w-16 rounded-lg border-slate-300 text-sm" required />
                <button type="button" class="text-red-500" onclick="this.parentElement.remove()">✕</button>
            `;
            container.appendChild(row);
        });
    </script>
</x-app-layout>
