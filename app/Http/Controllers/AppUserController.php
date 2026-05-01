<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationSubscription;
use App\Models\ApplicationUser;
use App\Models\ApplicationUserSubscription;
use App\Services\AuditService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AppUserController extends Controller
{
    public function index(Application $application, Request $request)
    {
        $query = $application->users();
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qb) use ($q) {
                $qb->where('username', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('hwid', 'like', "%{$q}%")
                    ->orWhere('last_ip', 'like', "%{$q}%");
            });
        }
        if ($request->filled('status')) {
            if ($request->status === 'banned') {
                $query->where('is_banned', true);
            } elseif ($request->status === 'expired') {
                $query->where('expires_at', '<', now());
            } elseif ($request->status === 'active') {
                $query->where('is_banned', false)->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                });
            }
        }
        $users = $query->latest()->paginate(20)->withQueryString();

        return view('app-users.index', compact('application', 'users'));
    }

    public function create(Application $application)
    {
        $subs = $application->subscriptions()->where('is_active', true)->get();

        return view('app-users.create', compact('application', 'subs'));
    }

    public function store(Application $application, Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|min:3|max:64',
            'email' => 'nullable|email',
            'password' => 'required|string|min:4|max:128',
            'level' => 'nullable|integer|min:0|max:99',
            'expires_at' => 'nullable|date',
            'subscription_id' => 'nullable|exists:application_subscriptions,id',
        ]);
        if (ApplicationUser::where('application_id', $application->id)->where('username', $validated['username'])->exists()) {
            return back()->withErrors(['username' => 'Username already exists in this app'])->withInput();
        }
        $user = ApplicationUser::create([
            'application_id' => $application->id,
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'password_hash' => Hash::make($validated['password']),
            'level' => $validated['level'] ?? 1,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);
        if (! empty($validated['subscription_id'])) {
            $sub = ApplicationSubscription::findOrFail($validated['subscription_id']);
            ApplicationUserSubscription::create([
                'application_user_id' => $user->id,
                'application_subscription_id' => $sub->id,
                'starts_at' => now(),
                'expires_at' => now()->addDays($sub->default_days),
                'is_active' => true,
            ]);
        }
        AuditService::log('app_user_created', $user, null, $validated, "Created app user: {$user->username}");

        return redirect()->route('applications.users.index', $application)->with('success', 'User created.');
    }

    public function show(Application $application, ApplicationUser $user)
    {
        abort_if($user->application_id !== $application->id, 404);
        $user->load(['userSubscriptions.subscription', 'sessions']);

        return view('app-users.show', compact('application', 'user'));
    }

    public function ban(Application $application, ApplicationUser $user, Request $request)
    {
        abort_if($user->application_id !== $application->id, 404);
        $reason = $request->input('reason', 'Banned by admin');
        $user->update(['is_banned' => true, 'ban_reason' => $reason, 'banned_at' => now()]);
        WebhookService::dispatch($application, 'user_banned', ['username' => $user->username, 'reason' => $reason]);
        AuditService::log('app_user_banned', $user, null, ['reason' => $reason], "Banned: {$user->username}");

        return back()->with('success', 'User banned.');
    }

    public function unban(Application $application, ApplicationUser $user)
    {
        abort_if($user->application_id !== $application->id, 404);
        $user->update(['is_banned' => false, 'ban_reason' => null, 'banned_at' => null]);

        return back()->with('success', 'User unbanned.');
    }

    public function resetHwid(Application $application, ApplicationUser $user)
    {
        abort_if($user->application_id !== $application->id, 404);
        $user->update(['hwid' => null]);

        return back()->with('success', 'HWID reset.');
    }

    public function extend(Application $application, ApplicationUser $user, Request $request)
    {
        abort_if($user->application_id !== $application->id, 404);
        $days = (int) $request->input('days', 30);
        $base = $user->expires_at && $user->expires_at->isFuture() ? $user->expires_at : now();
        $user->update(['expires_at' => $base->addDays($days)]);

        return back()->with('success', "Extended {$days} days.");
    }

    public function destroy(Application $application, ApplicationUser $user)
    {
        abort_if($user->application_id !== $application->id, 404);
        $user->delete();

        return back()->with('success', 'User deleted.');
    }
}
