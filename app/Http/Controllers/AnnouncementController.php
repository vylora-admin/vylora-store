<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Application;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('application')->latest()->paginate(20);
        $applications = Application::orderBy('name')->get();

        return view('announcements.index', compact('announcements', 'applications'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'application_id' => 'nullable|exists:applications,id',
            'title' => 'required|string|max:120',
            'body' => 'required|string',
            'level' => 'required|in:info,success,warning,critical',
            'expires_at' => 'nullable|date',
        ]);
        Announcement::create($validated + [
            'is_published' => true,
            'published_at' => now(),
        ]);

        return back()->with('success', 'Announcement published.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }
}
