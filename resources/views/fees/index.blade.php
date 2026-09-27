<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Iuran & Tagihan Warga') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($isStaff)
            <!-- Admin Panels for Fee Types & Bill Generation -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Configure Fee Type -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Tambah Jenis Iuran Baru</h3>
                    <form action="{{ route('fees.types.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Cakupan (Scope)</label>
                                <select name="scope_type" class="w-full rounded-lg border-slate-300 text-sm">
                                    <option value="rw">RW</option>
                                    <option value="rt">RT</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">ID Scope (ID RW/RT)</label>
                                <input type="number" name="scope_id" value="1" class="w-full rounded-lg border-slate-300 text-sm" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nama Iuran</label>
                            <input type="text" name="name" placeholder="Misal: Iuran Keamanan & Kebersihan" class="w-full rounded-lg border-slate-300 text-sm" required>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nominal (Rp)</label>
                                <input type="number" name="amount" placeholder="50000" class="w-full rounded-lg border-slate-300 text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tgl Jatuh Tempo</label>
                                <input type="number" name="due_day" value="10" min="1" max="31" class="w-full rounded-lg border-slate-300 text-sm" required>
                            </div>
                        </div>
                        <div x-data="{ 
                            hasBank: true, 
                            hasQris: true 
                        }" class="space-y-3 pt-2 border-t border-slate-100">
                            <label class="block text-xs font-bold text-slate-700 uppercase">Metode Pembayaran Yang Diizinkan</label>
                            
                            <div class="space-y-2">
                                <label class="flex items-center space-x-2 text-sm text-slate-700">
                                    <input type="checkbox" name="allowed_payment_methods[]" value="bank_transfer" x-model="hasBank" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span>Transfer Bank</span>
                                </label>
                                
                                <div x-show="hasBank" class="pl-6 space-y-2 pt-1 pb-2">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-500 uppercase mb-1">Nama Bank</label>
                                        <input type="text" name="bank_name" placeholder="Misal: BCA / Mandiri" class="w-full rounded-lg border-slate-300 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-500 uppercase mb-1">Nomor Rekening</label>
                                        <input type="text" name="bank_account_number" placeholder="1234567890" class="w-full rounded-lg border-slate-300 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-500 uppercase mb-1">Nama Pemilik Rekening</label>
                                        <input type="text" name="bank_account_name" placeholder="Kas RW/RT 01" class="w-full rounded-lg border-slate-300 text-xs">
                                    </div>
                                </div>

                                <label class="flex items-center space-x-2 text-sm text-slate-700">
                                    <input type="checkbox" name="allowed_payment_methods[]" value="qris" x-model="hasQris" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span>QRIS</span>
                                </label>

                                <div x-show="hasQris" class="pl-6 pt-1 pb-2">
                                    <label class="block text-[11px] font-semibold text-slate-500 uppercase mb-1">Upload Gambar QRIS</label>
                                    <input type="file" name="qris_image" class="w-full text-xs text-slate-500">
                                </div>

                                <label class="flex items-center space-x-2 text-sm text-slate-700">
                                    <input type="checkbox" name="allowed_payment_methods[]" value="manual" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span>Manual / Cash</span>
                                </label>
                            </div>
                        </div>

                        <input type="hidden" name="period_type" value="monthly">
                        <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-sm font-semibold">Simpan Jenis Iuran</button>
                    </form>
                </div>

                <!-- Generate Bills -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm" x-data="{
                    feeTypes: {{ json_encode($feeTypes) }},
                    selectedFeeTypeId: '',
                    amount: '',
                    updateAmount() {
                        let found = this.feeTypes.find(t => t.id == this.selectedFeeTypeId);
                        this.amount = found ? found.amount : '';
                    }
                }">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Buat Tagihan Iuran Warga</h3>
                    @if($errors->any())
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-xs">
                            <ul class="list-disc pl-4 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('fees.bills.generate') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Pilih Nama Warga</label>
                            <select name="resident_id" class="w-full rounded-lg border-slate-300 text-sm" required>
                                <option value="">Pilih nama warga</option>
                                @forelse($residents as $resident)
                                    <option value="{{ $resident->id }}" @selected(old('resident_id') == $resident->id)>
                                        {{ $resident->full_name }} — {{ $resident->family->rt->name ?? 'RT' }}
                                    </option>
                                @empty
                                    <option value="" disabled>Belum ada warga aktif yang tersedia untuk RT Anda.</option>
                                @endforelse
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Pilih Jenis Iuran</label>
                            <select name="fee_type_id" x-model="selectedFeeTypeId" @change="updateAmount()" class="w-full rounded-lg border-slate-300 text-sm" required>
                                <option value="">Pilih jenis iuran</option>
                                @foreach($feeTypes as $type)
                                    <option value="{{ $type->id }}" @selected(old('fee_type_id') == $type->id)>
                                        {{ $type->name }} (Rp {{ number_format($type->amount, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Periode Tagihan</label>
                            <input type="text" name="period_key" value="{{ old('period_key', date('Y-m')) }}" placeholder="2026-09" class="w-full rounded-lg border-slate-300 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nominal Tagihan (Rp)</label>
                            <input type="number" name="amount_due" x-model="amount" class="w-full rounded-lg border-slate-300 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Keterangan (Opsional)</label>
                            <textarea name="description" rows="2" class="w-full rounded-lg border-slate-300 text-sm" placeholder="Catatan tambahan tagihan...">{{ old('description') }}</textarea>
                        </div>
                        <div class="flex justify-end space-x-2 pt-2">
                            <button type="reset" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold">Simpan Tagihan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Bills List -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-900">{{ $isStaff ? 'Daftar Tagihan & Pembayaran Warga' : 'Daftar Tagihan Iuran Anda' }}</h3>
            </div>
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="p-4">Jenis Iuran</th>
                        <th class="p-4">Nama Warga / RT</th>
                        <th class="p-4">Periode</th>
                        <th class="p-4">Jumlah Tagihan</th>
                        @if($isStaff)
                            <th class="p-4">Metode</th>
                        @endif
                        <th class="p-4">Status</th>
                        @if($isStaff)
                            <th class="p-4">Bukti Pembayaran</th>
                            <th class="p-4 text-right">Aksi</th>
                        @else
                            <th class="p-4 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bills as $bill)
                        @php
                            $latestPayment = $bill->payments->first();
                        @endphp
                        <tr>
                            <td class="p-4 font-semibold text-slate-900">{{ $bill->feeType->name }}</td>
                            <td class="p-4">
                                <div class="text-slate-800">{{ $bill->resident->full_name ?? 'Warga' }}</div>
                                <div class="text-xs text-slate-400">{{ $bill->resident->family->rt->name ?? '-' }}</div>
                            </td>
                            <td class="p-4">{{ $bill->period_key }}</td>
                            <td class="p-4 font-bold text-slate-900">Rp {{ number_format($bill->amount_due, 0, ',', '.') }}</td>
                            
                            @if($isStaff)
                                <td class="p-4">
                                    @if($latestPayment)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $latestPayment->payment_method === 'qris' ? 'bg-purple-100 text-purple-700' : ($latestPayment->payment_method === 'manual' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                            {{ $latestPayment->payment_method === 'qris' ? 'QRIS' : ($latestPayment->payment_method === 'manual' ? 'Manual' : 'Transfer Bank') }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                            @endif

                            <td class="p-4">
                                @if($bill->status === 'paid')
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-600">Lunas</span>
                                @elseif($latestPayment && $latestPayment->status === 'pending')
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-600">Menunggu Verifikasi</span>
                                @elseif($latestPayment && $latestPayment->status === 'rejected')
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-red-50 text-red-600">Ditolak</span>
                                    <div class="text-[11px] text-red-500 mt-1">Alasan: {{ $latestPayment->rejection_reason }}</div>
                                @else
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-600">Belum Lunas</span>
                                @endif
                            </td>

                            @if($isStaff)
                                <td class="p-4">
                                    @if($latestPayment && $latestPayment->proofs->count() > 0)
                                        <button onclick="document.getElementById('modal-proof-{{ $latestPayment->id }}').classList.remove('hidden')" class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-white rounded text-xs font-semibold">
                                            Lihat Bukti
                                        </button>

                                        <!-- Proof Detail Modal -->
                                        <div id="modal-proof-{{ $latestPayment->id }}" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4 overflow-y-auto">
                                            <div class="bg-white rounded-xl max-w-xl w-full p-6 text-left shadow-xl my-8">
                                                <h4 class="font-bold text-slate-900 text-lg mb-1">Detail Bukti Pembayaran</h4>
                                                <p class="text-xs text-slate-500 mb-4 bg-slate-50 p-3 rounded-lg border border-slate-100 space-y-1">
                                                    <div><strong>Nama Warga:</strong> {{ $bill->resident->full_name }}</div>
                                                    <div><strong>Jenis Iuran:</strong> {{ $bill->feeType->name }}</div>
                                                    <div><strong>Periode:</strong> {{ $bill->period_key }}</div>
                                                    <div><strong>Nominal:</strong> Rp {{ number_format($latestPayment->total_amount, 0, ',', '.') }}</div>
                                                    <div><strong>Metode:</strong> {{ strtoupper(str_replace('_', ' ', $latestPayment->payment_method)) }}</div>
                                                    <div><strong>Tanggal Bayar:</strong> {{ \Carbon\Carbon::parse($latestPayment->payment_date)->format('d/m/Y') }}</div>
                                                    <div><strong>Catatan Warga:</strong> {{ $latestPayment->notes ?: '-' }}</div>
                                                </p>

                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">File Bukti Pembayaran</label>
                                                    @foreach($latestPayment->proofs as $proof)
                                                        @php
                                                            $ext = pathinfo($proof->file_path, PATHINFO_EXTENSION);
                                                        @endphp
                                                        @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png']))
                                                            <div class="border rounded-lg p-2 bg-slate-50 text-center">
                                                                <img src="{{ asset('storage/' . $proof->file_path) }}" alt="Bukti Pembayaran" class="max-h-64 mx-auto rounded">
                                                            </div>
                                                        @else
                                                            <div class="flex items-center justify-between p-3 bg-slate-50 border rounded-lg">
                                                                <span class="text-xs font-semibold text-slate-700">{{ $proof->original_filename }} (PDF)</span>
                                                                <a href="{{ asset('storage/' . $proof->file_path) }}" target="_blank" class="px-3 py-1 bg-indigo-600 text-white rounded text-xs font-semibold">Buka PDF</a>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>

                                                <div class="flex justify-end pt-2">
                                                    <button type="button" onclick="document.getElementById('modal-proof-{{ $latestPayment->id }}').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">Tidak ada file</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    @if($latestPayment && $latestPayment->status === 'pending')
                                        <div class="flex justify-end space-x-2">
                                            <!-- Approve Button Modal trigger -->
                                            <button onclick="document.getElementById('modal-approve-{{ $latestPayment->id }}').classList.remove('hidden')" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-semibold">
                                                Approve
                                            </button>
                                            <!-- Reject Button Modal trigger -->
                                            <button onclick="document.getElementById('modal-reject-{{ $latestPayment->id }}').classList.remove('hidden')" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-semibold">
                                                Reject
                                            </button>
                                        </div>

                                        <!-- Approve Modal -->
                                        <div id="modal-approve-{{ $latestPayment->id }}" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
                                            <div class="bg-white rounded-xl max-w-md w-full p-6 text-left shadow-xl">
                                                <h4 class="font-bold text-slate-900 text-lg mb-1">Konfirmasi Persetujuan Pembayaran</h4>
                                                <p class="text-xs text-slate-500 mb-4 bg-slate-50 p-3 rounded-lg border border-slate-100 space-y-1">
                                                    <div><strong>Nama Warga:</strong> {{ $bill->resident->full_name }}</div>
                                                    <div><strong>Jenis Iuran:</strong> {{ $bill->feeType->name }}</div>
                                                    <div><strong>Periode:</strong> {{ $bill->period_key }}</div>
                                                    <div><strong>Nominal:</strong> Rp {{ number_format($latestPayment->total_amount, 0, ',', '.') }}</div>
                                                    <div><strong>Metode:</strong> {{ strtoupper(str_replace('_', ' ', $latestPayment->payment_method)) }}</div>
                                                </p>
                                                <p class="text-xs text-slate-600 mb-6">Apakah Anda yakin ingin menyetujui pembayaran ini? Pastikan bukti pembayaran sudah diperiksa.</p>
                                                
                                                <form action="{{ route('fees.verify', $latestPayment->id) }}" method="POST" class="flex justify-end space-x-2">
                                                    @csrf
                                                    <input type="hidden" name="decision" value="verified">
                                                    <button type="button" onclick="document.getElementById('modal-approve-{{ $latestPayment->id }}').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">Batal</button>
                                                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-semibold">Setujui Pembayaran</button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div id="modal-reject-{{ $latestPayment->id }}" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
                                            <div class="bg-white rounded-xl max-w-md w-full p-6 text-left shadow-xl">
                                                <h4 class="font-bold text-slate-900 text-lg mb-1">Tolak Pembayaran</h4>
                                                <p class="text-xs text-slate-500 mb-4 bg-slate-50 p-3 rounded-lg border border-slate-100 space-y-1">
                                                    <div><strong>Nama Warga:</strong> {{ $bill->resident->full_name }}</div>
                                                    <div><strong>Jenis Iuran:</strong> {{ $bill->feeType->name }}</div>
                                                    <div><strong>Periode:</strong> {{ $bill->period_key }}</div>
                                                    <div><strong>Nominal:</strong> Rp {{ number_format($latestPayment->total_amount, 0, ',', '.') }}</div>
                                                    <div><strong>Metode:</strong> {{ strtoupper(str_replace('_', ' ', $latestPayment->payment_method)) }}</div>
                                                </p>

                                                <form action="{{ route('fees.verify', $latestPayment->id) }}" method="POST" class="space-y-4">
                                                    @csrf
                                                    <input type="hidden" name="decision" value="rejected">
                                                    <div>
                                                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Alasan Penolakan (Wajib)</label>
                                                        <textarea name="notes" rows="3" class="w-full rounded-lg border-slate-300 text-xs" placeholder="Contoh: Bukti transfer tidak jelas, nominal tidak sesuai tagihan..." required maxlength="1000"></textarea>
                                                    </div>
                                                    <div class="flex justify-end space-x-2">
                                                        <button type="button" onclick="document.getElementById('modal-reject-{{ $latestPayment->id }}').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">Batal</button>
                                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-xs font-semibold">Tolak Pembayaran</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">Selesai</span>
                                    @endif
                                </td>
                            @else
                                <td class="p-4 text-right">
                                    @if($bill->status === 'paid')
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-600">Lunas</span>
                                    @elseif($latestPayment && $latestPayment->status === 'pending')
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-600">Menunggu Verifikasi</span>
                                    @elseif($bill->status === 'unpaid' || ($latestPayment && $latestPayment->status === 'rejected'))
                                                            @unless($isStaff)
                                                                <button onclick="document.getElementById('modal-pay-{{ $bill->id }}').classList.remove('hidden')" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs font-semibold">
                                                                    {{ $latestPayment && $latestPayment->status === 'rejected' ? 'Kirim Ulang Bukti' : 'Bayar' }}
                                                                </button>
                                                            @endunless

                                        <!-- Pay Modal -->
                                        <div id="modal-pay-{{ $bill->id }}" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4 overflow-y-auto">
                                            <div class="bg-white rounded-xl max-w-lg w-full p-6 text-left shadow-xl my-8">
                                                <h4 class="font-bold text-slate-900 text-lg mb-1">Konfirmasi & Bukti Pembayaran</h4>
                                                <p class="text-xs text-slate-500 mb-4 bg-slate-50 p-3 rounded-lg border border-slate-100">
                                                    <strong>Tagihan:</strong> {{ $bill->feeType->name }}<br>
                                                    <strong>Periode:</strong> {{ $bill->period_key }}<br>
                                                    <strong>Nominal:</strong> Rp {{ number_format($bill->amount_due, 0, ',', '.') }}
                                                </p>

                                                @if($errors->any() && old('fee_bill_id') == $bill->id)
                                                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-xs">
                                                        <ul class="list-disc pl-4 space-y-1">
                                                            @foreach($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                                
                                                <form action="{{ route('fees.pay') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                                    @csrf
                                                    <input type="hidden" name="fee_bill_id" value="{{ $bill->id }}">
                                                    
                                                        @php
                                                            $allowedMethods = $bill->feeType->allowed_payment_methods ?? ['bank_transfer', 'qris', 'manual'];
                                                            $firstAllowed = $allowedMethods[0] ?? 'manual';
                                                        @endphp
                                                        <div x-data="{ method: '{{ $firstAllowed }}' }" class="space-y-4">
                                                            <div>
                                                                <label class="block text-xs font-semibold text-slate-600 uppercase mb-2">Metode Pembayaran</label>
                                                                <div class="grid grid-cols-{{ count($allowedMethods) }} gap-3">
                                                                    @if(in_array('bank_transfer', $allowedMethods))
                                                                        <label @click="method = 'bank_transfer'" :class="method === 'bank_transfer' ? 'border-indigo-600 bg-indigo-50/50 text-indigo-900 ring-2 ring-indigo-600' : 'border-slate-200 text-slate-700 hover:bg-slate-50'" class="border rounded-xl p-3 cursor-pointer text-center transition flex flex-col items-center justify-center">
                                                                            <input type="radio" name="payment_method" value="bank_transfer" x-model="method" class="sr-only" required>
                                                                            <span class="text-xs font-bold">Transfer Bank</span>
                                                                        </label>
                                                                    @endif
                                                                    @if(in_array('qris', $allowedMethods))
                                                                        <label @click="method = 'qris'" :class="method === 'qris' ? 'border-indigo-600 bg-indigo-50/50 text-indigo-900 ring-2 ring-indigo-600' : 'border-slate-200 text-slate-700 hover:bg-slate-50'" class="border rounded-xl p-3 cursor-pointer text-center transition flex flex-col items-center justify-center">
                                                                            <input type="radio" name="payment_method" value="qris" x-model="method" class="sr-only">
                                                                            <span class="text-xs font-bold">QRIS</span>
                                                                        </label>
                                                                    @endif
                                                                    @if(in_array('manual', $allowedMethods))
                                                                        <label @click="method = 'manual'" :class="method === 'manual' ? 'border-indigo-600 bg-indigo-50/50 text-indigo-900 ring-2 ring-indigo-600' : 'border-slate-200 text-slate-700 hover:bg-slate-50'" class="border rounded-xl p-3 cursor-pointer text-center transition flex flex-col items-center justify-center">
                                                                            <input type="radio" name="payment_method" value="manual" x-model="method" class="sr-only">
                                                                            <span class="text-xs font-bold">Manual</span>
                                                                        </label>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div x-show="method === 'bank_transfer'" class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                                                                <div class="text-xs font-bold text-slate-800 uppercase">Informasi Rekening Tujuan</div>
                                                                <div class="text-xs text-slate-600">Bank: <strong>{{ $bill->feeType->bank_name ?? 'BCA' }}</strong> | No. Rekening: <strong>{{ $bill->feeType->bank_account_number ?? '1234567890' }}</strong> (Atas Nama: {{ $bill->feeType->bank_account_name ?? 'Kas RW/RT' }})</div>
                                                            </div>

                                                            <div x-show="method === 'qris'" class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-center space-y-2">
                                                                <div class="text-xs font-bold text-slate-800 uppercase text-left">QRIS Resmi</div>
                                                                @if(!empty($bill->feeType->qris_image_path))
                                                                    <img src="{{ asset('storage/' . $bill->feeType->qris_image_path) }}" alt="QRIS" class="max-h-48 mx-auto rounded">
                                                                @else
                                                                    <div class="w-28 h-28 bg-slate-200 mx-auto rounded flex items-center justify-center text-slate-500 font-bold text-xs">[ QRIS ]</div>
                                                                @endif
                                                            </div>

                                                            <div>
                                                                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tanggal Bayar</label>
                                                                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-slate-300 text-sm" required>
                                                            </div>

                                                            <div x-show="method !== 'manual'">
                                                                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Upload Bukti Transfer (JPG/PNG/PDF) <span class="text-xs text-slate-400 font-normal">(Opsional)</span></label>
                                                                <input type="file" name="proof_file" class="w-full text-xs text-slate-500">
                                                            </div>

                                                            <div x-show="method === 'manual'">
                                                                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Upload Bukti / Nota (Opsional)</label>
                                                                <input type="file" name="proof_file" class="w-full text-xs text-slate-500">
                                                            </div>

                                                            <div>
                                                                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Catatan Tambahan</label>
                                                                <textarea name="notes" rows="2" class="w-full rounded-lg border-slate-300 text-sm" placeholder="Catatan..."></textarea>
                                                            </div>
                                                        </div>
                                                    <div class="flex justify-end space-x-2 pt-2">
                                                        <button type="button" onclick="document.getElementById('modal-pay-{{ $bill->id }}').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">Batal</button>
                                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-semibold">Kirim</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">Lunas</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isStaff ? 8 : 6 }}" class="p-6 text-center text-slate-400">Belum ada tagihan iuran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-100">
                {{ $bills->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
