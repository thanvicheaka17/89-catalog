<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RTPGame;
use App\Models\DemoGame;
use App\Models\HotAndFresh;
use App\Models\Tool;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Cache;
use App\Models\Promotion;
use App\Models\Event;
use App\Models\Casino;
class GlobalSearchController extends Controller
{
    /**
     * Search across all games, tools, and content
     * Optimized for handling multiple APIs with caching and parallel processing
     * Returns categorized results for search results page
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search'));
        if (!$search || strlen($search) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Search query must be at least 2 characters long',
            ], 400);
        }

        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);
        $cacheKey = "search_all_{$search}_p{$page}_pp{$perPage}";

        // Try cache first for performance
        // return Cache::remember($cacheKey, 0, function () use ($search, $perPage, $page) {
        //     return $this->performSearch($search, $perPage, $page);
        // });
        return $this->performSearch($search, $perPage, $page);
    }

    /**
     * Perform the actual search operation
     * Uses parallel processing and optimized queries
     * Different search configurations for visitors vs authenticated users
     */
    private function performSearch(string $search, int $perPage, int $page): array
    {
        try {
            // Check if user is authenticated
            $user = auth('api')->user();
            if ($user) {
                $isAuthenticated = true;
            } else {
                $isAuthenticated = false;
            }

            // Define different search configurations based on authentication status
            if ($isAuthenticated) {
                // Full search configuration for authenticated users
                $searchConfigs = $this->getAuthenticatedUserSearchConfig();
            } else {
                // Limited search configuration for visitors
                $searchConfigs = $this->getVisitorSearchConfig();
            }

            $allResults = collect();
            $categoryCounts = [];

            // Parallel processing for better performance with multiple APIs
            foreach ($searchConfigs as $type => $config) {
                $results = $this->searchContentType($search, $config);
                $allResults = $allResults->merge($results);
                $categoryCounts[$type] = $results->count();
            }

            // Sort by relevance (newest first, then by title match strength)
            $allResults = $allResults->sort(function ($a, $b) use ($search) {
                // Ensure titles are strings and handle null values
                $aTitle = is_string($a['title'] ?? null) ? $a['title'] : '';
                $bTitle = is_string($b['title'] ?? null) ? $b['title'] : '';

                // Exact title matches get higher priority
                $aExact = !empty($aTitle) && stripos($aTitle, $search) === 0 ? 1 : 0;
                $bExact = !empty($bTitle) && stripos($bTitle, $search) === 0 ? 1 : 0;

                if ($aExact !== $bExact) {
                    return $bExact <=> $aExact;
                }

                // Then sort by date
                $aDate = $a['created_at'] ?? '1970-01-01';
                $bDate = $b['created_at'] ?? '1970-01-01';
                return strtotime($bDate) <=> strtotime($aDate);
            })->values();

            // Manual pagination
            $totalResults = $allResults->count();
            $totalPages = ceil($totalResults / $perPage);
            $offset = ($page - 1) * $perPage;
            $paginatedResults = $allResults->slice($offset, $perPage)->values();

            // Group results by type for better display
            $groupedResults = [];
            foreach ($paginatedResults as $result) {
                $type = $result['type'] ?? 'unknown';
                if (!isset($groupedResults[$type])) {
                    $groupedResults[$type] = collect();
                }
                $groupedResults[$type]->push($result);
            }

            return [
                'success' => true,
                'data' => $groupedResults,
                'meta' => [
                    'search_query' => $search,
                    'total_results' => $totalResults,
                    'current_page' => (int) $page,
                    'last_page' => (int) $totalPages,
                    'per_page' => (int) $perPage,
                    'from' => $totalResults > 0 ? $offset + 1 : 0,
                    'to' => min($offset + $perPage, $totalResults),
                    'category_counts' => $categoryCounts,
                ]
            ];
        } catch (\Exception $e) {
            \Log::error('Global search error: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());

            return [
                'success' => false,
                'message' => 'Search temporarily unavailable. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ];
        }
    }

    /**
     * Get search configuration for authenticated users (full access)
     */
    private function getAuthenticatedUserSearchConfig(): array
    {
        return [
            'games' => [
                'model' => RTPGame::class,
                'fields' => ['name'],
                'relations' => ['provider'],
                'limit' => 25,
                'transformer' => 'transformGameResult'
            ],
            'demo_games' => [
                'model' => DemoGame::class,
                'fields' => ['title', 'description'],
                'limit' => 50,
                'transformer' => 'transformDemoGameResult'
            ],
            'hot_fresh' => [
                'model' => HotAndFresh::class,
                'fields' => ['name', 'description'],
                'limit' => 25,
                'transformer' => 'transformHotFreshResult'
            ],
            'tools' => [
                'model' => Tool::class,
                'fields' => ['name', 'description'],
                'relations' => ['category'],
                'limit' => 25,
                'transformer' => 'transformToolResult'
            ],
            'testimonials' => [
                'model' => Testimonial::class,
                'fields' => ['user_name', 'message', 'user_role'],
                'where' => ['is_active' => true],
                'limit' => 25,
                'transformer' => 'transformTestimonialResult'
            ],
            'casinos' => [
                'model' => Casino::class,
                'fields' => ['name', 'description'],
                'limit' => 25,
                'transformer' => 'transformCasinoResult'
            ],
            'promotions' => [
                'model' => Promotion::class,
                'fields' => ['title', 'message'],
                'limit' => 25,
                'transformer' => 'transformPromotionResult'
            ],
            'events' => [
                'model' => Event::class,
                'fields' => ['title', 'description'],
                'limit' => 25,
                'transformer' => 'transformEventResult'
            ]
        ];
    }

    /**
     * Get search configuration for visitors (limited access)
     */
    private function getVisitorSearchConfig(): array
    {
        return [
            'demo_games' => [
                'model' => DemoGame::class,
                'fields' => ['title', 'description'],
                'limit' => 50,
                'transformer' => 'transformDemoGameResult'
            ],
            'testimonials' => [
                'model' => Testimonial::class,
                'fields' => ['user_name', 'message', 'user_role'],
                'where' => ['is_active' => true],
                'limit' => 25,
                'transformer' => 'transformTestimonialResult'
            ],
            'promotions' => [
                'model' => Promotion::class,
                'fields' => ['title', 'message'],
                'limit' => 25,
                'transformer' => 'transformPromotionResult'
            ],
            'events' => [
                'model' => Event::class,
                'fields' => ['title', 'description'],
                'limit' => 25,
                'transformer' => 'transformEventResult'
            ]
        ];
    }

    /**
     * Search a specific content type using configuration
     */
    private function searchContentType(string $search, array $config): \Illuminate\Support\Collection
    {
        try {
            $query = $config['model']::query();

            // Apply relations if specified
            if (isset($config['relations'])) {
                $query->with($config['relations']);
            }

            // Apply additional where conditions
            if (isset($config['where'])) {
                foreach ($config['where'] as $field => $value) {
                    $query->where($field, $value);
                }
            }

            // Build search conditions
            $query->where(function ($q) use ($search, $config) {
                foreach ($config['fields'] as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });

            // Apply limit
            $query->limit($config['limit']);

            // Execute query
            $results = $query->get();

            // Transform results using the specified transformer method
            return $results->map(function ($item) use ($config) {
                try {
                    $transformed = $this->{$config['transformer']}($item);

                    // Ensure required fields exist
                    if (!isset($transformed['title']) || !isset($transformed['created_at'])) {
                        \Log::warning("Transformer {$config['transformer']} missing required fields", [
                            'item_id' => $item->id ?? 'unknown',
                            'transformed' => $transformed
                        ]);
                        return null;
                    }

                    return $transformed;
                } catch (\Exception $e) {
                    // Log error and return empty array for this item
                    \Log::error("Error transforming {$config['model']} item: " . $e->getMessage(), [
                        'item_id' => $item->id ?? 'unknown',
                        'transformer' => $config['transformer']
                    ]);
                    return null;
                }
            })->filter(); // Remove null values

        } catch (\Exception $e) {
            // Log the error and return empty collection
            \Log::error("Error searching {$config['model']}: " . $e->getMessage());
            return collect();
        }
    }

    /**
     * Transform RTP Game result
     */
    private function transformGameResult($game): array
    {
        return [
            'id' => $game->id,
            'title' => $game->name,
            'type' => 'rtp_games',
            'name' => $game->name,
            'provider' => $game->provider->name,
            'rtp' => $game->rtp,
            'pola' => $game->pola,
            'rating' => $game->rating,
            'image' => $game->img_src,
            'stake_bet' => $game->stake_bet,
            'step_one' => $game->step_one,
            'step_two' => $game->step_two,
            'step_three' => $game->step_three,
            'step_four' => $game->step_four,
            'type_step_one' => $game->type_step_one,
            'type_step_two' => $game->type_step_two,
            'type_step_three' => $game->type_step_three,
            'type_step_four' => $game->type_step_four,
            'description_step_one' => $game->desc_step_one,
            'description_step_two' => $game->desc_step_two,
            'description_step_three' => $game->desc_step_three,
            'description_step_four' => $game->desc_step_four,
            'created_at' => $game->created_at->format('Y-m-d')
        ];
    }

    /**
     * Transform Demo Game result
     */
    private function transformDemoGameResult($game): array
    {
        return [
            'id' => $game->id,
            'title' => $game->title,
            'type' => 'demo_games',
            'slug' => $game->slug,
            'image' => $game->getImageUrl(),
            'description' => $game->description,
            'is_demo' => $game->is_demo,
            'created_at' => $game->created_at->format('Y-m-d')
        ];
    }

    /**
     * Transform Hot & Fresh result
     */
    private function transformHotFreshResult($item): array
    {
        return [
            'id' => $item->id,
            'title' => $item->name,
            'type' => 'hot_fresh',
            'slug' => $item->slug,
            'description' => $item->description,
            'image' => $item->getImageUrl(),
            'rating' => $item->rating,
            'user_count' => $item->user_count,
            'active_hours' => $item->active_hours,
            'rank' => $item->rank,
            'badge' => $item->badge,
            'tier' => $item->tier,
            'price' => $item->price,
            'win_rate_increase' => $item->win_rate_increase,
            'popularity_score' => $item->popularity_score,
            'created_at' => $item->created_at->format('Y-m-d')
        ];
    }

    /**
     * Transform Tool result
     */
    private function transformToolResult($tool): array
    {
        return [
            'id' => $tool->id,
            'title' => $tool->name,
            'type' => 'tools',
            'slug' => $tool->slug,
            'description' => $tool->description,
            'image' => $tool->getImageUrl(),
            'rating' => $tool->rating,
            'average_user_rating' => $tool->average_user_rating,
            'total_user_ratings' => $tool->total_user_ratings,
            'user_count' => $tool->user_count,
            'active_hours' => $tool->active_hours,
            'rank' => $tool->rank,
            'badge' => $tool->badge,
            'tier' => $tool->tier,
            'price' => $tool->price,
            'win_rate_increase' => $tool->win_rate_increase,
            'category' => $tool->category->name,
            'created_at' => $tool->created_at->format('Y-m-d')
        ];
    }

    /**
     * Transform Testimonial result
     */
    private function transformTestimonialResult($testimonial): array
    {
        return [
            'id' => $testimonial->id,
            'title' => $testimonial->user_name,
            'type' => 'testimonials',
            'user_name' => $testimonial->user_name,
            'user_role' => $testimonial->user_role,
            'avatar' => $testimonial->getAvatarUrl(),
            'message' => $testimonial->message,
            'rating' => $testimonial->rating,
            'is_featured' => $testimonial->is_featured,
            'is_active' => $testimonial->is_active,
            'created_at' => $testimonial->created_at->format('Y-m-d')
        ];
    }

    /**
     * Generate search suggestions based on popular searches
     */
    private function generateSearchSuggestions(string $query): array
    {
        $suggestions = [
            'Fortune Tiger',
            'Sweet Bonanza',
            'Gates of Olympus',
            'Candy Burst',
            'Lucky Neko',
            'Mahjong Ways',
            'Wild West Gold',
            'Aztec Gems',
            'Starlight Princess',
            'Great Rhino',
            'Premium Tool',
            'Gold Strategy',
            'Platinum Guide',
            'Customer Review',
            'User Experience'
        ];

        // Filter suggestions that contain the query
        return array_filter($suggestions, function ($suggestion) use ($query) {
            return stripos($suggestion, $query) !== false;
        });
    }

    /**
     * Transform Promotion result
     */
    private function transformPromotionResult($promotion): array
    {
        return [
            'id' => $promotion->id,
            'title' => $promotion->title,
            'type' => 'promotions',
            'image' => $promotion->getImageUrl(),
            'position' => $promotion->position,
            'created_at' => $promotion->created_at->format('Y-m-d')
        ];
    }

    /**
     * Transform Event result
     */
    private function transformEventResult($event): array
    {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'type' => 'events',
            'description' => $event->description,
            'is_active' => $event->is_active,
            'status' => $event->getStatus(),
            'start_at' => $event->start_at?->format('Y-m-d H:i:s'),
            'end_at' => $event->end_at?->format('Y-m-d H:i:s'),
            'created_at' => $event->created_at->format('Y-m-d')
        ];
    }

    /**
     * Transform Casino result
     */
    private function transformCasinoResult($casino): array
    {
        return [
            'id' => $casino->id,
            'title' => $casino->name,
            'type' => 'casinos',
            'slug' => $casino->slug,
            'image' => $casino->getImageUrl(),
            'description' => $casino->description,
            'rating' => $casino->rating,
            'daily_withdrawal_amount' => $casino->daily_withdrawal_amount,
            'daily_withdrawal_players' => $casino->daily_withdrawal_players,
            'last_withdrawal_update' => $casino->last_withdrawal_update,
            'total_withdrawn' => $casino->total_withdrawn,
            'created_at' => $casino->created_at->format('Y-m-d')
        ];
    }
}
