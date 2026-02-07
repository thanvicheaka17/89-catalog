<?php

namespace Database\Seeders;

use App\Models\Tool;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rtpSlotCategory = \App\Models\ToolCategory::where('slug', 'rtp-slot')->first();
        $rtpCasinoCategory = \App\Models\ToolCategory::where('slug', 'rtp-casino')->first();
        $cheatCategory = \App\Models\ToolCategory::where('slug', 'cheat')->first();


        // RTP Slot Tools (4 tools)
        Tool::create([
            'name' => 'RTP Slot Promax',
            'slug' => 'rtp-slot-promax',
            'description' => 'Professional RTP analyzer for slot machines with advanced pattern recognition and real-time performance tracking.',
            'image_path' => 'images/tools/MKG-RTPSlotPromax.jpg',
            'rating' => 4.8,
            'user_count' => 1250,
            'active_hours' => 8760,
            'rank' => 1,
            'badge' => ['premium', 'best use', 'new', 'popular', 'best'][rand(0, 4)],
            'tier' => 'platinum',
            'price' => 150000,
            'win_rate_increase' => 15.5,
            'category_id' => $rtpSlotCategory->id,
            'display_order' => 1,
        ]);

        Tool::create([
            'name' => 'RTP Promax Slot',
            'slug' => 'rtp-promax-slot',
            'description' => 'Ultimate RTP optimization tool for slots with machine learning algorithms and real-time adjustments.',
            'image_path' => 'images/tools/MKG-RTPPromaxSlot.jpg',
            'rating' => 4.6,
            'user_count' => 980,
            'active_hours' => 6240,
            'rank' => 2,
            'badge' => ['premium', 'best use', 'new', 'popular', 'best'][rand(0, 4)],
            'tier' => 'gold',
            'price' => 75000,
            'win_rate_increase' => 14.1,
            'category_id' => $rtpSlotCategory->id,
            'display_order' => 2,
        ]);

        Tool::create([
            'name' => 'Mahjong Scatter Hunter',
            'slug' => 'mahjong-scatter-hunter',
            'description' => 'Advanced scatter symbol tracking system specifically designed for Mahjong-themed slot games.',
            'image_path' => 'images/tools/MKG-MahjongScatterHunter.jpg',
            'rating' => 4.4,
            'user_count' => 756,
            'active_hours' => 4320,
            'rank' => 3,
            'badge' => ['premium', 'best use', 'new', 'popular', 'best'][rand(0, 4)],
            'tier' => 'silver',
            'price' => 25000,
            'win_rate_increase' => 12.8,
            'category_id' => $rtpSlotCategory->id,
            'display_order' => 3,
        ]);

        Tool::create([
            'name' => 'Zona Promax Hub',
            'slug' => 'zona-promax-hub',
            'description' => 'Comprehensive gaming zone management system with multi-slot RTP monitoring and optimization hub.',
            'image_path' => 'images/tools/MKG-ZonaPromaxHub.jpg',
            'rating' => 4.5,
            'user_count' => 890,
            'active_hours' => 5760,
            'rank' => 4,
            'badge' => ['premium', 'best use', 'new', 'popular', 'best'][rand(0, 4)],
            'tier' => 'platinum',
            'price' => 125000,
            'win_rate_increase' => 16.3,
            'category_id' => $rtpSlotCategory->id,
            'display_order' => 4,
        ]);

        // RTP Casino Tools (3 tools)
        Tool::create([
            'name' => 'RTP Casino Promax',
            'slug' => 'rtp-casino-promax',
            'description' => 'Comprehensive casino RTP tracking system with multi-game analytics and performance optimization tools.',
            'image_path' => 'images/tools/MKG-RTPCasinoPromax.jpg',
            'rating' => 4.7,
            'user_count' => 1100,
            'active_hours' => 7200,
            'rank' => 5,
            'badge' => ['premium', 'best use', 'new', 'popular', 'best'][rand(0, 4)],
            'tier' => 'platinum',
            'price' => 175000,
            'win_rate_increase' => 18.2,
            'category_id' => $rtpCasinoCategory->id,
            'display_order' => 5,
        ]);

        Tool::create([
            'name' => 'RTP Mbah Gacor',
            'slug' => 'rtp-mbah-gacor',
            'description' => 'Traditional gacor wisdom combined with modern RTP analysis for unbeatable casino performance.',
            'image_path' => 'images/tools/MKG-RTPMbahGacor.jpg',
            'rating' => 4.6,
            'user_count' => 920,
            'active_hours' => 5040,
            'rank' => 6,
            'badge' => ['premium', 'best use', 'new', 'popular', 'best'][rand(0, 4)],
            'tier' => 'gold',
            'price' => 65000,
            'win_rate_increase' => 16.7,
            'category_id' => $rtpCasinoCategory->id,
            'display_order' => 6,
        ]);

        Tool::create([
            'name' => 'RTP Resmi PP99',
            'slug' => 'rtp-resmi-pp99',
            'description' => 'Official PP99 RTP analysis system with verified data sources and professional-grade accuracy.',
            'image_path' => 'images/tools/MKG-RTPResmiPP99.jpg',
            'rating' => 4.8,
            'user_count' => 1150,
            'active_hours' => 7920,
            'rank' => 7,
            'badge' => ['premium', 'best use', 'new', 'popular', 'best'][rand(0, 4)],
            'tier' => 'platinum',
            'price' => 200000,
            'win_rate_increase' => 19.5,
            'category_id' => $rtpCasinoCategory->id,
            'display_order' => 7,
        ]);

        // Cheat Tools (6 tools)
        Tool::create([
            'name' => 'Cheat Maxwin Pro',
            'slug' => 'cheat-maxwin-pro',
            'description' => 'Professional cheat system optimized for maximum wins with advanced algorithms and pattern exploitation.',
            'image_path' => 'images/tools/MKG-CheatMaxwinPro.jpg',
            'rating' => 4.9,
            'user_count' => 650,
            'active_hours' => 2880,
            'rank' => 8,
            'badge' => ['premium', 'best use', 'new', 'popular', 'best'][rand(0, 4)],
            'tier' => 'platinum',
            'price' => 250000,
            'win_rate_increase' => 22.5,
            'category_id' => $cheatCategory->id,
            'display_order' => 8,
        ]);

        Tool::create([
            'name' => 'Cheat Jackpot Plus',
            'slug' => 'cheat-jackpot-plus',
            'description' => 'Enhanced jackpot manipulation system with bonus round exploitation and progressive prize optimization.',
            'image_path' => 'images/tools/MKG-CheatJackpotPlus.jpg',
            'rating' => 4.7,
            'user_count' => 580,
            'active_hours' => 3600,
            'rank' => 9,
            'badge' => ['premium', 'best use', 'new', 'popular', 'best'][rand(0, 4)],
            'tier' => 'platinum',
            'price' => 180000,
            'win_rate_increase' => 20.1,
            'category_id' => $cheatCategory->id,
            'display_order' => 9,
        ]);

        Tool::create([
            'name' => 'Cheat Robo Pragma Pro',
            'slug' => 'cheat-robo-pragma-pro',
            'description' => 'Robotic automation system with pragma algorithms for consistent winning patterns and automated execution.',
            'image_path' => 'images/tools/MKG-CheatRoboPragmaPro.jpg',
            'rating' => 4.8,
            'user_count' => 720,
            'active_hours' => 4320,
            'rank' => 10,
            'badge' => ['premium', 'best use', 'new', 'popular', 'best'][rand(0, 4)],
            'tier' => 'platinum',
            'price' => 300000,
            'win_rate_increase' => 23.8,
            'category_id' => $cheatCategory->id,
            'display_order' => 10,
        ]);

        Tool::create([
            'name' => 'Cheat Bot Auto Spin',
            'slug' => 'cheat-bot-auto-spin',
            'description' => 'Intelligent auto-spin bot with adaptive algorithms that learn and optimize spinning patterns for maximum efficiency.',
            'image_path' => 'images/tools/MKG-CheatBotAutoSpin.jpg',
            'rating' => 4.6,
            'user_count' => 480,
            'active_hours' => 2160,
            'rank' => 11,
            'badge' => ['premium', 'best use', 'new', 'popular', 'best'][rand(0, 4)],
            'tier' => 'gold',
            'price' => 85000,
            'win_rate_increase' => 18.9,
            'category_id' => $cheatCategory->id,
            'display_order' => 11,
        ]);

        Tool::create([
            'name' => 'Mbah Gacor Pro',
            'slug' => 'mbah-gacor-pro',
            'description' => 'Legendary gacor prediction system with mystical algorithms and proven winning strategies.',
            'image_path' => 'images/tools/MKG-MbahGacorPro.jpg',
            'rating' => 4.5,
            'user_count' => 850,
            'active_hours' => 5040,
            'rank' => 12,
            'badge' => ['premium', 'best use', 'new', 'popular', 'best'][rand(0, 4)],
            'tier' => 'platinum',
            'price' => 220000,
            'win_rate_increase' => 21.3,
            'category_id' => $cheatCategory->id,
            'display_order' => 12,
        ]);

        Tool::create([
            'name' => 'Booster Akun Gacor',
            'slug' => 'booster-akun-gacor',
            'description' => 'Account performance booster that enhances gacor capabilities and optimizes account settings for better results.',
            'image_path' => 'images/tools/MKG-BoosterAkunGacor.jpg',
            'rating' => 4.4,
            'user_count' => 620,
            'active_hours' => 2880,
            'rank' => 13,
            'badge' => ['premium', 'best use', 'new', 'popular', 'best'][rand(0, 4)],
            'tier' => 'silver',
            'price' => 35000,
            'win_rate_increase' => 15.7,
            'category_id' => $cheatCategory->id,
            'display_order' => 13,
        ]);

        $this->command->info('Tools seeded successfully!');
    }
}
