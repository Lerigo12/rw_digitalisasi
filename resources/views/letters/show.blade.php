<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Detail Permohonan Surat') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex justify-between items-start border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">{{ $letter->letterType->name }}</h3>
                    <p class="text-xs font-mono text-slate-400 mt-0.5">No. Pengajuan: {{ $letter->request_number }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $letter->status === 'completed' ? 'bg-emerald-50 text-emerald-600' : ($letter->status === 'rejected' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600') }}">
                    {{ ucfirst($letter->status) }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase">Pemohon</span>
                    <span class="font-medium text-slate-800">{{ $letter->resident->full_name ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase">Wilayah RT / KK</span>
                    <span class="font-medium text-slate-800">{{ $letter->resident->family->rt->name ?? '-' }}</span>
                </div>
                <div class="col-span-2">
                    <span class="block text-xs font-semibold text-slate-400 uppercase">Keperluan</span>
                    <p class="font-medium text-slate-800 mt-0.5">{{ $letter->purpose }}</p>
                </div>
            </div>

            @if($letter->output)
                <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-lg flex items-center justify-between mt-4">
                    <div>
                        <div class="font-semibold text-sm text-indigo-900">Dokumen Surat Final Tersedia</div>
                        <div class="text-xs text-indigo-700">Nomor Dokumen: {{ $letter->output->document_number }}</div>
                    </div>
                    <a href="{{ route('letters.download', $letter->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-semibold hover:bg-indigo-700">Unduh PDF Surat</a>
                </div>
            @endif

            <div class="pt-4 flex justify-end">
                <a href="{{ route('letters.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-200">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
