<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Channels\SmsChannel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the custom SMS notification channel
        Notification::extend('sms', function ($app) {
            return new SmsChannel($app->make(\App\Services\AwsSmsService::class));
        });

        // Force HTTPS in production (Vercel)
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
        }
    }
}
