<?php

namespace App\Http\Controllers\Api;

use App\Helpers\IpHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use PragmaRX\Google2FAQRCode\Google2FA;
use App\Models\SiteSetting;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class TwoFactorController extends Controller
{
    public function enable2FA(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        
        // If 2FA is already enabled, return error
        if ($user->two_factor_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication is already enabled.',
            ], 400);
        }

        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey(32);

        // Store secret but DON'T enable 2FA yet (waiting for verification)
        // This prevents users from being locked out if they don't complete setup
        $user->two_factor_secret = $secret;
        $user->two_factor_enabled = false; // Keep disabled until verified
        $user->save();

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            SiteSetting::get('site_name', config('app.name')),
            $user->email,
            $secret
        );

        return response()->json([
            'success' => true,
            'message' => 'Please scan the QR code and verify with a 6-digit code to complete setup.',
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
            'setup_pending' => true,
        ]);
    }

    /**
     * Complete 2FA setup by verifying the code.
     * This enables 2FA only after successful verification.
     */
    public function complete2FASetup(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        
        $validator = Validator::make($request->all(), [
            'one_time_password' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if there's a pending setup (secret exists but 2FA not enabled)
        if (!$user->two_factor_secret) {
            return response()->json([
                'success' => false,
                'message' => 'No pending 2FA setup found. Please enable 2FA first.',
            ], 400);
        }

        if ($user->two_factor_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication is already enabled.',
            ], 400);
        }

        // Verify the 2FA code
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->one_time_password);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid two-factor authentication code. Please try again.',
            ], 401);
        }

        // Code verified successfully, enable 2FA
        $user->two_factor_enabled = true;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Two-factor authentication has been enabled successfully.',
        ]);
    }

    /**
     * Cancel pending 2FA setup.
     * Cleans up if user abandons the setup process.
     */
    public function cancel2FASetup(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        // Only allow canceling if 2FA is not enabled but secret exists (pending setup)
        if ($user->two_factor_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel. 2FA is already enabled. Use disable endpoint instead.',
            ], 400);
        }

        if (!$user->two_factor_secret) {
            return response()->json([
                'success' => false,
                'message' => 'No pending 2FA setup to cancel.',
            ], 400);
        }

        // Clear the pending setup
        $user->two_factor_secret = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => '2FA setup has been cancelled.',
        ]);
    }


    /**
     * Verify 2FA code - handles both login flow (with temp_token) and regular verification (when authenticated).
     */
    public function verifyLogin2FA(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'temp_token' => 'required|string',
            'one_time_password' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tempToken = $request->temp_token;
        $loginData = null;
        $user = null;

        // Check if this is a login 2FA verification (has temp_token)
        if ($tempToken) {
            try {
                // Decrypt the temporary token to retrieve login data
                $decryptedData = Crypt::decryptString($tempToken);
                $loginData = json_decode($decryptedData, true);
                
                if (!$loginData || !isset($loginData['user_id'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid temporary token. Please login again.',
                    ], 401);
                }
                
                // Check if token has expired
                if (isset($loginData['expires_at']) && $loginData['expires_at'] < now()->timestamp) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Temporary token has expired. Please login again.',
                    ], 401);
                }
                
                // Check if token has been used or is from an old login attempt
                // Verify the nonce matches the latest login attempt for this user
                if (isset($loginData['nonce']) && isset($loginData['user_id'])) {
                    $latestNonce = Cache::get("2fa_login_nonce_{$loginData['user_id']}");
                    
                    if (!$latestNonce || $latestNonce !== $loginData['nonce']) {
                        return response()->json([
                            'success' => false,
                            'message' => 'This token is no longer valid. Please login again to get a new token.',
                        ], 401);
                    }
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or corrupted temporary token. Please login again.',
                ], 401);
            }

            // Get user from login data
            $user = User::find($loginData['user_id']);
            
            if (!$user || !$user->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User account not found or inactive.',
                ], 401);
            }
        } else {
            // Regular 2FA verification (requires authentication)
            $user = auth('api')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required. Please provide temp_token for login verification or authenticate first.',
                ], 401);
            }

            if (!$user->two_factor_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Two-factor authentication is not enabled.'
                ], 401);
            }
        }

        // Verify 2FA code
        if (!$user->two_factor_enabled || !$user->two_factor_secret) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication is not enabled for this account.',
            ], 401);
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->one_time_password);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid two-factor authentication code.',
            ], 401);
        }

        // If this is a login flow (has temp_token), complete login and return JWT token
        if ($tempToken && $loginData) {
            // Remove the nonce from cache to prevent token reuse (single-use token)
            if (isset($loginData['nonce']) && isset($loginData['user_id'])) {
                Cache::forget("2fa_login_nonce_{$loginData['user_id']}");
            }
            
            // Update user login info
            $user->update([
                'last_login_at' => now(),
                'login_count' => $user->login_count + 1,
            ]);

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            // Handle device trust and notifications
            $this->handleLoginCompletion($user, $loginData, $request);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ]);
        }

        // Regular 2FA verification (not login flow)
        return response()->json([
            'success' => true,
            'message' => '2FA verified successfully.',
        ]);
    }

    /**
     * Handle login completion tasks (notifications, device trust).
     */
    private function handleLoginCompletion(User $user, array $loginData, Request $request): void
    {
        // Create a temporary request object with the stored login data for device checks
        $tempRequest = new Request();
        $tempRequest->headers->set('User-Agent', $loginData['user_agent']);
        $tempRequest->headers->set('Device-Name', $request->header('Device-Name', 'Unknown Device'));
        $tempRequest->server->set('REMOTE_ADDR', $loginData['ip']);
        $tempRequest->headers->set('X-Forwarded-For', $loginData['ip']);

        // Check if this is a new device and send notification if enabled
        if ($user->login_notifications) {
            $isNewDevice = $this->isNewDevice($user, $tempRequest);
            if ($isNewDevice) {
                $this->sendLoginNotification($user, $loginData['ip'], $loginData['user_agent']);
            }
        }

        // Record this device for future reference
        if ($loginData['trust_device'] ?? false) {
            $this->handleDeviceTrust($tempRequest, $user);
        }
    }

    /**
     * Check if this is a new device for the user.
     */
    private function isNewDevice(User $user, Request $request): bool
    {
        try {
            $ip = IpHelper::getClientIp($request);
            $userAgent = $request->header('User-Agent', 'Unknown');

            // Create device fingerprint based on IP and User Agent
            $deviceFingerprint = hash('sha256', $userAgent . $ip);

            // Check if this device combination has been seen before (within last 30 days)
            $existingDevice = \App\Models\UserDevice::where('user_id', $user->id)
                ->where(function ($query) use ($ip, $userAgent, $deviceFingerprint) {
                    $query->where('device_fingerprint', $deviceFingerprint)
                          ->orWhere(function ($q) use ($ip, $userAgent) {
                              $q->where('ip_address', $ip)
                                ->where('user_agent', $userAgent);
                          });
                })
                ->where('last_active_at', '>=', now()->subDays(30))
                ->where('revoked', false)
                ->exists();

            return !$existingDevice;
        } catch (\Exception $e) {
            \Log::warning('Error checking for new device: ' . $e->getMessage());
            return true;
        }
    }

    /**
     * Send login notification to user.
     */
    private function sendLoginNotification(User $user, string $ip, string $userAgent): void
    {
        try {
            if ($user->email_notifications) {
                \Illuminate\Support\Facades\Mail::to($user->email)
                    ->send(new \App\Mail\LoginNotification($user, $ip, $userAgent));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send login notification: ' . $e->getMessage());
        }
    }

    /**
     * Handle device trust functionality for user login.
     */
    private function handleDeviceTrust(Request $request, User $user): void
    {
        try {
            $clientIp = IpHelper::getClientIp($request);
            $deviceFingerprint = hash('sha256', ($request->userAgent() ?? 'Unknown User Agent') . $clientIp);

            // Check if this device combination already exists and is not revoked
            $existingDevice = \App\Models\UserDevice::where('user_id', $user->id)
                ->where(function ($query) use ($deviceFingerprint, $clientIp, $request) {
                    $query->where('device_fingerprint', $deviceFingerprint)
                          ->orWhere(function ($q) use ($clientIp, $request) {
                              $q->where('ip_address', $clientIp)
                                ->where('user_agent', $request->header('User-Agent', 'Unknown User Agent'));
                          });
                })
                ->where('revoked', false)
                ->first();

            if ($existingDevice) {
                $existingDevice->update(['last_active_at' => now()]);
            } else {
                \App\Models\UserDevice::create([
                    'user_id' => $user->id,
                    'device_name' => $request->header('Device-Name', 'Unknown Device'),
                    'ip_address' => $clientIp,
                    'user_agent' => $request->header('User-Agent', 'Unknown User Agent'),
                    'device_fingerprint' => $deviceFingerprint,
                    'last_active_at' => now(),
                    'revoked' => false,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create/update trusted device: ' . $e->getMessage());
        }
    }

    public function disable2FA(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        
        // Validate request
        $validator = Validator::make($request->all(), [
            'one_time_password' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if 2FA is enabled
        if (!$user->two_factor_enabled || !$user->two_factor_secret) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication is not enabled.',
            ], 400);
        }

        // Verify 2FA code before disabling
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->one_time_password);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid two-factor authentication code. Please verify your code to disable 2FA.',
            ], 401);
        }

        // 2FA code verified, disable 2FA
        $user->two_factor_secret = null;
        $user->two_factor_enabled = false;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => '2FA disabled successfully.',
        ]);
    }
}
