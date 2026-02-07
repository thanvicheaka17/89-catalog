<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserGamePlay;
use Illuminate\Http\Request;
use App\Models\DemoGame;

class UserGamePlayController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();
        $perPage = $request->input('per_page', 25);

        $userGamePlays = UserGamePlay::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $data = $userGamePlays->map(function ($userGamePlay) {
            $game = DemoGame::find($userGamePlay->game_id);
            return [
                'id' => $userGamePlay->id,
                'user_id' => $userGamePlay->user_id,
                'game_id' => $game->id,
                'title' => $game->title,
                'slug' => $game->slug,
                'description' => $game->description,
                'image' => $game->image_path,
                'is_demo' => $game->is_demo,
                'duration_minutes' => $userGamePlay->duration_minutes,
                'played_at' => $userGamePlay->played_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $userGamePlays->total(),
            'current_page' => $userGamePlays->currentPage(),
            'last_page' => $userGamePlays->lastPage(),
            'per_page' => $userGamePlays->perPage(),
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'game_id' => 'required|uuid|exists:demo_games,id',
            'duration_minutes' => 'required|integer',
            'played_at' => 'nullable|date',
        ]);

        $user = auth('api')->user();

        // Check if user already played this game in the last 5 minutes
        $recentPlay = UserGamePlay::where('user_id', $user->id)
            ->where('game_id', $request->game_id)
            ->where('played_at', '>', now()->subMinutes(5))
            ->first();

        if ($recentPlay) {
            // Update existing recent play instead of creating duplicate
            $recentPlay->update([
                'duration_minutes' => $recentPlay->duration_minutes + $request->duration_minutes,
                'played_at' => $request->played_at ?? now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Game play updated successfully',
                'data' => $recentPlay,
            ], 200);
        }

        // Create new play session if no recent play found
        $userGamePlay = UserGamePlay::create([
            'user_id' => $user->id,
            'game_id' => $request->game_id,
            'duration_minutes' => $request->duration_minutes,
            'played_at' => $request->played_at ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Game play stored successfully',
            'data' => $userGamePlay,
        ], 201);
    }
}
