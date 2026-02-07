<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tool extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tools';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_path',
        'rating',
        'user_count',
        'active_hours',
        'rank',
        'badge',
        'tier',
        'price',
        'win_rate_increase',
        'category_id',
        'display_order',
    ];

    /**
     * A tool belongs to a category
     */
    public function category()
    {
        return $this->belongsTo(ToolCategory::class, 'category_id');
    }

    /**
     * A tool has many ratings from users
     */
    public function ratings()
    {
        return $this->hasMany(ToolRating::class);
    }

    /**
     * Check if the tool has an image
     */
    public function hasImage()
    {
        return $this->image_path ? true : false;
    }

    /**
     * Get the image url for the tool
     */
    public function getImageUrl()
    {
        return $this->image_path ? url($this->image_path) : null;
    }

    /**
     * Get the average user rating for this tool
     */
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    /**
     * Get the total number of ratings for this tool
     */
    public function getTotalRatingsAttribute()
    {
        return $this->ratings()->count();
    }

    /**
     * Get rating distribution (count of each star rating)
     */
    public function getRatingDistributionAttribute()
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->ratings()->where('rating', $i)->count();
        }
        return $distribution;
    }

    /**
     * Check if user has already rated this tool
     */
    public function isRatedByUser($userId)
    {
        return $this->ratings()->where('user_id', $userId)->exists();
    }

    /**
     * Get user's rating for this tool
     */
    public function getUserRating($userId)
    {
        return $this->ratings()->where('user_id', $userId)->first();
    }

    public static function randomToolData()
    {
        // Generate price in IDR ranges for tier classification
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
