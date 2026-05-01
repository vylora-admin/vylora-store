<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'application_user_id',
        'level',
        'event_type',
        'message',
        'context',
        'ip',
        'hwid',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(ApplicationUser::class, 'application_user_id');
    }
}
