<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Provider;
class GameController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $providerSlug = $request->input('provider');
        $search = $request->input('search');

        $query = Game::query();

        if ($providerSlug) {
            $provider = Provider::where('slug', $providerSlug)->first();
            if ($provider) {
                $query->where('provider_id', $provider->id);
            }
        }
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        $games = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $data = $games->map(function ($game) {
            return [
                'id' => $game->id,
                'name' => $game->name,
                'slug' => $game->slug,
                'description' => $game->description,
                'image' => $game->getImageUrl(),
                'provider' => $game->provider->name,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $games->total(),
            'current_page' => $games->currentPage(),
            'last_page' => $games->lastPage(),
            'per_page' => $games->perPage(),
        ]);
    }
}
