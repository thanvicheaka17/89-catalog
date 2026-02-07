<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Casino;
use App\Models\CasinoCategory;
use Illuminate\Support\Str;

class CasinoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $availableCategories = ['evo-casino', 'pragmatic-casino', 'sa-casino', 'sbobet-casino'];

        $casinos = [
            [
                'name' => 'Baccarat',
                'description' => 'Classic baccarat game with professional dealers and high-stakes action.',
                'image' => 'Baccarat.webp',
            ],
            [
                'name' => 'BlackJack',
                'description' => 'Traditional blackjack with multiple betting options and live dealer interaction.',
                'image' => 'BlackJack.webp',
            ],
            [
                'name' => 'Dragon Tiger',
                'description' => 'Exciting dragon tiger card game with fast-paced action and high payout potential.',
                'image' => 'DragonTiger.webp',
            ],
            [
                'name' => 'Roulette',
                'description' => 'European and American roulette with various betting options and live streaming.',
                'image' => 'Roulette.webp',
            ],
            [
                'name' => 'Sicbo',
                'description' => 'Traditional sic bo dice game with multiple betting combinations and high RTP.',
                'image' => 'Sicbo.webp',
            ],
        ];

        foreach ($casinos as $casinoData) {
            $randomCategorySlug = $availableCategories[array_rand($availableCategories)];
            $category = CasinoCategory::where('slug', $randomCategorySlug)->first();

            if ($category) {
                Casino::updateOrCreate(
                    ['name' => $casinoData['name']],
                    [
                        'category_id' => $category->id,
                        'slug' => Str::slug($casinoData['name']),
                        'name' => $casinoData['name'],
                        'description' => $casinoData['description'],
                        'image' => 'images/casinos/' . $casinoData['image'],
                        'rtp' => rand(85, 98),
                        'rating' => rand(3, 5),
                        'daily_withdrawal_amount' => rand(100000, 1000000),
                        'daily_withdrawal_players' => rand(1000, 10000),
                        'last_withdrawal_update' => now(),
                        'total_withdrawn' => rand(100000, 1000000),
                    ]
                );
            }
        }
    }
}
