<?php

use App\Services\AddonManager;

AddonManager::on('app.user.banned', function ($user) {
    \Log::info('Discord-Alerts: user banned', ['user' => $user->username ?? null]);
});

AddonManager::on('app.user.login_failed', function ($application, $username) {
    \Log::info('Discord-Alerts: login failed', ['app' => $application->name ?? null, 'username' => $username]);
});

AddonManager::on('app.integrity_failure', function ($application, $request) {
    \Log::warning('Discord-Alerts: integrity check failed', ['app' => $application->name ?? null, 'ip' => $request?->ip()]);
});
