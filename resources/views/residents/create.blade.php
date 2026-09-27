<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Tambah Data Warga') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        @if($errors->any())
            <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-xs">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('residents.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Bagian 1: Data Kependudukan -->
            <div class="space-y-4" x-data="{
                query: '',
                results: [],
                loading: false,
                selectedFamily: null,
                searchKK() {
                    if (this.query.trim().length < 2) {
                        this.results = [];
                        return;
                    }
                    this.loading = true;
                    fetch('{{ route('families.search') }}?q=' + encodeURIComponent(this.query))
                        .then(res => res.json())
                        .then(data => {
                            this.results = data;
                            this.loading = false;
                        })
                        .catch(() => {
                            this.loading = false;
                        });
                },
                selectFamily(family) {
                    this.selectedFamily = family;
                    this.query = family.family_number;
                    this.results = [];
                },
                clearSelection() {
                    this.selectedFamily = null;
                    this.query = '';
                    this.results = [];
                }
            }">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">A. Data Kependudukan</h3>
                
                <div class="relative">
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nomor Kartu Keluarga (KK)</label>
                    <input type="hidden" name="family_id" :value="selectedFamily ? selectedFamily.id : ''" required>
                    
                    <div class="relative">
                        <input 
                            type="text" 
                            x-model="query" 
                            @input.debounce.300ms="searchKK()" 
                            placeholder="Masukkan nomor KK atau cari nomor KK..." 
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" 
                            autocomplete="off"
                            required
                        >
                        <div x-show="loading" class="absolute right-3 top-2.5 text-xs text-slate-400">
                            Mencari...
                        </div>
                    </div>

                    <!-- Dropdown Results -->
                    <div x-show="results.length > 0 && !selectedFamily" class="absolute z-20 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto divide-y divide-slate-100">
                        <template x-for="item in results" :key="item.id">
                            <div @click="selectFamily(item)" class="p-3 hover:bg-indigo-50 cursor-pointer transition">
                                <div class="font-bold text-sm text-slate-900" x-text="'Nomor KK: ' + item.family_number"></div>
                                <div class="text-xs text-slate-600" x-text="'RT: ' + item.rt_name + ' | Alamat: ' + item.address"></div>
                            </div>
                        </template>
                    </div>

                    <div x-show="query.trim().length >= 2 && results.length === 0 && !loading && !selectedFamily" class="mt-2 p-2 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg text-xs">
                        Nomor KK tidak ditemukan. Silakan pilih KK yang sudah terdaftar atau tambahkan data KK terlebih dahulu melalui menu Data Warga & KK.
                    </div>

                    <!-- Card Informasi KK Terpilih -->
                    <div x-show="selectedFamily" class="mt-3 p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-100 text-indigo-800 mb-2">KK Ditemukan</span>
                                <div class="text-xs text-slate-600"><strong>Nomor KK:</strong> <span x-text="selectedFamily?.family_number"></span></div>
                                <div class="text-xs text-slate-600"><strong>RT:</strong> <span x-text="selectedFamily?.rt_name"></span></div>
                                <div class="text-xs text-slate-600"><strong>Alamat:</strong> <span x-text="selectedFamily?.address"></span></div>
                            </div>
                            <button type="button" @click="clearSelection()" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">Ganti KK</button>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">NIK (16 digit)</label>
                        <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nama Lengkap</label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tempat Lahir</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Jenis Kelamin</label>
                        <select name="gender" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="L" @selected(old('gender') == 'L')>Laki-laki</option>
                            <option value="P" @selected(old('gender') == 'P')>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Agama</label>
                        <input type="text" name="religion" value="{{ old('religion') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Pekerjaan</label>
                        <input type="text" name="occupation" value="{{ old('occupation') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Status Warga</label>
                        <select name="status" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active" @selected(old('status') == 'active')>Aktif</option>
                            <option value="moved" @selected(old('status') == 'moved')>Pindah</option>
                            <option value="deceased" @selected(old('status') == 'deceased')>Meninggal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Status Verifikasi</label>
                        <select name="verification_status" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="pending" @selected(old('verification_status') == 'pending')>Menunggu Verifikasi</option>
                            <option value="verified" @selected(old('verification_status') == 'verified')>Terverifikasi</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Akun Login Warga -->
            <div class="space-y-4 pt-2">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">B. Akun Login Warga</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Email Login</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="email@warga.id" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Password Awal (Min 8 Karakter)</label>
                        <input type="password" name="password" placeholder="••••••••" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('residents.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-200">Batal</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">Simpan Warga</button>
            </div>
        </form>
    </div>
</x-app-layout>
