<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGamePlay extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'user_game_plays';

    protected $fillable = [
        'user_id',
        'game_id',
        'duration_minutes',
        'played_at',
    ];  

    protected $casts = [
        'duration_minutes' => 'integer',
        'played_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(DemoGame::class, 'game_id');
    }
}
