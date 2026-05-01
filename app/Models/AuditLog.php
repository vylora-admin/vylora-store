<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getModelNameAttribute(): string
    {
        if (!$this->model_type) {
            return 'System';
        }
        return class_basename($this->model_type);
    }

    public function scopeRecent($query)
    {
        return $query->latest()->limit(50);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }
}
