<?php
namespace App\Services;

use App\Models\{User, Habit, HabitLog};
use App\Enums\HabitDifficulty;

class PointsService
{
    public function awardPoints(
        User $user, 
        Habit $habit, 
        int $pomodorosUsed = 0,
        bool $isFirstTimeToday = false
    ): int {
        $basePoints = $habit->points_reward;
        
        // Bonus por racha
        $streakBonus = $this->calculateStreakBonus($habit->current_streak);
        
        // Bonus por pomodoros
        $pomodoroBonus = $pomodorosUsed > 0 ? ($pomodorosUsed * 5) : 0;
        
        // Bonus por completar todos los hábitos del día
        $dailyCompletionBonus = $this->checkDailyCompletion($user) ? 50 : 0;
        
        // Bonus por first time today (motivación temprana)
        $earlyBirdBonus = $isFirstTimeToday && now()->hour < 10 ? 10 : 0;
        
        $totalPoints = $basePoints + $streakBonus + $pomodoroBonus + 
                       $dailyCompletionBonus + $earlyBirdBonus;
        
        // XP = puntos
        $this->awardXP($user, $totalPoints);
        
        // Ensure user has stats
        if (!$user->stats) {
            $user->stats()->create([
                'total_points' => 0,
                'available_points' => 0,
                'weekly_points' => 0,
                'monthly_points' => 0,
                'current_global_streak' => 0,
                'best_global_streak' => 0,
                'week_start' => now()->startOfWeek(),
                'month_start' => now()->startOfMonth(),
            ]);
            $user->refresh();
        }
        
        $user->stats->increment('total_points', $totalPoints);
        $user->stats->increment('available_points', $totalPoints);
        $user->stats->increment('weekly_points', $totalPoints);
        $user->stats->increment('monthly_points', $totalPoints);
        
        return $totalPoints;
    }
    
    private function calculateStreakBonus(int $streak): int
    {
        return match(true) {
            $streak >= 100 => 200,
            $streak >= 50 => 100,
            $streak >= 30 => 50,
            $streak >= 14 => 25,
            $streak >= 7 => 10,
            default => 0,
        };
    }
    
    private function checkDailyCompletion(User $user): bool
    {
        $scheduledToday = $user->habits()
            ->where('is_active', true)
            ->get()
            ->filter->isScheduledForToday();
            
        $completedToday = $scheduledToday->filter->isCompletedToday();
        
        return $scheduledToday->count() > 0 && 
               $scheduledToday->count() === $completedToday->count();
    }
    
    public function spendPoints(User $user, int $amount): bool
    {
        if ($user->stats->available_points < $amount) {
            return false;
        }
        
        $user->stats->decrement('available_points', $amount);
        return true;
    }
    
    public function awardXP(User $user, int $xp): void
    {
        $level = $user->level;
        
        // Ensure user has a level record
        if (!$level) {
            $level = $user->level()->create([
                'current_level' => 1,
                'current_xp' => 0,
                'total_xp' => 0,
            ]);
        }
        
        $level->increment('current_xp', $xp);
        $level->increment('total_xp', $xp);
        
        // Check for level up
        // Calculate required XP dynamically: level * 100
        $requiredXp = $level->current_level * 100;
        
        while ($level->current_xp >= $requiredXp) {
            $overflow = $level->current_xp - $requiredXp;
            $level->increment('current_level');
            $level->current_xp = $overflow;
            
            // Recalculate required XP for new level
            $requiredXp = $level->current_level * 100;
            
            // Award bonus points for leveling up
            $bonusPoints = $level->current_level * 50;
            
            // Ensure user has stats
            if ($user->stats) {
                $user->stats->increment('available_points', $bonusPoints);
            }
            
            // TODO: Create UserLeveledUp event for celebration
            // event(new \App\Events\UserLeveledUp($user, $level->current_level));
        }
        
        $level->save();
    }
}