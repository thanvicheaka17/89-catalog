<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::create([
            'title' => 'New Year Casino Tournament',
            'description' => 'Join our exciting New Year tournament and compete with players worldwide. Win amazing prizes and showcase your skills!',
            'is_active' => true,
            'start_at' => now(),
            'end_at' => now()->addDays(30),
        ]);

        $this->command->info('Event seeded successfully!');
    }
}
