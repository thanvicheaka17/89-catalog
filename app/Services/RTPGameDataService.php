<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Provider;
use Exception;
use App\Models\RTPGame;

class RTPGameDataService
{
    /**
     * Base URL for the game data CDN
     */
    private const CDN_BASE_URL = 'https://cdn.it-cg.group/rtp-games/game-images';

    /**
     * Fetch game data for a provider from CDN
     *
     * @param string $providerSlug
     * @return array|null
     */
    public function fetchGamesFromCDN(string $providerSlug): ?array
    {
        try {
            $url = self::CDN_BASE_URL . "/{$providerSlug}/data.json";

            $response = Http::timeout(30)->get($url);

            if ($response->successful()) {
                $data = $response->json();

                // Validate the response structure
                if ($this->isValidGameData($data)) {
                    return $data;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } catch (Exception $e) {
            Log::error("Exception while fetching game data for provider {$providerSlug}: ");
            return null;
        }
    }

    /**
     * Fetch game data for a provider model
     *
     * @param Provider $provider
     * @return array|null
     */
    public function fetchGamesForProvider(Provider $provider): ?array
    {
        if (empty($provider->slug)) {
            return null;
        }

        return $this->fetchGamesFromCDN($provider->slug);
    }

    /**
     * Get game count for a provider
     *
     * @param string $providerSlug
     * @return int
     */
    public function getGameCount(string $providerSlug): int
    {
        $games = $this->fetchGamesFromCDN($providerSlug);
        return $games ? count($games) : 0;
    }

    /**
     * Validate the structure of game data from CDN
     *
     * @param mixed $data
     * @return bool
     */
    private function isValidGameData($data): bool
    {
        if (!is_array($data)) {
            return false;
        }

        // Check if at least one game has the expected structure
        foreach ($data as $gameKey => $gameData) {
            if (
                is_array($gameData) &&
                isset($gameData['game_name']) &&
                isset($gameData['img_src'])
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if provider has available game data on CDN
     *
     * @param string $providerSlug
     * @return bool
     */
    public function hasGameData(string $providerSlug): bool
    {
        return $this->getGameCount($providerSlug) > 0;
    }

    /**
     * Process and enhance game data with RTP values and clean identifiers
     *
     * @param array $rawGameData
     * @param int $minRTP
     * @param int $maxRTP
     * @return array
     */
    public function processGameData(array $rawGameData, int $minRTP = 50, int $maxRTP = 95): array
    {
        $processedGames = [];

        foreach ($rawGameData as $gameKey => $gameData) {
            if (!isset($gameData['game_name'])) {
                continue;
            }

            // Clean game name by removing identifiers like pg-1, pg-2, ap-1, fch-1, etc.
            $cleanName = $this->cleanGameName($gameData['game_name']);

            // Generate random RTP within the specified range
            $rtp = rand($minRTP, $maxRTP);

            $processedGames[] = [
                'name' => $cleanName,
                'rtp' => $rtp,
                'img_src' => $gameData['img_src'] ?? null,
                'last_rtp_update' => now()->toISOString(),
            ];
        }

        return $processedGames;
    }

    /**
     * Clean game name by removing provider identifiers
     *
     * @param string $gameName
     * @return string
     */
    private function cleanGameName(string $gameName): string
    {
        // Remove common provider prefixes like pg-, ap-, fch-, etc.
        $patterns = [
            '/^(pg|ap|fch|pp|jili|spade|fastspin|bgaming|netent|pragmatic|playtech|microgaming|redtiger|booming|habanero)-\d+\s*/i',
            '/^(pg|ap|fch|pp|jili|spade|fastspin|bgaming|netent|pragmatic|playtech|microgaming|redtiger|booming|habanero)\d+\s*/i',
        ];

        foreach ($patterns as $pattern) {
            $gameName = preg_replace($pattern, '', $gameName);
        }

        return trim($gameName);
    }

    /**
     * Get enhanced game data with RTP and effectiveness
     *
     * @param string $providerSlug
     * @param int $minRTP
     * @param int $maxRTP
     * @return array|null
     */
    public function getEnhancedGameData(string $providerSlug, int $minRTP = 50, int $maxRTP = 95): ?array
    {
        $rawData = $this->fetchGamesFromCDN($providerSlug);

        if (!$rawData) {
            return null;
        }

        $processedGames = $this->processGameData($rawData, $minRTP, $maxRTP);

        return [
            'provider_slug' => $providerSlug,
            'game_count' => count($processedGames),
            'games' => $processedGames,
            'last_sync' => now()->toISOString(),
            'rtp_update_interval' => 15, // minutes
        ];
    }

    /**
     * Sync games to database for a provider
     *
     * @param Provider $provider
     * @param array $gamesData
     * @return bool
     */
    public function syncGamesToDatabase(Provider $provider, array $gamesData): bool
    {
        try {
            $games = $gamesData['games'] ?? [];

            $deletedCount = RTPGame::where('provider_id', $provider->id)->delete();

            // Get provider's RTP and POLA configuration ranges
            $providerRTPConfig = [
                'min_rtp' => $provider->min_rtp ?? 50,
                'max_rtp' => $provider->max_rtp ?? 95,
                'min_pola' => $provider->min_pola ?? 50,
                'max_pola' => $provider->max_pola ?? 95,
            ];

            // Insert new games with random RTP and POLA values within provider's ranges
            $gameRecords = [];
            foreach ($games as $game) {
                $randomData = RTPGame::randomRTPGameData($provider->id);

                $gameRecords[] = [
                    'id' => (string) Str::uuid(),
                    'provider_id' => $provider->id,
                    'name' => $game['name'],
                    'rtp' => $randomData['rtp'],
                    'pola' => $randomData['pola'],
                    'rating' => $randomData['rating'],
                    'step_one' => $randomData['step_one'],
                    'type_step_one' => $randomData['type_step_one'],
                    'desc_step_one' => $randomData['desc_step_one'],
                    'step_two' => $randomData['step_two'],
                    'type_step_two' => $randomData['type_step_two'],
                    'desc_step_two' => $randomData['desc_step_two'],
                    'step_three' => $randomData['step_three'],
                    'type_step_three' => $randomData['type_step_three'],
                    'desc_step_three' => $randomData['desc_step_three'],
                    'step_four' => $randomData['step_four'],
                    'type_step_four' => $randomData['type_step_four'],
                    'desc_step_four' => $randomData['desc_step_four'],
                    'stake_bet' => $randomData['stake_bet'],
                    'img_src' => $game['img_src'],
                    'last_rtp_update' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert in chunks to avoid memory issues
            $insertedCount = 0;
            foreach (array_chunk($gameRecords, 100) as $chunk) {
                try {
                    RTPGame::insert($chunk);
                    $insertedCount += count($chunk);
                } catch (Exception $chunkException) {
                    foreach ($chunk as $singleRecord) {
                        try {
                            if (!isset($singleRecord['id'])) {
                                $singleRecord['id'] = (string) Str::uuid();
                            }
                            RTPGame::insert($singleRecord);
                            $insertedCount++;
                        } catch (Exception $recordException) {
                            Log::error("Failed to insert record for game '{$singleRecord['name']}' in provider {$provider->name}: " );
                        }
                    }
                    break;
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error("Failed to sync games to database for provider {$provider->name}: ");
            return false;
        }
    }

    /**
     * Update RTP values for games in database
     *
     * @param string $providerId
     * @return bool
     */
    public function updateGamesRTPInDatabase(string $providerId): bool
    {
        try {
            $games = RTPGame::where('provider_id', $providerId)->get();

            foreach ($games as $game) {
                if ($game->rtpNeedsUpdate()) {
                    $game->updateRTP();
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error("Failed to update RTP values for provider {$providerId}: ");
            return false;
        }
    }
}
