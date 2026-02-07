<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HotAndFresh;

class HotAndFreshController extends Controller
{
    /**
     * Get list of Hot & Fresh items
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $sorting = $request->input('sorting', 'most_popular');

        $query = HotAndFresh::query();

        // Sorting Options
        switch ($sorting) {
            case 'most_popular':
                $query->orderBy('user_count', 'desc')
                      ->orderBy('rating', 'desc')
                      ->orderBy('active_hours', 'desc');
                break;
            case 'highest_rated':
                $query->orderBy('rating', 'desc')
                      ->orderBy('user_count', 'desc');
                break;
            case 'rank':
                $query->orderBy('rank', 'asc')
                      ->orderBy('rating', 'desc');
                break;
            case 'price_low_to_high':
                $query->orderBy('price', 'asc')
                      ->orderBy('rating', 'desc');
                break;
            case 'price_high_to_low':
                $query->orderBy('price', 'desc')
                      ->orderBy('rating', 'desc');
                break;
            default:
                $query->orderBy('rank', 'asc')
                      ->orderBy('user_count', 'desc');
                break;
        }

        $hotAndFresh = $query->paginate($perPage);

        $data = $hotAndFresh->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->name,
                'slug' => $item->slug,
                'description' => $item->description,
                'image' => $item->getImageUrl(),
                'rating' => (float) $item->rating,
                'user_count' => $item->user_count,
                'active_hours' => $item->active_hours,
                'rank' => $item->rank,
                'badge' => $item->badge,
                'tier' => $item->tier,
                'price' => (float) $item->price,
                'win_rate_increase' => $item->win_rate_increase,
                'popularity_score' => $item->user_count + ($item->rating * 100) + ($item->active_hours / 10),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $hotAndFresh->total(),
            'current_page' => $hotAndFresh->currentPage(),
            'last_page' => $hotAndFresh->lastPage(),
            'per_page' => $hotAndFresh->perPage(),
            'sort_options' => [
                'most_popular' => 'Most Popular',
                'highest_rated' => 'Highest Rated',
                'rank' => 'By Rank',
                'price_low_to_high' => 'Price: Low to High',
                'price_high_to_low' => 'Price: High to Low',
            ],
        ]);
    }

    /**
     * Get a single Hot & Fresh item by slug
     */
    public function show(Request $request, $slug)
    {
        $hotAndFresh = HotAndFresh::where('slug', $slug)->first();

        if (!$hotAndFresh) {
            return response()->json([
                'success' => false,
                'message' => 'Hot & Fresh item not found'
            ], 404);
        }

        $data = [
            'id' => $hotAndFresh->id,
            'title' => $hotAndFresh->name,
            'slug' => $hotAndFresh->slug,
            'description' => $hotAndFresh->description,
            'image' => $hotAndFresh->getImageUrl(),
            'rating' => (float) $hotAndFresh->rating,
            'user_count' => $hotAndFresh->user_count,
            'active_hours' => $hotAndFresh->active_hours,
            'rank' => $hotAndFresh->rank,
            'badge' => $hotAndFresh->badge,
            'tier' => $hotAndFresh->tier,
            'price' => (float) $hotAndFresh->price,
            'win_rate_increase' => $hotAndFresh->win_rate_increase,
            'popularity_score' => $hotAndFresh->user_count + ($hotAndFresh->rating * 100) + ($hotAndFresh->active_hours / 10),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
