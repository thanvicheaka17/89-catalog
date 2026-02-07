<?php

namespace App\Console\Commands;

use App\Services\AwsSmsService;
use Illuminate\Console\Command;

class SmsMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:monitor {--status : Show AWS SNS configuration} {--test : Send test SMS} {--limits : Show AWS SNS limits and costs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor AWS SNS SMS service status and send test messages';

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
        $this->info('📱 AWS SNS SMS Monitor');
        $this->newLine();

        if ($this->option('limits')) {
            return $this->showLimits();
        }

        if ($this->option('test')) {
            return $this->sendTest();
        }

        if ($this->option('status')) {
            return $this->showStatus();
        }

        $this->showHelp();
        return 0;
    }

    /**
     * Send test SMS via AWS SNS.
     */
    private function sendTest(): int
    {
        $phone = $this->ask('Enter phone number to test (+855XXXXXXXX)');

        if (!$phone) {
            $this->error('Phone number is required');
            return 1;
        }

        if (!$this->smsService->isConfigured()) {
            $this->error('❌ AWS SNS is not configured');
            $this->comment('Set AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY in your .env file');
            return 1;
        }

        $this->info("📤 Sending test SMS to: {$phone} via AWS SNS");

        $message = 'AWS SNS Test - ' . now()->format('H:i:s d/m/Y');
        $success = $this->smsService->send($phone, $message);

        if (!$success) {
            $this->error('❌ Failed to send SMS via AWS SNS');
            $this->comment('Check logs for detailed error information');
            return 1;
        }

        $this->info('✅ SMS sent successfully via AWS SNS!');
        $this->newLine();
        
        // Check sandbox status and warn if needed
        $sandboxStatus = $this->smsService->checkSandboxStatus();
        if ($sandboxStatus['in_sandbox'] === true) {
            $this->warn('⚠️  WARNING: Account is in SANDBOX MODE');
            $this->error('The SMS was accepted by AWS but may NOT be delivered!');
            $this->newLine();
            $this->comment('In sandbox mode, SMS can only be sent to verified phone numbers.');
            $this->comment('If you don\'t receive the message, verify your phone number in AWS SNS Console:');
            $this->comment('https://console.aws.amazon.com/sns/v3/home#/mobile/text-messaging/phone-numbers');
            $this->newLine();
            $this->comment('Or request production access to send to any phone number.');
        } else {
            $this->comment('Check your phone for the message');
        }
        
        $this->newLine();
        $this->comment('Note: AWS SNS free tier = 1,000 messages/month for first 12 months');
        $this->comment('Message ID logged - check AWS SNS Console for delivery status');

        return 0;
    }


    /**
     * Show AWS SNS configuration status.
     */
    private function showStatus(): int
    {
        $this->info('📊 AWS SNS Configuration Status');
        $this->newLine();

        $isConfigured = $this->smsService->isConfigured();

        $tableData = [
            ['AWS SDK Installed', class_exists('Aws\\Sns\\SnsClient') ? '✅ Yes' : '❌ No'],
            ['AWS_ACCESS_KEY_ID', env('AWS_ACCESS_KEY_ID') ? '✅ Set' : '❌ Not set'],
            ['AWS_SECRET_ACCESS_KEY', env('AWS_SECRET_ACCESS_KEY') ? '✅ Set' : '❌ Not set'],
            ['AWS_DEFAULT_REGION', env('AWS_DEFAULT_REGION', 'us-east-1')],
            ['Service Status', $isConfigured ? '✅ Ready' : '❌ Not configured'],
        ];

        $this->table(['Setting', 'Status'], $tableData);

        $this->newLine();

        if ($isConfigured) {
            $this->info('✅ AWS SNS is properly configured and ready to send SMS!');
            
            // Check sandbox status
            $sandboxStatus = $this->smsService->checkSandboxStatus();
            $this->newLine();
            
            if ($sandboxStatus['in_sandbox'] === true) {
                $this->warn('⚠️  AWS SNS Account is in SANDBOX MODE');
                $this->comment('In sandbox mode, you can ONLY send SMS to verified phone numbers.');
                $this->newLine();
                $this->comment('To fix this:');
                $this->comment('1. Go to AWS SNS Console: https://console.aws.amazon.com/sns/');
                $this->comment('2. Navigate to "Text messaging (SMS)" → "Phone numbers"');
                $this->comment('3. Click "Request production access" or verify your phone number');
                $this->comment('4. Fill out the request form and wait for AWS approval');
                $this->newLine();
                $this->comment('Or verify your phone number:');
                $this->comment('1. Go to "Phone numbers" → "Create phone number"');
                $this->comment('2. Enter your phone number and verify it');
            } elseif ($sandboxStatus['in_sandbox'] === false) {
                $this->info('✅ Account is in PRODUCTION mode - can send to any phone number');
            } else {
                $this->comment('⚠️  Could not determine sandbox status: ' . $sandboxStatus['message']);
            }
            
            $this->newLine();
            $limits = $this->smsService->getLimits();
            $this->comment("Free Tier: {$limits['free_tier']}");
            $this->comment("Cost after: {$limits['cost_after']}");
        } else {
            $this->error('❌ AWS SNS is not configured');
            $this->comment('Add these to your .env file:');
            $this->comment('AWS_ACCESS_KEY_ID=your_access_key');
            $this->comment('AWS_SECRET_ACCESS_KEY=your_secret_key');
            $this->comment('AWS_DEFAULT_REGION=us-east-1');
        }

        return 0;
    }

    /**
     * Show AWS SNS limits and costs.
     */
    private function showLimits(): int
    {
        $limits = $this->smsService->getLimits();

        $this->info('💰 AWS SNS Pricing & Limits');
        $this->newLine();

        $tableData = [
            ['Free Tier', $limits['free_tier']],
            ['Cost After Free Tier', $limits['cost_after']],
            ['Regional Variations', $limits['regional_limits']],
            ['Setup Requirements', $limits['setup_required']],
        ];

        $this->table(['Item', 'Details'], $tableData);

        $this->newLine();
        $this->comment('📍 Pricing varies by country and carrier');
        $this->comment('🔗 Full pricing: https://aws.amazon.com/sns/pricing/');

        return 0;
    }

    /**
     * Show help information.
     */
    private function showHelp(): void
    {
        $this->comment('Usage:');
        $this->line('  php artisan sms:monitor --status    # Show AWS SNS configuration');
        $this->line('  php artisan sms:monitor --test      # Send test SMS via AWS SNS');
        $this->line('  php artisan sms:monitor --limits    # Show pricing and limits');
        $this->newLine();

        $this->comment('Setup:');
        $this->line('1. Create IAM user at https://console.aws.amazon.com/iam/');
        $this->line('2. Attach SNS policy to the user');
        $this->line('3. Add credentials to .env file');
        $this->line('4. Run: composer require aws/aws-sdk-php');
        $this->newLine();

        $this->comment('Free tier: 1,000 SMS/month for first 12 months');
    }
}
