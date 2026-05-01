<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TwoFactorSecret extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'secret',
        'recovery_codes',
        'confirmed_at',
    ];

    protected $hidden = [
        'secret',
        'recovery_codes',
    ];

    protected $casts = [
        'recovery_codes' => 'array',
        'confirmed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
