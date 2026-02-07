<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::where('email', 'admin@cgg.holdings')->first();

        Promotion::create([
            'title' => 'Welcome Bonus',
            'message' => 'Get 100% bonus on your first deposit! Play now and start winning.',
            'button_text' => 'Claim Bonus',
            'button_url' => 'https://www.google.com/',
            'image_path' => null,
            'background_color' => '#AA2FC5',
            'background_color_2' => '#A70985',
            'background_gradient_type' => 'gradient',
            'background_gradient_direction' => 'to right',
            'text_color' => '#ffffff',
            'button_color' => '#F59E0B',
            'button_color_2' => '#FBBF24',
            'button_gradient_type' => 'gradient',
            'button_gradient_direction' => 'to right',
            'button_text_color' => '#ffffff',
            'position' => 'top',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'is_active' => true,
            'priority' => 1,
            'created_by' => $admin->id,
        ]);

        Promotion::create([
            'title' => 'Weekend Special',
            'message' => 'Double your winnings this weekend! Special bonuses and free spins await.',
            'button_text' => 'Play Now',
            'button_url' => 'https://www.google.com/',
            'image_path' => null,
            'background_color' => '#0f766e',
            'background_color_2' => '#14b8a6',
            'background_gradient_type' => 'gradient',
            'background_gradient_direction' => 'to bottom right',
            'text_color' => '#ffffff',
            'button_color' => '#f59e0b',
            'button_color_2' => '#fbbf24',
            'button_gradient_type' => 'gradient',
            'button_gradient_direction' => 'to right',
            'button_text_color' => '#ffffff',
            'position' => 'top',
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'is_active' => true,
            'priority' => 2,
            'created_by' => $admin->id,
        ]);

        Promotion::create([
            'title' => 'VIP Exclusive Offer',
            'message' => 'Join our VIP program and unlock exclusive rewards, cashback, and personalized bonuses.',
            'button_text' => 'Join VIP',
            'button_url' => 'https://www.google.com/',
            'image_path' => null,
            'background_color' => '#7c3aed',
            'background_color_2' => '#a855f7',
            'background_gradient_type' => 'gradient',
            'background_gradient_direction' => 'to bottom',
            'text_color' => '#ffffff',
            'button_color' => '#ec4899',
            'button_color_2' => null,
            'button_gradient_type' => 'solid',
            'button_gradient_direction' => null,
            'button_text_color' => '#ffffff',
            'position' => 'top',
            'start_date' => now(),
            'end_date' => now()->addDays(60),
            'is_active' => true,
            'priority' => 3,
            'created_by' => $admin->id,
        ]);

        Promotion::create([
            'title' => 'Free Spins Friday',
            'message' => 'Get 50 free spins every Friday! No deposit required. Claim yours now!',
            'button_text' => 'Get Free Spins',
            'button_url' => 'https://www.google.com/',
            'image_path' => null,
            'background_color' => '#dc2626',
            'background_color_2' => '#ef4444',
            'background_gradient_type' => 'gradient',
            'background_gradient_direction' => 'to left',
            'text_color' => '#ffffff',
            'button_color' => '#000000',
            'button_color_2' => null,
            'button_gradient_type' => 'solid',
            'button_gradient_direction' => null,
            'button_text_color' => '#ffffff',
            'position' => 'top',
            'start_date' => now(),
            'end_date' => now()->addDays(14),
            'is_active' => true,
            'priority' => 4,
            'created_by' => $admin->id,
        ]);

        Promotion::create([
            'title' => 'New Game Launch',
            'message' => 'Check out our latest game release! Play now and get exclusive launch bonuses.',
            'button_text' => 'Explore Games',
            'button_url' => 'https://www.google.com/',
            'image_path' => null,
            'background_color' => '#1e40af',
            'background_color_2' => '#3b82f6',
            'background_gradient_type' => 'gradient',
            'background_gradient_direction' => 'to top right',
            'text_color' => '#ffffff',
            'button_color' => '#10b981',
            'button_color_2' => '#34d399',
            'button_gradient_type' => 'gradient',
            'button_gradient_direction' => 'to right',
            'button_text_color' => '#ffffff',
            'position' => 'top',
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(45),
            'is_active' => true,
            'priority' => 5,
            'created_by' => $admin->id,
        ]);

        $this->command->info('Promotion seeded successfully!');
    }
}
