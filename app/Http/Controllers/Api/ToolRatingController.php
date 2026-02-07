<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToolRatingController extends Controller
{
    /**
     * Store a new rating (accepts tool_id or tool_slug in request body)
     */
    public function store(Request $request)
    {
        $request->validate([
            'tool_id' => 'required_without:tool_slug|uuid|exists:tools,id',
            'tool_slug' => 'required_without:tool_id|string|exists:tools,slug',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);

        // Find tool by ID or slug
        $tool = null;
        if ($request->has('tool_id')) {
            $tool = Tool::find($request->tool_id);
        } elseif ($request->has('tool_slug')) {
            $tool = Tool::where('slug', $request->tool_slug)->first();
        }

        if (!$tool) {
            return response()->json([
                'success' => false,
                'message' => 'Tool not found'
            ], 404);
        }

        $userId = Auth::id();

        // Check if user already rated this tool
        $existingRating = ToolRating::where('user_id', $userId)
            ->where('tool_id', $tool->id)
            ->first();

        if ($existingRating) {
            return response()->json([
                'success' => false,
                'message' => 'You have already rated this tool.'
            ], 409);
        }

        // Create new rating
        $rating = ToolRating::create([
            'user_id' => $userId,
            'tool_id' => $tool->id,
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rating successfully',
            // 'data' => $rating->load('user:id,name')
        ], 201);
    }

    /**
     * Rate a tool (create or update rating) - URL-based approach
     */
    public function rate(Request $request, $toolSlug)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);

        $tool = Tool::where('slug', $toolSlug)->first();

        if (!$tool) {
            return response()->json([
                'success' => false,
                'message' => 'Tool not found'
            ], 404);
        }

        $userId = Auth::id();

        // Check if user already rated this tool
        $existingRating = ToolRating::where('user_id', $userId)
            ->where('tool_id', $tool->id)
            ->first();

        if ($existingRating) {
            // Update existing rating
            $existingRating->update([
                'rating' => $request->rating,
                'review' => $request->review
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rating updated successfully',
            ]);
        } else {
            // Create new rating
            $rating = ToolRating::create([
                'user_id' => $userId,
                'tool_id' => $tool->id,
                'rating' => $request->rating,
                'review' => $request->review
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rating submitted successfully',
                'data' => $rating->load('user:id,name')
            ], 201);
        }
    }

    /**
     * Get all ratings for a specific tool
     */
    public function getToolRatings(Request $request, $toolSlug)
    {
        $perPage = $request->input('per_page', 10);

        $tool = Tool::where('slug', $toolSlug)->first();

        if (!$tool) {
            return response()->json([
                'success' => false,
                'message' => 'Tool not found'
            ], 404);
        }

        $ratings = $tool->ratings()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $data = $ratings->map(function ($rating) {
            return [
                'id' => $rating->id,
                'user_name' => $rating->user->name,
                'rating' => $rating->rating,
                'review' => $rating->review,
                'created_at' => $rating->created_at,
                'is_owner' => $rating->user_id === Auth::id()
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'tool_stats' => [
                'average_rating' => $tool->average_rating,
                'total_ratings' => $tool->total_ratings,
                'rating_distribution' => $tool->rating_distribution
            ],
            'total' => $ratings->total(),
            'current_page' => $ratings->currentPage(),
            'last_page' => $ratings->lastPage(),
            'per_page' => $ratings->perPage(),
        ]);
    }

    /**
     * Update user's rating for a tool
     */
    public function updateRating(Request $request, $toolSlug)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);

        $tool = Tool::where('slug', $toolSlug)->first();

        if (!$tool) {
            return response()->json([
                'success' => false,
                'message' => 'Tool not found'
            ], 404);
        }

        $rating = ToolRating::where('user_id', Auth::id())
            ->where('tool_id', $tool->id)
            ->first();

        if (!$rating) {
            return response()->json([
                'success' => false,
                'message' => 'Rating not found'
            ], 404);
        }

        $rating->update([
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rating updated successfully',
            'data' => $rating->load('user:id,name')
        ]);
    }


    /**
     * Get user's ratings across all tools
     */
    public function getUserRatings(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        $ratings = ToolRating::where('user_id', Auth::id())
            ->with(['tool:id,name,slug,image_path'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $data = $ratings->map(function ($rating) {
            return [
                'id' => $rating->id,
                'tool' => [
                    'id' => $rating->tool->id,
                    'name' => $rating->tool->name,
                    'slug' => $rating->tool->slug,
                    'image' => $rating->tool->image_path
                ],
                'rating' => $rating->rating,
                'review' => $rating->review,
                'created_at' => $rating->created_at,
                'updated_at' => $rating->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $ratings->total(),
            'current_page' => $ratings->currentPage(),
            'last_page' => $ratings->lastPage(),
            'per_page' => $ratings->perPage(),
        ]);
    }

    /**
     * Get rating statistics across all tools
     */
    public function getRatingStats(Request $request)
    {
        $stats = [
            'total_ratings' => ToolRating::count(),
            'average_rating_all' => ToolRating::avg('rating') ?? 0,
            'rating_distribution' => $this->getOverallRatingDistribution(),
            'top_rated_tools' => $this->getTopRatedTools(5),
            'recent_ratings' => ToolRating::with(['user:id,name', 'tool:id,name,slug'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($rating) {
                    return [
                        'user_name' => $rating->user->name,
                        'tool_name' => $rating->tool->name,
                        'tool_slug' => $rating->tool->slug,
                        'rating' => $rating->rating,
                        'created_at' => $rating->created_at
                    ];
                })
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Helper method to get overall rating distribution
     */
    private function getOverallRatingDistribution()
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = ToolRating::where('rating', $i)->count();
        }
        return $distribution;
    }

    /**
     * Helper method to get top rated tools
     */
    private function getTopRatedTools($limit = 5)
    {
        return Tool::withCount('ratings')
            ->having('ratings_count', '>', 0)
            ->get()
            ->map(function ($tool) {
                return [
                    'id' => $tool->id,
                    'name' => $tool->name,
                    'slug' => $tool->slug,
                    'average_rating' => $tool->average_rating,
                    'total_ratings' => $tool->total_ratings,
                    'image' => $tool->image_path
                ];
            })
            ->sortByDesc('average_rating')
            ->take($limit)
            ->values();
    }
}
