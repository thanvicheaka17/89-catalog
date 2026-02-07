<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Booster Akun Gacor',
                'subtitle' => 'Maximize your gaming potential with our premium account booster',
                'image_path' => 'MKG-BoosterAkunGacor.jpg',
                'link_url' => 'https://www.google.com/',
                'priority' => 1,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'title' => 'Cheat Bot Auto Spin',
                'subtitle' => 'Automated spinning tool for maximum efficiency',
                'image_path' => 'MKG-CheatBotAutoSpin.jpg',
                'link_url' => 'https://www.google.com/',
                'priority' => 2,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'title' => 'Cheat Jackpot Plus',
                'subtitle' => 'Enhanced jackpot winning strategies and tools',
                'image_path' => 'MKG-CheatjackpotPlus.jpg',
                'link_url' => 'https://www.google.com/',
                'priority' => 3,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'title' => 'Cheat Maxwin Pro',
                'subtitle' => 'Professional tools for maximum winning potential',
                'image_path' => 'MKG-CheatMaxwinPro.jpg',
                'link_url' => 'https://www.google.com/',
                'priority' => 4,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'title' => 'Cheat Robo Pragma Pro+',
                'subtitle' => 'Advanced robotic automation for Pragmatic games',
                'image_path' => 'MKG-CheatRoboPragmaPro+.jpg',
                'link_url' => 'https://www.google.com/',
                'priority' => 5,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'title' => 'Premium Gaming Tools',
                'subtitle' => 'Discover our complete suite of gaming enhancement tools',
                'image_path' => 'MKG-ma.jpg',
                'link_url' => 'https://www.google.com/',
                'priority' => 6,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'title' => 'Mahjong Scatter Hunter',
                'subtitle' => 'Specialized tools for Mahjong scatter hunting',
                'image_path' => 'MKG-MahjongScatterHunter.jpg',
                'link_url' => 'https://www.google.com/',
                'priority' => 7,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'title' => 'Mbah Gacor Pro',
                'subtitle' => 'Professional Gacor strategies and automation',
                'image_path' => 'MKG-MbahGacorPro.jpg',
                'link_url' => 'https://www.google.com/',
                'priority' => 8,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'title' => 'RTP Casino Promax',
                'subtitle' => 'Maximum RTP optimization for casino games',
                'image_path' => 'MKG-RTPCasinoPromax.jpg',
                'link_url' => 'https://www.google.com/',
                'priority' => 9,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'title' => 'RTP Mbah Gacor',
                'subtitle' => 'Gacor RTP strategies for better returns',
                'image_path' => 'MKG-RTPMbahGacor.jpg',
                'link_url' => 'https://www.google.com/',
                'priority' => 10,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'title' => 'RTP Promax Plus',
                'subtitle' => 'Enhanced RTP optimization tools',
                'image_path' => 'MKG-RTPPromaxPlus.jpg',
                'link_url' => 'https://www.google.com/',
                'priority' => 11,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'title' => 'RTP Resmi PP99',
                'subtitle' => 'Official RTP tools for PP99 platform',
                'image_path' => 'MKG-RTPResmiPP99.jpg',
                'link_url' => 'https://www.google.com/',
                'priority' => 12,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'title' => 'RTP Slot Promax',
                'subtitle' => 'Maximum RTP optimization for slot games',
                'image_path' => 'MKG-RTPSlotPromax.jpg',
                'link_url' => 'https://www.google.com/',
                'priority' => 13,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'title' => 'Zona Promax Hub',
                'subtitle' => 'Your ultimate hub for Promax gaming tools',
                'image_path' => 'MKG-ZonaPromaxHub.jpg',
                'link_url' => 'https://www.google.com/',
                'priority' => 14,
                'is_active' => true,
                'visibility' => 'public',
            ],
        ];

        foreach ($banners as $banner) {
            Banner::updateOrCreate(
                [
                    'image_path' => 'images/banners/' . $banner['image_path'],
                    'title' => $banner['title'],
                    'subtitle' => $banner['subtitle'],
                    'link_url' => $banner['link_url'],
                    'priority' => $banner['priority'],
                    'is_active' => $banner['is_active'],
                    'visibility' => $banner['visibility'] ?? 'public',
                ]
            );
        }
    }
}
