<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Casino;
use App\Models\CasinoCategory;
class CasinoController extends Controller
{
    /**
     * Get all casinos
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $categorySlug = $request->input('category');
        $sortBy = $request->input('sort', 'newest'); // Default sort by newest

        $query = Casino::query();

        if ($categorySlug) {
            $getCategory = CasinoCategory::where('slug', $categorySlug)->first();
            if ($getCategory) {
                $query->where('category_id', $getCategory->id);
            } else {
                // Category not found, return empty result
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage,
                ]);
            }
        }

        // Apply sorting based on the sort parameter
        switch ($sortBy) {
            case 'rtp_high':
                $query->orderBy('rtp', 'desc');
                break;
            case 'rtp_low':
                $query->orderBy('rtp', 'asc');
                break;
            case 'wd_high':
                $query->orderBy('daily_withdrawal_amount', 'desc');
                break;
            case 'wd_low':
                $query->orderBy('daily_withdrawal_amount', 'asc');
                break;
            case 'wd_players_high':
                $query->orderBy('daily_withdrawal_players', 'desc');
                break;
            case 'wd_players_low':
                $query->orderBy('daily_withdrawal_players', 'asc');
                break;
            case 'jp_players_high':
                // Jackpot players field doesn't exist yet, fallback to withdrawal players
                $query->orderBy('daily_withdrawal_players', 'desc');
                break;
            case 'jp_players_low':
                // Jackpot players field doesn't exist yet, fallback to withdrawal players
                $query->orderBy('daily_withdrawal_players', 'asc');
                break;
            case 'rating_high':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('rating', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $casinos = $query->with('category')->paginate($perPage);

        $data = $casinos->map(function ($casino) {
            return [
                'id' => $casino->id,
                'slug' => $casino->slug,
                'name' => $casino->name,
                'category' => $casino->category->name,
                'description' => $casino->description,
                'image' => $casino->getImageUrl(),
                'rtp' => $casino->rtp,
                'rating' => $casino->rating,
                'daily_withdrawal_amount' => $casino->daily_withdrawal_amount,
                'daily_withdrawal_players' => $casino->daily_withdrawal_players,
                'last_withdrawal_update' => $casino->last_withdrawal_update,
                'total_withdrawn' => $casino->total_withdrawn,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'sort_options' => [
                'rtp_high' => 'RTP High to Low',
                'rtp_low' => 'RTP Low to High',
                'wd_high' => 'Daily Withdrawal Amount High to Low',
                'wd_low' => 'Daily Withdrawal Amount Low to High',
                'wd_players_high' => 'Daily Withdrawal Players High to Low',
                'wd_players_low' => 'Daily Withdrawal Players Low to High',
            ],
            'total' => $casinos->total(),
            'current_page' => $casinos->currentPage(),
            'last_page' => $casinos->lastPage(),
            'per_page' => $casinos->perPage(),
        ]);
    }
}
