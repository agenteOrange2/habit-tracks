<?php

namespace App\Services;

use App\Models\{User, Habit, HabitLog};
use Carbon\Carbon;

class StreakService
{
    public function updateStreak(Habit $habit): void
    {
        $yesterday = Carbon::yesterday();
        $wasCompletedYesterday = $habit->logs()
            ->whereDate('completed_date', $yesterday)
            ->exists();
        
        if ($wasCompletedYesterday) {
            $habit->increment('current_streak');
        } else {
            $daysAgo = $this->getLastCompletionDaysAgo($habit);
            
            if ($daysAgo > 1) {
                $habit->current_streak = 1;
            } else {
                $habit->increment('current_streak');
            }
        }
        
        if ($habit->current_streak > $habit->best_streak) {
            $habit->best_streak = $habit->current_streak;
        }
        
        $habit->save();
    }
    
    private function getLastCompletionDaysAgo(Habit $habit): int
    {
        $lastLog = $habit->logs()
            ->orderBy('completed_date', 'desc')
            ->first();
            
        return $lastLog 
            ? Carbon::parse($lastLog->completed_date)->diffInDays(Carbon::today())
            : 999;
    }
    
    public function updateGlobalStreak(User $user): void
    {
        $yesterday = Carbon::yesterday();
        $hadActivityYesterday = HabitLog::where('user_id', $user->id)
            ->whereDate('completed_date', $yesterday)
            ->exists();
        
        if ($hadActivityYesterday) {
            $user->stats->increment('current_global_streak');
        } else {
            $daysSinceLastActivity = $this->getDaysSinceLastActivity($user);
            
            if ($daysSinceLastActivity > 1) {
                $user->stats->current_global_streak = 1;
            } else {
                $user->stats->increment('current_global_streak');
            }
        }
        
        if ($user->stats->current_global_streak > $user->stats->best_global_streak) {
            $user->stats->best_global_streak = $user->stats->current_global_streak;
        }
        
        $user->stats->last_activity_date = Carbon::today();
        $user->stats->save();
    }
    
    private function getDaysSinceLastActivity(User $user): int
    {
        $lastLog = HabitLog::where('user_id', $user->id)
            ->orderBy('completed_date', 'desc')
            ->first();
            
        return $lastLog 
            ? Carbon::parse($lastLog->completed_date)->diffInDays(Carbon::today())
            : 999;
    }
}