<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HotAndFresh;

class HotAndFreshSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hot & Fresh Entry 1 - Premium RTP Tool
        HotAndFresh::create([
            'name' => 'RTP MBAH GACOR',
            'slug' => 'rtp-mabar-gacor',
            'image_path' => 'images/hot-and-fresh/8527f0afabe7d42c8cde43c66c5bca65f36397df.png',
            'description' => 'Advanced strategy assistant for online casinos 
with proven winning techniques',
            'rating' => 4.8,
            'user_count' => 1250,
            'active_hours' => 8760,
            'rank' => 1,
            'badge' => 'premium',
            'tier' => 'platinum',
            'price' => 150000,
            'win_rate_increase' => 15,
        ]);

        // Hot & Fresh Entry 2 - Casino RTP Tool
        HotAndFresh::create([
            'name' => 'RTP RESMI PP +99',
            'slug' => 'rtp-resmi-pp-99',
            'image_path' => 'images/hot-and-fresh/d23dc938c655f4b475421e2fa1cd7b4f9a9f8243.png',
            'description' => 'Fine-tine your aim with this essential cheat for
competitive advantage',
            'rating' => 4.7,
            'user_count' => 1100,
            'active_hours' => 7200,
            'rank' => 2,
            'badge' => 'best use',
            'tier' => 'platinum',
            'price' => 175000,
            'win_rate_increase' => 18,
        ]);

        // Hot & Fresh Entry 3 - Zona Promax Hub
        HotAndFresh::create([
            'name' => 'ZONA PROMAX HUB',
            'slug' => 'zona-promax-hub',
            'image_path' => 'images/hot-and-fresh/030d4b2e378c6e206250976350270a17d5d4c986.png',
            'description' => 'High-volatility RTP tool for furturistis slots with advances prediction algorithms',
            'rating' => 4.5,
            'user_count' => 890,
            'active_hours' => 5760,
            'rank' => 3,
            'badge' => 'popular',
            'tier' => 'platinum',
            'price' => 125000,
            'win_rate_increase' => 16,
        ]);

        // Hot & Fresh Entry 4 - RTP Mbah Gacor
        HotAndFresh::create([
            'name' => 'RTP PROMAX',
            'slug' => 'rtp-promax',
            'image_path' => 'images/hot-and-fresh/01f7d448a26aee9569892a15e35d0368c91d1aad.png',
            'description' => 'FInd the sweet spots for big payout with enhanced accuracy',
            'rating' => 4.6,
            'user_count' => 920,
            'active_hours' => 5040,
            'rank' => 4,
            'badge' => 'new',
            'tier' => 'gold',
            'price' => 65000,
            'win_rate_increase' => 17,
        ]);

        // Hot & Fresh Entry 5 - Cheat Maxwin Pro
        HotAndFresh::create([
            'name' => 'SLOT',
            'slug' => 'slot',
            'image_path' => 'images/hot-and-fresh/80201766dcb55708f243fce980d6a78d7dc8946d.png',
            'description' => 'Increase your chances with intelligent spin algorithms and pattern recognition',
            'rating' => 4.9,
            'user_count' => 650,
            'active_hours' => 2880,
            'rank' => 5,
            'badge' => 'best',
            'tier' => 'platinum',
            'price' => 250000,
            'win_rate_increase' => 22,
        ]);

        // Hot & Fresh Entry 6 - RTP Resmi PP99
        HotAndFresh::create([
            'name' => 'CASINO',
            'slug' => 'casino',
            'image_path' => 'images/hot-and-fresh/3bed4c15f9ed78f6bd00a74bf6559a39aa51bbe2.png',
            'description' => 'harness the power of dragons to maximize your
winnings ',
            'rating' => 4.8,
            'user_count' => 1150,
            'active_hours' => 7920,
            'rank' => 6,
            'badge' => 'premium',
            'tier' => 'platinum',
            'price' => 200000,
            'win_rate_increase' => 19,
        ]);
    }
}
