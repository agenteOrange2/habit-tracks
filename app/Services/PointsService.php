<?php

namespace App\Services;

use App\Models\{User, Habit, XPTransaction};

class PointsService
{
    protected LevelService $levelService;

    public function __construct(LevelService $levelService)
    {
        $this->levelService = $levelService;
    }

    public function awardPoints(
        User $user, 
        Habit $habit, 
        int $pomodorosUsed = 0,
        bool $isFirstTimeToday = false
    ): int {
        // Base points from habit's difficulty
        $basePoints = $habit->points_reward;
        
        // Ensure user has stats
        $this->ensureUserHasStats($user);
        
        // Award base XP for habit completion
        $this->levelService->awardXP(
            $user, 
            $basePoints, 
            XPTransaction::SOURCE_HABIT,
            (string) $habit->id,
            $habit->name
        );
        
        $totalPoints = $basePoints;
        
        // Bonus por racha
        $streakBonus = $this->calculateStreakBonus($habit->current_streak);
        if ($streakBonus > 0) {
            $this->levelService->awardXP(
                $user,
                $streakBonus,
                XPTransaction::SOURCE_STREAK_BONUS,
                (string) $habit->id,
                "Racha de {$habit->current_streak} días"
            );
            $totalPoints += $streakBonus;
        }
        
        // Bonus por pomodoros
        $pomodoroBonus = $pomodorosUsed > 0 ? ($pomodorosUsed * 5) : 0;
        if ($pomodoroBonus > 0) {
            $this->levelService->awardXP(
                $user,
                $pomodoroBonus,
                XPTransaction::SOURCE_POMODORO,
                null,
                "{$pomodorosUsed} pomodoros usados"
            );
            $totalPoints += $pomodoroBonus;
        }
        
        // Bonus por completar todos los hábitos del día
        if ($this->checkDailyCompletion($user)) {
            $dailyBonus = 50;
            $this->levelService->awardXP(
                $user,
                $dailyBonus,
                XPTransaction::SOURCE_DAILY_COMPLETION,
                null,
                '¡Todos los hábitos del día completados!'
            );
            $totalPoints += $dailyBonus;
        }
        
        // Bonus por first time today (motivación temprana)
        if ($isFirstTimeToday && now()->hour < 10) {
            $earlyBirdBonus = 10;
            $this->levelService->awardXP(
                $user,
                $earlyBirdBonus,
                XPTransaction::SOURCE_EARLY_BIRD,
                null,
                'Madrugador - antes de las 10am'
            );
            $totalPoints += $earlyBirdBonus;
        }
        
        // Update user stats
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
        if (!$user->stats || $user->stats->available_points < $amount) {
            return false;
        }
        
        $user->stats->decrement('available_points', $amount);
        return true;
    }

    private function ensureUserHasStats(User $user): void
    {
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
    }
}
