<?php

namespace App\Http\Controllers\Admin\SystemManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\RTPGame;
use App\Services\RTPGameDataService;

class RTPConfigurationController extends Controller
{
    public function index(Request $request)
    {
        // Get all providers with their games and count
        $providers = Provider::with([
            'games' => function ($query) {
                $query->orderBy('name', 'asc');
            }
        ])->withCount('games')->orderBy('name')->get();

        // Check if there's any existing RTP game data
        $hasExistingData = RTPGame::exists();

        return view('system-management.rtp-configurations.index', compact('providers', 'hasExistingData'));
    }

    /**
     * Sync all providers with progress updates
     */
    public function syncAllProvidersProgress(Request $request)
    {
        try {
            $providers = Provider::all();
            $totalCount = $providers->count();
            $successCount = 0;
            $failedCount = 0;
            $errors = [];

            // Send initial response
            if (ob_get_level())
                ob_end_clean();
            header('Content-Type: application/json');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');


            echo json_encode([
                'type' => 'start',
                'data' => [
                    'total_providers' => $totalCount,
                    'message' => 'Starting sync process...'
                ]
            ]) . "\n";
            flush();

            foreach ($providers as $index => $provider) {
                $currentNumber = $index + 1;

                // Send progress update
                echo json_encode([
                    'type' => 'progress',
                    'data' => [
                        'current' => $currentNumber,
                        'total' => $totalCount,
                        'provider_name' => $provider->name,
                        'message' => "Syncing provider {$currentNumber}/{$totalCount}: {$provider->name}"
                    ]
                ]) . "\n";
                flush();

                try {
                    $success = $provider->syncGameData();
                    if ($success) {
                        $successCount++;
                        echo json_encode([
                            'type' => 'success',
                            'data' => [
                                'provider_name' => $provider->name,
                                'message' => "✅ Successfully synced {$provider->name}"
                            ]
                        ]) . "\n";
                    } else {
                        $failedCount++;
                        $errorMsg = "Failed to sync {$provider->name}";
                        $errors[] = $errorMsg;
                        echo json_encode([
                            'type' => 'error',
                            'data' => [
                                'provider_name' => $provider->name,
                                'message' => "❌ {$errorMsg}"
                            ]
                        ]) . "\n";
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $errorMsg = "Error syncing {$provider->name}: " . $e->getMessage();
                    $errors[] = $errorMsg;
                    echo json_encode([
                        'type' => 'error',
                        'data' => [
                            'provider_name' => $provider->name,
                            'message' => "❌ {$errorMsg}"
                        ]
                    ]) . "\n";
                }

                flush();

                // Small delay between providers
                usleep(100000); // 0.1 seconds
            }

            // Send final results
            echo json_encode([
                'type' => 'complete',
                'data' => [
                    'total_providers' => $totalCount,
                    'successful_syncs' => $successCount,
                    'failed_syncs' => $failedCount,
                    'errors' => $errors,
                    'message' => "Sync completed: {$successCount} successful, {$failedCount} failed"
                ]
            ]) . "\n";
            flush();

        } catch (\Exception $e) {
            echo json_encode([
                'type' => 'error',
                'data' => [
                    'message' => 'Critical error: ' . $e->getMessage()
                ]
            ]) . "\n";
            flush();
        }

        exit();
    }

    /**
     * Delete all RTP game data
     */
    public function deleteAllData(Request $request)
    {
        try {
            $totalGames = RTPGame::count();

            if ($totalGames === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No RTP game data found to delete'
                ], 404);
            }

            $deletedCount = RTPGame::query()->delete();

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} RTP game records",
                'data' => [
                    'total_deleted' => $deletedCount
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting RTP data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting RTP data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync game data for a specific provider
     */
    public function syncGameData(Request $request, $providerId)
    {
        try {
            $provider = Provider::findOrFail($providerId);

            // Sync game data using the Provider model method
            $success = $provider->syncGameData();

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch or store game data from CDN'
                ], 400);
            }

            $gameCount = $provider->getGameCount();

            return response()->json([
                'success' => true,
                'message' => "Successfully synced {$gameCount} games for {$provider->name}",
                'data' => [
                    'game_count' => $gameCount,
                    'games' => $provider->games()->take(5)->get()->map(function ($game) {
                        return [
                            'name' => $game->name,
                            'rtp' => $game->rtp,
                            'img_src' => $game->img_src,
                        ];
                    })->toArray()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error syncing game data: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Sync game data for all providers
     */
    public function syncAllProviders(Request $request)
    {
        try {
            $providers = Provider::all();
            $successCount = 0;
            $totalCount = $providers->count();
            $errors = [];

            foreach ($providers as $provider) {
                try {
                    $success = $provider->syncGameData();
                    if ($success) {
                        $successCount++;
                    } else {
                        $errors[] = "Failed to sync {$provider->name}";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error syncing {$provider->name}: " . $e->getMessage();
                }
            }

            $message = "Synced {$successCount} out of {$totalCount} providers successfully.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return response()->json([
                'success' => $successCount > 0,
                'message' => $message,
                'data' => [
                    'total_providers' => $totalCount,
                    'successful_syncs' => $successCount,
                    'errors' => $errors
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error syncing all providers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get global RTP configuration settings (same for all providers)
     */
    public function getGlobalSettings(Request $request)
    {
        try {
            // Get the first provider's config as the global config
            // All providers should have the same values
            $firstProvider = Provider::select('min_rtp', 'max_rtp', 'min_pola', 'max_pola')
                ->first();

            $globalConfig = [
                'min_rtp' => $firstProvider->min_rtp ?? 50,
                'max_rtp' => $firstProvider->max_rtp ?? 95,
                'min_pola' => $firstProvider->min_pola ?? 50,
                'max_pola' => $firstProvider->max_pola ?? 95,
            ];

            // Get all providers but use the same global config for all
            $providers = Provider::select('id', 'name', 'slug')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'global_config' => $globalConfig,
                    'providers' => $providers->map(function ($provider) use ($globalConfig) {
                        return [
                            'id' => $provider->id,
                            'name' => $provider->name,
                            'slug' => $provider->slug,
                            'min_rtp' => $globalConfig['min_rtp'],
                            'max_rtp' => $globalConfig['max_rtp'],
                            'min_pola' => $globalConfig['min_pola'],
                            'max_pola' => $globalConfig['max_pola'],
                        ];
                    })->toArray()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching global RTP settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save global RTP configuration settings (apply to all providers)
     */
    public function saveGlobalSettings(Request $request)
    {
        try {
            $request->validate([
                'min_rtp' => 'required|integer|min:0|max:100',
                'max_rtp' => 'required|integer|min:0|max:100|gte:min_rtp',
                'min_pola' => 'required|integer|min:0|max:100',
                'max_pola' => 'required|integer|min:0|max:100|gte:min_pola',
            ]);

            // Apply the same global configuration to all providers
            $globalConfig = [
                'min_rtp' => $request->min_rtp,
                'max_rtp' => $request->max_rtp,
                'min_pola' => $request->min_pola,
                'max_pola' => $request->max_pola,
            ];

            $providers = Provider::all();
            $successCount = 0;
            $errors = [];

            foreach ($providers as $provider) {
                try {
                    $provider->setRTPConfiguration($globalConfig);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to update {$provider->name}: " . $e->getMessage();
                }
            }

            $message = "Successfully applied global RTP settings to {$successCount} providers.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return response()->json([
                'success' => $successCount > 0,
                'message' => $message,
                'data' => [
                    'updated_providers' => $successCount,
                    'global_config' => $globalConfig,
                    'errors' => $errors
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving global RTP settings: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * RPT route handler
     */
    public function getRTPGames(Request $request)
    {
        $query = RTPGame::with('provider');
        $providers = Provider::select('id', 'name', 'slug')->orderBy('name')->get();

        if ($request->has('provider') && $request->provider) {
            $provider = Provider::where('slug', $request->provider)->first();
            if ($provider) {
                $query->where('provider_id', $provider->id);
            }
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;
        $query->orderBy('rtp', 'desc')->orderBy('rating', 'desc');
        $rtpGames = $query->paginate($perPage);

        return view('system-management.rtp-configurations.rtp-games', compact('rtpGames', 'perPage', 'providers'));
    }
}
