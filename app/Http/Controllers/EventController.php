<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $resident = $user->resident;
        $registeredEventIds = [];

        if ($resident) {
            $registeredEventIds = EventRegistration::where('resident_id', $resident->id)
                ->where('status', 'registered')
                ->pluck('event_id')
                ->toArray();
        }

        $events = Event::withCount(['registrations as registrations_count' => function ($q) {
            $q->where('status', 'registered');
        }])->latest()->paginate(10);

        return view('events.index', compact('events', 'registeredEventIds'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->hasRole('resident') && ! $user->hasRole('super-admin') && ! $user->hasRole('rw-admin') && ! $user->hasRole('rt-admin')) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuat kegiatan.');
        }

        return view('events.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('resident') && ! $user->hasRole('super-admin') && ! $user->hasRole('rw-admin') && ! $user->hasRole('rt-admin')) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuat kegiatan.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'registration_enabled' => 'required|boolean',
        ]);

        $event = Event::create([
            'created_by' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'registration_enabled' => $validated['registration_enabled'],
            'status' => 'upcoming',
        ]);

        return redirect()->route('events.index')->with('success', 'Kegiatan berhasil dibuat.');
    }

    public function show(Event $event)
    {
        $user = Auth::user();
        $resident = $user->resident;
        $isRegistered = false;

        if ($resident) {
            $isRegistered = EventRegistration::where('event_id', $event->id)
                ->where('resident_id', $resident->id)
                ->where('status', 'registered')
                ->exists();
        }

        $event->load(['registrations.resident.family.rt', 'attendanceSessions']);
        $activeRegistrationsCount = $event->registrations->where('status', 'registered')->count();

        return view('events.show', compact('event', 'isRegistered', 'activeRegistrationsCount'));
    }

    public function register(Request $request, Event $event)
    {
        $user = Auth::user();
        $resident = $user->resident;
        if (! $resident) {
            return redirect()->back()->with('error', 'Akun tidak terhubung ke data warga.');
        }

        if (! $event->registration_enabled) {
            return redirect()->back()->with('error', 'Pendaftaran untuk kegiatan ini ditutup atau dinonaktifkan.');
        }

        if ($event->status === 'completed' || now()->greaterThan($event->starts_at)) {
            return redirect()->back()->with('error', 'Kegiatan sudah dimulai atau telah selesai, pendaftaran ditutup.');
        }

        $existing = EventRegistration::where('event_id', $event->id)
            ->where('resident_id', $resident->id)
            ->first();

        if ($existing) {
            if ($existing->status === 'registered') {
                return redirect()->back()->with('error', 'Anda sudah terdaftar pada kegiatan ini.');
            } else {
                $existing->update([
                    'status' => 'registered',
                    'registered_at' => now(),
                ]);
            }
        } else {
            EventRegistration::create([
                'event_id' => $event->id,
                'resident_id' => $resident->id,
                'status' => 'registered',
                'registered_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Berhasil mendaftar kegiatan.');
    }

    public function cancelRegistration(Request $request, Event $event)
    {
        $user = Auth::user();
        $resident = $user->resident;
        if (! $resident) {
            return redirect()->back()->with('error', 'Akun tidak terhubung ke data warga.');
        }

        if (now()->greaterThan($event->starts_at)) {
            return redirect()->back()->with('error', 'Pembatalan tidak diizinkan karena kegiatan sudah dimulai.');
        }

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('resident_id', $resident->id)
            ->where('status', 'registered')
            ->first();

        if (! $registration) {
            return redirect()->back()->with('error', 'Anda belum terdaftar pada kegiatan ini.');
        }

        $registration->update([
            'status' => 'cancelled',
        ]);

        return redirect()->back()->with('success', 'Pendaftaran kegiatan berhasil dibatalkan.');
    }

    public function startSession(Request $request, Event $event)
    {
        $validated = $request->validate([
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
        ]);

        $token = bin2hex(random_bytes(16));
        $hash = hash('sha256', $token);

        AttendanceSession::create([
            'event_id' => $event->id,
            'token_hash' => $hash,
            'valid_from' => $validated['valid_from'],
            'valid_until' => $validated['valid_until'],
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('events.show', $event->id)->with('success', "Sesi QR dibuat. Token: {$token}");
    }

    public function complete(Event $event)
    {
        $user = Auth::user();
        if ($user->hasRole('resident') && ! $user->hasRole('super-admin') && ! $user->hasRole('rw-admin') && ! $user->hasRole('rt-admin')) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah status kegiatan.');
        }

        $event->update([
            'status' => 'completed',
        ]);

        return redirect()->route('events.index')->with('success', 'Kegiatan berhasil ditandai sebagai selesai.');
    }
}
