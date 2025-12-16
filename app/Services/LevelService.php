<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserLevel;
use App\Models\XPTransaction;
use App\Events\UserLeveledUp;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LevelService
{
    public const MILESTONE_LEVELS = [10, 25, 50, 75, 100];

    /**
     * Award XP to a user and handle level ups
     */
    public function awardXP(User $user, int $xp, string $sourceType, ?string $sourceId = null, ?string $sourceName = null): array
    {
        $result = [
            'xp_awarded' => $xp,
            'leveled_up' => false,
            'new_level' => null,
            'bonus_points' => 0,
            'is_milestone' => false,
        ];

        if ($xp <= 0) {
            return $result;
        }

        return DB::transaction(function () use ($user, $xp, $sourceType, $sourceId, $sourceName, $result) {
            // Ensure user has a level record
            $level = $user->level ?? $user->level()->create([
                'current_level' => 1,
                'current_xp' => 0,
                'total_xp' => 0,
            ]);

            // Log the XP transaction
            XPTransaction::create([
                'user_id' => $user->id,
                'amount' => $xp,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'source_name' => $sourceName ?? $this->getDefaultSourceName($sourceType),
            ]);

            // Update XP
            $level->increment('current_xp', $xp);
            $level->increment('total_xp', $xp);
            
            // Sync XP with available points (XP earned = points earned)
            if ($user->stats) {
                $user->stats->increment('available_points', $xp);
                $user->stats->increment('total_points', $xp);
            }

            // Check for level up
            $requiredXp = $this->getRequiredXPForLevel($level->current_level);
            $oldLevel = $level->current_level;

            while ($level->current_xp >= $requiredXp) {
                $overflow = $level->current_xp - $requiredXp;
                $level->increment('current_level');
                $level->current_xp = $overflow;

                // Calculate bonus
                $bonus = $this->getLevelBonus($level->current_level);
                $isMilestone = $this->isMilestoneLevel($level->current_level);

                // Log bonus transaction
                XPTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $bonus,
                    'source_type' => $isMilestone ? XPTransaction::SOURCE_MILESTONE_BONUS : XPTransaction::SOURCE_LEVEL_BONUS,
                    'source_id' => (string) $level->current_level,
                    'source_name' => $isMilestone 
                        ? "¡Milestone nivel {$level->current_level}!" 
                        : "Subiste al nivel {$level->current_level}",
                ]);

                // Award bonus to available points
                if ($user->stats) {
                    $user->stats->increment('available_points', $bonus);
                }

                $result['leveled_up'] = true;
                $result['new_level'] = $level->current_level;
                $result['bonus_points'] += $bonus;
                $result['is_milestone'] = $isMilestone;

                // Recalculate for next potential level
                $requiredXp = $this->getRequiredXPForLevel($level->current_level);
            }

            $level->save();

            // Dispatch event if leveled up
            if ($result['leveled_up']) {
                event(new UserLeveledUp(
                    $user,
                    $level->current_level,
                    $result['bonus_points'],
                    $result['is_milestone'],
                    $level->level_title
                ));
            }

            return $result;
        });
    }

    /**
     * Calculate level from total XP
     */
    public function calculateLevelFromXP(int $totalXP): int
    {
        $level = 1;
        $xpRemaining = $totalXP;

        while ($xpRemaining >= $this->getRequiredXPForLevel($level)) {
            $xpRemaining -= $this->getRequiredXPForLevel($level);
            $level++;
        }

        return $level;
    }

    /**
     * Get required XP for a specific level
     * Formula: level * 100
     */
    public function getRequiredXPForLevel(int $level): int
    {
        return $level * 100;
    }

    /**
     * Get bonus points for reaching a level
     * Normal: level * 50
     * Milestone: level * 100 (double)
     */
    public function getLevelBonus(int $level): int
    {
        $baseBonus = $level * 50;
        
        if ($this->isMilestoneLevel($level)) {
            return $baseBonus * 2; // Double for milestones
        }

        return $baseBonus;
    }

    /**
     * Check if a level is a milestone level
     */
    public function isMilestoneLevel(int $level): bool
    {
        return in_array($level, self::MILESTONE_LEVELS);
    }

    /**
     * Get milestone badges for a user's current level
     */
    public function getMilestoneBadges(int $currentLevel): array
    {
        $badges = [];

        foreach (self::MILESTONE_LEVELS as $milestone) {
            if ($currentLevel >= $milestone) {
                $badges[] = [
                    'level' => $milestone,
                    'icon' => $this->getMilestoneIcon($milestone),
                    'name' => $this->getMilestoneName($milestone),
                    'achieved' => true,
                ];
            } else {
                $badges[] = [
                    'level' => $milestone,
                    'icon' => '🔒',
                    'name' => $this->getMilestoneName($milestone),
                    'achieved' => false,
                ];
            }
        }

        return $badges;
    }

    /**
     * Get XP summary for a user
     */
    public function getXPSummary(User $user): array
    {
        return [
            'today' => XPTransaction::forUser($user->id)->today()->sum('amount'),
            'this_week' => XPTransaction::forUser($user->id)->thisWeek()->sum('amount'),
            'this_month' => XPTransaction::forUser($user->id)->thisMonth()->sum('amount'),
            'total' => $user->level?->total_xp ?? 0,
        ];
    }

    /**
     * Get recent XP transactions for a user
     */
    public function getRecentTransactions(User $user, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return XPTransaction::forUser($user->id)
            ->recent($limit)
            ->get();
    }

    /**
     * Get milestone icon
     */
    private function getMilestoneIcon(int $level): string
    {
        return match ($level) {
            10 => '🥉',
            25 => '🥈',
            50 => '🥇',
            75 => '💎',
            100 => '👑',
            default => '⭐',
        };
    }

    /**
     * Get milestone name
     */
    private function getMilestoneName(int $level): string
    {
        return match ($level) {
            10 => 'Iniciado',
            25 => 'Dedicado',
            50 => 'Experto',
            75 => 'Maestro',
            100 => 'Leyenda',
            default => "Nivel $level",
        };
    }

    /**
     * Get default source name for a source type
     */
    private function getDefaultSourceName(string $sourceType): string
    {
        return match ($sourceType) {
            XPTransaction::SOURCE_HABIT => 'Hábito completado',
            XPTransaction::SOURCE_POMODORO => 'Pomodoro completado',
            XPTransaction::SOURCE_LEVEL_BONUS => 'Bonus de nivel',
            XPTransaction::SOURCE_MILESTONE_BONUS => 'Bonus milestone',
            XPTransaction::SOURCE_STREAK_BONUS => 'Bonus de racha',
            XPTransaction::SOURCE_DAILY_COMPLETION => 'Todos los hábitos del día',
            XPTransaction::SOURCE_EARLY_BIRD => 'Madrugador',
            default => 'XP ganado',
        };
    }
}
