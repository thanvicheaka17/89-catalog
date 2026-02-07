<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\RTPGame;
use App\Models\Tool;
use App\Models\HotAndFresh;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\Notification;
class UpdateRandomData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:update-random {--force : Force update all records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update RTP games, tools, and hot_and_fresh with random RTP/rating values every 30 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting random data update...');

        try {
            $force = $this->option('force');

            // Update RTP Games
            $this->updateRTPGames($force);

            // Update Tools
            $this->updateTools($force);

            // Update Hot and Fresh
            $this->updateHotAndFresh($force);

            $this->info('Random data update completed successfully');
            return 0;

        } catch (\Exception $e) {
            Log::error('Random data update failed: ' . $e->getMessage());
            $this->error('Update failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Update RTP Games with random RTP and rating values
     */
    private function updateRTPGames(bool $force = false): void
    {
        try {
            $query = RTPGame::query();

            // If not forcing, update a random subset of games
            if (!$force) {
                $totalGames = $query->count();
                $gamesToUpdate = max(10, (int) ($totalGames * 0.1)); // Update 10% of games, minimum 10

                $games = $query->inRandomOrder()->limit($gamesToUpdate)->get();
            } else {
                $games = $query->get();
            }

            if ($games->isEmpty()) {
                $this->warn('No RTP games found in database');
                return;
            }

            $updated = 0;
            foreach ($games as $game) {
                // Random RTP between 50-95 (realistic range)
                $newRtp = rand(min: 50, max: 95);
                $newPola = rand(min: 50, max: 95);
                $newRating = rand(min: 1, max: 5);
                $newStakeBet = rand(min: 400, max: 15000);
                $newStepOne = rand(min: 50, max: 95);
                $newStepTwo = rand(min: 50, max: 95);
                $newStepThree = rand(min: 50, max: 95);
                $newStepFour = rand(min: 50, max: 95);

                $game->update([
                    'rtp' => $newRtp,
                    'pola' => $newPola,
                    'rating' => $newRating,
                    'stake_bet' => $newStakeBet,
                    'step_one' => $newStepOne,
                    'step_two' => $newStepTwo,
                    'step_three' => $newStepThree,
                    'step_four' => $newStepFour,
                    'last_rtp_update' => now()
                ]);

                $updated++;
            }

            $this->info("Updated {$updated} RTP games (RTP: 50-95, Pola: 50-95)");
        } catch (\Exception $e) {
            $this->error("Failed to update RTP games: " . $e->getMessage());
            Log::error("Failed to update RTP games: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update Tools with random rating values
     */
    private function updateTools(bool $force = false): void
    {
        try {
            $query = Tool::query();

            // If not forcing, update a random subset of tools
            if (!$force) {
                $totalTools = $query->count();
                $toolsToUpdate = max(5, (int) ($totalTools * 0.2)); // Update 20% of tools, minimum 5

                $tools = $query->inRandomOrder()->limit($toolsToUpdate)->get();
            } else {
                $tools = $query->get();
            }

            if ($tools->isEmpty()) {
                $this->warn('No tools found in database');
                return;
            }

            $updated = 0;
            foreach ($tools as $tool) {
                $randomData = Tool::randomToolData();

                $tool->update([
                    'rating' => $randomData['rating'],
                    'user_count' => $randomData['user_count'],
                    'active_hours' => $randomData['active_hours'],
                    // 'rank' => $randomData['rank'],
                    // 'badge' => $randomData['badge'],
                    // 'tier' => $randomData['tier'],
                    // 'price' => $randomData['price'],
                    'win_rate_increase' => $randomData['win_rate_increase']
                ]);

                $updated++;
            }

            $this->info("Updated {$updated} tools (Rating: 3.0-5.0)");
        } catch (\Exception $e) {
            $this->error("Failed to update tools: " . $e->getMessage());
            Log::error("Failed to update tools: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update Hot and Fresh with random rating values
     */
    private function updateHotAndFresh(bool $force = false): void
    {
        try {
            $query = HotAndFresh::query();

            // If not forcing, update a random subset
            if (!$force) {
                $totalItems = $query->count();
                $itemsToUpdate = max(3, (int) ($totalItems * 0.3)); // Update 30% of items, minimum 3

                $items = $query->inRandomOrder()->limit($itemsToUpdate)->get();
            } else {
                $items = $query->get();
            }

            if ($items->isEmpty()) {
                $this->warn('No hot_and_fresh items found in database');
                return;
            }

            $updated = 0;
            foreach ($items as $item) {
                $randomData = HotAndFresh::randomHotAndFreshData();

                $item->update([
                    'rating' => $randomData['rating'],
                    'user_count' => $randomData['user_count'],
                    'active_hours' => $randomData['active_hours'],
                    // 'rank' => $randomData['rank'],
                    // 'badge' => $randomData['badge'],
                    // 'tier' => $randomData['tier'],
                    // 'price' => $randomData['price'],
                    'win_rate_increase' => $randomData['win_rate_increase']
                ]);

                $updated++;
            }

            $this->info("Updated {$updated} hot_and_fresh items (Rating: 3.0-5.0)");
        } catch (\Exception $e) {
            $this->error("Failed to update hot_and_fresh: " . $e->getMessage());
            Log::error("Failed to update hot_and_fresh: " . $e->getMessage());
            throw $e;
        }
    }
}
