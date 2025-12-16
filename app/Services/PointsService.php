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
        
        // Bonus por completar todos los hábitos del día (solo una vez por día)
        $dailyCompletionCheck = $this->checkDailyCompletion($user);
        $hasBonus = $this->hasReceivedDailyCompletionBonus($user);

        \Log::info('Daily completion bonus check', [
            'user_id' => $user->id,
            'daily_completion_check' => $dailyCompletionCheck,
            'has_received_bonus' => $hasBonus,
            'will_award_bonus' => $dailyCompletionCheck && !$hasBonus,
        ]);

        if ($dailyCompletionCheck && !$hasBonus) {
            $dailyBonus = 50;
            $this->levelService->awardXP(
                $user,
                $dailyBonus,
                XPTransaction::SOURCE_DAILY_COMPLETION,
                null,
                '¡Todos los hábitos del día completados!'
            );
            $totalPoints += $dailyBonus;

            \Log::info('Daily completion bonus awarded', [
                'user_id' => $user->id,
                'bonus_amount' => $dailyBonus,
            ]);
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

        // NOTE: Ya NO actualizamos user->stats aquí porque LevelService::awardXP
        // ya registra las transacciones de XP. Los stats deben calcularse
        // desde las XPTransactions para evitar duplicación.

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

        \Log::info('Daily completion check', [
            'user_id' => $user->id,
            'scheduled_count' => $scheduledToday->count(),
            'completed_count' => $completedToday->count(),
            'scheduled_ids' => $scheduledToday->pluck('id')->toArray(),
            'completed_ids' => $completedToday->pluck('id')->toArray(),
        ]);

        return $scheduledToday->count() > 0 &&
               $scheduledToday->count() === $completedToday->count();
    }

    private function hasReceivedDailyCompletionBonus(User $user): bool
    {
        return XPTransaction::where('user_id', $user->id)
            ->where('source_type', XPTransaction::SOURCE_DAILY_COMPLETION)
            ->whereDate('created_at', today())
            ->exists();
    }
    
    public function spendPoints(User $user, int $amount): bool
    {
        return $this->spendPointsSafely($user, $amount);
    }
    
    /**
     * Spend points with database lock to prevent race conditions
     */
    public function spendPointsSafely(User $user, int $amount): bool
    {
        return \DB::transaction(function () use ($user, $amount) {
            // Lock the stats row to prevent race conditions
            $stats = $user->stats()->lockForUpdate()->first();
            
            if (!$stats || $stats->available_points < $amount) {
                return false;
            }
            
            $stats->decrement('available_points', $amount);
            return true;
        });
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
