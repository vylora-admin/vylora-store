<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'application_user_id',
        'session_token',
        'hwid',
        'ip',
        'country',
        'user_agent',
        'is_validated',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_validated' => 'boolean',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(ApplicationUser::class, 'application_user_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
