<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tool;
use App\Models\UserTool;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users and tools
        $users = User::all();
        $tools = Tool::all();

        // If no users or tools exist, skip seeding
        if ($users->isEmpty() || $tools->isEmpty()) {
            $this->command->info('No users or tools found. Skipping UserTool seeding.');
            return;
        }

        // Get admin user
        $adminUser = User::where('email', 'admin@cgg.holdings')->first();

        if ($adminUser) {
            // Give admin user some premium tools
            $premiumTools = $tools->where('badge', 'premium')->take(3);

            foreach ($premiumTools as $tool) {
                UserTool::create([
                    'user_id' => $adminUser->id,
                    'tool_id' => $tool->id,
                    'status' => 'active',
                    'purchased_at' => Carbon::now()->subDays(rand(1, 30)),
                    'expires_at' => Carbon::now()->addDays(rand(30, 365)),
                    'usage_count' => rand(0, 50),
                    'max_usage' => rand(100, 1000),
                    'price_paid' => $tool->price,
                    'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                    'metadata' => [
                        'source' => 'seeder',
                        'auto_assigned' => true,
                    ],
                ]);
            }

            // Give admin some expired tools for testing
            $expiredTool = $tools->where('badge', 'best use')->first();
            if ($expiredTool) {
                UserTool::create([
                    'user_id' => $adminUser->id,
                    'tool_id' => $expiredTool->id,
                    'status' => 'expired',
                    'purchased_at' => Carbon::now()->subDays(60),
                    'expires_at' => Carbon::now()->subDays(5),
                    'usage_count' => 100,
                    'max_usage' => 100,
                    'price_paid' => $expiredTool->price,
                    'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                    'metadata' => [
                        'source' => 'seeder',
                        'expired_demo' => true,
                    ],
                ]);
            }
        }

        // Create random user tool assignments for variety
        $this->createRandomUserTools($users, $tools, 25);

        $this->command->info('User tools seeded successfully!');
    }

    /**
     * Create random user tool assignments
     */
    private function createRandomUserTools($users, $tools, $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $user = $users->random();
            $tool = $tools->random();

            // Skip if user already has this tool
            $existing = UserTool::where('user_id', $user->id)
                ->where('tool_id', $tool->id)
                ->exists();

            if ($existing) {
                continue;
            }

            // Random status
            $statuses = ['active', 'inactive', 'expired'];
            $status = $statuses[array_rand($statuses)];

            // Set expires_at based on status
            $expiresAt = null;
            if ($status === 'active') {
                $expiresAt = Carbon::now()->addDays(rand(7, 365));
            } elseif ($status === 'expired') {
                $expiresAt = Carbon::now()->subDays(rand(1, 30));
            }

            UserTool::create([
                'user_id' => $user->id,
                'tool_id' => $tool->id,
                'status' => $status,
                'purchased_at' => Carbon::now()->subDays(rand(1, 60)),
                'expires_at' => $expiresAt,
                'usage_count' => rand(0, 200),
                'max_usage' => rand(50, 500),
                'price_paid' => $tool->price > 0 ? $tool->price : rand(1000, 50000),
                'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                'metadata' => [
                    'source' => 'seeder',
                    'random_assignment' => true,
                ],
            ]);
        }
    }
}
