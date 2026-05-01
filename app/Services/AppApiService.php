<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationBlacklist;
use App\Models\ApplicationLog;
use App\Models\ApplicationSession;
use App\Models\ApplicationUser;
use Illuminate\Http\Request;

class AppApiService
{
    public static function findApplication(Request $request): ?Application
    {
        $name = $request->input('name');
        $ownerid = $request->input('ownerid');
        $secret = $request->input('secret');
        $version = $request->input('ver');

        if (empty($name) || empty($ownerid) || empty($secret)) {
            return null;
        }

        $app = Application::where('name', $name)
            ->where('owner_uid', $ownerid)
            ->where('secret', $secret)
            ->first();

        return $app;
    }

    public static function isBlacklisted(Application $app, ?string $hwid, ?string $ip, ?string $username = null): ?string
    {
        $items = ApplicationBlacklist::where('application_id', $app->id)->get();
        foreach ($items as $b) {
            if ($b->isExpired()) {
                continue;
            }
            if ($b->type === 'hwid' && $hwid && hash_equals($b->value, $hwid)) {
                return 'HWID is blacklisted';
            }
            if ($b->type === 'ip' && $ip && $b->value === $ip) {
                return 'IP is blacklisted';
            }
            if ($b->type === 'username' && $username && strcasecmp($b->value, $username) === 0) {
                return 'Username is blacklisted';
            }
        }

        return null;
    }

    public static function createSession(Application $app, ?ApplicationUser $user, Request $request, bool $validated = false): ApplicationSession
    {
        return ApplicationSession::create([
            'application_id' => $app->id,
            'application_user_id' => $user?->id,
            'session_token' => 'sess_'.bin2hex(random_bytes(32)),
            'hwid' => $request->input('hwid'),
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 250),
            'is_validated' => $validated,
            'expires_at' => now()->addHours(24),
        ]);
    }

    public static function findSession(?string $sessionId): ?ApplicationSession
    {
        if (empty($sessionId)) {
            return null;
        }
        $sess = ApplicationSession::where('session_token', $sessionId)->first();
        if (! $sess || $sess->isExpired()) {
            return null;
        }

        return $sess;
    }

    public static function log(Application $app, ?ApplicationUser $user, string $event, string $message, string $level = 'info', array $context = [], ?Request $request = null): void
    {
        ApplicationLog::create([
            'application_id' => $app->id,
            'application_user_id' => $user?->id,
            'level' => $level,
            'event_type' => $event,
            'message' => substr($message, 0, 4000),
            'context' => $context ?: null,
            'ip' => $request?->ip(),
            'hwid' => $request?->input('hwid'),
        ]);
    }

    public static function ok(array $data = [], string $message = 'success'): array
    {
        return array_merge([
            'success' => true,
            'message' => $message,
            'time' => time(),
        ], $data);
    }

    public static function err(string $message, array $extra = []): array
    {
        return array_merge([
            'success' => false,
            'message' => $message,
            'time' => time(),
        ], $extra);
    }

    public static function userPayload(ApplicationUser $user): array
    {
        $sub = $user->activeSubscription()->with('subscription')->first();

        return [
            'username' => $user->username,
            'email' => $user->email,
            'level' => $user->level,
            'expires' => $user->expires_at?->timestamp,
            'created' => $user->created_at?->timestamp,
            'lastlogin' => $user->last_login_at?->timestamp,
            'ip' => $user->last_ip,
            'hwid' => $user->hwid,
            'subscription' => $sub?->subscription?->name,
            'subscription_expires' => $sub?->expires_at?->timestamp,
        ];
    }
}
