<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationUserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_user_id',
        'application_subscription_id',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(ApplicationUser::class, 'application_user_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(ApplicationSubscription::class, 'application_subscription_id');
    }
}
