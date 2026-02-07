<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DemoGame;
use App\Models\UserGamePlay;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserGamePlaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users and demo games
        $users = User::all();
        $demoGames = DemoGame::all();

        // If no users or games exist, skip seeding
        if ($users->isEmpty() || $demoGames->isEmpty()) {
            $this->command->info('No users or demo games found. Skipping UserGamePlay seeding.');
            return;
        }

        $playData = [
            // Admin user's play history (recent activity)
            [
                'user' => $users->first(), // Admin user
                'games' => [
                    ['game_slug' => 'gates-of-olympus-1000', 'duration' => 45, 'days_ago' => 0], // Today
                    ['game_slug' => 'big-bass-bonanza-1000', 'duration' => 30, 'days_ago' => 1], // Yesterday
                    ['game_slug' => 'sweet-bonanza-1000', 'duration' => 60, 'days_ago' => 2], // 2 days ago
                    ['game_slug' => 'gates-of-olympus-super-scatter', 'duration' => 25, 'days_ago' => 3], // 3 days ago
                    ['game_slug' => 'mahjong-wins-3-black-scatter', 'duration' => 40, 'days_ago' => 5], // 5 days ago
                    ['game_slug' => 'starlight-princess-100', 'duration' => 35, 'days_ago' => 7], // 7 days ago
                    ['game_slug' => 'sugar-rush-1000', 'duration' => 20, 'days_ago' => 10], // 10 days ago
                ]
            ],
            // If there are more users, create additional play history
            [
                'user' => $users->skip(1)->first() ?? $users->first(), // Second user or fallback to admin
                'games' => [
                    ['game_slug' => 'wisdom-athena-1000', 'duration' => 55, 'days_ago' => 1],
                    ['game_slug' => 'gates-of-gatot-kaca', 'duration' => 42, 'days_ago' => 3],
                    ['game_slug' => 'big-bass-bonanza-1000', 'duration' => 28, 'days_ago' => 5],
                    ['game_slug' => 'starlight-princess-100', 'duration' => 33, 'days_ago' => 8],
                ]
            ],
            // Third user's play history if available
            [
                'user' => $users->skip(2)->first() ?? $users->first(),
                'games' => [
                    ['game_slug' => 'sweet-bonanza-1000', 'duration' => 75, 'days_ago' => 2],
                    ['game_slug' => 'sugar-rush-1000', 'duration' => 18, 'days_ago' => 4],
                    ['game_slug' => 'mahjong-wins-3-black-scatter', 'duration' => 48, 'days_ago' => 6],
                ]
            ]
        ];

        foreach ($playData as $userPlayData) {
            $user = $userPlayData['user'];

            foreach ($userPlayData['games'] as $gameData) {
                $game = $demoGames->where('slug', $gameData['game_slug'])->first();

                if ($game) {
                    UserGamePlay::create([
                        'user_id' => $user->id,
                        'game_id' => $game->id,
                        'duration_minutes' => $gameData['duration'],
                        'played_at' => Carbon::now()->subDays($gameData['days_ago'])->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                    ]);
                }
            }
        }

        // Create some additional random play sessions for variety
        $this->createRandomPlaySessions($users, $demoGames, 15);

        $this->command->info('User game plays seeded successfully!');
    }

    /**
     * Create random play sessions for additional variety
     */
    private function createRandomPlaySessions($users, $demoGames, $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $user = $users->random();
            $game = $demoGames->random();

            // Random duration between 5-120 minutes
            $duration = rand(5, 120);

            // Random time within the last 30 days
            $daysAgo = rand(0, 30);
            $hoursAgo = rand(0, 23);
            $minutesAgo = rand(0, 59);

            UserGamePlay::create([
                'user_id' => $user->id,
                'game_id' => $game->id,
                'duration_minutes' => $duration,
                'played_at' => Carbon::now()->subDays($daysAgo)->subHours($hoursAgo)->subMinutes($minutesAgo),
            ]);
        }
    }
}
