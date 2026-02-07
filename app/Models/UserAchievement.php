<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
class UserAchievement extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'user_achievements';

    protected $fillable = [
        'user_id',
        'achievement_code',
        'title',
        'description',
        'unlocked_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievements::class, 'achievement_code', 'code');
    }

    public function isUnlocked(): bool
    {
        return $this->unlocked_at !== null;
    }

    public function getUnlockedAt(): ?Carbon
    {
        return $this->unlocked_at;
    }

    public function getUnlockedAtFormatted(): string
    {
        return $this->unlocked_at ? $this->unlocked_at->format('d M Y H:i') : 'N/A';
    }
}
