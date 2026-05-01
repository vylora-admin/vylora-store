<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationFile;
use App\Models\ApplicationLog;
use App\Models\ApplicationSession;
use App\Models\ApplicationUser;
use App\Models\ApplicationWebhook;
use App\Models\License;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_applications' => Application::count(),
            'active_applications' => Application::where('is_paused', false)->count(),
            'total_app_users' => ApplicationUser::count(),
            'banned_app_users' => ApplicationUser::where('is_banned', true)->count(),
            'total_licenses' => License::count(),
            'active_licenses' => License::where('status', 'active')->count(),
            'expired_licenses' => License::where('status', 'expired')->count(),
            'suspended_licenses' => License::where('status', 'suspended')->count(),
            'revoked_licenses' => License::where('status', 'revoked')->count(),
            'total_files' => ApplicationFile::count(),
            'total_webhooks' => ApplicationWebhook::count(),
            'total_users' => User::count(),
            'online_now' => ApplicationSession::where('is_validated', true)->where('expires_at', '>', now())->count(),
            'sessions_24h' => ApplicationSession::where('created_at', '>', now()->subDay())->count(),
        ];

        $sessionsTrend = collect(range(13, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo)->startOfDay();

            return [
                'label' => $date->format('M j'),
                'count' => ApplicationSession::whereDate('created_at', $date)->count(),
            ];
        });

        $eventBreakdown = ApplicationLog::select('event_type', DB::raw('count(*) as count'))
            ->where('created_at', '>', now()->subDay())
            ->groupBy('event_type')
            ->orderByDesc('count')
            ->limit(7)
            ->get();

        if ($eventBreakdown->isEmpty()) {
            $eventBreakdown = collect([['event_type' => 'no events yet', 'count' => 1]]);
        }

        $topApplications = Application::withCount('users')
            ->orderByDesc('users_count')
            ->limit(6)
            ->get();

        $recentLogs = ApplicationLog::latest()->limit(10)->get();

        return view('dashboard', compact('stats', 'sessionsTrend', 'eventBreakdown', 'topApplications', 'recentLogs'));
    }
}
