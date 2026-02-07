<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Mail\ResetPasswordNotification;
use App\Services\LevelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, HasUuids, Notifiable;

    /**
     * Role constants
     */
    const ROLE_SYSTEM = 'system';
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    /**
     * Available roles
     */
    public static array $roles = [
        self::ROLE_SYSTEM => 'System',
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_USER => 'User',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'api_token',
        'role',
        'status',
        'phone_number',
        'country_code',
        'location',
        'birth_date',
        'last_login_at',
        'login_count',
        'two_factor_enabled',
        'login_notifications',
        'email_notifications',
        'sms_notifications',
        'push_notifications',
        'language',
        'timezone',
        'account_balance',
        'total_accumulated_funds',
        'current_level',
        'tier',
        'two_factor_secret',
        'two_factor_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'api_token',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
            'last_login_at' => 'datetime',
            'login_count' => 'integer',
            'birth_date' => 'date',
            'two_factor_enabled' => 'boolean',
            'login_notifications' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'language' => 'string',
            'timezone' => 'string',
            'country_code' => 'string',
            'account_balance' => 'decimal:2',
            'total_accumulated_funds' => 'decimal:2',
            'current_level' => 'integer',
            'tier' => 'string',
            'two_factor_secret' => 'string',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === true;
    }

    /**
     * Check if user is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === false;
    }

    /**
     * Check if user is a system (super-admin)
     */
    public function isSystem(): bool
    {
        return $this->role === self::ROLE_SYSTEM;
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is a regular user
     */
    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * Check if user has admin or higher privileges
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this->role, [self::ROLE_SYSTEM, self::ROLE_ADMIN]);
    }

    /**
     * Check if this user can be deleted
     */
    public function canBeDeleted(): bool
    {
        return !$this->isSystem();
    }

    /**
     * Check if this user's role can be changed
     */
    public function canChangeRole(): bool
    {
        return !$this->isSystem();
    }

    /**
     * Check if this user's status can be changed by the given user
     */
    public function canChangeStatus(?User $byUser = null): bool
    {
        // Cannot change own status
        if ($byUser && $this->id === $byUser->id) {
            return false;
        }
        return true;
    }

    /**
     * Get roles that can be assigned by the given user
     */
    public static function getAssignableRoles(?User $byUser = null): array
    {
        // System role can never be assigned
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_USER => 'User',
        ];
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayName(): string
    {
        return self::$roles[$this->role] ?? 'Unknown';
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayName(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    /**
     * Check if user has an avatar
     */
    public function hasAvatar(): bool
    {
        return !empty($this->avatar);
    }

    /**
     * Get avatar URL (returns default avatar if not set)
     */
    public function getAvatarUrl(): string
    {
        if ($this->hasAvatar()) {
            return url($this->avatar);
        }

        return url('images/avatars/default-avatar.webp');
    }


    /**
     * Check if custom avatar uploads are enabled
     */
    public function isCustomAvatarUploadEnabled(): bool
    {
        return \App\Models\SiteSetting::get('custom_avatar_upload_enabled', false);
    }

    /**
     * Get maximum avatar file size in KB
     */
    public function getMaxAvatarFileSize(): int
    {
        return \App\Models\SiteSetting::get('max_avatar_file_size', 2048); // Default 2MB
    }

    /**
     * Get all available avatars from gallery
     */
    public function getAvailableAvatars(): array
    {
        $avatars = \App\Models\SiteSetting::get('available_avatars', ['images/avatars/default-avatar.webp']);
        return array_map(function($avatar) {
            return [
                'url' => url($avatar),
                'path' => $avatar,
                'title' => basename($avatar)
            ];
        }, $avatars);
    }

    /**
     * Check if selected avatar is valid (exists in available gallery)
     */
    public function isValidAvatar(string $avatarPath): bool
    {
        $availableAvatars = \App\Models\SiteSetting::get('available_avatars', []);
        return in_array($avatarPath, $availableAvatars);
    }

    /**
     * Check if avatar is a custom uploaded file (not from gallery)
     */
    public function isCustomAvatar(): bool
    {
        if (!$this->hasAvatar()) {
            return false;
        }

        // If avatar path doesn't exist in available avatars, it's custom
        return !$this->isValidAvatar($this->avatar);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        Mail::to($this->email)->send(new ResetPasswordNotification($token, $this->email, $this->name));
    }

    public function login(Request $request)
    {
        $this->last_login_at = now();
        $this->login_count++;
        $this->save();
    }

    public function userGamePlays()
    {
        return $this->hasMany(UserGamePlay::class, 'user_id');
    }

    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class, 'user_id');
    }

    public function userFriends()
    {
        return $this->hasMany(UserFriend::class, 'user_id');
    }

    public function userDevices()
    {
        return $this->hasMany(UserDevice::class, 'user_id');
    }

    /**
     * Get full phone number with country code.
     */
    public function getFullPhoneNumberAttribute(): string
    {
        if (!$this->phone_number) {
            return '';
        }

        if ($this->country_code && !str_starts_with($this->phone_number, $this->country_code)) {
            return $this->country_code . ' ' . $this->phone_number;
        }

        return $this->phone_number;
    }

    /**
     * Add funds to total accumulated funds and update level
     *
     * @param float $amount
     * @return void
     */
    public function addAccumulatedFunds(float $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        $currentFunds = (float) ($this->total_accumulated_funds ?? 0);
        $newTotal = $currentFunds + $amount;
        $this->setAttribute('total_accumulated_funds', $newTotal);
        $this->updateLevel();
    }

    /**
     * Update user level based on total accumulated funds
     *
     * @return void
     */
    public function updateLevel(): void
    {
        $levelService = app(LevelService::class);
        $totalFunds = (float) ($this->total_accumulated_funds ?? 0);
        
        $newLevel = $levelService->calculateLevel($totalFunds);
        $newTier = $levelService->getTierForLevel($newLevel);
        $oldTier = $this->tier ?? 'Bronze';

        // Only update if level increased (never decrease)
        if ($newLevel >= ($this->current_level ?? 1)) {
            $this->current_level = $newLevel;
            $this->tier = $newTier;
            
            // Award tier achievement if tier changed
            if ($newTier !== $oldTier) {
                $this->awardTierAchievement($newTier);
            }
        }
    }

    /**
     * Award tier-based achievement when user reaches a new tier
     *
     * @param string $tier
     * @return void
     */
    private function awardTierAchievement(string $tier): void
    {
        $tierAchievementCode = 'TIER_' . strtoupper($tier);

        // Check if user already has this tier achievement
        $existingAchievement = \App\Models\UserAchievement::where('user_id', $this->id)
            ->where('achievement_code', $tierAchievementCode)
            ->first();

        if (!$existingAchievement) {
            $tierDescriptions = [
                'BRONZE' => 'Reached Bronze Tier (Level 1-10) - Welcome to the ranks!',
                'SILVER' => 'Reached Silver Tier (Level 11-20) - You\'re making progress!',
                'GOLD' => 'Reached Gold Tier (Level 21-30) - You\'re a valuable member!',
                'PLATINUM' => 'Reached Platinum Tier (Level 31-40) - Elite status achieved!',
                'DIAMOND' => 'Reached Diamond Tier (Level 41-50) - Sultan/VIP status unlocked!',
            ];

            \App\Models\UserAchievement::create([
                'user_id' => $this->id,
                'achievement_code' => $tierAchievementCode,
                'title' => ucfirst($tier) . ' Tier Unlocked',
                'description' => $tierDescriptions[strtoupper($tier)] ?? "Reached {$tier} Tier",
                'unlocked_at' => now(),
            ]);
        }
    }

    /**
     * Get level information
     *
     * @return array
     */
    public function getLevelInfo(): array
    {
        $levelService = app(LevelService::class);
        $totalFunds = (float) ($this->total_accumulated_funds ?? 0);
        
        return $levelService->getLevelInfo($totalFunds);
    }

    /**
     * Get current tier information
     *
     * @return array|null
     */
    public function getTierInfo(): ?array
    {
        $levelService = app(LevelService::class);
        return $levelService->getTierInfo($this->tier ?? 'Bronze');
    }

    /**
     * Check if user has reached max level
     *
     * @return bool
     */
    public function isMaxLevel(): bool
    {
        return ($this->current_level ?? 1) >= 50;
    }

}
