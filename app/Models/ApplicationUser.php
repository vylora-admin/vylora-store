<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

class ApplicationUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'username',
        'email',
        'password_hash',
        'hwid',
        'last_ip',
        'country',
        'last_login_at',
        'expires_at',
        'level',
        'variables',
        'discord_id',
        'two_factor_enabled',
        'two_factor_secret',
        'is_banned',
        'ban_reason',
        'banned_at',
    ];

    protected $hidden = [
        'password_hash',
        'two_factor_secret',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'expires_at' => 'datetime',
        'banned_at' => 'datetime',
        'level' => 'integer',
        'variables' => 'array',
        'two_factor_enabled' => 'boolean',
        'is_banned' => 'boolean',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ApplicationSession::class);
    }

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(ApplicationUserSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(ApplicationUserSubscription::class)
            ->where('is_active', true)
            ->latest('expires_at');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function checkPassword(string $password): bool
    {
        return Hash::check($password, $this->password_hash);
    }

    public function setPassword(string $password): void
    {
        $this->password_hash = Hash::make($password);
    }
}
