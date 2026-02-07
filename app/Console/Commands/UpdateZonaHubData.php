<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Events\ZonaHubDataUpdated;
use App\Models\RTPGame;
use App\Models\Provider;

class UpdateZonaHubData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zona:update-data {--force : Force update all data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update ZONA PROMAX HUB live data and broadcast changes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting ZONA PROMAX HUB data update...');

        // Check database connectivity first
        if (!$this->checkDatabaseConnection()) {
            $this->error('Database connection failed. Skipping RTP updates.');
            $this->warn('Please ensure the SQLite PDO driver is installed:');
            $this->warn('  Docker: Add pdo_sqlite to Dockerfile');
            $this->warn('  System: Install php-sqlite3 package');

            // Continue with cache clearing even if DB fails
            $this->clearCachesOnly();
            return 1;
        }

        try {
            $force = $this->option('force');

            // Update RTP values for random games (simulate live data)
            $this->updateRTPValues();

            // Update pattern analysis data
            $this->updatePatternAnalysis();

            // Update provider performance
            $this->updateProviderPerformance();

            // Update hot times schedule
            $this->updateHotTimesSchedule();

            // Broadcast updates
            $this->broadcastUpdates();

            $this->info('ZONA PROMAX HUB data update completed successfully');

        } catch (\PDOException $e) {
            Log::error('ZONA HUB database connection error: ' . $e->getMessage());
            $this->error('Database connection error: ' . $e->getMessage());
            $this->clearCachesOnly();
            return 1;
        } catch (\Exception $e) {
            Log::error('ZONA HUB update failed: ' . $e->getMessage());
            $this->error('Update failed: ' . $e->getMessage());
            $this->clearCachesOnly();
            return 1;
        }

        return 0;
    }

    /**
     * Check if database connection is working
     */
    private function checkDatabaseConnection(): bool
    {
        try {
            \DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Clear caches only (when database is not available)
     */
    private function clearCachesOnly(): void
    {
        try {
            Cache::forget('zona_hub_tool_stats');
            Cache::forget('provider_performance_10');
            Cache::forget('hot_times_schedule_Asia/Jakarta');
            Cache::forget('zona_hub_providers');
            Cache::forget('pattern_analysis_');

            $this->info('Cache cleared successfully (database updates skipped)');
        } catch (\Exception $e) {
            $this->error('Failed to clear caches: ' . $e->getMessage());
        }
    }

    /**
     * Update RTP values for games
     */
    private function updateRTPValues(): void
    {
        try {
            // Get games in a way that doesn't rely on database-specific random functions
            $gamesToUpdate = RTPGame::orderBy('id')->take(50)->get();

            if ($gamesToUpdate->isEmpty()) {
                $this->warn('No RTP games found in database');
                return;
            }

            // Randomly select up to 10 games from the retrieved set
            $gamesToUpdate = $gamesToUpdate->random(min(10, $gamesToUpdate->count()));

            foreach ($gamesToUpdate as $game) {
                // Simulate RTP fluctuation (-2 to +2 points)
                $currentRtp = $game->rtp;
                $change = rand(-2, 2);
                $newRtp = max(50, min(95, $currentRtp + $change));

                $game->update([
                    'rtp' => $newRtp,
                    'last_rtp_update' => now()
                ]);
            }

            $this->info("Updated RTP values for {$gamesToUpdate->count()} games");
        } catch (\Exception $e) {
            $this->error("Failed to update RTP values: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update pattern analysis data
     */
    private function updatePatternAnalysis(): void
    {
        try {
            // Clear pattern analysis cache to force refresh
            Cache::forget('pattern_analysis_');
            $providers = Provider::whereHas('games')->pluck('slug');

            foreach ($providers as $slug) {
                Cache::forget("pattern_analysis_{$slug}");
            }

            $this->info('Pattern analysis cache cleared');
        } catch (\Exception $e) {
            $this->error("Failed to update pattern analysis: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update provider performance data
     */
    private function updateProviderPerformance(): void
    {
        try {
            Cache::forget('provider_performance_10');
            $this->info('Provider performance cache cleared');
        } catch (\Exception $e) {
            $this->error("Failed to update provider performance: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update hot times schedule
     */
    private function updateHotTimesSchedule(): void
    {
        try {
            $timezones = ['Asia/Jakarta', 'Asia/Singapore', 'UTC'];

            foreach ($timezones as $timezone) {
                Cache::forget("hot_times_schedule_{$timezone}");
            }

            $this->info('Hot times schedule cache updated');
        } catch (\Exception $e) {
            $this->error("Failed to update hot times schedule: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Broadcast data updates to connected clients
     */
    private function broadcastUpdates(): void
    {
        try {
            $updateData = [
                'rtp_updated' => true,
                'patterns_updated' => true,
                'providers_updated' => true,
                'schedule_updated' => true,
                'timestamp' => now()->toISOString()
            ];

            broadcast(new ZonaHubDataUpdated($updateData, 'live_update'));

            $this->info('Broadcasted live updates to connected clients');
        } catch (\Exception $e) {
            $this->error("Failed to broadcast updates: " . $e->getMessage());
            // Don't throw here as broadcasting failure shouldn't stop the command
        }
    }
}
