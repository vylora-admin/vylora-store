<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'application_id',
        'display_name',
        'balance',
        'permissions',
        'api_key',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Seller $seller) {
            if (empty($seller->api_key)) {
                $seller->api_key = 'sk_'.Str::lower(Str::random(48));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(SellerTransaction::class);
    }
}
