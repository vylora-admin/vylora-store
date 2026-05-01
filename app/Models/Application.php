<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'secret',
        'owner_uid',
        'version',
        'download_url',
        'description',
        'icon_url',
        'is_paused',
        'pause_reason',
        'hwid_check_enabled',
        'integrity_check_enabled',
        'integrity_hash',
        'disable_user_panel',
        'allow_register',
        'allow_login',
        'allow_extend',
        'default_subscription_days',
        'webhook_events',
        'discord_webhook_url',
        'settings',
    ];

    protected $casts = [
        'is_paused' => 'boolean',
        'hwid_check_enabled' => 'boolean',
        'integrity_check_enabled' => 'boolean',
        'disable_user_panel' => 'boolean',
        'allow_register' => 'boolean',
        'allow_login' => 'boolean',
        'allow_extend' => 'boolean',
        'webhook_events' => 'array',
        'settings' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Application $app) {
            if (empty($app->slug)) {
                $app->slug = Str::slug($app->name).'-'.Str::lower(Str::random(4));
            }
            if (empty($app->secret)) {
                $app->secret = bin2hex(random_bytes(32));
            }
            if (empty($app->owner_uid)) {
                $app->owner_uid = Str::lower(Str::random(16));
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(ApplicationUser::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ApplicationSubscription::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ApplicationSession::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ApplicationFile::class);
    }

    public function variables(): HasMany
    {
        return $this->hasMany(ApplicationVariable::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(ApplicationWebhook::class);
    }

    public function blacklist(): HasMany
    {
        return $this->hasMany(ApplicationBlacklist::class);
    }

    public function chatChannels(): HasMany
    {
        return $this->hasMany(ApplicationChatChannel::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ApplicationLog::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
}
