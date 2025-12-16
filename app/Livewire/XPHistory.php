<?php

namespace App\Livewire;

use App\Models\MilestoneBadge;
use App\Services\LevelService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class XPHistory extends Component
{
    protected LevelService $levelService;

    public function boot(LevelService $levelService): void
    {
        $this->levelService = $levelService;
    }

    #[Computed]
    public function xpSummary(): array
    {
        return $this->levelService->getXPSummary(auth()->user());
    }

    #[Computed]
    public function recentTransactions()
    {
        return $this->levelService->getRecentTransactions(auth()->user(), 10);
    }

    #[Computed]
    public function userLevel()
    {
        return auth()->user()?->level;
    }

    #[Computed]
    public function milestoneBadges()
    {
        $user = auth()->user();
        
        // If user has no badges in DB, create defaults
        if ($user->milestoneBadges()->count() === 0) {
            MilestoneBadge::createDefaultsForUser($user);
        }
        
        return $user->milestoneBadges()->ordered()->get()->map(function ($badge) use ($user) {
            return [
                'id' => $badge->id,
                'name' => $badge->name,
                'icon' => $badge->icon,
                'level' => $badge->level_required,
                'description' => $badge->description,
                'achieved' => $badge->isAchievedBy($user),
                'is_default' => $badge->is_default,
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.xp-history');
    }
}
