<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;
use App\Models\ToolCategory;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure avatar gallery directory exists with proper permissions
        $galleryPath = public_path('images/avatars/gallery');
        if (!file_exists($galleryPath)) {
            mkdir($galleryPath, 0777, true);
            chmod($galleryPath, 0777);
        }

        // Get category slugs dynamically from database
        $categoryOrder = ToolCategory::orderBy('created_at', 'asc')
            ->pluck('slug')
            ->toArray();

        // If no categories exist, use default empty array
        if (empty($categoryOrder)) {
            $categoryOrder = [];
        }

        $settings = [
            [
                'key' => 'site_name',
                'group' => 'global',
                'value' => 'CLICKENGINE',
            ],
            [
                'key' => 'frontend_url',
                'group' => 'global',
                'value' => 'http://127.0.0.1:8000/',
            ],
            [
                'key' => 'available_avatars',
                'group' => 'global',
                'value' => [
                    'images/avatars/gallery/avatar-1.svg',
                    'images/avatars/gallery/avatar-2.svg',
                    'images/avatars/gallery/avatar-3.svg',
                    'images/avatars/gallery/avatar-4.svg',
                    'images/avatars/gallery/avatar-5.svg',
                    'images/avatars/gallery/avatar-6.svg',
                    'images/avatars/gallery/avatar-7.svg',
                    'images/avatars/gallery/avatar-8.svg',
                    'images/avatars/gallery/avatar-9.svg',
                    'images/avatars/gallery/avatar-10.svg',
                ], // Array of available avatar paths
            ],
            [
                'key' => 'contact_email',
                'group' => 'contact',
                'value' => 'support@clickengine.com',
            ],
            [
                'key' => 'tool_filtering',
                'group' => 'tools',
                'value' => [
                    'tool_filter_sorting_order' => [
                        'most_relevant',
                        'most_popular',
                        'highest_rated',
                        'price_low_to_high',
                        'price_high_to_low'
                    ],
                    'tool_filter_category_order' => $categoryOrder,
                    'tool_filter_tier_order' => [
                        'silver',
                        'gold',
                        'platinum'
                    ]
                ],
            ]
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'group' => $setting['group'],
                    'value' => $setting['value'],
                ]
            );
        }

        $this->command->info('Site settings seeded successfully!');
        $this->command->info('Total settings created: ' . count($settings));
        
        if (!empty($categoryOrder)) {
            $this->command->info('Tool filter category order: ' . implode(', ', $categoryOrder));
        } else {
            $this->command->warn('No tool categories found. Category order will be empty.');
        }
    }
}

