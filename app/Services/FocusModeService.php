<?php

namespace App\Services;

use App\Models\{User, FocusMode};
use Carbon\Carbon;

class FocusModeService
{
    public function isActive(User $user): bool
    {
        $currentTime = now()->format('H:i:s');
        
        return $user->focusModes()
            ->where('is_active', true)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->exists();
    }
    
    public function getActiveFocusMode(User $user): ?FocusMode
    {
        $currentTime = now()->format('H:i:s');
        
        return $user->focusModes()
            ->where('is_active', true)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->first();
    }
    
    public function canAccessReward(User $user, string $rewardCategory): bool
    {
        $focusMode = $this->getActiveFocusMode($user);
        
        if (!$focusMode) {
            return true;
        }
        
        return !in_array($rewardCategory, $focusMode->blocked_categories);
    }
}

