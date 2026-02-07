<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Casino extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'casinos';

    protected $fillable = [
        'category_id',
        'slug',
        'name',
        'rtp',
        'description',
        'image',
        'daily_withdrawal_amount',
        'daily_withdrawal_players',
        'last_withdrawal_update',
        'total_withdrawn',
        'rating',
    ];

    protected $casts = [
        'daily_withdrawal_amount' => 'decimal:2',
        'last_withdrawal_update' => 'datetime',
        'total_withdrawn' => 'decimal:2',
        'rtp' => 'integer',
        'rating' => 'integer',
    ];

    /**
     * Get the category for this casino
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CasinoCategory::class, 'category_id');
    }

    /**
     * Get the full URL to the casino image.
     */
    public function getImageUrl(): ?string
    {
        return $this->image ? url($this->image) : null;
    }

    /**
     * Check if casino has an image.
     */
    public function hasImage(): bool
    {
        return !empty($this->image);
    }
}
