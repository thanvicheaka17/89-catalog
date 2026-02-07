<?php

namespace Database\Seeders;

use App\Models\LevelConfiguration;
use Illuminate\Database\Seeder;

class LevelConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            // Bronze Tier (Level 1-10)
            ['level' => 1, 'threshold' => 0, 'tier' => 'bronze', 'tier_name' => 'Bronze', 'tier_min_level' => 1, 'tier_max_level' => 10, 'description' => 'Beginner'],
            ['level' => 2, 'threshold' => 50000, 'tier' => 'bronze', 'tier_name' => 'Bronze', 'tier_min_level' => 1, 'tier_max_level' => 10, 'description' => 'Beginner'],
            ['level' => 3, 'threshold' => 100000, 'tier' => 'bronze', 'tier_name' => 'Bronze', 'tier_min_level' => 1, 'tier_max_level' => 10, 'description' => 'Beginner'],
            ['level' => 4, 'threshold' => 200000, 'tier' => 'bronze', 'tier_name' => 'Bronze', 'tier_min_level' => 1, 'tier_max_level' => 10, 'description' => 'Beginner'],
            ['level' => 5, 'threshold' => 350000, 'tier' => 'bronze', 'tier_name' => 'Bronze', 'tier_min_level' => 1, 'tier_max_level' => 10, 'description' => 'Beginner'],
            ['level' => 6, 'threshold' => 550000, 'tier' => 'bronze', 'tier_name' => 'Bronze', 'tier_min_level' => 1, 'tier_max_level' => 10, 'description' => 'Beginner'],
            ['level' => 7, 'threshold' => 800000, 'tier' => 'bronze', 'tier_name' => 'Bronze', 'tier_min_level' => 1, 'tier_max_level' => 10, 'description' => 'Beginner'],
            ['level' => 8, 'threshold' => 1100000, 'tier' => 'bronze', 'tier_name' => 'Bronze', 'tier_min_level' => 1, 'tier_max_level' => 10, 'description' => 'Beginner'],
            ['level' => 9, 'threshold' => 1500000, 'tier' => 'bronze', 'tier_name' => 'Bronze', 'tier_min_level' => 1, 'tier_max_level' => 10, 'description' => 'Beginner'],
            ['level' => 10, 'threshold' => 2000000, 'tier' => 'bronze', 'tier_name' => 'Bronze', 'tier_min_level' => 1, 'tier_max_level' => 10, 'description' => 'Beginner'],

            // Silver Tier (Level 11-20)
            ['level' => 11, 'threshold' => 2700000, 'tier' => 'silver', 'tier_name' => 'Silver', 'tier_min_level' => 11, 'tier_max_level' => 20, 'description' => 'Intermediate'],
            ['level' => 12, 'threshold' => 3500000, 'tier' => 'silver', 'tier_name' => 'Silver', 'tier_min_level' => 11, 'tier_max_level' => 20, 'description' => 'Intermediate'],
            ['level' => 13, 'threshold' => 4500000, 'tier' => 'silver', 'tier_name' => 'Silver', 'tier_min_level' => 11, 'tier_max_level' => 20, 'description' => 'Intermediate'],
            ['level' => 14, 'threshold' => 6000000, 'tier' => 'silver', 'tier_name' => 'Silver', 'tier_min_level' => 11, 'tier_max_level' => 20, 'description' => 'Intermediate'],
            ['level' => 15, 'threshold' => 8000000, 'tier' => 'silver', 'tier_name' => 'Silver', 'tier_min_level' => 11, 'tier_max_level' => 20, 'description' => 'Intermediate'],
            ['level' => 16, 'threshold' => 10500000, 'tier' => 'silver', 'tier_name' => 'Silver', 'tier_min_level' => 11, 'tier_max_level' => 20, 'description' => 'Intermediate'],
            ['level' => 17, 'threshold' => 13500000, 'tier' => 'silver', 'tier_name' => 'Silver', 'tier_min_level' => 11, 'tier_max_level' => 20, 'description' => 'Intermediate'],
            ['level' => 18, 'threshold' => 17000000, 'tier' => 'silver', 'tier_name' => 'Silver', 'tier_min_level' => 11, 'tier_max_level' => 20, 'description' => 'Intermediate'],
            ['level' => 19, 'threshold' => 21000000, 'tier' => 'silver', 'tier_name' => 'Silver', 'tier_min_level' => 11, 'tier_max_level' => 20, 'description' => 'Intermediate'],
            ['level' => 20, 'threshold' => 25000000, 'tier' => 'silver', 'tier_name' => 'Silver', 'tier_min_level' => 11, 'tier_max_level' => 20, 'description' => 'Intermediate'],

            // Gold Tier (Level 21-30)
            ['level' => 21, 'threshold' => 30000000, 'tier' => 'gold', 'tier_name' => 'Gold', 'tier_min_level' => 21, 'tier_max_level' => 30, 'description' => 'Advanced'],
            ['level' => 22, 'threshold' => 36000000, 'tier' => 'gold', 'tier_name' => 'Gold', 'tier_min_level' => 21, 'tier_max_level' => 30, 'description' => 'Advanced'],
            ['level' => 23, 'threshold' => 43000000, 'tier' => 'gold', 'tier_name' => 'Gold', 'tier_min_level' => 21, 'tier_max_level' => 30, 'description' => 'Advanced'],
            ['level' => 24, 'threshold' => 51000000, 'tier' => 'gold', 'tier_name' => 'Gold', 'tier_min_level' => 21, 'tier_max_level' => 30, 'description' => 'Advanced'],
            ['level' => 25, 'threshold' => 60000000, 'tier' => 'gold', 'tier_name' => 'Gold', 'tier_min_level' => 21, 'tier_max_level' => 30, 'description' => 'Advanced'],
            ['level' => 26, 'threshold' => 70000000, 'tier' => 'gold', 'tier_name' => 'Gold', 'tier_min_level' => 21, 'tier_max_level' => 30, 'description' => 'Advanced'],
            ['level' => 27, 'threshold' => 82000000, 'tier' => 'gold', 'tier_name' => 'Gold', 'tier_min_level' => 21, 'tier_max_level' => 30, 'description' => 'Advanced'],
            ['level' => 28, 'threshold' => 96000000, 'tier' => 'gold', 'tier_name' => 'Gold', 'tier_min_level' => 21, 'tier_max_level' => 30, 'description' => 'Advanced'],
            ['level' => 29, 'threshold' => 112000000, 'tier' => 'gold', 'tier_name' => 'Gold', 'tier_min_level' => 21, 'tier_max_level' => 30, 'description' => 'Advanced'],
            ['level' => 30, 'threshold' => 130000000, 'tier' => 'gold', 'tier_name' => 'Gold', 'tier_min_level' => 21, 'tier_max_level' => 30, 'description' => 'Advanced'],

            // Platinum Tier (Level 31-40)
            ['level' => 31, 'threshold' => 150000000, 'tier' => 'platinum', 'tier_name' => 'Platinum', 'tier_min_level' => 31, 'tier_max_level' => 40, 'description' => 'Elite'],
            ['level' => 32, 'threshold' => 175000000, 'tier' => 'platinum', 'tier_name' => 'Platinum', 'tier_min_level' => 31, 'tier_max_level' => 40, 'description' => 'Elite'],
            ['level' => 33, 'threshold' => 205000000, 'tier' => 'platinum', 'tier_name' => 'Platinum', 'tier_min_level' => 31, 'tier_max_level' => 40, 'description' => 'Elite'],
            ['level' => 34, 'threshold' => 240000000, 'tier' => 'platinum', 'tier_name' => 'Platinum', 'tier_min_level' => 31, 'tier_max_level' => 40, 'description' => 'Elite'],
            ['level' => 35, 'threshold' => 280000000, 'tier' => 'platinum', 'tier_name' => 'Platinum', 'tier_min_level' => 31, 'tier_max_level' => 40, 'description' => 'Elite'],
            ['level' => 36, 'threshold' => 325000000, 'tier' => 'platinum', 'tier_name' => 'Platinum', 'tier_min_level' => 31, 'tier_max_level' => 40, 'description' => 'Elite'],
            ['level' => 37, 'threshold' => 375000000, 'tier' => 'platinum', 'tier_name' => 'Platinum', 'tier_min_level' => 31, 'tier_max_level' => 40, 'description' => 'Elite'],
            ['level' => 38, 'threshold' => 430000000, 'tier' => 'platinum', 'tier_name' => 'Platinum', 'tier_min_level' => 31, 'tier_max_level' => 40, 'description' => 'Elite'],
            ['level' => 39, 'threshold' => 490000000, 'tier' => 'platinum', 'tier_name' => 'Platinum', 'tier_min_level' => 31, 'tier_max_level' => 40, 'description' => 'Elite'],
            ['level' => 40, 'threshold' => 555000000, 'tier' => 'platinum', 'tier_name' => 'Platinum', 'tier_min_level' => 31, 'tier_max_level' => 40, 'description' => 'Elite'],

            // Diamond Tier (Level 41-50)
            ['level' => 41, 'threshold' => 630000000, 'tier' => 'diamond', 'tier_name' => 'Diamond', 'tier_min_level' => 41, 'tier_max_level' => 50, 'description' => 'Sultan / VIP'],
            ['level' => 42, 'threshold' => 715000000, 'tier' => 'diamond', 'tier_name' => 'Diamond', 'tier_min_level' => 41, 'tier_max_level' => 50, 'description' => 'Sultan / VIP'],
            ['level' => 43, 'threshold' => 810000000, 'tier' => 'diamond', 'tier_name' => 'Diamond', 'tier_min_level' => 41, 'tier_max_level' => 50, 'description' => 'Sultan / VIP'],
            ['level' => 44, 'threshold' => 920000000, 'tier' => 'diamond', 'tier_name' => 'Diamond', 'tier_min_level' => 41, 'tier_max_level' => 50, 'description' => 'Sultan / VIP'],
            ['level' => 45, 'threshold' => 1050000000, 'tier' => 'diamond', 'tier_name' => 'Diamond', 'tier_min_level' => 41, 'tier_max_level' => 50, 'description' => 'Sultan / VIP'],
            ['level' => 46, 'threshold' => 1200000000, 'tier' => 'diamond', 'tier_name' => 'Diamond', 'tier_min_level' => 41, 'tier_max_level' => 50, 'description' => 'Sultan / VIP'],
            ['level' => 47, 'threshold' => 1380000000, 'tier' => 'diamond', 'tier_name' => 'Diamond', 'tier_min_level' => 41, 'tier_max_level' => 50, 'description' => 'Sultan / VIP'],
            ['level' => 48, 'threshold' => 1600000000, 'tier' => 'diamond', 'tier_name' => 'Diamond', 'tier_min_level' => 41, 'tier_max_level' => 50, 'description' => 'Sultan / VIP'],
            ['level' => 49, 'threshold' => 1900000000, 'tier' => 'diamond', 'tier_name' => 'Diamond', 'tier_min_level' => 41, 'tier_max_level' => 50, 'description' => 'Sultan / VIP'],
            ['level' => 50, 'threshold' => 2300000000, 'tier' => 'diamond', 'tier_name' => 'Diamond', 'tier_min_level' => 41, 'tier_max_level' => 50, 'description' => 'Sultan / VIP'],
        ];

        foreach ($levels as $levelData) {
            // Create tier_info JSON structure
            $tierDisplay = $this->getTierDisplay($levelData['tier_name'], $levelData['tier_min_level'], $levelData['tier_max_level']);

            $tierInfo = [
                'tier' => $levelData['tier'],
                'tier_name' => $levelData['tier_name'],
                'tier_display' => $tierDisplay,
                'level_range' => [
                    'min' => $levelData['tier_min_level'],
                    'max' => $levelData['tier_max_level'],
                ],
                'description' => $levelData['description'],
                'characteristics' => $this->getTierCharacteristics($levelData['tier_name']),
            ];

            LevelConfiguration::updateOrCreate(
                ['level' => $levelData['level']],
                [
                    'threshold' => $levelData['threshold'],
                    'tier' => $levelData['tier'],
                    'tier_name' => $levelData['tier_name'],
                    'tier_info' => $tierInfo,
                    'tier_min_level' => $levelData['tier_min_level'],
                    'tier_max_level' => $levelData['tier_max_level'],
                    'description' => $levelData['description'],
                    'is_active' => true,
                    'sort_order' => $levelData['level'],
                ]
            );
        }

        $this->command->info('Level configurations seeded successfully!');
    }

    /**
     * Get tier display string
     */
    private function getTierDisplay(string $tierName, int $minLevel, int $maxLevel): string
    {
        $tierEmojis = [
            'Bronze' => '🟤',
            'Silver' => '⚪',
            'Gold' => '🟡',
            'Platinum' => '🔵',
            'Diamond' => '💎',
        ];

        $emoji = $tierEmojis[$tierName] ?? '🟤';
        return "{$emoji} {$tierName} Tier (Level {$minLevel} – {$maxLevel})";
    }

    /**
     * Get tier characteristics
     */
    private function getTierCharacteristics(string $tierName): array
    {
        $characteristics = [
            'Bronze' => [
                'Fast progression',
                'Designed for onboarding and early engagement',
            ],
            'Silver' => [
                'Moderate progression speed',
                'Separates active users from casual users',
            ],
            'Gold' => [
                'Loyal and high-value users',
                'Suitable for unlocking premium features',
            ],
            'Platinum' => [
                'High-value members',
                'Focus on retention and exclusivity',
            ],
            'Diamond' => [
                'Whale / Sultan-tier members',
                'Maximum exclusivity',
                'Ideal for VIP events, priority support, and personalized rewards',
            ],
        ];

        return $characteristics[$tierName] ?? [];
    }
}
