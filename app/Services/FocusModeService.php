<?php

namespace App\Services;

use App\Models\{User, FocusMode};
use Illuminate\Support\Facades\Schema;

class FocusModeService
{
    public function isActive(User $user): bool
    {
        // Return false if focus_modes table doesn't exist
        if (!Schema::hasTable('focus_modes')) {
            return false;
        }

        $currentTime = now()->format('H:i:s');

        return $user->focusMode()
            ->where('is_active', true)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->exists();
    }

    public function getActiveFocusMode(User $user): ?FocusMode
    {
        // Return null if focus_modes table doesn't exist
        if (!Schema::hasTable('focus_modes')) {
            return null;
        }

        $currentTime = now()->format('H:i:s');

        return $user->focusMode()
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

