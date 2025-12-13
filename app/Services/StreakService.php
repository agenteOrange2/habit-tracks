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
        // Ensure user has stats
        if (!$user->stats) {
            $user->stats()->create([
                'total_points' => 0,
                'available_points' => 0,
                'current_global_streak' => 0,
                'best_global_streak' => 0,
            ]);
            $user->refresh();
        }

        $today = Carbon::today();
        $lastActivityDate = $user->stats->last_activity_date;
        
        // If already updated today, don't increment again
        if ($lastActivityDate && Carbon::parse($lastActivityDate)->isSameDay($today)) {
            return;
        }
        
        $yesterday = Carbon::yesterday();
        
        // Check if there was activity yesterday
        $hadActivityYesterday = $lastActivityDate && Carbon::parse($lastActivityDate)->isSameDay($yesterday);
        
        if ($hadActivityYesterday) {
            // Continue the streak
            $user->stats->increment('current_global_streak');
        } else {
            // Check how many days since last activity
            if ($lastActivityDate) {
                $daysSinceLastActivity = Carbon::parse($lastActivityDate)->diffInDays($today);
                
                if ($daysSinceLastActivity > 1) {
                    // Streak broken, start new streak
                    $user->stats->current_global_streak = 1;
                } else {
                    // First activity ever or same day (shouldn't happen due to check above)
                    $user->stats->increment('current_global_streak');
                }
            } else {
                // First activity ever
                $user->stats->current_global_streak = 1;
            }
        }
        
        // Update best streak if current is higher
        if ($user->stats->current_global_streak > $user->stats->best_global_streak) {
            $user->stats->best_global_streak = $user->stats->current_global_streak;
        }
        
        $user->stats->last_activity_date = $today;
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