<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationWebhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'name',
        'url',
        'secret',
        'events',
        'is_active',
        'retry_count',
        'timeout_seconds',
        'headers',
    ];

    protected $casts = [
        'events' => 'array',
        'headers' => 'array',
        'is_active' => 'boolean',
        'retry_count' => 'integer',
        'timeout_seconds' => 'integer',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(ApplicationWebhookDelivery::class);
    }
}
