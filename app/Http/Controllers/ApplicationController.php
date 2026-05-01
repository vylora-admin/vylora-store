<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationLog;
use App\Models\ApplicationSession;
use App\Models\ApplicationUser;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::query()->withCount(['users', 'sessions']);
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        $applications = $query->latest()->paginate(15)->withQueryString();

        return view('applications.index', compact('applications'));
    }

    public function create()
    {
        return view('applications.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120|unique:applications,name',
            'version' => 'nullable|string|max:32',
            'description' => 'nullable|string',
            'download_url' => 'nullable|url',
            'icon_url' => 'nullable|url',
            'default_subscription_days' => 'nullable|integer|min:1|max:3650',
            'discord_webhook_url' => 'nullable|url',
            'allow_register' => 'sometimes|boolean',
            'allow_login' => 'sometimes|boolean',
            'allow_extend' => 'sometimes|boolean',
            'hwid_check_enabled' => 'sometimes|boolean',
            'integrity_check_enabled' => 'sometimes|boolean',
        ]);
        $validated['allow_register'] = (bool) $request->boolean('allow_register', true);
        $validated['allow_login'] = (bool) $request->boolean('allow_login', true);
        $validated['allow_extend'] = (bool) $request->boolean('allow_extend', true);
        $validated['hwid_check_enabled'] = (bool) $request->boolean('hwid_check_enabled', true);
        $validated['integrity_check_enabled'] = (bool) $request->boolean('integrity_check_enabled', false);
        $validated['owner_id'] = auth()->id();
        $app = Application::create($validated);
        AuditService::log('application_created', $app, null, $validated, "Created app: {$app->name}");

        return redirect()->route('applications.show', $app)->with('success', 'Application created.');
    }

    public function show(Application $application)
    {
        $stats = [
            'users' => $application->users()->count(),
            'banned' => $application->users()->where('is_banned', true)->count(),
            'online' => $application->sessions()->where('is_validated', true)->where('expires_at', '>', now())->count(),
            'logs_today' => $application->logs()->whereDate('created_at', today())->count(),
            'subscriptions' => $application->subscriptions()->count(),
            'files' => $application->files()->count(),
        ];
        $recentSessions = ApplicationSession::with('user')
            ->where('application_id', $application->id)
            ->latest()->limit(8)->get();
        $recentLogs = ApplicationLog::where('application_id', $application->id)
            ->latest()->limit(20)->get();
        $recentUsers = ApplicationUser::where('application_id', $application->id)
            ->latest()->limit(8)->get();

        return view('applications.show', compact('application', 'stats', 'recentSessions', 'recentLogs', 'recentUsers'));
    }

    public function edit(Application $application)
    {
        return view('applications.edit', compact('application'));
    }

    public function update(Request $request, Application $application)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120|unique:applications,name,'.$application->id,
            'version' => 'nullable|string|max:32',
            'description' => 'nullable|string',
            'download_url' => 'nullable|url',
            'icon_url' => 'nullable|url',
            'default_subscription_days' => 'nullable|integer|min:1|max:3650',
            'discord_webhook_url' => 'nullable|url',
            'pause_reason' => 'nullable|string|max:500',
        ]);
        $validated['is_paused'] = $request->boolean('is_paused');
        $validated['allow_register'] = $request->boolean('allow_register');
        $validated['allow_login'] = $request->boolean('allow_login');
        $validated['allow_extend'] = $request->boolean('allow_extend');
        $validated['hwid_check_enabled'] = $request->boolean('hwid_check_enabled');
        $validated['integrity_check_enabled'] = $request->boolean('integrity_check_enabled');
        $validated['disable_user_panel'] = $request->boolean('disable_user_panel');
        $application->update($validated);
        AuditService::log('application_updated', $application, null, $validated, 'Updated app');

        return redirect()->route('applications.show', $application)->with('success', 'Application updated.');
    }

    public function destroy(Application $application)
    {
        $name = $application->name;
        $application->delete();
        AuditService::log('application_deleted', null, null, ['name' => $name], "Deleted app: {$name}");

        return redirect()->route('applications.index')->with('success', 'Application deleted.');
    }

    public function resetSecret(Application $application)
    {
        $application->update(['secret' => bin2hex(random_bytes(32))]);
        AuditService::log('application_secret_reset', $application, null, null, 'Secret rotated');

        return back()->with('success', 'Secret rotated.');
    }

    public function pause(Application $application, Request $request)
    {
        $application->update([
            'is_paused' => ! $application->is_paused,
            'pause_reason' => $request->input('pause_reason'),
        ]);

        return back()->with('success', $application->is_paused ? 'Application paused.' : 'Application resumed.');
    }
}
