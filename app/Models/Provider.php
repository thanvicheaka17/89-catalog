<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\RTPGameDataService;

class Provider extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'providers';

    protected $fillable = [
        'name',
        'description',
        'slug',
        'logo',
        'min_rtp',
        'max_rtp',
        'min_pola',
        'max_pola',
        'rtp_promax_name',
        'rtp_promax_plus_name',
        'is_rtp_promax',
        'is_rtp_promax_plus',
        'rtp_promax_logo',
        'rtp_promax_plus_logo',
    ];

    protected $casts = [
        'min_rtp' => 'integer',
        'max_rtp' => 'integer',
        'min_pola' => 'integer',
        'max_pola' => 'integer',
        'is_rtp_promax' => 'boolean',
        'is_rtp_promax_plus' => 'boolean',
    ];


    /**
     * Get the games for this provider
     */
    public function games(): HasMany
    {
        return $this->hasMany(RTPGame::class, 'provider_id');
    }


    public function hasLogo(): bool
    {
        return !empty($this->logo);
    }

    public function getLogoUrl(): string
    {
        return $this->logo ? url($this->logo) : null;
    }

    public function getRTPPromaxLogoUrl(): string
    {
        return $this->rtp_promax_logo ? url($this->rtp_promax_logo) : null;
    }

    public function getRTPPromaxPlusLogoUrl(): string
    {
        return $this->rtp_promax_plus_logo ? url($this->rtp_promax_plus_logo) : null;
    }

    /**
     * Get RTP configuration settings for this provider
     *
     * @return array
     */
    public function getRTPConfiguration(): array
    {
        return [
            'min_rtp' => $this->min_rtp ?? 50,
            'max_rtp' => $this->max_rtp ?? 95,
            'min_pola' => $this->min_pola ?? 50,
            'max_pola' => $this->max_pola ?? 95,
        ];
    }

    /**
     * Set RTP configuration settings for this provider
     *
     * @param array $config
     * @return bool
     */
    public function setRTPConfiguration(array $config): bool
    {
        try {
            $this->update([
                'min_rtp' => $config['min_rtp'] ?? 50,
                'max_rtp' => $config['max_rtp'] ?? 95,
                'min_pola' => $config['min_pola'] ?? 50,
                'max_pola' => $config['max_pola'] ?? 95,
            ]);
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to update RTP configuration for provider ' . $this->name . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get game count from database
     *
     * @return int
     */
    public function getGameCount(): int
    {
        return $this->games()->count();
    }


    /**
     * Sync enhanced game data with RTP values and effectiveness to database
     *
     * @return bool
     */
    public function syncGameData(): bool
    {
        if (empty($this->slug)) {
            return false;
        }

        $rtpGameDataService = new RTPGameDataService();

        // First, try to fetch raw data from CDN
        $rawData = $rtpGameDataService->fetchGamesFromCDN($this->slug);
        if (!$rawData) {
            return false;
        }

        // Process the data with provider's RTP range
        $enhancedGameData = $rtpGameDataService->getEnhancedGameData(
            $this->slug,
            $this->min_rtp ?? 1,
            $this->max_rtp ?? 99
        );
        if (!$enhancedGameData) {
            return false;
        }

        // Sync RTP games to database with provider's RTP configuration values
        $syncResult = $rtpGameDataService->syncGamesToDatabase($this, $enhancedGameData);

        if (!$syncResult) {
            return false;
        }

        return true;
    }

    /**
     * Update RTP values for all games (call every 15 minutes)
     *
     * @return bool
     */
    public function updateRTPValues(): bool
    {
        $rtpGameDataService = new RTPGameDataService();
        return $rtpGameDataService->updateGamesRTPInDatabase($this->id);
    }


    /**
     * Get current RTP statistics from stored games
     *
     * @return array
     */
    public function getRTPStatistics(): array
    {
        $games = $this->games()->get();

        if ($games->isEmpty()) {
            return [
                'total_games' => 0,
                'avg_rtp' => 0,
                'min_rtp' => 0,
                'max_rtp' => 0,
                'rtp_distribution' => [
                    'low' => 0,
                    'medium' => 0,
                    'high' => 0
                ]
            ];
        }

        $rtpValues = $games->pluck('rtp')->filter()->values();
        $totalGames = $rtpValues->count();

        if ($totalGames === 0) {
            return [
                'total_games' => $games->count(),
                'avg_rtp' => 0,
                'min_rtp' => 0,
                'max_rtp' => 0,
                'rtp_distribution' => [
                    'low' => 0,
                    'medium' => 0,
                    'high' => 0
                ]
            ];
        }

        $avgRtp = $rtpValues->avg();
        $minRtp = $rtpValues->min();
        $maxRtp = $rtpValues->max();

        // Calculate RTP distribution
        $low = $rtpValues->filter(function ($rtp) {
            return $rtp >= 1 && $rtp <= 49;
        })->count();

        $medium = $rtpValues->filter(function ($rtp) {
            return $rtp >= 50 && $rtp <= 79;
        })->count();

        $high = $rtpValues->filter(function ($rtp) {
            return $rtp >= 80 && $rtp <= 99;
        })->count();

        return [
            'total_games' => $games->count(),
            'avg_rtp' => round($avgRtp, 1),
            'min_rtp' => $minRtp,
            'max_rtp' => $maxRtp,
            'rtp_distribution' => [
                'low' => $low,
                'medium' => $medium,
                'high' => $high
            ]
        ];
    }

    /**
     * Get stored game data from database
     *
     * @return array|null
     */
    public function getStoredGameData(): ?array
    {
        $games = $this->games()->get();

        if ($games->isEmpty()) {
            return null;
        }

        return [
            'games' => $games->map(function ($game) {
                return [
                    'name' => $game->name,
                    'rtp' => $game->rtp,
                    'img_src' => $game->img_src,
                    'effectiveness' => $game->effectiveness,
                    'last_rtp_update' => $game->last_rtp_update?->toISOString(),
                ];
            })->toArray(),
            'game_count' => $games->count(),
            'game_names' => $games->pluck('name')->toArray(),
        ];
    }

    /**
     * Check if RTP values need updating (every 15 minutes)
     *
     * @return bool
     */
    public function rtpNeedsUpdate(): bool
    {
        // Check if any games need RTP update
        return $this->games()->where(function ($query) {
            $query->whereNull('last_rtp_update')
                  ->orWhere('last_rtp_update', '<', now()->subMinutes(15));
        })->exists();
    }

    /**
     * Check if game data needs syncing
     *
     * @return bool
     */
    public function gameDataNeedsSync(): bool
    {
        // Check if no games exist for this provider
        return !$this->games()->exists();
    }
}
