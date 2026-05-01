<?php

use App\Services\AddonManager;

AddonManager::on('schedule.hourly', function () {
    \App\Models\ApplicationSession::where('expires_at', '<', now())->delete();
    \App\Models\ApplicationBlacklist::whereNotNull('expires_at')->where('expires_at', '<', now())->delete();
});
