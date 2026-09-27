<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Detail Pengumuman') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-900 text-lg">{{ $announcement->title }}</h3>
            <p class="mt-2 text-slate-700">{{ $announcement->body }}</p>
            <div class="mt-4"
                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $announcement->status === 'published' ? 'bg-emerald-50 text-emerald-600' : ($announcement->status === 'archived' ? 'bg-amber-50 text-amber-600' : 'bg-slate-50 text-slate-600') }}">
                    {{ ucfirst($announcement->status) }}
                </span>
            </div>
            @if(!$announcement->targets->isEmpty())
                <div class="mt-4">
                    <h4 class="font-semibold text-slate-800 mb-2">Target Pengumuman</h4>
                    <ul class="list-disc list-inside text-sm text-slate-600">
                        @foreach($announcement->targets as $t)
                            <li>{{ ucfirst($t->target_type) }} #{{ $t->target_id }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="mt-6 flex justify-end">
                <a href="{{ route('announcements.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-200">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
