<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::where('email', 'admin@cgg.holdings')->first();

        Testimonial::create([
            'user_id' => $admin->id,
            'user_name' => 'John Smith',
            'user_role' => 'Professional Player',
            'avatar' => 'images/avatars/default-avatar.webp',
            'message' => 'EightNine Catalog has completely changed my gaming experience. The tools are incredibly accurate and have helped me increase my winnings significantly.',
            'rating' => 5,
            'is_featured' => true,
            'is_active' => true,
        ]);

        Testimonial::create([
            'user_id' => $admin->id,
            'user_name' => 'Mike Johnson',
            'user_role' => 'Tournament Player',
            'avatar' => 'images/avatars/default-avatar.webp',
            'message' => 'As a competitive gamer, precision matters. EightNine Catalog delivers exactly what I need - accurate predictions and reliable tools that give me the edge.',
            'rating' => 5,
            'is_featured' => false,
            'is_active' => true,
        ]);

        Testimonial::create([
            'user_id' => $admin->id,
            'user_name' => 'Emma Rodriguez',
            'user_role' => 'Gaming Enthusiast',
            'avatar' => 'images/avatars/default-avatar.webp',
            'message' => 'The variety of tools available is amazing. Whether I\'m playing slots or table games, there\'s always something to help me make better decisions.',
            'rating' => 4,
            'is_featured' => true,
            'is_active' => true,
        ]);

        Testimonial::create([
            'user_id' => $admin->id,
            'user_name' => 'David Kim',
            'user_role' => 'Strategy Expert',
            'avatar' => 'images/avatars/default-avatar.webp',
            'message' => 'What sets EightNine apart is the data-driven approach. The tools are backed by real statistics and proven methodologies.',
            'rating' => 5,
            'is_featured' => false,
            'is_active' => true,
        ]);

        Testimonial::create([
            'user_id' => $admin->id,
            'user_name' => 'Lisa Thompson',
            'user_role' => 'Weekend Player',
            'avatar' => 'images/avatars/default-avatar.webp',
            'message' => 'Even as a casual player, I can see the difference. The tools help me understand the games better and make more informed choices.',
            'rating' => 4,
            'is_featured' => true,
            'is_active' => true,
        ]);

        Testimonial::create([
            'user_id' => $admin->id,
            'user_name' => 'Robert Garcia',
            'user_role' => 'High Roller',
            'avatar' => 'images/avatars/default-avatar.webp',
            'message' => 'For serious players, EightNine Catalog is essential. The premium tools provide insights that you won\'t find anywhere else.',
            'rating' => 5,
            'is_featured' => false,
            'is_active' => true,
        ]);

        Testimonial::create([
            'user_id' => $admin->id,
            'user_name' => 'Anna Williams',
            'user_role' => 'Game Analyst',
            'avatar' => 'images/avatars/default-avatar.webp',
            'message' => 'The analytical tools are top-notch. They help me break down complex games into manageable strategies that actually work.',
            'rating' => 5,
            'is_featured' => true,
            'is_active' => true,
        ]);

        Testimonial::create([
            'user_id' => $admin->id,
            'user_name' => 'James Brown',
            'user_role' => 'Beginner Gamer',
            'avatar' => 'images/avatars/default-avatar.webp',
            'message' => 'Coming from someone who knew nothing about advanced gaming strategies, these tools made everything so much clearer and more enjoyable.',
            'rating' => 4,
            'is_featured' => false,
            'is_active' => true,
        ]);

        Testimonial::create([
            'user_id' => $admin->id,
            'user_name' => 'Maria Davis',
            'user_role' => 'Team Player',
            'avatar' => 'images/avatars/default-avatar.webp',
            'message' => 'Our gaming team uses EightNine tools for every session. They\'ve become an integral part of our strategy and success rate.',
            'rating' => 5,
            'is_featured' => true,
            'is_active' => true,
        ]);

        Testimonial::create([
            'user_id' => $admin->id,
            'user_name' => 'Alex Wilson',
            'user_role' => 'Tech-Savvy Gamer',
            'avatar' => 'images/avatars/default-avatar.webp',
            'message' => 'The real-time updates and responsive design make these tools perfect for mobile gaming. Always reliable and up-to-date.',
            'rating' => 5,
            'is_featured' => false,
            'is_active' => true,
        ]);

        $this->command->info('Testimonials seeded successfully!');
    }
}
