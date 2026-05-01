<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'name',
        'level',
        'price',
        'default_days',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'level' => 'integer',
        'default_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(ApplicationUserSubscription::class);
    }
}
