<?php

namespace App\Services;

use App\Models\{User, Achievement};
use App\Enums\AchievementType;

class AchievementService
{
    public function checkAndUnlock(User $user, AchievementType $type, int $value): void
    {
        $achievements = Achievement::where('requirement_type', $type->value)
            ->where('requirement_value', '<=', $value)
            ->get();
        
        foreach ($achievements as $achievement) {
            $userAchievement = $user->achievements()
                ->where('achievement_id', $achievement->id)
                ->first();
            
            if (!$userAchievement || !$userAchievement->pivot->unlocked_at) {
                $this->unlockAchievement($user, $achievement);
            }
        }
    }
    
    private function unlockAchievement(User $user, Achievement $achievement): void
    {
        $user->achievements()->syncWithoutDetaching([
            $achievement->id => [
                'unlocked_at' => now(),
                'progress' => $achievement->requirement_value,
            ]
        ]);
        
        // Award points
        $user->stats->increment('available_points', $achievement->points_reward);
        $user->stats->increment('total_points', $achievement->points_reward);
        
        // Dispatch event for notification
        event(new \App\Events\AchievementUnlocked($user, $achievement));
    }
    
    public function updateProgress(User $user, AchievementType $type, int $currentValue): void
    {
        $achievements = Achievement::where('requirement_type', $type->value)
            ->where('requirement_value', '>', $currentValue)
            ->get();
        
        foreach ($achievements as $achievement) {
            $user->achievements()->syncWithoutDetaching([
                $achievement->id => [
                    'progress' => $currentValue,
                ]
            ]);
        }
    }
}