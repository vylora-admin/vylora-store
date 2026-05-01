<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationChatChannel;
use App\Models\ApplicationChatMessage;
use App\Models\ApplicationFile;
use App\Models\ApplicationSession;
use App\Models\ApplicationUser;
use App\Models\ApplicationUserSubscription;
use App\Models\ApplicationVariable;
use App\Models\License;
use App\Models\LicenseActivation;
use App\Services\AppApiService;
use App\Services\WebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KeyAuthApiController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $type = $request->input('type');
        if (empty($type)) {
            return response()->json(AppApiService::err('Missing type parameter'), 400);
        }

        $app = AppApiService::findApplication($request);
        if (! $app) {
            return response()->json(AppApiService::err('Application not found or invalid credentials'));
        }

        if ($app->is_paused && $type !== 'init') {
            return response()->json(AppApiService::err('Application is currently paused: '.($app->pause_reason ?: 'No reason given')));
        }

        $bl = AppApiService::isBlacklisted($app, $request->input('hwid'), $request->ip(), $request->input('username'));
        if ($bl !== null && ! in_array($type, ['init'])) {
            return response()->json(AppApiService::err($bl));
        }

        $method = 'handle'.Str::studly($type);
        if (! method_exists($this, $method)) {
            return response()->json(AppApiService::err('Unknown type: '.$type));
        }

        try {
            return $this->{$method}($request, $app);
        } catch (\Throwable $e) {
            \Log::error('API error', ['type' => $type, 'error' => $e->getMessage()]);

            return response()->json(AppApiService::err('Server error: '.$e->getMessage()), 500);
        }
    }

    protected function handleInit(Request $request, Application $app): JsonResponse
    {
        if ($app->is_paused) {
            return response()->json(AppApiService::err('Application paused: '.($app->pause_reason ?: '')));
        }
        if ($app->integrity_check_enabled && ! empty($app->integrity_hash)) {
            $hash = $request->input('hash');
            if ($hash !== $app->integrity_hash) {
                return response()->json(AppApiService::err('Integrity check failed'));
            }
        }
        $session = AppApiService::createSession($app, null, $request, false);
        AppApiService::log($app, null, 'init', 'Client initialized', 'info', [], $request);

        return response()->json(AppApiService::ok([
            'sessionid' => $session->session_token,
            'app' => [
                'name' => $app->name,
                'version' => $app->version,
                'download_url' => $app->download_url,
                'description' => $app->description,
            ],
            'newSession' => true,
        ], 'Initialized'));
    }

    protected function handleRegister(Request $request, Application $app): JsonResponse
    {
        if (! $app->allow_register) {
            return response()->json(AppApiService::err('Registration disabled'));
        }
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session) {
            return response()->json(AppApiService::err('Invalid session'));
        }
        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('pass');
        $licenseKey = (string) $request->input('key');
        $email = $request->input('email');
        $hwid = $request->input('hwid');

        if (strlen($username) < 3 || strlen($password) < 4) {
            return response()->json(AppApiService::err('Username/password too short'));
        }
        if (ApplicationUser::where('application_id', $app->id)->where('username', $username)->exists()) {
            return response()->json(AppApiService::err('Username taken'));
        }

        $license = License::where('license_key', $licenseKey)->first();
        if (! $license || ! $license->isValid()) {
            return response()->json(AppApiService::err('Invalid or used license'));
        }
        if (! $license->canActivate()) {
            return response()->json(AppApiService::err('License has no activations remaining'));
        }

        return DB::transaction(function () use ($request, $app, $username, $password, $email, $hwid, $license, $session) {
            $user = ApplicationUser::create([
                'application_id' => $app->id,
                'username' => $username,
                'email' => $email,
                'password_hash' => Hash::make($password),
                'hwid' => $hwid,
                'last_ip' => $request->ip(),
                'last_login_at' => now(),
                'level' => 1,
                'expires_at' => now()->addDays($app->default_subscription_days ?: 30),
            ]);

            LicenseActivation::create([
                'license_id' => $license->id,
                'machine_name' => $request->input('machine'),
                'hardware_id' => $hwid,
                'ip_address' => $request->ip(),
                'is_active' => true,
            ]);
            $license->increment('current_activations');

            $session->update(['application_user_id' => $user->id, 'is_validated' => true]);
            AppApiService::log($app, $user, 'register', 'User registered: '.$username, 'info', ['license' => $licenseKey], $request);
            WebhookService::dispatch($app, 'user_register', ['username' => $username, 'ip' => $request->ip(), 'hwid' => $hwid]);

            return response()->json(AppApiService::ok(['info' => AppApiService::userPayload($user)], 'Registered'));
        });
    }

    protected function handleLogin(Request $request, Application $app): JsonResponse
    {
        if (! $app->allow_login) {
            return response()->json(AppApiService::err('Login disabled'));
        }
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session) {
            return response()->json(AppApiService::err('Invalid session'));
        }
        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('pass');
        $hwid = $request->input('hwid');

        $user = ApplicationUser::where('application_id', $app->id)->where('username', $username)->first();
        if (! $user || ! $user->checkPassword($password)) {
            AppApiService::log($app, null, 'login_failed', 'Bad credentials for '.$username, 'warning', [], $request);

            return response()->json(AppApiService::err('Invalid username or password'));
        }
        if ($user->is_banned) {
            return response()->json(AppApiService::err('User is banned: '.($user->ban_reason ?: '')));
        }
        if ($user->isExpired()) {
            return response()->json(AppApiService::err('Subscription expired'));
        }
        if ($app->hwid_check_enabled && ! empty($user->hwid) && $user->hwid !== $hwid) {
            return response()->json(AppApiService::err('HWID mismatch'));
        }
        if ($user->two_factor_enabled) {
            $code = (string) $request->input('twofa');
            if (empty($code) || ! \App\Services\TotpService::verify((string) $user->two_factor_secret, $code)) {
                return response()->json(AppApiService::err('2FA required or invalid', ['twofa_required' => true]));
            }
        }

        $user->update([
            'last_login_at' => now(),
            'last_ip' => $request->ip(),
            'hwid' => $user->hwid ?: $hwid,
        ]);
        $session->update(['application_user_id' => $user->id, 'is_validated' => true]);
        AppApiService::log($app, $user, 'login', 'Logged in', 'info', [], $request);
        WebhookService::dispatch($app, 'user_login', ['username' => $username, 'ip' => $request->ip(), 'hwid' => $hwid]);

        return response()->json(AppApiService::ok(['info' => AppApiService::userPayload($user)], 'Logged in'));
    }

    protected function handleLicense(Request $request, Application $app): JsonResponse
    {
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session) {
            return response()->json(AppApiService::err('Invalid session'));
        }
        $key = (string) $request->input('key');
        $hwid = $request->input('hwid');

        $license = License::where('license_key', $key)->first();
        if (! $license || ! $license->isValid()) {
            return response()->json(AppApiService::err('Invalid license'));
        }

        $existing = LicenseActivation::where('license_id', $license->id)
            ->where('hardware_id', $hwid)
            ->where('is_active', true)
            ->first();
        if (! $existing) {
            if (! $license->canActivate()) {
                return response()->json(AppApiService::err('License has no activations remaining'));
            }
            LicenseActivation::create([
                'license_id' => $license->id,
                'machine_name' => $request->input('machine'),
                'hardware_id' => $hwid,
                'ip_address' => $request->ip(),
                'is_active' => true,
            ]);
            $license->increment('current_activations');
        }

        $username = 'license_'.Str::lower(substr(md5($key), 0, 8));
        $user = ApplicationUser::firstOrCreate(
            ['application_id' => $app->id, 'username' => $username],
            [
                'password_hash' => Hash::make(Str::random(32)),
                'hwid' => $hwid,
                'last_ip' => $request->ip(),
                'last_login_at' => now(),
                'level' => 1,
                'expires_at' => $license->expires_at,
            ]
        );
        $user->update(['last_login_at' => now(), 'last_ip' => $request->ip()]);
        $session->update(['application_user_id' => $user->id, 'is_validated' => true]);
        AppApiService::log($app, $user, 'license', 'License auth', 'info', ['key' => substr($key, 0, 8).'...'], $request);
        WebhookService::dispatch($app, 'license_used', ['key_prefix' => substr($key, 0, 8), 'hwid' => $hwid]);

        return response()->json(AppApiService::ok(['info' => AppApiService::userPayload($user)], 'License OK'));
    }

    protected function handleUpgrade(Request $request, Application $app): JsonResponse
    {
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session || ! $session->is_validated) {
            return response()->json(AppApiService::err('Not logged in'));
        }
        $username = trim((string) $request->input('username'));
        $key = (string) $request->input('key');
        $user = ApplicationUser::where('application_id', $app->id)->where('username', $username)->first();
        if (! $user) {
            return response()->json(AppApiService::err('User not found'));
        }
        $license = License::where('license_key', $key)->first();
        if (! $license || ! $license->isValid()) {
            return response()->json(AppApiService::err('Invalid key'));
        }
        if (! $license->canActivate()) {
            return response()->json(AppApiService::err('Key has no uses left'));
        }
        $extension = match ($license->type) {
            'lifetime' => null,
            'extended' => 365,
            default => 30,
        };
        $newExpiry = $extension === null
            ? null
            : ($user->expires_at && $user->expires_at->isFuture() ? $user->expires_at->addDays($extension) : now()->addDays($extension));
        $user->update(['expires_at' => $newExpiry]);
        LicenseActivation::create([
            'license_id' => $license->id,
            'machine_name' => $request->input('machine'),
            'hardware_id' => $request->input('hwid'),
            'ip_address' => $request->ip(),
            'is_active' => true,
        ]);
        $license->increment('current_activations');
        AppApiService::log($app, $user, 'upgrade', 'Subscription extended', 'info', [], $request);

        return response()->json(AppApiService::ok(['info' => AppApiService::userPayload($user)], 'Upgraded'));
    }

    protected function handleCheck(Request $request, Application $app): JsonResponse
    {
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session) {
            return response()->json(AppApiService::err('Invalid session'));
        }
        if (! $session->is_validated) {
            return response()->json(AppApiService::err('Session not validated'));
        }
        $user = $session->user;
        if ($user && ($user->is_banned || $user->isExpired())) {
            return response()->json(AppApiService::err('User no longer valid'));
        }

        return response()->json(AppApiService::ok(['info' => $user ? AppApiService::userPayload($user) : null], 'Session OK'));
    }

    protected function handleVar(Request $request, Application $app): JsonResponse
    {
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session || ! $session->is_validated) {
            return response()->json(AppApiService::err('Not logged in'));
        }
        $varid = (string) $request->input('varid');
        $var = ApplicationVariable::where('application_id', $app->id)
            ->where('scope', 'global')
            ->where('key', $varid)
            ->first();
        if (! $var) {
            return response()->json(AppApiService::err('Variable not found'));
        }
        $userLevel = $session->user?->level ?? 0;
        if ($var->required_level > $userLevel) {
            return response()->json(AppApiService::err('Insufficient level for variable'));
        }

        return response()->json(AppApiService::ok(['response' => $var->value]));
    }

    protected function handleGetvar(Request $request, Application $app): JsonResponse
    {
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session || ! $session->is_validated || ! $session->user) {
            return response()->json(AppApiService::err('Not logged in'));
        }
        $key = (string) $request->input('var');
        $var = ApplicationVariable::where('application_id', $app->id)
            ->where('scope', 'user')
            ->where('application_user_id', $session->user->id)
            ->where('key', $key)
            ->first();

        return response()->json(AppApiService::ok(['response' => $var?->value]));
    }

    protected function handleSetvar(Request $request, Application $app): JsonResponse
    {
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session || ! $session->is_validated || ! $session->user) {
            return response()->json(AppApiService::err('Not logged in'));
        }
        $key = (string) $request->input('var');
        $data = (string) $request->input('data');
        ApplicationVariable::updateOrCreate(
            [
                'application_id' => $app->id,
                'scope' => 'user',
                'application_user_id' => $session->user->id,
                'key' => $key,
            ],
            ['value' => $data]
        );

        return response()->json(AppApiService::ok([], 'Variable set'));
    }

    protected function handleFile(Request $request, Application $app): JsonResponse
    {
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session || ! $session->is_validated || ! $session->user) {
            return response()->json(AppApiService::err('Not logged in'));
        }
        $fileid = $request->input('fileid');
        $file = ApplicationFile::where('application_id', $app->id)->where('id', $fileid)->where('is_active', true)->first();
        if (! $file) {
            return response()->json(AppApiService::err('File not found'));
        }
        if ($file->required_level > $session->user->level) {
            return response()->json(AppApiService::err('Insufficient level for file'));
        }
        $file->increment('download_count');
        $abs = storage_path('app/'.$file->file_path);
        if (! file_exists($abs)) {
            return response()->json(AppApiService::err('File missing'));
        }
        $contents = base64_encode(file_get_contents($abs));

        return response()->json(AppApiService::ok([
            'contents' => $contents,
            'name' => $file->name,
            'hash' => $file->hash,
            'size' => $file->size,
        ]));
    }

    protected function handleLog(Request $request, Application $app): JsonResponse
    {
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session) {
            return response()->json(AppApiService::err('Invalid session'));
        }
        $message = (string) $request->input('message');
        $pcuser = $request->input('pcuser');
        AppApiService::log($app, $session->user, 'client_log', $message, 'info', ['pcuser' => $pcuser], $request);

        return response()->json(AppApiService::ok([], 'Logged'));
    }

    protected function handleWebhook(Request $request, Application $app): JsonResponse
    {
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session || ! $session->is_validated) {
            return response()->json(AppApiService::err('Not logged in'));
        }
        $webid = $request->input('webid');
        $params = $request->input('params');
        $body = $request->input('body');
        $webhook = \App\Models\ApplicationWebhook::where('application_id', $app->id)->where('id', $webid)->first();
        if (! $webhook || ! $webhook->is_active) {
            return response()->json(AppApiService::err('Webhook not found'));
        }
        try {
            $url = $webhook->url.(str_contains($webhook->url, '?') ? '&' : '?').(string) $params;
            $resp = \Illuminate\Support\Facades\Http::timeout($webhook->timeout_seconds ?: 10)->post($url, ['body' => $body]);

            return response()->json(AppApiService::ok(['response' => $resp->body()]));
        } catch (\Throwable $e) {
            return response()->json(AppApiService::err('Webhook failed: '.$e->getMessage()));
        }
    }

    protected function handleChatGet(Request $request, Application $app): JsonResponse
    {
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session || ! $session->is_validated || ! $session->user) {
            return response()->json(AppApiService::err('Not logged in'));
        }
        $channelName = (string) $request->input('channel');
        $channel = ApplicationChatChannel::where('application_id', $app->id)
            ->where('name', $channelName)
            ->where('is_active', true)
            ->first();
        if (! $channel) {
            return response()->json(AppApiService::err('Channel not found'));
        }
        if ($channel->required_level > $session->user->level) {
            return response()->json(AppApiService::err('Insufficient level'));
        }
        $messages = $channel->messages()->latest('id')->limit(50)->get()->reverse()->values();

        return response()->json(AppApiService::ok([
            'messages' => $messages->map(fn ($m) => [
                'author' => $m->username_snapshot,
                'message' => $m->message,
                'timestamp' => $m->created_at->timestamp,
            ])->all(),
        ]));
    }

    protected function handleChatSend(Request $request, Application $app): JsonResponse
    {
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session || ! $session->is_validated || ! $session->user) {
            return response()->json(AppApiService::err('Not logged in'));
        }
        $channelName = (string) $request->input('channel');
        $msg = trim((string) $request->input('message'));
        if ($msg === '' || strlen($msg) > 1000) {
            return response()->json(AppApiService::err('Bad message'));
        }
        $channel = ApplicationChatChannel::where('application_id', $app->id)
            ->where('name', $channelName)
            ->where('is_active', true)
            ->first();
        if (! $channel) {
            return response()->json(AppApiService::err('Channel not found'));
        }
        if ($channel->required_level > $session->user->level) {
            return response()->json(AppApiService::err('Insufficient level'));
        }
        ApplicationChatMessage::create([
            'application_chat_channel_id' => $channel->id,
            'application_user_id' => $session->user->id,
            'username_snapshot' => $session->user->username,
            'message' => $msg,
        ]);

        return response()->json(AppApiService::ok([], 'Sent'));
    }

    protected function handleBan(Request $request, Application $app): JsonResponse
    {
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session || ! $session->is_validated || ! $session->user) {
            return response()->json(AppApiService::err('Not logged in'));
        }
        $reason = (string) $request->input('reason', 'Banned via client');
        $user = $session->user;
        $user->update(['is_banned' => true, 'ban_reason' => $reason, 'banned_at' => now()]);
        AppApiService::log($app, $user, 'ban', 'Self-banned: '.$reason, 'critical', [], $request);
        WebhookService::dispatch($app, 'user_banned', ['username' => $user->username, 'reason' => $reason]);

        return response()->json(AppApiService::ok([], 'Banned'));
    }

    protected function handleChangeUsername(Request $request, Application $app): JsonResponse
    {
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session || ! $session->is_validated || ! $session->user) {
            return response()->json(AppApiService::err('Not logged in'));
        }
        $newName = trim((string) $request->input('newUsername'));
        if (strlen($newName) < 3) {
            return response()->json(AppApiService::err('Name too short'));
        }
        if (ApplicationUser::where('application_id', $app->id)->where('username', $newName)->exists()) {
            return response()->json(AppApiService::err('Name taken'));
        }
        $session->user->update(['username' => $newName]);

        return response()->json(AppApiService::ok([], 'Renamed'));
    }

    protected function handleChangePw(Request $request, Application $app): JsonResponse
    {
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session || ! $session->is_validated || ! $session->user) {
            return response()->json(AppApiService::err('Not logged in'));
        }
        $new = (string) $request->input('newPassword');
        if (strlen($new) < 4) {
            return response()->json(AppApiService::err('Password too short'));
        }
        $session->user->update(['password_hash' => Hash::make($new)]);

        return response()->json(AppApiService::ok([], 'Password changed'));
    }

    protected function handleSubscriptions(Request $request, Application $app): JsonResponse
    {
        $session = AppApiService::findSession($request->input('sessionid'));
        if (! $session || ! $session->is_validated || ! $session->user) {
            return response()->json(AppApiService::err('Not logged in'));
        }
        $subs = ApplicationUserSubscription::with('subscription')
            ->where('application_user_id', $session->user->id)
            ->get()
            ->map(fn ($s) => [
                'name' => $s->subscription?->name,
                'level' => $s->subscription?->level,
                'expires' => $s->expires_at?->timestamp,
                'is_active' => $s->is_active,
            ]);

        return response()->json(AppApiService::ok(['subscriptions' => $subs->all()]));
    }

    protected function handleAnnouncement(Request $request, Application $app): JsonResponse
    {
        $list = $app->announcements()
            ->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('published_at')
            ->limit(5)
            ->get(['title', 'body', 'level', 'published_at']);

        return response()->json(AppApiService::ok(['announcements' => $list]));
    }

    protected function handleOnlineUsers(Request $request, Application $app): JsonResponse
    {
        $count = ApplicationSession::where('application_id', $app->id)
            ->where('is_validated', true)
            ->where('expires_at', '>', now())
            ->count();

        return response()->json(AppApiService::ok(['online' => $count]));
    }
}
