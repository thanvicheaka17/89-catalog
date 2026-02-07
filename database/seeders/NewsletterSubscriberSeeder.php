<?php

namespace Database\Seeders;

use App\Models\NewsletterSubscriber;
use Illuminate\Database\Seeder;

class NewsletterSubscriberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::where('email', 'admin@cgg.holdings')->first();

        NewsletterSubscriber::create([
            'email' => 'john.doe@example.com',
            'user_id' => $admin->id,
            'is_active' => true,
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        $this->command->info('Newsletter subscriber seeded successfully!');
    }
}
