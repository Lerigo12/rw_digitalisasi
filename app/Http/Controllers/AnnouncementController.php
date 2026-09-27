<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementTarget;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('targets')->latest()->paginate(15);

        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user && $user->hasRole('resident') && ! $user->hasRole('super-admin') && ! $user->hasRole('rw-admin') && ! $user->hasRole('rt-admin') && ! $user->hasRole('sekretaris-rw')) {
            abort(403, 'Akses ditolak. Warga tidak memiliki hak untuk membuat pengumuman.');
        }

        return view('announcements.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user && $user->hasRole('resident') && ! $user->hasRole('super-admin') && ! $user->hasRole('rw-admin') && ! $user->hasRole('rt-admin') && ! $user->hasRole('sekretaris-rw')) {
            abort(403, 'Akses ditolak. Warga tidak memiliki hak untuk membuat pengumuman.');
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'targets' => 'nullable|array',
            'targets.*.target_type' => 'required|string',
            'targets.*.target_id' => 'required|integer',
        ]);

        DB::transaction(function () use ($validated, &$announcement) {
            $announcement = Announcement::create([
                'created_by' => Auth::id(),
                'title' => $validated['title'],
                'body' => $validated['body'],
                'status' => $validated['status'],
            ]);

            if (! empty($validated['targets'])) {
                foreach ($validated['targets'] as $t) {
                    AnnouncementTarget::create([
                        'announcement_id' => $announcement->id,
                        'target_type' => $t['target_type'],
                        'target_id' => $t['target_id'],
                    ]);
                }
            }
        });

        if ($announcement && $announcement->status === 'published') {
            $usersQuery = User::whereHas('roles', fn ($q) => $q->where('slug', 'resident'));
            if (! empty($validated['targets'])) {
                // If specific targets, we can notify users belonging to those rt/rw
                // For simplicity, notify all active residents or targeted ones
            }
            $residentsUsers = $usersQuery->get();
            foreach ($residentsUsers as $resUser) {
                $resUser->notify(new GeneralNotification(
                    'Pengumuman Baru',
                    'Pengumuman: '.$announcement->title,
                    'announcement',
                    route('announcements.show', $announcement->id)
                ));
            }
        }

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil disimpan.');
    }

    public function show(Announcement $announcement)
    {
        $announcement->load(['targets']);

        return view('announcements.show', compact('announcement'));
    }
}
