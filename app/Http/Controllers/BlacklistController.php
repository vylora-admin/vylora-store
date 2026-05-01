<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationBlacklist;
use Illuminate\Http\Request;

class BlacklistController extends Controller
{
    public function index(Application $application, Request $request)
    {
        $query = $application->blacklist();
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $query->where('value', 'like', "%{$request->search}%");
        }
        $items = $query->latest()->paginate(30)->withQueryString();

        return view('blacklist.index', compact('application', 'items'));
    }

    public function store(Application $application, Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:hwid,ip,username,country,email',
            'value' => 'required|string|max:255',
            'reason' => 'nullable|string|max:500',
            'expires_at' => 'nullable|date|after:now',
        ]);
        ApplicationBlacklist::create([
            'application_id' => $application->id,
            'type' => $validated['type'],
            'value' => $validated['value'],
            'reason' => $validated['reason'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Blacklist entry added.');
    }

    public function destroy(Application $application, ApplicationBlacklist $blacklist)
    {
        abort_if($blacklist->application_id !== $application->id, 404);
        $blacklist->delete();

        return back()->with('success', 'Blacklist entry removed.');
    }
}
