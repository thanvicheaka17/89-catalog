<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAchievement;
use App\Models\Achievements;

class UserAchievementController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();
        $perPage = $request->input('per_page', 25);

        $userAchievements = UserAchievement::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $data = $userAchievements->map(function ($userAchievement) {
            return [
                'id' => $userAchievement->id,
                'user_id' => $userAchievement->user_id,
                'achievement_code' => $userAchievement->achievement_code,
                'title' => $userAchievement->title,
                'description' => $userAchievement->description,
                'unlocked_at' => $userAchievement->unlocked_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $userAchievements->total(),
            'current_page' => $userAchievements->currentPage(),
            'last_page' => $userAchievements->lastPage(),
            'per_page' => $userAchievements->perPage(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'achievement_code' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unlocked_at' => 'nullable|date',
        ]);
    
        $user = auth('api')->user();
    
        $achievement = UserAchievement::firstOrCreate(
            [
                'user_id' => $user->id,
                'achievement_code' => $request->achievement_code
            ],
            [
                'title' => $request->title,
                'description' => $request->description,
                'unlocked_at' => $request->unlocked_at ?? now(),
            ]
        );
    
        return response()->json([
            'success' => true,
            'data' => $achievement,
        ], 201);
    }
}
