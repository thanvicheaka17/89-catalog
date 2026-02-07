<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class UserFriend extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'user_friends';

    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
        'accepted_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function friend(): BelongsTo
    {
        return $this->belongsTo(User::class, 'friend_id');
    }   

    public function isAccepted(): bool
    {
        return $this->status === 'accepted' && $this->accepted_at !== null;
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    
}
