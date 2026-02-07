<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Services\AwsSmsService;

class SmsChannel
{
    protected $smsService;

    public function __construct(AwsSmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // Check if the notifiable has SMS notifications enabled
        if (!$notifiable->sms_notifications) {
            // Log::info('SMS notification skipped: User has SMS notifications disabled', [
            //     'user_id' => $notifiable->id,
            // ]);
            return;
        }

        // Check if the notifiable has a phone number
        if (!$notifiable->phone_number) {
            // Log::info('SMS notification skipped: User has no phone number', [
            //     'user_id' => $notifiable->id,
            // ]);
            return;
        }

        // Get the phone number (use full phone number with country code)
        $phoneNumber = $notifiable->getFullPhoneNumberAttribute();

        if (!$phoneNumber) {
            // Log::info('SMS notification skipped: Could not format phone number', [
            //     'user_id' => $notifiable->id,
            // ]);
            return;
        }

        // Get the message from the notification
        $message = $notification->toSms($notifiable);

        // Send the SMS via AWS SNS
        // Log::info('Sending SMS notification via AWS SNS', [
        //     'user_id' => $notifiable->id,
        //     'phone_number' => $phoneNumber,
        //     'notification_class' => get_class($notification),
        //     'message_length' => strlen($message),
        // ]);

        $success = $this->smsService->send($phoneNumber, $message);

        // if (!$success) {
        //     Log::error('AWS SNS SMS notification failed', [
        //         'user_id' => $notifiable->id,
        //         'phone_number' => $phoneNumber,
        //         'notification_class' => get_class($notification),
        //         'message_preview' => substr($message, 0, 50) . '...',
        //     ]);
        // } else {
        //     Log::info('AWS SNS SMS notification sent successfully', [
        //         'user_id' => $notifiable->id,
        //         'phone_number' => $phoneNumber,
        //         'notification_class' => get_class($notification),
        //     ]);
        // }
    }
}
