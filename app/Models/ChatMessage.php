<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Events\ChatMessageBroadcast;

class ChatMessage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'username',
        'message',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($chatMessage) {
            broadcast(new ChatMessageBroadcast($chatMessage))->toOthers();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get masked username for privacy
     */
    public function getMaskedUsernameAttribute(): string
    {
        $username = $this->username;

        if (strlen($username) <= 4) {
            return $username . '**XX';
        }

        return substr($username, 0, 4) . '**XX';
    }

    /**
     * Format message timestamp
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->format('H:i');
    }
}
