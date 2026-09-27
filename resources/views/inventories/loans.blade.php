<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Peminjaman Inventaris') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="font-bold text-slate-900 text-base mb-4">Formulir Peminjaman Barang</h3>
            <form action="{{ route('inventories.loans.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ residentId: '{{ old('resident_id', '') }}', phone: '' }" x-init="
                @if($isStaff)
                    $watch('residentId', value => {
                        let select = document.getElementById('resident_select');
                        if (select && select.selectedIndex > 0) {
                            let opt = select.options[select.selectedIndex];
                            phone = opt.getAttribute('data-phone') || '-';
                        } else {
                            phone = '';
                        }
                    });
                    if (residentId) {
                        let select = document.getElementById('resident_select');
                        if (select && select.selectedIndex > 0) {
                            let opt = select.options[select.selectedIndex];
                            phone = opt.getAttribute('data-phone') || '-';
                        }
                    }
                @endif
            ">
                @csrf
                @if($isStaff)
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Pilih Warga (Peminjam)</label>
                        <select name="resident_id" id="resident_select" x-model="residentId" class="w-full rounded-lg border-slate-300 text-sm" required>
                            <option value="">-- Pilih Warga --</option>
                            @foreach($residents as $res)
                                <option value="{{ $res->id }}" data-phone="{{ $res->phone ?? '' }}">
                                    {{ $res->full_name }} (NIK: {{ $res->nik ? substr($res->nik, 0, 6).'****' : '-' }} - HP: {{ $res->phone ?? 'Belum ada HP' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nomor HP Warga</label>
                        <input type="text" x-model="phone" class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm text-slate-500" readonly placeholder="Otomatis terisi dari data warga...">
                    </div>
                @else
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nama Peminjam</label>
                        <input type="text" value="{{ auth()->user()->resident->full_name ?? auth()->user()->name }}" class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm text-slate-700 font-semibold" readonly>
                        @if(!auth()->user()->resident || empty(auth()->user()->resident->phone))
                            <p class="text-xs text-red-600 mt-1">Nomor HP profil Anda belum lengkap. Mohon lengkapi profil terlebih dahulu.</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nomor HP</label>
                        <input type="text" value="{{ auth()->user()->resident->phone ?? '-' }}" class="w-full rounded-lg border-slate-200 bg-slate-50 text-sm text-slate-700 font-semibold" readonly>
                    </div>
                @endif
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Pilih Barang</label>
                    <select name="barang_id" class="w-full rounded-lg border-slate-300 text-sm" required>
                        <option value="">-- Pilih Barang Tersedia --</option>
                        @foreach($inventories as $inv)
                            <option value="{{ $inv->id }}">{{ $inv->nama_barang }} (Tersedia: {{ $inv->tersedia_count }} {{ $inv->satuan }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Jumlah Pinjam</label>
                    <input type="number" name="jumlah" min="1" value="{{ old('jumlah', 1) }}" class="w-full rounded-lg border-slate-300 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tanggal Pinjam</label>
                    <input type="date" name="tanggal_pinjam" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-slate-300 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Rencana Tanggal Kembali</label>
                    <input type="date" name="tanggal_kembali_rencana" value="{{ date('Y-m-d', strtotime('+3 days')) }}" class="w-full rounded-lg border-slate-300 text-sm" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Keperluan</label>
                    <textarea name="keperluan" rows="2" class="w-full rounded-lg border-slate-300 text-sm" placeholder="Tujuan penggunaan barang...">{{ old('keperluan') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 shadow-sm">Ajukan Peminjaman</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-6">
            <h3 class="font-bold text-slate-900 text-base mb-4">Riwayat Peminjaman</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Kode</th>
                            <th class="p-3">Peminjam</th>
                            <th class="p-3">Barang</th>
                            <th class="p-3">Jumlah</th>
                            <th class="p-3">Pinjam</th>
                            <th class="p-3">Rencana Kembali</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($loans as $loan)
                            <tr>
                                <td class="p-3 font-mono text-xs font-bold text-slate-900">{{ $loan->kode_peminjaman }}</td>
                                <td class="p-3 font-semibold text-slate-900">{{ $loan->nama_peminjam }}<br><span class="text-xs text-slate-400">{{ $loan->nomor_hp }}</span></td>
                                <td class="p-3">{{ $loan->inventory->nama_barang ?? '-' }}</td>
                                <td class="p-3">{{ $loan->jumlah }} {{ $loan->inventory->satuan ?? 'Unit' }}</td>
                                <td class="p-3 text-xs">{{ $loan->tanggal_pinjam }}</td>
                                <td class="p-3 text-xs">{{ $loan->tanggal_kembali_rencana }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $loan->status === 'Dikembalikan' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                        {{ $loan->status }}
                                    </span>
                                </td>
                                <td class="p-3 text-center">
                                    @php
                                        $user = auth()->user();
                                        $isStaff = $user && ($user->hasRole('super-admin') || $user->hasRole('rw-admin') || $user->hasRole('rt-admin'));
                                    @endphp
                                    @if($isStaff && in_array($loan->status, ['Menunggu Persetujuan', 'Dipinjam']))
                                        <form action="{{ route('inventories.loans.status', $loan->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="Disetujui">
                                            <button type="submit" class="text-xs bg-indigo-50 text-indigo-600 px-2 py-1 rounded font-semibold hover:bg-indigo-100">Setujui</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-6 text-center text-slate-400">Belum ada data peminjaman inventaris.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $loans->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
