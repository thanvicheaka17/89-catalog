<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotAndFresh extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'hot_and_fresh';

    protected $fillable = [
        'name',
        'slug',
        'image_path',
        'description',
        'rating',
        'user_count',
        'active_hours',
        'rank',
        'badge',
        'tier',
        'price',
        'win_rate_increase',
    ];
    protected $casts = [
        'rating' => 'decimal:1',
        'user_count' => 'integer',
        'active_hours' => 'integer',
        'rank' => 'integer',
        'price' => 'decimal:2',
        'win_rate_increase' => 'integer',
    ];


    /**
     * Check if this item is active
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Check if this item is inactive
     */
    public function isInactive(): bool
    {
        return $this->is_active === false;
    }

    /**
     * Get the status display name
     */
    public function getStatusDisplayName(): string
    {
        return $this->isActive() ? 'Active' : 'Inactive';
    }

    /**
     * Get the image url for the tool
     */
    public function getImageUrl()
    {
        return $this->image_path ? url($this->image_path) : null;
    }

    /**
     * Check if the tool has an image
     */
    public function hasImage()
    {
        return $this->image_path ? true : false;
    }

    public static function randomHotAndFreshData()
    {
        $priceRanges = [
            ['min' => 10000, 'max' => 49999],  // Silver range
            ['min' => 50000, 'max' => 99999],  // Gold range
            ['min' => 100000, 'max' => 500000] // Platinum range
        ];
        $selectedRange = $priceRanges[array_rand($priceRanges)];
        $price = rand($selectedRange['min'], $selectedRange['max']);

        // Set tier based on price ranges
        if ($price >= 100000) {
            $tier = 'platinum';
        } elseif ($price >= 50000) {
            $tier = 'gold';
        } else {
            $tier = 'silver';
        }

        $rating = rand(1, 5);
        $user_count = rand(1, 10000);
        $active_hours = rand(1, 10000);
        $rank = rand(1, 100);
        $badge = ['premium', 'best use', 'new', 'popular', 'best'][rand(0, 4)];
        $win_rate_increase = rand(1, 100);

        return [
            'tier' => $tier,
            'price' => $price,
            'rating' => $rating,
            'user_count' => $user_count,
            'active_hours' => $active_hours,
            'rank' => $rank,
            'badge' => $badge,
            'win_rate_increase' => $win_rate_increase,
        ];
    }
}

