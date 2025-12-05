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
        $level->increment('current_xp', $xp);
        $level->increment('total_xp', $xp);
        
        // Check for level up
        while ($level->current_xp >= $level->required_xp) {
            $overflow = $level->current_xp - $level->required_xp;
            $level->increment('current_level');
            $level->current_xp = $overflow;
            
            // Award bonus points for leveling up
            $bonusPoints = $level->current_level * 50;
            $user->stats->increment('available_points', $bonusPoints);
            
            // Dispatch event for celebration
            event(new \App\Events\UserLeveledUp($user, $level->current_level));
        }
        
        $level->save();
    }
}