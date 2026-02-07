<?php

namespace App\Services;

use App\Models\LevelConfiguration;

class LevelService
{
    /**
     * Get all level configurations from database
     *
     * @return array
     */
    private function getLevelConfigurations(): array
    {
        return LevelConfiguration::active()
            ->ordered()
            ->get()
            ->keyBy('level')
            ->toArray();
    }

    /**
     * Get level thresholds from database
     *
     * @return array Level => Threshold Amount
     */
    private function getLevelThresholds(): array
    {
        $configurations = $this->getLevelConfigurations();
        $thresholds = [];

        foreach ($configurations as $level => $config) {
            $thresholds[$level] = (float) $config['threshold'];
        }

        return $thresholds;
    }

    /**
     * Get tier definitions from database
     *
     * @return array
     */
    private function getTiers(): array
    {
        $configurations = $this->getLevelConfigurations();
        $tiers = [];

        foreach ($configurations as $config) {
            $tierName = $config['tier_name'];
            if (!isset($tiers[$tierName])) {
                $tierInfo = $config['tier_info'] ?? [];
                $tiers[$tierName] = [
                    'min' => $config['tier_min_level'],
                    'max' => $config['tier_max_level'],
                    'description' => $config['description'],
                    'tier_info' => $tierInfo,
                ];
            }
        }

        return $tiers;
    }

    /**
     * Calculate user level based on total accumulated funds
     *
     * @param float $totalFunds
     * @return int
     */
    public function calculateLevel(float $totalFunds): int
    {
        $levelThresholds = $this->getLevelThresholds();
        $level = 1;

        foreach ($levelThresholds as $levelNum => $threshold) {
            if ($totalFunds >= $threshold) {
                $level = $levelNum;
            } else {
                break;
            }
        }

        return $level;
    }

    /**
     * Get tier name for a given level
     *
     * @param int $level
     * @return string
     */
    public function getTierForLevel(int $level): string
    {
        $configurations = $this->getLevelConfigurations();
        
        if (isset($configurations[$level])) {
            return $configurations[$level]['tier_name'];
        }

        // Fallback: find tier by level range
        $tiers = $this->getTiers();
        foreach ($tiers as $tierName => $tierInfo) {
            if ($level >= $tierInfo['min'] && $level <= $tierInfo['max']) {
                return $tierName;
            }
        }

        return 'Bronze'; // Default fallback
    }

    /**
     * Get tier information
     *
     * @param string $tierName
     * @return array|null
     */
    public function getTierInfo(string $tierName): ?array
    {
        $tiers = $this->getTiers();
        return $tiers[$tierName] ?? null;
    }

    /**
     * Get threshold for a specific level
     *
     * @param int $level
     * @return float|null
     */
    public function getLevelThreshold(int $level): ?float
    {
        $levelThresholds = $this->getLevelThresholds();
        return $levelThresholds[$level] ?? null;
    }

    /**
     * Get next level threshold
     *
     * @param int $currentLevel
     * @return float|null
     */
    public function getNextLevelThreshold(int $currentLevel): ?float
    {
        $levelThresholds = $this->getLevelThresholds();
        $maxLevel = max(array_keys($levelThresholds));
        
        if ($currentLevel >= $maxLevel) {
            return null; // Max level reached
        }

        return $levelThresholds[$currentLevel + 1] ?? null;
    }

    /**
     * Calculate progress percentage to next level
     *
     * @param float $currentFunds
     * @param int $currentLevel
     * @return float
     */
    public function calculateProgressToNextLevel(float $currentFunds, int $currentLevel): float
    {
        $currentThreshold = $this->getLevelThreshold($currentLevel);
        $nextThreshold = $this->getNextLevelThreshold($currentLevel);

        if ($nextThreshold === null) {
            return 100.0; // Max level reached
        }

        if ($currentThreshold === null) {
            return 0.0;
        }

        $fundsProgress = $currentFunds - $currentThreshold;
        $thresholdDifference = $nextThreshold - $currentThreshold;

        if ($thresholdDifference <= 0) {
            return 100.0;
        }

        $progress = ($fundsProgress / $thresholdDifference) * 100;

        return min(100.0, max(0.0, $progress));
    }

    /**
     * Get level information including progress
     *
     * @param float $totalFunds
     * @return array
     */
    public function getLevelInfo(float $totalFunds): array
    {
        $level = $this->calculateLevel($totalFunds);
        $tier = $this->getTierForLevel($level);
        $tierInfo = $this->getTierInfo($tier);
        $currentThreshold = $this->getLevelThreshold($level);
        $nextThreshold = $this->getNextLevelThreshold($level);
        $progress = $this->calculateProgressToNextLevel($totalFunds, $level);

        return [
            'level' => $level,
            'tier' => $tier,
            'tier_description' => $tierInfo['description'] ?? '',
            'total_funds' => $totalFunds,
            'current_level_threshold' => $currentThreshold,
            'next_level_threshold' => $nextThreshold,
            'progress_percentage' => round($progress, 2),
            'funds_needed_for_next_level' => $nextThreshold ? max(0, $nextThreshold - $totalFunds) : 0,
            'is_max_level' => $nextThreshold === null,
        ];
    }

    /**
     * Get all tier information
     *
     * @return array
     */
    public function getAllTiers(): array
    {
        return $this->getTiers();
    }

    /**
     * Get all level thresholds
     *
     * @return array
     */
    public function getAllLevelThresholds(): array
    {
        return $this->getLevelThresholds();
    }

    /**
     * Get level configuration by level number
     *
     * @param int $level
     * @return array|null
     */
    public function getLevelConfiguration(int $level): ?array
    {
        $configurations = $this->getLevelConfigurations();
        return $configurations[$level] ?? null;
    }

    /**
     * Get all level configurations
     *
     * @return array
     */
    public function getAllLevelConfigurations(): array
    {
        return $this->getLevelConfigurations();
    }
}
