<?php

namespace App\Console\Commands;

use App\Mail\LoginNotification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email?} {--user=}';

    // php artisan email:test --email=test@example.com 

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending login notification email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
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

        // Use provided email or user's email
        $recipientEmail = $email ?: $user->email;

        $this->info('📧 Testing Login Notification Email');
        $this->info('👤 User: ' . $user->name . ' (' . $user->email . ')');
        $this->info('📬 Sending to: ' . $recipientEmail);
        $this->newLine();

        // Show current mail configuration
        $this->comment('📋 Current Mail Configuration:');
        $this->comment('Mailer: ' . config('mail.default'));
        $this->comment('Host: ' . config('mail.mailers.smtp.host'));
        $this->comment('Port: ' . config('mail.mailers.smtp.port'));
        $this->newLine();

        try {
            // Send the email
            Mail::to($recipientEmail)->send(new LoginNotification(
                $user,
                '192.168.1.100', // Test IP
                'Chrome Browser Test' // Test User Agent
            ));

            $this->info('✅ Email sent successfully!');
            $this->comment('Check your mail logs or inbox for the email.');
            $this->newLine();

            // Show email content directly
            $this->comment('📧 Email Content Preview:');
            $this->line('─────────────────────────────────────────────────────');
            $this->comment('To: ' . $recipientEmail);
            $this->comment('Subject: New Login to Your Account');
            $this->comment('From: ' . config('app.name', 'Laravel'));
            $this->newLine();

            // Show a summary of what the email contains
            $this->comment('📋 Email includes:');
            $this->comment('• Login time: Current timestamp');
            $this->comment('• IP Address: 192.168.1.100');
            $this->comment('• Device: Chrome Browser Test');
            $this->comment('• Security warnings and links');
            $this->line('─────────────────────────────────────────────────────');

            // Show mail configuration
            $this->newLine();
            $this->comment('📋 Current Mail Configuration:');
            $this->comment('Mailer: ' . config('mail.default'));
            $this->comment('Host: ' . config('mail.mailers.smtp.host'));
            $this->comment('Port: ' . config('mail.mailers.smtp.port'));

            if (config('mail.default') === 'log') {
                $this->warn('⚠️  Mail is configured to log only. Check storage/logs/laravel.log');
            }

        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
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
        ];
    }
}
