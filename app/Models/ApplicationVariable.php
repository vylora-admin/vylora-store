<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationVariable extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'application_user_id',
        'key',
        'value',
        'scope',
        'required_level',
        'is_secret',
    ];

    protected $casts = [
        'is_secret' => 'boolean',
        'required_level' => 'integer',
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
