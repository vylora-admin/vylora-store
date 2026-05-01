<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationSubscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Application $application)
    {
        $subscriptions = $application->subscriptions()->withCount('userSubscriptions')->orderBy('level')->get();

        return view('subscriptions.index', compact('application', 'subscriptions'));
    }

    public function store(Application $application, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:64',
            'level' => 'required|integer|min:0|max:99',
            'price' => 'nullable|numeric|min:0',
            'default_days' => 'required|integer|min:1|max:3650',
            'description' => 'nullable|string|max:500',
        ]);
        $validated['application_id'] = $application->id;
        $validated['is_active'] = true;
        ApplicationSubscription::create($validated);

        return back()->with('success', 'Subscription created.');
    }

    public function update(Application $application, ApplicationSubscription $subscription, Request $request)
    {
        abort_if($subscription->application_id !== $application->id, 404);
        $validated = $request->validate([
            'name' => 'required|string|max:64',
            'level' => 'required|integer|min:0|max:99',
            'price' => 'nullable|numeric|min:0',
            'default_days' => 'required|integer|min:1|max:3650',
            'description' => 'nullable|string|max:500',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $subscription->update($validated);

        return back()->with('success', 'Subscription updated.');
    }

    public function destroy(Application $application, ApplicationSubscription $subscription)
    {
        abort_if($subscription->application_id !== $application->id, 404);
        $subscription->delete();

        return back()->with('success', 'Subscription deleted.');
    }
}
