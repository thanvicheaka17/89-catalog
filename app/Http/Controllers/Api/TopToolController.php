<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolCategory;
class TopToolController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $category = $request->input('category', null);
        $filters = $request->input('filters', null);
        $sorting = $request->input('sorting', 'most_relevant');

        $query = Tool::query();

        if ($category) {
            $query->where('category_id', $category);
        }

        if ($filters) {
            switch ($filters) {
                case 'new_releases':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
                default:
                    // For badge-based filters, filter by badge directly
                    $query->where('badge', $filters);
                    break;
            }
        }

        // Always order by display_order first (manual positioning)
        $query->orderBy('display_order', 'asc');
        
        // Sorting Options
        switch ($sorting) {
            case 'most_relevant':
                $query->orderBy('rating', 'desc')
                      ->orderBy('user_count', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
            case 'most_popular':
                $query->orderBy('user_count', 'desc')
                      ->orderBy('rating', 'desc');
                break;
            case 'highest_rated':
                $query->orderBy('rating', 'desc')
                      ->orderBy('user_count', 'desc');
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
                $query->orderBy('created_at', 'desc');
                break;
        }

        $topTools = $query->paginate($perPage);

        $data = $topTools->map(function ($topTool) {
            return [
                'id' => $topTool->id,
                'title' => $topTool->name,
                'slug' => $topTool->slug,
                'description' => $topTool->description,
                'image' => $topTool->getImageUrl(),
                'rating' => $topTool->rating, // Admin-set rating
                'average_user_rating' => round($topTool->average_rating, 1),
                'total_user_ratings' => $topTool->total_ratings,
                'user_count' => $topTool->user_count,
                'active_hours' => $topTool->active_hours,
                'rank' => $topTool->rank,
                'badge' => $topTool->badge,
                'tier' => $topTool->tier,
                'price' => $topTool->price,
                'win_rate_increase' => $topTool->win_rate_increase,
                'category' => $topTool->category->name,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $topTools->total(),
            'current_page' => $topTools->currentPage(),
            'last_page' => $topTools->lastPage(),
            'per_page' => $topTools->perPage(),
            'sort_options' => [
                'most_relevant' => 'Most Relevant',
                'most_popular' => 'Most Popular',
                'highest_rated' => 'Highest Rated',
                'price_low_to_high' => 'Price: Low to High',
                'price_high_to_low' => 'Price: High to Low',
            ],
            'filter_options' => [
                'premium' => 'Premium Only',
                'best' => 'Best Use',
                'new' => 'New Tools',
                'popular' => 'Popular Tools',
                'new_releases' => 'New Releases',
            ],
        ]);
    }

    public function show(Request $request, $slug)
    {
        $tool = Tool::where('slug', $slug)->first();

        if (!$tool) {
            return response()->json([
                'success' => false,
                'message' => 'Tool not found'
            ], 404);
        }

        $data = [
            'id' => $tool->id,
            'title' => $tool->name,
            'slug' => $tool->slug,
            'description' => $tool->description,
            'image' => $tool->getImageUrl(),
            'rating' => $tool->rating, // Admin-set rating
            'average_user_rating' => round($tool->average_rating, 1),
            'total_user_ratings' => $tool->total_ratings,
            'rating_distribution' => $tool->rating_distribution,
            'user_count' => $tool->user_count,
            'active_hours' => $tool->active_hours,
            'rank' => $tool->rank,
            'badge' => $tool->badge,
            'tier' => $tool->tier,
            'price' => $tool->price,
            'win_rate_increase' => $tool->win_rate_increase,
            'category' => $tool->category->name,
            'user_has_rated' => $tool->isRatedByUser(auth()->id()),
            'user_rating' => $tool->getUserRating(auth()->id()),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function filterOptions(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'filters' => [
                    [
                        'key' => 'premium_only',
                        'label' => 'Premium Only',
                        'type' => 'checkbox',
                        'description' => 'Show only premium tools'
                    ],
                    [
                        'key' => 'best_use',
                        'label' => 'Best Use',
                        'type' => 'checkbox',
                        'description' => 'Show only best use tools'
                    ],
                    [
                        'key' => 'new_releases',
                        'label' => 'New Releases',
                        'type' => 'checkbox',
                        'description' => 'Show tools released in the last 30 days'
                    ]
                ],
                'sorting' => [
                    [
                        'key' => 'most_relevant',
                        'label' => 'Most Relevant',
                        'description' => 'Sort by rating, popularity, and recency'
                    ],
                    [
                        'key' => 'most_popular',
                        'label' => 'Most Popular',
                        'description' => 'Sort by user count and rating'
                    ],
                    [
                        'key' => 'highest_rated',
                        'label' => 'Highest Rated',
                        'description' => 'Sort by rating and popularity'
                    ],
                    [
                        'key' => 'price_low_to_high',
                        'label' => 'Price: Low to High',
                        'description' => 'Sort by price ascending'
                    ],
                    [
                        'key' => 'price_high_to_low',
                        'label' => 'Price: High to Low',
                        'description' => 'Sort by price descending'
                    ]
                ]
            ]
        ]);
    }

    public function toolCategories(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $toolCategories = ToolCategory::orderBy('created_at', 'desc')->paginate($perPage);

        $data = $toolCategories->map(function ($toolCategory) {
            return [
                'id' => $toolCategory->id,
                'name' => $toolCategory->name,
                'tool_count' => $toolCategory->tools->count(),
                'slug' => $toolCategory->slug,
                'description' => $toolCategory->description,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $toolCategories->total(),
            'current_page' => $toolCategories->currentPage(),
            'last_page' => $toolCategories->lastPage(),
            'per_page' => $toolCategories->perPage(),
        ]);
    }

    public function allToolsBoostersByAccountBalance(Request $request)
    {
        $user = auth('api')->user();
        $accountBalance = $user->account_balance;
        $perPage = $request->input('per_page', 25);


        $topTools = Tool::where('price', '<=', $accountBalance)->orderBy('rating', 'desc')->paginate($perPage);

        $data = $topTools->map(function ($topTool) {
            return [
                'id' => $topTool->id,
                'title' => $topTool->name,
                'slug' => $topTool->slug,
                'description' => $topTool->description,
                'image' => $topTool->getImageUrl(),
                'rating' => $topTool->rating,
                'user_count' => $topTool->user_count,
                'active_hours' => $topTool->active_hours,
                'rank' => $topTool->rank,
                'badge' => $topTool->badge,
                'tier' => $topTool->tier,
                'price' => $topTool->price,
                'win_rate_increase' => $topTool->win_rate_increase,
                'category' => $topTool->category->name,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $topTools->total(),
            'current_page' => $topTools->currentPage(),
            'last_page' => $topTools->lastPage(),
            'per_page' => $topTools->perPage(),
        ]);
    }
}
