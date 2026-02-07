<?php

namespace Database\Seeders;

use App\Models\ToolCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class ToolCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create system super-admin (protected)
        ToolCategory::create([
            'name' => 'RTP Slot',
            'slug' => 'rtp-slot',
            'description' => 'RTP Slot tools',
        ]);
        ToolCategory::create([
            'name' => 'RTP Casino',
            'slug' => 'rtp-casino',
            'description' => 'RTP Casino tools',
        ]);
        ToolCategory::create([
            'name' => 'Cheat',
            'slug' => 'cheat',
            'description' => 'Cheat tools',
        ]);
    }
}
