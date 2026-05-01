<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationChatChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'name',
        'description',
        'required_level',
        'is_active',
    ];

    protected $casts = [
        'required_level' => 'integer',
        'is_active' => 'boolean',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ApplicationChatMessage::class);
    }
}
