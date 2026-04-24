<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatRoomInvitation extends Model
{
    protected $fillable = ['conversation_id', 'invited_by_id', 'user_id', 'status'];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
