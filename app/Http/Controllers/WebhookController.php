<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationWebhook;
use App\Services\WebhookService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public const EVENTS = [
        'user_register', 'user_login', 'user_banned', 'user_extended',
        'license_used', 'session_created', 'init', 'file_downloaded',
    ];

    public function index(Application $application)
    {
        $webhooks = $application->webhooks()->withCount('deliveries')->latest()->get();

        return view('webhooks.index', compact('application', 'webhooks'));
    }

    public function store(Application $application, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'url' => 'required|url',
            'secret' => 'nullable|string|max:128',
            'events' => 'required|array|min:1',
            'events.*' => 'in:'.implode(',', self::EVENTS),
            'retry_count' => 'nullable|integer|min:0|max:10',
            'timeout_seconds' => 'nullable|integer|min:1|max:60',
        ]);
        ApplicationWebhook::create([
            'application_id' => $application->id,
            'name' => $validated['name'],
            'url' => $validated['url'],
            'secret' => $validated['secret'] ?: bin2hex(random_bytes(16)),
            'events' => $validated['events'],
            'retry_count' => $validated['retry_count'] ?? 3,
            'timeout_seconds' => $validated['timeout_seconds'] ?? 10,
            'is_active' => true,
        ]);

        return back()->with('success', 'Webhook created.');
    }

    public function update(Application $application, ApplicationWebhook $webhook, Request $request)
    {
        abort_if($webhook->application_id !== $application->id, 404);
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'url' => 'required|url',
            'events' => 'required|array|min:1',
            'events.*' => 'in:'.implode(',', self::EVENTS),
            'retry_count' => 'nullable|integer|min:0|max:10',
            'timeout_seconds' => 'nullable|integer|min:1|max:60',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $webhook->update($validated);

        return back()->with('success', 'Webhook updated.');
    }

    public function destroy(Application $application, ApplicationWebhook $webhook)
    {
        abort_if($webhook->application_id !== $application->id, 404);
        $webhook->delete();

        return back()->with('success', 'Webhook deleted.');
    }

    public function test(Application $application, ApplicationWebhook $webhook)
    {
        abort_if($webhook->application_id !== $application->id, 404);
        WebhookService::deliver($webhook, 'test', ['hello' => 'world', 'application' => $application->name, 'sent_at' => now()->toIso8601String()]);

        return back()->with('success', 'Test webhook dispatched.');
    }
}
