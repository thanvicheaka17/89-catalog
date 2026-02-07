<?php

namespace Database\Seeders;

use App\Models\Tool;
use App\Models\ToolRating;
use App\Models\User;
use App\Models\LevelConfiguration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ToolRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some test users for ratings
        $users = [];
        $userData = [
            ['name' => 'John Gamer', 'email' => 'john@example.com'],
            ['name' => 'Sarah Player', 'email' => 'sarah@example.com'],
            ['name' => 'Mike Strategy', 'email' => 'mike@example.com'],
            ['name' => 'Emma Casino', 'email' => 'emma@example.com'],
            ['name' => 'David Slots', 'email' => 'david@example.com'],
            ['name' => 'Lisa Poker', 'email' => 'lisa@example.com'],
            ['name' => 'Robert Tables', 'email' => 'robert@example.com'],
            ['name' => 'Anna Roulette', 'email' => 'anna@example.com'],
        ];

        // Get available level configurations
        $levelConfigurations = LevelConfiguration::active()->ordered()->get();
        
        if ($levelConfigurations->isEmpty()) {
            $this->command->warn('No level configurations found. Users will be created without levels.');
        }

        foreach ($userData as $data) {
            // Get random level configuration
            $randomLevelConfig = $levelConfigurations->isNotEmpty() 
                ? $levelConfigurations->random() 
                : null;

            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => User::ROLE_USER,
                'status' => true,
            ];

            // Assign random level if level configurations exist
            if ($randomLevelConfig) {
                // Set total accumulated funds to match the level threshold (or slightly above for variety)
                $fundsVariation = rand(0, 500000); // Add 0-500k variation
                $userData['total_accumulated_funds'] = $randomLevelConfig->threshold + $fundsVariation;
                $userData['current_level'] = $randomLevelConfig->level;
                $userData['account_balance'] = $userData['total_accumulated_funds'];
                $userData['tier'] = $randomLevelConfig->tier_name;
            }

            $user = User::create($userData);
            
            // Update level to ensure it's correct (in case funds variation changed level)
            if ($randomLevelConfig) {
                $user->updateLevel();
                $user->save();
            }
            
            $users[] = $user;
        }

        // Get admin user
        $admin = User::where('email', 'admin@cgg.holdings')->first();
        $users[] = $admin;

        // Get available tools
        $tools = Tool::all();

        // Sample reviews
        $reviews = [
            'This tool has significantly improved my gaming strategy. Highly recommended!',
            'Great RTP calculator. The interface is intuitive and the results are accurate.',
            'Very helpful for analyzing casino games. Worth every penny.',
            'Excellent tool with detailed analysis. My win rate has improved noticeably.',
            'Solid RTP calculator. Does exactly what it promises.',
            'Good tool but could use some additional features for advanced users.',
            'Reliable and accurate. Helps me make better betting decisions.',
            'Fantastic tool! The premium features are worth the investment.',
            'Clear and precise calculations. Essential for serious gamers.',
            'Well-designed interface and accurate results. Five stars!',
            'This has become an essential part of my gaming toolkit.',
            'Outstanding performance and user-friendly design.',
            'The best RTP calculator I\'ve used. Highly accurate results.',
            'Great investment for serious casino players.',
            'Simple to use but powerful. Exactly what I needed.',
        ];

        // Create ratings for each tool
        foreach ($tools as $tool) {
            // Create 5-12 ratings per tool from different users
            $numRatings = rand(1, max: 5);
            $selectedUsers = collect($users)->random($numRatings);

            foreach ($selectedUsers as $user) {
                // Skip if user already rated this tool
                if (ToolRating::where('user_id', $user->id)->where('tool_id', $tool->id)->exists()) {
                    continue;
                }

                // Weight ratings toward higher scores (4-5 stars)
                $ratingWeights = [1 => 5, 2 => 10, 3 => 20, 4 => 35, 5 => 30];
                $rating = $this->weightedRandom($ratingWeights);

                ToolRating::create([
                    'user_id' => $user->id,
                    'tool_id' => $tool->id,
                    'rating' => $rating,
                    'review' => rand(0, 1) ? collect($reviews)->random() : null,
                ]);
            }
        }

        $this->command->info('Tool ratings seeded successfully!');
    }

    /**
     * Get weighted random number based on weights array
     */
    private function weightedRandom(array $weights)
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);

        foreach ($weights as $value => $weight) {
            $random -= $weight;
            if ($random <= 0) {
                return $value;
            }
        }

        return array_key_last($weights);
    }
}
