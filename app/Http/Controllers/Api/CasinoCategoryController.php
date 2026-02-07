<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CasinoCategory;

class CasinoCategoryController extends Controller
{
    /**
     * Get all casino categories
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $query = CasinoCategory::query();
        $query->orderBy('created_at', 'desc');
        $categories = $query->paginate($perPage);

        $data = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'slug' => $category->slug,
                'name' => $category->name,
                'description' => $category->description,
                'logo' => $category->getLogoUrl(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $categories->total(),
            'current_page' => $categories->currentPage(),
            'last_page' => $categories->lastPage(),
            'per_page' => $categories->perPage(),
        ]);
    }

    /**
     * Get category by slug with casinos
     */
    public function show($slug)
    {
        $category = CasinoCategory::where('slug', $slug)->active()->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        $casinos = $category->casinos()->active()->ordered()->get();

        $casinoData = $casinos->map(function ($casino) {
            return [
                'id' => $casino->id,
                'provider_slug' => $casino->provider_slug,
                'name' => $casino->name,
                'description' => $casino->description,
                'logo_url' => $casino->logo_url,
                'play_url' => $casino->play_url,
                'type' => $casino->type,
                'game_types' => $casino->game_types,
                'features' => $casino->features,
                'min_bet' => $casino->min_bet,
                'max_bet' => $casino->max_bet,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $category->id,
                'slug' => $category->slug,
                'name' => $category->name,
                'description' => $category->description,
                'icon' => $category->icon,
                'casinos' => $casinoData,
                'casino_count' => $casinos->count(),
            ],
        ]);
    }
}
