<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\LoginSmsNotification;
use App\Services\AwsSmsService;
use Illuminate\Console\Command;

class TestSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:test {phone?} {--user=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending SMS login notification';

    protected $smsService;

    public function __construct(AwsSmsService $smsService)
    {
        parent::__construct();
        $this->smsService = $smsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->argument('phone');
        $userId = $this->option('user');

        // Get user
        $user = null;
        if ($userId) {
            try {
                $user = User::find($userId);
                if (!$user) {
                    $this->error("User with ID {$userId} not found.");
                    return 1;
                }
            } catch (\Exception $e) {
                $this->warn("Database connection issue: " . $e->getMessage());
                $this->info("Creating a mock user for testing...");
                $user = $this->createMockUser();
            }
        } else {
            try {
                $user = User::first();
                if (!$user) {
                    $this->warn("No users found in database.");
                    $this->info("Creating a mock user for testing...");
                    $user = $this->createMockUser();
                }
            } catch (\Exception $e) {
                $this->warn("Database connection issue: " . $e->getMessage());
                $this->info("Creating a mock user for testing...");
                $user = $this->createMockUser();
            }
        }

        // Use provided phone or user's phone
        $recipientPhone = $phone ?: $user->getFullPhoneNumberAttribute();

        if (!$recipientPhone) {
            $this->error('No phone number available for testing.');
            $this->info('Please provide a phone number: php artisan sms:test +1234567890');
            return 1;
        }

        $this->info('📱 Testing SMS Login Notification');
        $this->info('👤 User: ' . $user->name . ' (' . $user->email . ')');
        $this->info('📞 Sending to: ' . $recipientPhone);
        $this->newLine();

        // Show AWS SNS configuration status
        $this->comment('📋 AWS SNS Configuration Status:');
        $isConfigured = $this->smsService->isConfigured();

        $tableData = [
            ['AWS SDK Installed', class_exists('Aws\Sns\SnsClient') ? '✅ Yes' : '❌ No'],
            ['AWS_ACCESS_KEY_ID', env('AWS_ACCESS_KEY_ID') ? '✅ Set' : '❌ Not set'],
            ['AWS_SECRET_ACCESS_KEY', env('AWS_SECRET_ACCESS_KEY') ? '✅ Set' : '❌ Not set'],
            ['Service Status', $isConfigured ? '✅ Ready' : '❌ Not configured'],
        ];

        $this->table(['Setting', 'Status'], $tableData);
        $this->newLine();

        if (!$isConfigured) {
            $this->error('❌ AWS SNS is not properly configured!');
            $this->comment('Please set the following environment variables:');
            $this->comment('• AWS_ACCESS_KEY_ID - Your AWS Access Key');
            $this->comment('• AWS_SECRET_ACCESS_KEY - Your AWS Secret Key');
            $this->comment('• AWS_DEFAULT_REGION - Your AWS region (optional, defaults to us-east-1)');
            $this->newLine();
            $this->comment('Get credentials from: https://console.aws.amazon.com/iam/');
            return 1;
        }

        try {
            // Test direct SMS service
            $testMessage = "🔐 Test SMS from " . config('app.name', 'Laravel') . "\n\n" .
                          "👤 User: {$user->name}\n" .
                          "🕐 Time: " . now()->format('M j, Y g:i A') . "\n" .
                          "📍 Location: Test Environment\n" .
                          "📱 Device: Command Line Test\n\n" .
                          "This is a test SMS notification.";

            $this->comment('📱 Sending test SMS...');

            $success = $this->smsService->send($recipientPhone, $testMessage);

            if ($success) {
                $this->info('✅ SMS sent successfully!');
                $this->comment('Check your phone for the SMS message.');
                $this->newLine();

                // Show SMS content preview
                $this->comment('📱 SMS Content Preview:');
                $this->line('─────────────────────────────────────────────────────');
                $this->comment('To: ' . $recipientPhone);
                $this->comment('From: ' . config('services.twilio.from'));
                $this->newLine();
                $this->comment('Message:');
                foreach (explode("\n", $testMessage) as $line) {
                    $this->comment('  ' . $line);
                }
                $this->line('─────────────────────────────────────────────────────');

                // Test via notification system (skip for mock users)
                $isMockUser = property_exists($user, 'is_mock') && $user->is_mock;

                if (!$isMockUser) {
                    $this->newLine();
                    $this->comment('🔔 Testing via Notification System...');
                    $user->notify(new LoginSmsNotification($user, '192.168.1.100', 'Command Line Test'));
                    $this->info('✅ Notification sent via notification system (AWS SNS)!');
                } else {
                    $this->newLine();
                    $this->comment('⚠️  Skipping notification system test (using mock user)');
                    $this->comment('   Notifications require a proper database user model');
                }

            } else {
                $this->error('❌ Failed to send SMS via service');
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('❌ Failed to send SMS: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Create a mock user for testing when database is not available
     */
    private function createMockUser(): object
    {
        return (object) [
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone_number' => '1234567890',
            'country_code' => '+1',
            'getFullPhoneNumberAttribute' => function() {
                return '+11234567890';
            },
            'sms_notifications' => true,
            'is_mock' => true, // Flag to identify mock users
        ];
    }
}
