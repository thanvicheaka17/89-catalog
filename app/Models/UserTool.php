<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserTool extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'user_tools';

    protected $fillable = [
        'user_id',
        'tool_id',
        'status',
        'purchased_at',
        'expires_at',
        'usage_count',
        'max_usage',
        'price_paid',
        'transaction_id',
        'metadata',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'expires_at' => 'datetime',
        'usage_count' => 'integer',
        'max_usage' => 'integer',
        'price_paid' => 'decimal:2',
        'metadata' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class, 'tool_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                    ->orWhere('expires_at', '<=', now());
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helper methods
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_usage && $this->usage_count >= $this->max_usage) {
            return false;
        }

        return true;
    }

    public function canUse(): bool
    {
        return $this->isActive();
    }

    public function incrementUsage(): bool
    {
        if (!$this->canUse()) {
            return false;
        }

        $this->increment('usage_count');

        // Auto-expire if max usage reached
        if ($this->max_usage && $this->usage_count >= $this->max_usage) {
            $this->update(['status' => 'expired']);
        }

        return true;
    }

    public function getRemainingUses(): ?int
    {
        if (!$this->max_usage) {
            return null; // Unlimited
        }

        return max(0, $this->max_usage - $this->usage_count);
    }

    public function getDaysUntilExpiry(): ?int
    {
        if (!$this->expires_at) {
            return null; // Never expires
        }

        return now()->diffInDays($this->expires_at, false);
    }
}
