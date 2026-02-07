<?php

namespace App\Services;

use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;

class AwsSmsService
{
    protected $snsClient;
    protected $isConfigured = false;

    public function __construct()
    {
        $this->initializeClient();
    }

    /**
     * Initialize AWS SNS client.
     */
    protected function initializeClient(): void
    {
        $keyId = env('AWS_ACCESS_KEY_ID');
        $secretKey = env('AWS_SECRET_ACCESS_KEY');
        $region = env('AWS_DEFAULT_REGION', 'us-east-1');

        if ($keyId && $secretKey) {
            try {
                $this->snsClient = new SnsClient([
                    'version' => 'latest',
                    'region' => $region,
                    'credentials' => [
                        'key' => $keyId,
                        'secret' => $secretKey,
                    ],
                ]);
                $this->isConfigured = true;
            } catch (\Exception $e) {
                Log::warning('AWS SNS client initialization failed', [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send SMS message via AWS SNS.
     *
     * @param string $to The recipient's phone number
     * @param string $message The message content
     * @return bool Success status
     */
    public function send(string $to, string $message): bool
    {
        if (!$this->isConfigured) {
            Log::warning('AWS SNS not configured - missing AWS_ACCESS_KEY_ID or AWS_SECRET_ACCESS_KEY');
            return false;
        }

        try {
            // Format phone number to E.164
            $to = $this->formatPhoneNumber($to);

            $result = $this->snsClient->publish([
                'Message' => $message,
                'PhoneNumber' => $to,
                'MessageAttributes' => [
                    'AWS.SNS.SMS.SMSType' => [
                        'DataType' => 'String',
                        'StringValue' => 'Transactional', // or 'Promotional'
                    ],
                ],
            ]);

            $messageId = $result['MessageId'] ?? null;
            
            Log::info('AWS SNS SMS sent successfully', [
                'to' => $to,
                'message_length' => strlen($message),
                'message_id' => $messageId,
                'note' => 'Message accepted by AWS SNS. Check AWS SNS Console for delivery status.',
            ]);

            // Log warning if account might be in sandbox mode
            $sandboxStatus = $this->checkSandboxStatus();
            if ($sandboxStatus['in_sandbox'] === true) {
                Log::warning('AWS SNS SMS sent but account is in SANDBOX mode', [
                    'to' => $to,
                    'message_id' => $messageId,
                    'warning' => 'In sandbox mode, SMS can only be sent to verified phone numbers. Verify the phone number in AWS SNS Console or request production access.',
                ]);
            }

            return true;

        } catch (AwsException $e) {
            Log::error('AWS SNS SMS sending failed', [
                'error_code' => $e->getAwsErrorCode(),
                'error_message' => $e->getAwsErrorMessage(),
                'to' => $to,
                'message_length' => strlen($message),
                'aws_request_id' => $e->getAwsRequestId(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Unexpected AWS SNS error', [
                'error' => $e->getMessage(),
                'to' => $to,
            ]);
            return false;
        }
    }

    /**
     * Check if AWS SNS is properly configured.
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        return $this->isConfigured;
    }

    /**
     * Format phone number to E.164 format.
     *
     * @param string $phone
     * @return string
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove any non-numeric characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // If it doesn't start with +, assume it's international
        if (!str_starts_with($phone, '+')) {
            // For Cambodia, add +855 if it starts with 0
            if (str_starts_with($phone, '0')) {
                $phone = '+855' . substr($phone, 1);
            } else {
                $phone = '+' . $phone;
            }
        }

        return $phone;
    }

    /**
     * Check if AWS SNS account is in Sandbox mode.
     * In Sandbox mode, you can only send SMS to verified phone numbers.
     *
     * @return array ['in_sandbox' => bool, 'message' => string]
     */
    public function checkSandboxStatus(): array
    {
        if (!$this->isConfigured) {
            return [
                'in_sandbox' => null,
                'message' => 'AWS SNS not configured',
            ];
        }

        try {
            $result = $this->snsClient->getSMSAttributes([
                'attributes' => ['DeliveryStatusSuccessRate', 'DeliveryStatusIAMRole', 'DefaultSMSType', 'UsageReportS3Bucket'],
            ]);

            // Check account spending limit to determine sandbox status
            // In sandbox mode, spending limit is typically $1.00
            $spendingLimit = $this->snsClient->getSMSAttributes([
                'attributes' => ['SpendingLimit'],
            ]);

            $spendingLimitValue = $spendingLimit['attributes']['SpendingLimit'] ?? null;
            $isSandbox = $spendingLimitValue === '1.00' || $spendingLimitValue === '1';

            return [
                'in_sandbox' => $isSandbox,
                'spending_limit' => $spendingLimitValue,
                'message' => $isSandbox 
                    ? 'Account is in SANDBOX mode. You can only send SMS to verified phone numbers. Request production access in AWS SNS Console.'
                    : 'Account appears to be in PRODUCTION mode.',
            ];
        } catch (AwsException $e) {
            Log::warning('Failed to check AWS SNS sandbox status', [
                'error_code' => $e->getAwsErrorCode(),
                'error_message' => $e->getAwsErrorMessage(),
            ]);

            return [
                'in_sandbox' => null,
                'message' => 'Could not determine sandbox status: ' . $e->getAwsErrorMessage(),
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to check AWS SNS sandbox status', [
                'error' => $e->getMessage(),
            ]);

            return [
                'in_sandbox' => null,
                'message' => 'Could not determine sandbox status: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get SMS account attributes and status.
     *
     * @return array
     */
    public function getAccountAttributes(): array
    {
        if (!$this->isConfigured) {
            return ['error' => 'AWS SNS not configured'];
        }

        try {
            $result = $this->snsClient->getSMSAttributes();
            return $result['attributes'] ?? [];
        } catch (AwsException $e) {
            Log::error('Failed to get AWS SNS account attributes', [
                'error_code' => $e->getAwsErrorCode(),
                'error_message' => $e->getAwsErrorMessage(),
            ]);
            return ['error' => $e->getAwsErrorMessage()];
        } catch (\Exception $e) {
            Log::error('Failed to get AWS SNS account attributes', [
                'error' => $e->getMessage(),
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get SMS sending limits and costs info.
     *
     * @return array
     */
    public function getLimits(): array
    {
        return [
            'free_tier' => '1,000 messages/month for first 12 months',
            'cost_after' => '$0.00645 per message (Transactional)',
            'regional_limits' => 'May vary by country/carrier',
            'setup_required' => 'AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY',
        ];
    }
}
