<?php

namespace App\Listeners;

use App\Models\MilestoneBadge;
use Illuminate\Auth\Events\Registered;

class CreateDefaultMilestoneBadges
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;
        
        // Only create if user doesn't have any badges yet
        if ($user->milestoneBadges()->count() === 0) {
            MilestoneBadge::createDefaultsForUser($user);
        }
    }
}
