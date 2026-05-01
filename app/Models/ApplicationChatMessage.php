<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_chat_channel_id',
        'application_user_id',
        'username_snapshot',
        'message',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(ApplicationChatChannel::class, 'application_chat_channel_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(ApplicationUser::class, 'application_user_id');
    }
}
