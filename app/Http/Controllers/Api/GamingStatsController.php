<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserGamePlay;
use App\Models\User;
use App\Models\UserFriend;
use App\Models\UserAchievement;
use App\Models\UserTool;

class GamingStatsController extends Controller
{
    public function index()
    {
        $user = auth('api')->user();

        // Ensure level is up to date (automatically awards tier achievements)
        $user->updateLevel();
        $user->save();
        $user->refresh();

        // Get funds-based level information (Primary System)
        $fundsLevelInfo = $user->getLevelInfo();

        // Games Played: Total number of times users have used tools
        $gamesPlayed = UserTool::where('user_id', $user->id)
            ->sum('usage_count');

        // Achievements: Tier Level achievements (backlog #19)
        $tierAchievements = UserAchievement::where('user_id', $user->id)
            ->where('achievement_code', 'like', 'TIER_%')
            ->orderBy('unlocked_at', 'desc')
            ->get();

        $totalTierAchievements = $tierAchievements->count();
        $latestTierAchievement = $tierAchievements->first();

        // Current Level: The current level (funds-based)
        $currentLevel = $fundsLevelInfo['level'];

        // Progress Level: Progress percentage toward next level (0-100%)
        $progressLevel = $fundsLevelInfo['progress_percentage'];

        // Additional stats
        $gamingStats = UserGamePlay::where('user_id', $user->id)->get();
        $totalDuration = $gamingStats->sum('duration_minutes');
        $totalHours = (int) ($totalDuration / 60);
        $totalGamePlays = $gamingStats->count();
        $totalFriends = UserFriend::where('user_id', $user->id)->count();
        $totalAllAchievements = UserAchievement::where('user_id', $user->id)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'games_played' => $totalGamePlays,
                'hours_played' => $totalDuration,
                'friends' => $totalFriends,
                'achievements' => $totalTierAchievements,
                'current_level' => $fundsLevelInfo['level'],
                'next_level' => $fundsLevelInfo['level'] + 1,
                'tier' => $fundsLevelInfo['tier'],
                'tier_description' => $fundsLevelInfo['tier_description'],
                'total_accumulated_funds' => $fundsLevelInfo['total_funds'],
                'current_level_threshold' => $fundsLevelInfo['current_level_threshold'],
                'next_level_threshold' => $fundsLevelInfo['next_level_threshold'],
                'progress_level_percentage' => $fundsLevelInfo['progress_percentage'],
                'funds_needed_for_next_level' => $fundsLevelInfo['funds_needed_for_next_level'],
                'is_max_level' => $fundsLevelInfo['is_max_level'],
                'will_level_up' => $progressLevel >= 100 && !$fundsLevelInfo['is_max_level'],

            ],
        ], 200);
    }

    /**
     * Calculate user's current level based on hours played
     * 
     * Level calculation formula:
     * - Base XP: 0
     * - Each hour played: +10 XP
     * 
     * Level = floor(sqrt(XP / 100))
     * 
     * Examples:
     * - 0 hours: Level 0 (0 XP)
     * - 1-9 hours: Level 0 (10-90 XP)
     * - 10-39 hours: Level 1 (100-390 XP)
     * - 40-89 hours: Level 2 (400-890 XP)
     * - 90+ hours: Level 3+ (900+ XP)
     */
    private function calculateLevel(int $gamesPlayed, int $hoursPlayed, int $achievements, int $friends): int
    {
        // Calculate XP based ONLY on hours played
        // Each hour = 10 XP
        $xp = $hoursPlayed * 10;

        // Calculate level based on XP (exponential growth)
        // Level starts from 0: Level 0 = 0-99 XP, Level 1 = 100-399 XP, Level 2 = 400-899 XP, etc.
        $level = floor(sqrt($xp / 100));

        return $level;
    }

    /**
     * Calculate XP needed to reach a specific level
     * Level 0: 0 XP, Level 1: 100 XP, Level 2: 400 XP, Level 3: 900 XP, etc.
     */
    private function getXpForLevel(int $level): int
    {
        if ($level <= 0) {
            return 0;
        }
        // XP needed = (level^2) * 100
        return (int) pow($level, 2) * 100;
    }

    /**
     * Calculate current XP based on hours played only
     * Each hour played = 10 XP
     */
    private function calculateCurrentXp(int $gamesPlayed, int $hoursPlayed, int $achievements, int $friends): int
    {
        // Calculate XP based ONLY on hours played
        return $hoursPlayed * 10;
    }

    /**
     * Calculate user's level and XP progression data
     * 
     * @param int $gamesPlayed Total games played
     * @param int $hoursPlayed Total hours played
     * @param int $achievements Total achievements unlocked
     * @param int $friends Total friends count
     * @return array Level and XP progression data
     */
    private function calculateLevelAndXp(int $gamesPlayed, int $hoursPlayed, int $achievements, int $friends): array
    {
        // Step 1: Calculate user's total XP from all activities
        $totalXp = $this->calculateCurrentXp($gamesPlayed, $hoursPlayed, $achievements, $friends);

        // Step 2: Calculate what level the user is currently at
        $userLevel = $this->calculateLevel($gamesPlayed, $hoursPlayed, $achievements, $friends);

        // Handle level 0 case
        if ($userLevel == 0) {
            $xpRequiredForCurrentLevel = 0; // Level 0 starts at 0 XP
            $xpRequiredForNextLevel = $this->getXpForLevel(1); // Level 1 requires 100 XP
            $xpEarnedInCurrentLevel = $totalXp - $xpRequiredForCurrentLevel;
            $xpNeededToLevelUp = $xpRequiredForNextLevel - $xpRequiredForCurrentLevel; // 100 XP
            $xpRemainingToLevelUp = max(0, $xpRequiredForNextLevel - $totalXp);

            // Calculate progress percentage for level 0
            if ($xpNeededToLevelUp > 0) {
                $levelProgressPercent = round(($xpEarnedInCurrentLevel / $xpNeededToLevelUp) * 100, 1);
            } else {
                $levelProgressPercent = 100;
            }

            return [
                'current_level' => 0,
                'next_level' => 1,
                'total_xp' => $totalXp,
                'xp_at_level_start' => $xpRequiredForCurrentLevel,
                'xp_for_next_level' => $xpRequiredForNextLevel,
                'xp_in_current_level' => $xpEarnedInCurrentLevel,
                'xp_needed_to_level_up' => $xpNeededToLevelUp,
                'xp_remaining_to_level_up' => $xpRemainingToLevelUp,
                'level_progress_percent' => $levelProgressPercent,
            ];
        }

        // Step 3: Calculate XP thresholds for level progression
        // Example: If user is Level 3, they need 400 XP to be at Level 3
        $xpRequiredForCurrentLevel = $this->getXpForLevel($userLevel);

        // Example: To reach Level 4, user needs 900 XP total
        $xpRequiredForNextLevel = $this->getXpForLevel($userLevel + 1);

        // Step 4: Calculate how much XP user has earned in their current level
        // Example: User has 500 XP total, Level 3 starts at 400 XP
        // So they have 100 XP progress in Level 3
        $xpEarnedInCurrentLevel = $totalXp - $xpRequiredForCurrentLevel;

        // Step 5: Calculate how much XP is needed to level up (total range for current level)
        // Example: Level 4 needs 900 XP, Level 3 starts at 400 XP
        // So the total XP range for Level 3 is 500 XP
        $xpNeededToLevelUp = $xpRequiredForNextLevel - $xpRequiredForCurrentLevel;

        // Step 6: Calculate remaining XP needed to reach next level
        // Example: User has 270 XP total, needs 400 XP for Level 3
        // So they need 130 more XP to level up
        $xpRemainingToLevelUp = max(0, $xpRequiredForNextLevel - $totalXp);

        // Step 7: Calculate progress percentage (0-100%)
        // Example: User has 100 XP progress, needs 500 XP total range
        // So they are 20% complete (100 / 500 * 100 = 20%)
        if ($xpNeededToLevelUp > 0) {
            $levelProgressPercent = round(($xpEarnedInCurrentLevel / $xpNeededToLevelUp) * 100, 1);
        } else {
            // User is at max level or calculation error
            $levelProgressPercent = 100;
        }

        return [
            'current_level' => $userLevel,
            'next_level' => $userLevel + 1,
            'total_xp' => $totalXp,
            'xp_at_level_start' => $xpRequiredForCurrentLevel,
            'xp_for_next_level' => $xpRequiredForNextLevel,
            'xp_in_current_level' => $xpEarnedInCurrentLevel,
            'xp_needed_to_level_up' => $xpNeededToLevelUp,
            'xp_remaining_to_level_up' => $xpRemainingToLevelUp,
            'level_progress_percent' => $levelProgressPercent,
        ];
    }
}