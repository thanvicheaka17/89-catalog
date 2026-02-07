<?php

namespace App\Http\Controllers\Api;

use App\Helpers\IpHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use App\Models\UserDevice;
use App\Mail\LoginNotification;
use App\Notifications\LoginSmsNotification;
class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:password',
        ], [
            'confirm_password.same' => 'The confirm password must match the password.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Extract name from email (before @)
        $name = explode('@', $request->email)[0];

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_USER,
            'status' => true,
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ],
        ], 201);
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
            'confirm_new_password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = auth('api')->user();

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 400);
        }

        // Check if new password and confirm password match
        if ($request->new_password !== $request->confirm_new_password) {
            return response()->json([
                'success' => false,
                'message' => 'New password and confirm password do not match.',
            ], 400);
        }

        // Check if new password is different from current password
        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'New password must be different from current password.',
            ], 400);
        }

        // Update password (don't use Hash::make - the model has 'hashed' cast)
        $user->password = $request->new_password;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }

    /**
     * Login user and return JWT token.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'trust_device' => 'nullable|boolean',
        ]);

        $trustDevice = $validator->validated()['trust_device'] ?? false;

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        // Check if user exists and is active
        $user = User::where('email', $credentials['email'])->first();

        if ($user && !$user->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact administrator.',
            ], 403);
        }

        // Prevent 'system' and 'admin' role from logging in via API
        if ($user && ($user->isSystem() || $user->isAdmin())) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records',
            ], 401);
        }

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password',
            ], 401);
        }

        // Check if 2FA is enabled for this user (only require 2FA if both enabled AND secret exists)
        if ($user->two_factor_enabled && !empty($user->two_factor_secret)) {
            // Generate unique nonce for this login attempt to prevent token reuse
            $nonce = Str::random(32);

            // Encrypt user info and login data into temporary token (expires in 10 minutes)
            $loginData = [
                'user_id' => $user->id,
                'email' => $user->email,
                'trust_device' => $trustDevice,
                'ip' => IpHelper::getClientIp($request),
                'user_agent' => $request->header('User-Agent', 'Unknown'),
                'created_at' => now()->timestamp,
                'expires_at' => now()->addMinutes(10)->timestamp,
                'nonce' => $nonce, // Unique identifier to prevent token reuse
            ];

            // Store the nonce in cache to track the latest login attempt (expires in 10 minutes)
            Cache::put("2fa_login_nonce_{$user->id}", $nonce, now()->addMinutes(10));

            // Encrypt the login data into a token
            $tempToken = Crypt::encryptString(json_encode($loginData));

            return response()->json([
                'success' => true,
                'message' => 'Two-factor authentication required',
                'requires_2fa' => true,
                'temp_token' => $tempToken,
            ], 200);
        }

        // 2FA not enabled, proceed with normal login
        $user->update([
            'last_login_at' => now(),
            'login_count' => $user->login_count + 1,
        ]);

        // Get the real client IP address
        $clientIp = IpHelper::getClientIp($request);

        // Check if this is a new device and send notification if enabled
        $isNewDevice = $this->isNewDevice($user, $request);
        if ($user->login_notifications && $isNewDevice) {
            $this->sendLoginNotification($user, $clientIp, $request->header('User-Agent', 'Unknown'));
        }

        // Record this device for future reference
        if ($trustDevice) {
            $this->handleDeviceTrust($request);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Handle device trust functionality for user login.
     */
    private function handleDeviceTrust(Request $request, ?User $user = null): void
    {
        try {
            // Get the authenticated user (safer than using $user from earlier)
            $authenticatedUser = $user ?? auth('api')->user();
            $clientIp = IpHelper::getClientIp($request);
            $deviceFingerprint = hash('sha256', ($request->userAgent() ?? 'Unknown User Agent') . $clientIp);


            if (!$authenticatedUser) {
                return;
            }

            // Check if this device combination already exists and is not revoked
            $existingDevice = UserDevice::where('user_id', $authenticatedUser->id)
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
                // Update last active time for existing trusted device
                $existingDevice->update(['last_active_at' => now()]);
            } else {
                // Create new trusted device record
                UserDevice::create([
                    'user_id' => $authenticatedUser->id,
                    'device_name' => $request->header('Device-Name', 'Unknown Device'),
                    'ip_address' => $clientIp,
                    'user_agent' => $request->header('User-Agent', 'Unknown User Agent'),
                    'device_fingerprint' => $deviceFingerprint,
                    'last_active_at' => now(),
                    'revoked' => false,
                ]);
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the login
            \Log::error('Failed to create/update trusted device: ' . $e->getMessage(), [
                'user_id' => $authenticatedUser->id ?? null,
                'ip' => $clientIp ?? $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
        }
    }

    /**
     * Get the authenticated user.
     */
    public function me(): JsonResponse
    {
        $user = auth('api')->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->getAvatarUrl(),
                'role' => $user->role,
                'account_status' => $user->status,
                'account_balance' => $user->account_balance,
                'created_at' => $user->created_at,
                'phone_number' => $user->phone_number,
                'country_code' => $user->country_code,
                'location' => $user->location,
                'birth_date' => $user->birth_date,
                'last_login_at' => $user->last_login_at,
                'login_count' => $user->login_count,
                'member_since' => $user->created_at,
                'two_factor_enabled' => $user->two_factor_enabled,
                'login_notifications' => $user->login_notifications,
                'email_notifications' => $user->email_notifications,
                'sms_notifications' => $user->sms_notifications,
                'push_notifications' => $user->push_notifications,
                'language' => $user->language,
                'timezone' => $user->timezone,
            ],
        ]);
    }

    /**
     * Log the user out (invalidate the token).
     */
    public function logout(): JsonResponse
    {
        // Invalidate the token and add it to the blacklist
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        $newToken = JWTAuth::refresh(JWTAuth::getToken());

        return $this->respondWithToken($newToken);
    }

    /**
     * Get the token array structure.
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ]);
    }

    /**
     * Forgot password.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        
        if ($user && $user->isInactive()) {
            return response()->json([
                'success' => false,
                'message' => 'Your email address has been deactivated. Please contact administrator.',
            ], 404);
        }

        if (!$user || !$user->isUser()) {
            return response()->json([
                'success' => false,
                'message' => 'The provided email address is invalid.',
            ], 404);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return response()->json([
            'success' => $status === Password::RESET_LINK_SENT,
            'message' => __($status),
        ]);
    }

    /**
     * Reset password.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $encryptionService = app(\App\Services\TokenEncryptionService::class);
        $decryptedToken = $encryptionService->decryptFromUrl($request->token);

        if (!$decryptedToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token.',
            ], 422);
        }

        // Create a new request with the decrypted token
        $resetData = $request->only('email', 'password', 'password_confirmation');
        $resetData['token'] = $decryptedToken;

        $status = Password::reset(
            $resetData,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['success' => true, 'message' => __($status)])
            : response()->json(['success' => false, 'message' => __($status)], 422);
    }

    /**
     * Delete the user account.
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        $password = $request->input('password');

        if (empty($password)) {
            return response()->json([
                'success' => false,
                'message' => 'The password is required.',
            ], 400);
        }

        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The password is incorrect.',
            ], 401);
        }

        // Delete all related records first to maintain data integrity
        $user->userDevices()->delete();
        $user->userFriends()->delete();
        $user->userGamePlays()->delete();
        $user->userAchievements()->delete();

        $user->delete();

        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['success' => true, 'message' => 'Account deleted successfully']);
    }

    /**
     * Update the user account.
     */
    public function updateAccount(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $validator = $request->validate([
            'name' => 'required|string|max:255',
            // 'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date|before:today',
            'avatar' => 'nullable', // Can be string (gallery selection) or file (upload)
            // 'password' => 'nullable|string|min:8',
            // 'confirm_password' => 'nullable|string|same:password',
            'two_factor_enabled' => 'nullable|boolean',
            'login_notifications' => 'nullable|boolean',
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
            'push_notifications' => 'nullable|boolean',
            'language' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:255',
            // 'account_balance' => 'nullable|numeric|min:0',
        ], [
            // 'password.min' => 'The password must be at least 8 characters.',
            // 'confirm_password.same' => 'The confirm password must match the password.',
            // 'email.unique' => 'The email has already been taken.',
            'phone_number.max' => 'The phone number must be less than 255 characters.',
            'location.max' => 'The location must be less than 255 characters.',
            'birth_date.date' => 'The birth date must be a valid date.',
            'two_factor_enabled.boolean' => 'The two factor enabled must be a boolean.',
            'login_notifications.boolean' => 'The login notifications must be a boolean.',
            'email_notifications.boolean' => 'The email notifications must be a boolean.',
            'sms_notifications.boolean' => 'The sms notifications must be a boolean.',
            'push_notifications.boolean' => 'The push notifications must be a boolean.',
            'language.max' => 'The language must be less than 255 characters.',
            'timezone.max' => 'The timezone must be less than 255 characters.',
            'birth_date.before' => 'The birth date must be before today.',
            // 'account_balance.numeric' => 'The account balance must be a number.',
            // 'account_balance.min' => 'The account balance must be greater than 0.',
            'avatar.string' => 'The avatar must be a valid string.',
        ]);

        // Handle avatar selection/upload
        if ($request->has('avatar') || $request->hasFile('avatar')) {
            // Handle file upload
            if ($request->hasFile('avatar')) {
                $request->validate([
                    'avatar' => ['file', 'mimes:jpeg,png,gif,webp,svg', 'max:' . $user->getMaxAvatarFileSize()],
                ], [
                    'avatar.mimes' => 'The avatar must be a valid image file (JPEG, PNG, GIF, WebP, SVG).',
                    'avatar.max' => 'The avatar must be less than ' . ($user->getMaxAvatarFileSize() / 1024) . 'MB.',
                ]);

                if ($user->avatar && $user->isCustomAvatar()) {
                    $this->deleteAvatarFile($user->avatar);
                }

                $avatar = $request->file('avatar');
                $filename = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();

                $avatarDir = public_path('images/avatars/');
                if (!file_exists($avatarDir)) {
                    mkdir($avatarDir, 0755, true);
                }

                $avatar->move($avatarDir, $filename);
                $validator['avatar'] = 'images/avatars/' . $filename;

            } elseif ($request->has('avatar') && !empty($request->avatar)) {
                if ($user->isValidAvatar($request->avatar)) {
                    if ($user->avatar && $user->isCustomAvatar()) {
                        $this->deleteAvatarFile($user->avatar);
                    }
                    $validator['avatar'] = $request->avatar;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid avatar selection',
                        'errors' => ['avatar' => ['Selected avatar is not available in the gallery']],
                    ], 422);
                }
            }
        }

        $user->update($validator);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Account updated successfully',
            // 'data' => [
            //     'user' => $user,
            //     'access_token' => $token,
            //     'token_type' => 'Bearer',
            //     'expires_in' => config('jwt.ttl') * 60,
            // ],
        ]);
    }

    private function isNewDevice(User $user, Request $request): bool
    {
        try {
            $ip = IpHelper::getClientIp($request);
            $userAgent = $request->header('User-Agent', 'Unknown');

            // Create device fingerprint based on IP and User Agent
            $deviceFingerprint = hash('sha256', $userAgent . $ip);

            // Check if this device combination has been seen before (within last 30 days)
            $existingDevice = UserDevice::where('user_id', $user->id)
                ->where(function ($query) use ($ip, $userAgent, $deviceFingerprint) {
                    $query->where('device_fingerprint', $deviceFingerprint)
                        ->orWhere(function ($q) use ($ip, $userAgent) {
                            $q->where('ip_address', $ip)
                                ->where('user_agent', $userAgent);
                        });
                })
                ->where('last_active_at', '>=', now()->subDays(30)) // Consider devices active within 30 days
                ->where('revoked', false)
                ->exists();

            // If no existing device found, this is a new device
            return !$existingDevice;

        } catch (\Exception $e) {
            // If there's an error checking devices, err on the side of caution and send notification
            \Log::warning('Error checking for new device, sending notification: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);
            return true;
        }
    }

    /**
     * Send login notification to user.
     */
    private function sendLoginNotification(User $user, string $ip, string $userAgent): void
    {
        try {
            // Send email notification
            if ($user->email_notifications) {
                Mail::to($user->email)->send(new LoginNotification($user, $ip, $userAgent));
            }

            // Send SMS notification
            // if ($user->sms_notifications && $user->phone_number) {
            //     $user->notify(new LoginSmsNotification($user, $ip, $userAgent));
            // }

            // TODO: Implement push notification
            // if ($user->push_notifications && $user->push_token) {
            //     Push::to($user->push_token)->send(new LoginNotification($user, $ip, $userAgent));
            // }
        } catch (\Exception $e) {
            // Log the error but don't fail the login
            \Log::error('Failed to send login notification: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $ip,
            ]);
        }
    }

    /**
     * Get the user's login notifications history.
     */
    public function loginNotifications(): JsonResponse
    {
        $user = auth('api')->user();

        // Get recent device activities for login notifications
        $recentDevices = $user->userDevices()
            ->where('revoked', false)
            ->orderBy('last_active_at', 'desc')
            ->limit(10)
            ->get();

        $notifications = [];

        // Create notifications based on device activity
        foreach ($recentDevices as $device) {
            $notifications[] = [
                'id' => $device->id,
                'user_id' => $device->user_id,
                'title' => 'Login from ' . ($device->device_name ?: 'Unknown Device'),
                'message' => 'You logged in from ' . ($device->device_name ?: 'Unknown Device') . ' at ' . $device->ip_address,
                'created_at' => $device->last_active_at,
            ];
        }

        // If no device activity, add a default notification about account security
        if (empty($notifications)) {
            $notifications[] = [
                'id' => 'default',
                'user_id' => $user->id,
                'title' => 'Account Security',
                'message' => 'Your account is secure. Login notifications are enabled.',
                'created_at' => $user->last_login_at ?: $user->created_at,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Notification preferences.
     */
    public function notificationPreferences(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $validator = Validator::make($request->all(), [
            'notification_type' => 'required|string|in:login,email,sms,push',
            'enable' => 'required|boolean',
        ], [
            'notification_type.in' => 'The notification type must be login, email, sms, or push.',
            'enable.boolean' => 'The enable must be a boolean.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        switch ($request->notification_type) {
            case 'login':
                $user->login_notifications = $request->enable;
                break;
            case 'email':
                $user->email_notifications = $request->enable;
                break;
            case 'sms':
                $user->sms_notifications = $request->enable;
                break;
            case 'push':
                $user->push_notifications = $request->enable;
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid notification type',
                ], 422);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated successfully',
        ]);
    }

    /**
     * Get available avatars from gallery and upload settings
     */
    public function getAvailableAvatars(): JsonResponse
    {
        $user = auth('api')->user();

        return response()->json([
            'success' => true,
            'data' => $user->getAvailableAvatars()
        ]);
    }

    /**
     * Delete avatar file from storage.
     */
    private function deleteAvatarFile(string $avatarPath): void
    {
        try {
            // If the path is a full URL, extract the path
            if (filter_var($avatarPath, FILTER_VALIDATE_URL)) {
                $path = parse_url($avatarPath, PHP_URL_PATH);
                $fullPath = public_path($path);
            } else {
                // If it's already a relative path
                $fullPath = public_path($avatarPath);
            }

            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the update
            \Log::warning('Failed to delete avatar file: ' . $e->getMessage(), [
                'avatar_path' => $avatarPath,
            ]);
        }
    }
}

