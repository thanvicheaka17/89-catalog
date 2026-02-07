<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RTPGameDataService;
use App\Models\Provider;

class TestGameDataFetch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:game-data {provider_slug?} {--all : Test all providers}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test fetching game data from CDN for providers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rtpGameDataService = new RTPGameDataService();

        if ($this->option('all')) {
            $this->testAllProviders($rtpGameDataService);
        } elseif ($this->argument('provider_slug')) {
            $this->testSingleProvider($rtpGameDataService, $this->argument('provider_slug'));
        } else {
            $this->testDefaultProviders($rtpGameDataService);
        }
    }

    private function testAllProviders(RTPGameDataService $service)
    {
        // Test some common provider slugs
        $commonSlugs = Provider::all()->pluck('slug')->toArray();

        $this->info("Testing game data fetch for common providers...");
        $this->newLine();

        $results = [];

        foreach ($commonSlugs as $slug) {
            $result = $this->testProvider($service, null, $slug);
            $results[] = $result;

            if ($result['success']) {
                $this->info("✓ {$slug}: {$result['game_count']} games");
            } else {
                $this->error("✗ {$slug}: {$result['error']}");
            }
        }

        $this->newLine();
        $successful = count(array_filter($results, fn($r) => $r['success']));
        $this->info("Results: {$successful}/" . count($results) . " providers have game data");
    }

    private function testSingleProvider(RTPGameDataService $service, string $slug)
    {
        $this->info("Testing game data fetch for provider slug: {$slug}");
        $this->newLine();

        $result = $this->testProvider($service, null, $slug);

        if ($result['success']) {
            $this->info("✓ Success! Found {$result['game_count']} games");
            $this->newLine();

            // Show first few games
            $games = array_slice($result['games'], 0, 3);
            $this->info("Sample games:");
            foreach ($games as $game) {
                $this->line("  - {$game['game_name']}");
            }
        } else {
            $this->error("✗ Failed: {$result['error']}");
        }
    }

    private function testDefaultProviders(RTPGameDataService $service)
    {
        $defaultProviders = Provider::all()->pluck('slug')->toArray();

        $this->info("Testing game data fetch for default providers...");
        $this->newLine();

        foreach ($defaultProviders as $slug) {
            $result = $this->testProvider($service, null, $slug);

            if ($result['success']) {
                $this->info("✓ {$slug}: {$result['game_count']} games");
            } else {
                $this->error("✗ {$slug}: {$result['error']}");
            }
        }
    }

    private function testProvider(RTPGameDataService $service, ?Provider $provider = null, ?string $slug = null): array
    {
        $testSlug = $slug ?? $provider?->slug;

        if (!$testSlug) {
            return [
                'success' => false,
                'error' => 'No slug provided',
                'game_count' => 0,
                'games' => []
            ];
        }

        try {
            $games = $service->fetchGamesFromCDN($testSlug);

            if ($games !== null) {
                return [
                    'success' => true,
                    'game_count' => count($games),
                    'games' => $games,
                    'error' => null
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'No data returned from CDN',
                    'game_count' => 0,
                    'games' => []
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'game_count' => 0,
                'games' => []
            ];
        }
    }
}
