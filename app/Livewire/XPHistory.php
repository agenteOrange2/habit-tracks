<?php

namespace App\Livewire;

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
    public function milestoneBadges(): array
    {
        return $this->userLevel?->milestone_badges ?? [];
    }

    public function render()
    {
        return view('livewire.xp-history');
    }
}
