<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class ToolRating extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tool_ratings';

    protected $fillable = [
        'user_id',
        'tool_id',
        'rating',
        'review',
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * A rating belongs to a user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A rating belongs to a tool
     */
    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class, 'tool_id');
    }

    /**
     * Scope to filter by rating value
     */
    public function scopeRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope for recent ratings
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
