<?php

namespace App\Http\Controllers;

use App\Models\LetterApproval;
use App\Models\LetterOutput;
use App\Models\LetterRequest;
use App\Models\LetterType;
use App\Notifications\GeneralNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LetterController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isStaff = $user->hasRole('super-admin') || $user->hasRole('rw-admin') || $user->hasRole('rt-admin') || $user->hasRole('sekretaris-rw');

        if ($isStaff) {
            $letterRequests = LetterRequest::with(['letterType', 'resident.family.rt', 'output'])->latest()->paginate(15);
        } else {
            $letterRequests = LetterRequest::with(['letterType', 'output'])
                ->where('resident_id', $user->resident_id)
                ->latest()
                ->paginate(15);
        }

        $letterTypes = LetterType::paginate(10);

        return view('letters.index', compact('letterRequests', 'letterTypes', 'isStaff'));
    }

    public function create()
    {
        $letterTypes = LetterType::where('is_active', true)->get();

        return view('letters.create', compact('letterTypes'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user->resident_id) {
            return redirect()->back()->with('error', 'Akun Anda belum terhubung dengan data warga untuk mengajukan surat.');
        }

        $validated = $request->validate([
            'letter_type_id' => 'required|exists:letter_types,id',
            'purpose' => 'required|string',
        ]);

        $letterType = LetterType::where('id', $validated['letter_type_id'])
            ->where('is_active', true)
            ->firstOrFail();

        DB::transaction(function () use ($validated, $user, $letterType) {
            $requestNumber = 'REQ/'.date('Y/m/').strtoupper(dechex(rand(1000, 9999)));

            LetterRequest::create([
                'request_number' => $requestNumber,
                'resident_id' => $user->resident_id,
                'letter_type_id' => $letterType->id,
                'purpose' => $validated['purpose'],
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);
        });

        return redirect()->route('letters.index')->with('success', 'Permohonan surat berhasil diajukan.');
    }

    public function storeType(Request $request)
    {
        $user = Auth::user();
        $isStaff = $user->hasRole('super-admin') || $user->hasRole('rw-admin') || $user->hasRole('sekretaris-rw');
        if (! $isStaff) {
            abort(403, 'Anda tidak memiliki hak mengelola jenis surat.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'scope_type' => 'required|in:rw,rt',
            'scope_id' => 'required|integer',
            'is_active' => 'required|boolean',
            'template_content' => 'nullable|string',
        ]);

        LetterType::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'],
            'requirements' => $validated['requirements'],
            'scope_type' => $validated['scope_type'],
            'scope_id' => $validated['scope_id'],
            'is_active' => $validated['is_active'],
            'template_content' => $validated['template_content'] ?? null,
        ]);

        return redirect()->route('letters.index')->with('success', 'Jenis surat berhasil ditambahkan.');
    }

    public function updateType(Request $request, LetterType $letterType)
    {
        $user = Auth::user();
        $isStaff = $user->hasRole('super-admin') || $user->hasRole('rw-admin') || $user->hasRole('sekretaris-rw');
        if (! $isStaff) {
            abort(403, 'Anda tidak memiliki hak mengelola jenis surat.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'scope_type' => 'required|in:rw,rt',
            'scope_id' => 'required|integer',
            'is_active' => 'required|boolean',
            'template_content' => 'nullable|string',
        ]);

        $letterType->update($validated);

        return redirect()->route('letters.index')->with('success', 'Jenis surat berhasil diperbarui.');
    }

    public function toggleType(LetterType $letterType)
    {
        $user = Auth::user();
        $isStaff = $user->hasRole('super-admin') || $user->hasRole('rw-admin') || $user->hasRole('sekretaris-rw');
        if (! $isStaff) {
            abort(403, 'Anda tidak memiliki hak mengelola jenis surat.');
        }

        $letterType->update([
            'is_active' => ! $letterType->is_active,
        ]);

        return redirect()->route('letters.index')->with('success', 'Status jenis surat berhasil diubah.');
    }

    public function show(LetterRequest $letter)
    {
        $user = Auth::user();
        $isStaff = $user->hasRole('super-admin') || $user->hasRole('rw-admin') || $user->hasRole('rt-admin') || $user->hasRole('sekretaris-rw');

        if (! $isStaff && $letter->resident_id !== $user->resident_id) {
            abort(403, 'Unauthorized access.');
        }

        $letter->load(['letterType', 'resident.family.rt', 'approvals.user', 'output']);

        return view('letters.show', compact('letter'));
    }

    public function approve(Request $request, LetterRequest $letter)
    {
        $validated = $request->validate([
            'decision' => 'required|in:approved,rejected',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $letter) {
            LetterApproval::create([
                'letter_request_id' => $letter->id,
                'user_id' => Auth::id(),
                'step_order' => 1,
                'decision' => $validated['decision'],
                'notes' => $validated['notes'] ?? null,
                'acted_at' => now(),
            ]);

            $statusText = 'diproses';
            if ($validated['decision'] === 'approved') {
                $statusText = 'disetujui';
                if (! $letter->output) {
                    $docNumber = 'DOC/RW/'.date('Y/m/').rand(100, 999);
                    $filePath = 'letters/sample-letter-output.pdf';

                    $letter->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]);

                    LetterOutput::create([
                        'letter_request_id' => $letter->id,
                        'document_number' => $docNumber,
                        'file_path' => $filePath,
                        'generated_by' => Auth::id(),
                        'generated_at' => now(),
                    ]);
                }
            } else {
                $statusText = 'ditolak';
                $letter->update([
                    'status' => 'rejected',
                ]);
            }

            if ($letter->resident && $letter->resident->user) {
                $letter->resident->user->notify(new GeneralNotification(
                    'Status Surat Diperbarui',
                    'Pengajuan surat Anda ('.optional($letter->letterType)->name.') telah '.$statusText.'.',
                    'letter',
                    route('letters.show', $letter->id)
                ));
            }
        });

        return redirect()->route('letters.index')->with('success', 'Status permohonan surat berhasil diproses.');
    }

    public function download(LetterRequest $letter)
    {
        $user = Auth::user();
        $isStaff = $user->hasRole('super-admin') || $user->hasRole('rw-admin') || $user->hasRole('rt-admin') || $user->hasRole('sekretaris-rw');

        if (! $isStaff && $letter->resident_id !== $user->resident_id) {
            abort(403, 'Unauthorized access.');
        }

        if ($letter->status !== 'completed' || ! $letter->output) {
            return redirect()->back()->with('error', 'Dokumen surat resmi belum tersedia atau belum disetujui.');
        }

        $letter->load(['letterType', 'resident.family.rt', 'output']);

        $template = $letter->letterType->template_content;
        $resident = $letter->resident;
        $renderedHtml = null;

        if ($template) {
            $renderedHtml = str_replace([
                '{{nama_warga}}',
                '{{nik}}',
                '{{nomor_kk}}',
                '{{tempat_lahir}}',
                '{{tanggal_lahir}}',
                '{{alamat}}',
                '{{rt}}',
                '{{rw}}',
                '{{keperluan}}',
                '{{nomor_surat}}',
                '{{tanggal_surat}}',
            ], [
                $resident?->full_name ?? '-',
                $resident?->nik ?? '-',
                $resident?->family?->family_number ?? '-',
                $resident?->birth_place ?? '-',
                $resident?->birth_date ? Carbon::parse($resident->birth_date)->locale('id')->translatedFormat('d F Y') : '-',
                $resident?->address ?? '-',
                $resident?->family?->rt?->name ?? '-',
                '05',
                $letter->purpose ?? '-',
                $letter->output->document_number ?? '-',
                $letter->output->generated_at ? Carbon::parse($letter->output->generated_at)->locale('id')->translatedFormat('d F Y') : ($letter->completed_at ? Carbon::parse($letter->completed_at)->locale('id')->translatedFormat('d F Y') : now()->locale('id')->translatedFormat('d F Y')),
            ], $template);
        } else {
            $renderedHtml = 'Template surat untuk jenis ini belum diatur oleh admin/sekretaris.';
        }

        $pdf = Pdf::loadView('letters.pdf', compact('letter', 'resident', 'renderedHtml'));

        $fileName = 'surat-'.strtolower(str_replace(['/', ' '], '-', $letter->request_number)).'.pdf';

        return $pdf->download($fileName);
    }
}
