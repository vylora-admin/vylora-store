<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'license_key',
        'customer_name',
        'customer_email',
        'type',
        'status',
        'max_activations',
        'current_activations',
        'issued_at',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'max_activations' => 'integer',
        'current_activations' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function activations(): HasMany
    {
        return $this->hasMany(LicenseActivation::class);
    }

    public function activeActivations(): HasMany
    {
        return $this->hasMany(LicenseActivation::class)->where('is_active', true);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function canActivate(): bool
    {
        return $this->isValid() && $this->current_activations < $this->max_activations;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeSearch($query, ?string $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('license_key', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }
        return $query;
    }
}
