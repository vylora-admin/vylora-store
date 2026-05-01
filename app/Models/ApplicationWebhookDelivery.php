<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationWebhookDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_webhook_id',
        'event',
        'payload',
        'status_code',
        'response',
        'is_success',
        'attempts',
        'delivered_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'is_success' => 'boolean',
        'delivered_at' => 'datetime',
        'attempts' => 'integer',
        'status_code' => 'integer',
    ];

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(ApplicationWebhook::class, 'application_webhook_id');
    }
}
