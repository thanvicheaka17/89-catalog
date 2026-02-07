<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class LevelConfiguration extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'level',
        'threshold',
        'tier',
        'tier_name',
        'tier_info',
        'tier_min_level',
        'tier_max_level',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'threshold' => 'decimal:2',
        'tier_info' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'level' => 'integer',
        'tier_min_level' => 'integer',
        'tier_max_level' => 'integer',
    ];

    /**
     * Scope for active configurations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered by level
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('level', 'asc');
    }

    /**
     * Get tier configuration by tier name
     */
    public static function getByTier(string $tierName)
    {
        return static::where('tier_name', $tierName)
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Get level by threshold
     */
    public static function getLevelByThreshold(float $threshold)
    {
        return static::where('threshold', '<=', $threshold)
            ->active()
            ->ordered()
            ->orderBy('level', 'desc')
            ->first();
    }

    /**
     * Get next level threshold
     */
    public static function getNextLevelThreshold(int $currentLevel)
    {
        return static::where('level', '>', $currentLevel)
            ->active()
            ->ordered()
            ->first();
    }

}
