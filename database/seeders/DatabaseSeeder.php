<?php

namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run admin seeder
        $this->call([
            UsersSeeder::class,
            ToolCategorySeeder::class,
            SiteSettingSeeder::class,
            PromotionSeeder::class,
            BannerSeeder::class,
            DemoGameSeeder::class,
            ToolSeeder::class,
            TestimonialSeeder::class,
            LevelConfigurationSeeder::class,
            ToolRatingSeeder::class,
            NewsletterSubscriberSeeder::class,
            EventSeeder::class,
            ProviderSeeder::class,
            GameSeeder::class,
            CasinoCategorySeeder::class,
            CasinoSeeder::class,
            UserGamePlaySeeder::class,
            UserToolSeeder::class,
            HotAndFreshSeeder::class,
            BlogPostSeeder::class,
        ]);
    }
}
