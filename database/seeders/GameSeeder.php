<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Provider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all providers for random assignment
        $providers = Provider::all();

        if ($providers->isEmpty()) {
            $this->command->error('No providers found. Please run ProviderSeeder first.');
            return;
        }

        // Games data
        $games = [
            [
                'name' => 'Aloha! Christmas™',
                'slug' => 'aloha-christmas',
                'description' => 'Celebrate the holiday season with a tropical twist in this festive slot game. Enjoy Christmas cheer under the palm trees with exciting bonus features and winning opportunities.',
                'image_path' => 'images/games/net-84.webp',
            ],
            [
                'name' => 'Dark King: Forbidden Riches™',
                'slug' => 'dark-king-forbidden-riches',
                'description' => 'Enter the realm of the Dark King and uncover forbidden treasures. This mysterious slot features powerful bonus rounds and the chance to claim royal riches.',
                'image_path' => 'images/games/net-19.webp',
            ],
            [
                'name' => 'Glorious Kingdom',
                'slug' => 'glorious-kingdom',
                'description' => 'Rule over a majestic kingdom filled with glory and wealth. Spin the reels to discover ancient treasures and build your empire in this epic adventure.',
                'image_path' => 'images/games/ps-43.webp',
            ],
        ];

        foreach ($games as $index => $gameData) {
            Game::create([
                'name' => $gameData['name'],
                'slug' => $gameData['slug'],
                'description' => $gameData['description'],
                'image_path' => $gameData['image_path'],
                'provider_id' => $providers->random()->id,
                'status' => 'active',
            ]);
        }

        $this->command->info('Games seeded successfully! Created ' . count($games) . ' games.');
    }
}
