<?php

namespace Database\Seeders;

use App\Models\DemoGame;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoGameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    /**
     * Generate a unique URL for a demo game based on its slug.
     */
    private function generateUniqueUrl(string $slug): string
    {
        $baseUrl = url('demo-games');
        return $baseUrl . '/' . $slug;
    }

    public function run(): void
    {
        $admin = \App\Models\User::where('email', 'admin@cgg.holdings')->first();

        // Slots Games (6 games)
        DemoGame::create([
            'title' => 'Big Bass Bonanza 1000',
            'slug' => 'big-bass-bonanza-1000',
            'description' => 'Experience the underwater adventure with massive bass catches and 1000 ways to win. This demo showcases the popular fishing-themed slot mechanics.',
            'is_demo' => true,
            'url' => $this->generateUniqueUrl('big-bass-bonanza-1000'),
            'image_path' =>  'images/demo-games/MKG-BigBassBonanza1000.png',
            'created_by' => $admin->id,
        ]);

        DemoGame::create([
            'title' => 'Gates of Gatot Kaca',
            'slug' => 'gates-of-gatot-kaca',
            'description' => 'Indonesian mythology-inspired slot with epic battles and ancient powers. Learn about cascading reels and expanding wilds in this demo.',
            'is_demo' => true,
            'url' => $this->generateUniqueUrl('gates-of-gatot-kaca'),
            'image_path' => 'images/demo-games/MKG-GatesOfGatotKaca.png',
            'created_by' => $admin->id,
        ]);

        DemoGame::create([
            'title' => 'Gates of Olympus 1000',
            'slug' => 'gates-of-olympus-1000',
            'description' => 'Greek mythology meets modern slots with Zeus and 1000 ways to win. This demo explores multiplier mechanics and free spins.',
            'is_demo' => true,
            'url' => $this->generateUniqueUrl('gates-of-olympus-1000'),
            'image_path' => 'images/demo-games/MKG-GatesOfOlympus1000.png',
            'created_by' => $admin->id,
        ]);

        DemoGame::create([
            'title' => 'Gates of Olympus Super Scatter',
            'slug' => 'gates-of-olympus-super-scatter',
            'description' => 'Enhanced Olympus experience with super scatter mechanics. Test the power of Zeus with multiple scatter symbols in this demo.',
            'is_demo' => true,
            'url' => $this->generateUniqueUrl('gates-of-olympus-super-scatter'),
            'image_path' => 'images/demo-games/MKG-GatesOfOlympusSuperScatter.png',
            'created_by' => $admin->id,
        ]);

        DemoGame::create([
            'title' => 'Mahjong Wins 3 Black Scatter',
            'slug' => 'mahjong-wins-3-black-scatter',
            'description' => 'Traditional Mahjong tiles meet modern slot mechanics with black scatter features. Experience the unique cascading tile system.',
            'is_demo' => true,
            'url' => $this->generateUniqueUrl('mahjong-wins-3-black-scatter'),
            'image_path' => 'images/demo-games/MKG-MahjongWins3BlackScatter.png',
            'created_by' => $admin->id,
        ]);

        DemoGame::create([
            'title' => 'Starlight Princess 100',
            'slug' => 'starlight-princess-100',
            'description' => 'Magical princess adventure with 100 paylines and starlight bonuses. This demo teaches about payline mechanics and bonus rounds.',
            'is_demo' => true,
            'url' => $this->generateUniqueUrl('starlight-princess-100'),
            'image_path' => 'images/demo-games/MKG-StarlightPrincess100.png',
            'created_by' => $admin->id,
        ]);

        // Crash Games (1 game)
        DemoGame::create([
            'title' => 'Sugar Rush 1000',
            'slug' => 'sugar-rush-1000',
            'description' => 'Sweet-themed crash game with candy multipliers and rush mechanics. Learn crash game timing and sweet bonus features.',
            'is_demo' => true,
            'url' => $this->generateUniqueUrl('sugar-rush-1000'),
            'image_path' => 'images/demo-games/MKG-SugarRush1000.png',
            'created_by' => $admin->id,
        ]);

        // Jackpot Games (2 games)
        DemoGame::create([
            'title' => 'Sweet Bonanza 1000',
            'slug' => 'sweet-bonanza-1000',
            'description' => 'Candy-themed jackpot extravaganza with 1000 ways to win and massive progressive prizes. Experience sweet jackpot mechanics.',
            'is_demo' => true,
            'url' => $this->generateUniqueUrl('sweet-bonanza-1000'),
            'image_path' => 'images/demo-games/MKG-SweetBonanza1000.png',
            'created_by' => $admin->id,
        ]);

        DemoGame::create([
            'title' => 'Wisdom Athena 1000',
            'slug' => 'wisdom-athena-1000',
            'description' => 'Ancient wisdom meets modern jackpots with Athena\'s guidance. This demo explores strategic jackpot selection and wisdom bonuses.',
            'is_demo' => true,
            'url' => $this->generateUniqueUrl('wisdom-athena-1000'),
            'image_path' => 'images/demo-games/MKG-WisdomAthena1000.png',
            'created_by' => $admin->id,
        ]);

        $this->command->info('Demo games seeded successfully!');
    }
}
