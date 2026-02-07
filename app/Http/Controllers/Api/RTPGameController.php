<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\RTPGame;
use App\Models\Provider;
use App\Models\DemoGame;
use App\Models\HotAndFresh;
use App\Models\Tool;
use App\Models\Promotion;

class RTPGameController extends Controller
{
    public function index(Request $request)
    {
        $query = RTPGame::query();
        $perPage = $request->input('per_page', 25);
        $providerSlug = $request->input('provider');
        $sortBy = $request->input('sort', ''); // Default sort by RTP high to low

        if ($providerSlug) {
            $getProvider = Provider::where('slug', $providerSlug)->first();
            if ($getProvider) {
                $query->where('provider_id', $getProvider->id);
            } else {
                // Provider not found, return empty result
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
            case 'rating_high':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('rating', 'asc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'most_popular':
                // For now, sort by RTP and rating as popularity indicators
                $query->orderBy('rtp', 'desc')->orderBy('rating', 'desc');
                break;
            case 'least_popular':
                // For now, sort by RTP and rating ascending as inverse popularity
                $query->orderBy('rtp', 'asc')->orderBy('rating', 'asc');
                break;
            case 'most_rated':
                // For now, sort by rating descending
                $query->orderBy('rating', 'desc');
                break;
            case 'least_rated':
                // For now, sort by rating ascending
                $query->orderBy('rating', 'asc');
                break;
            default:
                break;
        }

        $rtpGames = $query->with('provider')->paginate($perPage);

        $data = $rtpGames->map(function ($rtpGame) {
            return [
                'id' => $rtpGame->id,
                'name' => $rtpGame->name,
                'provider' => $rtpGame->provider->name,
                'rtp' => $rtpGame->rtp,
                'pola' => $rtpGame->pola,
                'rating' => $rtpGame->rating,
                'image' => $rtpGame->img_src,
                'stake_bet' => $rtpGame->stake_bet,
                'step_one' => $rtpGame->step_one,
                'step_two' => $rtpGame->step_two,
                'step_three' => $rtpGame->step_three,
                'step_four' => $rtpGame->step_four,
                'type_step_one' => $rtpGame->type_step_one,
                'type_step_two' => $rtpGame->type_step_two,
                'type_step_three' => $rtpGame->type_step_three,
                'type_step_four' => $rtpGame->type_step_four,
                'description_step_one' => $rtpGame->desc_step_one,
                'description_step_two' => $rtpGame->desc_step_two,
                'description_step_three' => $rtpGame->desc_step_three,
                'description_step_four' => $rtpGame->desc_step_four,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'sort_options' => [
                'rtp_high' => 'RTP High to Low',
                'rtp_low' => 'RTP Low to High',
                'rating_high' => 'Rating High to Low',
                'rating_low' => 'Rating Low to High',
                'newest' => 'Newest',
                'oldest' => 'Oldest',
                'most_popular' => 'Most Popular',
                'least_popular' => 'Least Popular',
                'most_rated' => 'Most Rated',
                'least_rated' => 'Least Rated',
            ],
            'total' => $rtpGames->total(),
            'current_page' => $rtpGames->currentPage(),
            'last_page' => $rtpGames->lastPage(),
            'per_page' => $rtpGames->perPage(),
        ]);
    }

    public function rtpPromaxGames(Request $request)
    {
        $providers = Provider::where('is_rtp_promax', true)->get();
        $query = RTPGame::whereIn('provider_id', $providers->pluck('id'));
        $perPage = $request->input('per_page', 25);
        $providerSlug = $request->input('provider');
        $sortBy = $request->input('sort', 'rtp_high'); // Default sort by RTP high to low

        if ($providerSlug) {
            $getProvider = Provider::where('slug', $providerSlug)->first();
            if ($getProvider) {
                $query->where('provider_id', $getProvider->id);
            } else {
                // Provider not found, return empty result
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
            case 'rating_high':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('rating', 'asc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'most_popular':
                // For now, sort by RTP and rating as popularity indicators
                $query->orderBy('rtp', 'desc')->orderBy('rating', 'desc');
                break;
            case 'least_popular':
                // For now, sort by RTP and rating ascending as inverse popularity
                $query->orderBy('rtp', 'asc')->orderBy('rating', 'asc');
                break;
            case 'most_rated':
                // For now, sort by rating descending
                $query->orderBy('rating', 'desc');
                break;
            case 'least_rated':
                // For now, sort by rating ascending
                $query->orderBy('rating', 'asc');
                break;
            default:
                $query->orderBy('rtp', 'desc')->orderBy('rating', 'desc');
                break;
        }

        $rtpGames = $query->with('provider')->paginate($perPage);

        $data = $rtpGames->map(function ($rtpPromaxGame) {
            return [
                'id' => $rtpPromaxGame->id,
                'name' => $rtpPromaxGame->name,
                'provider' => $rtpPromaxGame->provider->name,
                'rtp' => $rtpPromaxGame->rtp,
                'pola' => $rtpPromaxGame->pola,
                'rating' => $rtpPromaxGame->rating,
                'image' => $rtpPromaxGame->img_src,
                'stake_bet' => $rtpPromaxGame->stake_bet,
                'step_one' => $rtpPromaxGame->step_one,
                'step_two' => $rtpPromaxGame->step_two,
                'step_three' => $rtpPromaxGame->step_three,
                'step_four' => $rtpPromaxGame->step_four,
                'type_step_one' => $rtpPromaxGame->type_step_one,
                'type_step_two' => $rtpPromaxGame->type_step_two,
                'type_step_three' => $rtpPromaxGame->type_step_three,
                'type_step_four' => $rtpPromaxGame->type_step_four,
                'description_step_one' => $rtpPromaxGame->desc_step_one,
                'description_step_two' => $rtpPromaxGame->desc_step_two,
                'description_step_three' => $rtpPromaxGame->desc_step_three,
                'description_step_four' => $rtpPromaxGame->desc_step_four,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'sort_options' => [
                'rtp_high' => 'RTP High to Low',
                'rtp_low' => 'RTP Low to High',
                'rating_high' => 'Rating High to Low',
                'rating_low' => 'Rating Low to High',
                'newest' => 'Newest',
                'oldest' => 'Oldest',
                'most_popular' => 'Most Popular',
                'least_popular' => 'Least Popular',
                'most_rated' => 'Most Rated',
                'least_rated' => 'Least Rated',
            ],
            'total' => $rtpGames->total(),
            'current_page' => $rtpGames->currentPage(),
            'last_page' => $rtpGames->lastPage(),
            'per_page' => $rtpGames->perPage(),
        ]);
    }

    public function rtpPromaxPlusGames(Request $request)
    {
        $providers = Provider::where('is_rtp_promax_plus', true)->get();
        $query = RTPGame::whereIn('provider_id', $providers->pluck('id'));
        $perPage = $request->input('per_page', 25);
        $providerSlug = $request->input('provider');
        $sortBy = $request->input('sort', 'rtp_high');
        $search = $request->input('search');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($providerSlug) {
            $getProvider = Provider::where('slug', $providerSlug)->first();
            if ($getProvider) {
                $query->where('provider_id', $getProvider->id);
            } else {
                // Provider not found, return empty result
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage,​
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
            case 'rating_high':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('rating', 'asc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'most_popular':
                // For now, sort by RTP and rating as popularity indicators
                $query->orderBy('rtp', 'desc')->orderBy('rating', 'desc');
                break;
            case 'least_popular':
                // For now, sort by RTP and rating ascending as inverse popularity
                $query->orderBy('rtp', 'asc')->orderBy('rating', 'asc');
                break;
            case 'most_rated':
                // For now, sort by rating descending
                $query->orderBy('rating', 'desc');
                break;
            case 'least_rated':
                // For now, sort by rating ascending
                $query->orderBy('rating', 'asc');
                break;
            default:
                $query->orderBy('rtp', 'desc')->orderBy('rating', 'desc');
                break;
        }

        $rtpGames = $query->with('provider')->paginate($perPage);

        $data = $rtpGames->map(function ($rtpPromaxGame) {
            return [
                'id' => $rtpPromaxGame->id,
                'name' => $rtpPromaxGame->name,
                'provider' => $rtpPromaxGame->provider->name,
                'rtp' => $rtpPromaxGame->rtp,
                'pola' => $rtpPromaxGame->pola,
                'rating' => $rtpPromaxGame->rating,
                'image' => $rtpPromaxGame->img_src,
                'stake_bet' => $rtpPromaxGame->stake_bet,
                'step_one' => $rtpPromaxGame->step_one,
                'step_two' => $rtpPromaxGame->step_two,
                'step_three' => $rtpPromaxGame->step_three,
                'step_four' => $rtpPromaxGame->step_four,
                'type_step_one' => $rtpPromaxGame->type_step_one,
                'type_step_two' => $rtpPromaxGame->type_step_two,
                'type_step_three' => $rtpPromaxGame->type_step_three,
                'type_step_four' => $rtpPromaxGame->type_step_four,
                'description_step_one' => $rtpPromaxGame->desc_step_one,
                'description_step_two' => $rtpPromaxGame->desc_step_two,
                'description_step_three' => $rtpPromaxGame->desc_step_three,
                'description_step_four' => $rtpPromaxGame->desc_step_four,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'sort_options' => [
                'rtp_high' => 'RTP High to Low',
                'rtp_low' => 'RTP Low to High',
                'rating_high' => 'Rating High to Low',
                'rating_low' => 'Rating Low to High',
                'newest' => 'Newest',
                'oldest' => 'Oldest',
                'most_popular' => 'Most Popular',
                'least_popular' => 'Least Popular',
                'most_rated' => 'Most Rated',
                'least_rated' => 'Least Rated',
            ],
            'total' => $rtpGames->total(),
            'current_page' => $rtpGames->currentPage(),
            'last_page' => $rtpGames->lastPage(),
            'per_page' => $rtpGames->perPage(),
        ]);
    }

}
