<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CasinoCategory;

class CasinoCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'evo-casino',
                'name' => 'RTP EVO CASINO',
                'description' => 'Premium live casino games and entertainment',
                'logo' => 'EVOGAMING.png',
            ],
            [
                'slug' => 'pragmatic-casino',
                'name' => 'RTP PRAGMATIC CASINO',
                'description' => 'High-quality casino gaming experience',
                'logo' => 'PPLIVECASINO.png',
            ],
            [
                'slug' => 'sa-casino',
                'name' => 'RTP SA CASINO',
                'description' => 'Exciting slot games and casino entertainment',
                'logo' => 'SAGAMING.png',
            ],
            [
                'slug' => 'sbobet-casino',
                'name' => 'RTP SEXY BACCARAT',
                'description' => 'Elegant baccarat games with stunning dealers',
                'logo' => 'SBOSEXYBACCARAT.png',
            ],
        ];

        foreach ($categories as $category) {
            $category['logo'] = 'images/casino-categories/' . $category['logo'];
            CasinoCategory::updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'slug' => $category['slug'],
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'logo' => $category['logo'],
                ]
            );
        }
    }
}
