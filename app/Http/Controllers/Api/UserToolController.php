<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserTool;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserToolController extends Controller
{
    /**
     * Display a listing of user's tools/boosters.
     */
    public function index(Request $request)
    {
        $user = auth('api')->user();
        $perPage = $request->input('per_page', 25);
        $status = $request->input('status', 'active'); // active, inactive, expired, all

        $query = UserTool::with(['tool.category'])
            ->where('user_id', $user->id);

        // Filter by status
        if ($status !== 'all') {
            if ($status === 'active') {
                $query->active();
            } else {
                $query->where('status', $status);
            }
        }

        $userTools = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $data = $userTools->through(function ($userTool) {
            return [
                'user_tool_id' => $userTool->id,
                'tool_id' => $userTool->tool->id,
                'title' => $userTool->tool->name,
                'slug' => $userTool->tool->slug,
                'description' => $userTool->tool->description,
                'image' => $userTool->tool->image_path,
                'badge' => $userTool->tool->badge,
                'tier' => $userTool->tool->tier,
                'category' => $userTool->tool->category->name,
                'win_rate_increase' => $userTool->tool->win_rate_increase,
                'status' => $userTool->status,
                'purchased_at' => $userTool->purchased_at,
                'expires_at' => $userTool->expires_at,
                'usage_count' => $userTool->usage_count,
                'max_usage' => $userTool->max_usage,
                'remaining_uses' => $userTool->getRemainingUses(),
                'price_paid' => $userTool->price_paid,
                'transaction_id' => $userTool->transaction_id,
                'is_active' => $userTool->isActive(),
                'days_until_expiry' => $userTool->getDaysUntilExpiry(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
        ]);
    }

    /**
     * Purchase/Add a tool to user (Admin or programmatic assignment).
     */
    public function store(Request $request)
    {
        $request->validate([
            'tool_id' => 'required|exists:tools,id',
            'expires_at' => 'nullable|date|after:now',
            'max_usage' => 'nullable|integer|min:1',
            'price_paid' => 'nullable|numeric|min:0',
            'transaction_id' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
        ]);

        $user = auth('api')->user();
        $tool = Tool::findOrFail($request->tool_id);

        // Check if user already has this tool and it's active
        $existingTool = UserTool::where('user_id', $user->id)
            ->where('tool_id', $tool->id)
            ->active()
            ->first();

        if ($existingTool) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active version of this tool.',
                'existing_tool' => $existingTool,
            ], 409);
        }

        DB::beginTransaction();
        try {
            $pricePaid = $request->price_paid ?? $tool->price;
            $previousLevel = $user->current_level ?? 1;
            
            $userTool = UserTool::create([
                'user_id' => $user->id,
                'tool_id' => $tool->id,
                'status' => 'active',
                'purchased_at' => now(),
                'expires_at' => $request->expires_at,
                'max_usage' => $request->max_usage,
                'price_paid' => $pricePaid,
                'transaction_id' => $request->transaction_id,
                'metadata' => $request->metadata,
            ]);

            // Add funds to total accumulated funds for level system
            if ($pricePaid > 0) {
                $user->addAccumulatedFunds($pricePaid);
                $user->save();
            }

            DB::commit();

            // Refresh user to get updated level info
            $user->refresh();
            $levelInfo = $user->getLevelInfo();

            return response()->json([
                'success' => true,
                'message' => 'Tool purchased successfully!',
                'data' => $userTool->load(['tool.category']),
                'level_info' => [
                    'level' => $levelInfo['level'],
                    'tier' => $levelInfo['tier'],
                    'progress_percentage' => $levelInfo['progress_percentage'],
                    'leveled_up' => $levelInfo['level'] > $previousLevel,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to purchase tool.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified user tool.
     */
    public function show(string $id)
    {
        $user = auth('api')->user();

        $userTool = UserTool::with(['tool.category'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'user_tool_id' => $userTool->id,
                'tool_id' => $userTool->tool->id,
                'title' => $userTool->tool->name,
                'slug' => $userTool->tool->slug,
                'description' => $userTool->tool->description,
                'image' => $userTool->tool->image_path,
                'badge' => $userTool->tool->badge,
                'tier' => $userTool->tool->tier,
                'category' => $userTool->tool->category->name,
                'win_rate_increase' => $userTool->tool->win_rate_increase,
                'status' => $userTool->status,
                'purchased_at' => $userTool->purchased_at,
                'expires_at' => $userTool->expires_at,
                'usage_count' => $userTool->usage_count,
                'max_usage' => $userTool->max_usage,
                'remaining_uses' => $userTool->getRemainingUses(),
                'price_paid' => $userTool->price_paid,
                'transaction_id' => $userTool->transaction_id,
                'is_active' => $userTool->isActive(),
                'days_until_expiry' => $userTool->getDaysUntilExpiry(),
            ],
        ]);
    }

    /**
     * Use a tool (increment usage count).
     */
    public function useTool(Request $request, string $id)
    {
        $user = auth('api')->user();

        $userTool = UserTool::where('user_id', $user->id)
            ->findOrFail($id);

        if (!$userTool->canUse()) {
            return response()->json([
                'success' => false,
                'message' => 'This tool is not available for use.',
                'reason' => $userTool->status === 'expired' ? 'Tool has expired' :
                    ($userTool->max_usage && $userTool->usage_count >= $userTool->max_usage ? 'Usage limit reached' : 'Tool is inactive'),
            ], 403);
        }

        $userTool->incrementUsage();

        return response()->json([
            'success' => true,
            'message' => 'Tool used successfully!',
            'data' => [
                'usage_count' => $userTool->usage_count,
                'remaining_uses' => $userTool->getRemainingUses(),
                'is_active' => $userTool->isActive(),
            ],
        ]);
    }

    /**
     * Update the specified user tool.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'nullable|in:active,inactive,expired,suspended',
            'expires_at' => 'nullable|date',
            'max_usage' => 'nullable|integer|min:1',
            'metadata' => 'nullable|array',
        ]);

        $user = auth('api')->user();

        $userTool = UserTool::where('user_id', $user->id)
            ->findOrFail($id);

        $userTool->update($request->only([
            'status',
            'expires_at',
            'max_usage',
            'metadata',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Tool updated successfully!',
            'data' => $userTool->fresh(),
        ]);
    }

    /**
     * Remove the specified user tool.
     */
    public function destroy(string $id)
    {
        $user = auth('api')->user();

        $userTool = UserTool::where('user_id', $user->id)
            ->findOrFail($id);

        $userTool->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tool removed successfully!',
        ]);
    }

    /**
     * Get user's booster statistics.
     */
    public function stats(Request $request)
    {
        $user = auth('api')->user();

        $stats = [
            'total_tools' => UserTool::where('user_id', $user->id)->count(),
            'active_tools' => UserTool::where('user_id', $user->id)->active()->count(),
            'expired_tools' => UserTool::where('user_id', $user->id)->expired()->count(),
            'inactive_tools' => UserTool::where('user_id', $user->id)->where('status', 'inactive')->count(),
            'suspended_tools' => UserTool::where('user_id', $user->id)->where('status', 'suspended')->count(),
            'total_spent' => (float) UserTool::where('user_id', $user->id)->sum('price_paid'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
