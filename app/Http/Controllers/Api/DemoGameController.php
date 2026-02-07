<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DemoGame;
class DemoGameController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $demoGames = DemoGame::orderBy('created_at', 'desc')->paginate($perPage);

        $data = $demoGames->map(function ($demoGame) {
            return [
                'id' => $demoGame->id,
                'title' => $demoGame->title,
                'slug' => $demoGame->slug,
                'url' => $demoGame->url,
                'description' => $demoGame->description,
                'image' => $demoGame->getImageUrl(),
                'is_demo' => $demoGame->is_demo,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $demoGames->total(),
            'current_page' => $demoGames->currentPage(),
            'last_page' => $demoGames->lastPage(),
            'per_page' => $demoGames->perPage(),
        ]);
    }

    public function show(Request $request, $slug)
    {
        $demoGame = DemoGame::where('slug', $slug)->first();
        
        if (!$demoGame) {
            return response()->json([
                'success' => false,
                'message' => 'Demo game not found',
            ], 404);
        }

        $data = [
            'id' => $demoGame->id,
            'title' => $demoGame->title,
            'slug' => $demoGame->slug,
            'url' => $demoGame->url,
            'description' => $demoGame->description,
            'image' => $demoGame->getImageUrl(),
            'is_demo' => $demoGame->is_demo,
        ];
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);  
    }
}
