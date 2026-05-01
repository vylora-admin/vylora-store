<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'name',
        'file_path',
        'original_filename',
        'size',
        'hash',
        'is_encrypted',
        'required_level',
        'required_subscription_id',
        'download_count',
        'is_active',
    ];

    protected $casts = [
        'size' => 'integer',
        'is_encrypted' => 'boolean',
        'required_level' => 'integer',
        'download_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function requiredSubscription(): BelongsTo
    {
        return $this->belongsTo(ApplicationSubscription::class, 'required_subscription_id');
    }
}
