<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\Notification;

class AddNewTestimonials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testimonials:add-new';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new testimonials every hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to add new testimonials...');

        try {
            $roles = ['Professional Player', 'Tournament Player', 'Gaming Enthusiast', 'Beginner Gamer', 'Team Player', 'Tech-Savvy Gamer'];

            // Generate dynamic testimonials using the model
            $testimonialMessages = Testimonial::generateTestimonialMessages(100);

            // Get a random user for the testimonial
            $user = User::inRandomOrder()->first();
            
            if (!$user) {
                $this->warn('No users found in database. Cannot create testimonial.');
                return 1;
            }

            // Insert 1 testimonial with random data
            $role = $roles[array_rand($roles)];
            $name = fake()->name();

            $testimonial = Testimonial::create([
                'user_id' => $user->id,
                'user_name' => $name,
                'user_role' => $role,
                'rating' => rand(1, 5),
                'avatar' => 'images/avatars/default-avatar.webp',
                'message' => $testimonialMessages[array_rand($testimonialMessages)],
                'is_featured' => true,
                'is_active' => true,
            ]);

            // Create and broadcast notification for the new testimonial
            Notification::create([
                'type' => 'testimonial',
                'data' => [
                    'type' => 'testimonial',
                    'testimonial_id' => $testimonial->id,
                    'title' => '🎉 New Testimonial!',
                    'message' => "⭐ " . $testimonial->user_name . " gave the platform a " . $testimonial->rating . "-star rating!",
                    'description' => $testimonial->message,
                    'created_at' => $testimonial->created_at,
                ],
                'user_id' => null, // Global notification for all users
                'is_read' => false,
            ]);

            $this->info("Successfully inserted 1 new testimonial from {$name} ({$role})");
            return 0;

        } catch (\Exception $e) {
            Log::error('Failed to add new testimonials: ' . $e->getMessage());
            $this->error('Failed to add testimonials: ' . $e->getMessage());
            return 1;
        }
    }
}
