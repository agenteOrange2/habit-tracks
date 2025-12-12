<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

/**
 * RecentAchievements Component
 * 
 * Displays the 2-3 most recently unlocked achievements for the authenticated user.
 * Automatically refreshes when the 'achievementUnlocked' event is dispatched.
 * 
 * Usage in Blade:
 * <livewire:dashboard.recent-achievements />
 * 
 * Requirements: 5.1, 5.2, 5.5
 */
class RecentAchievements extends Component
{
    public Collection $achievements;

    public function mount(): void
    {
        $this->loadAchievements();
    }

    #[On('achievementUnlocked')]
    public function refreshAchievements(): void
    {
        $this->loadAchievements();
    }

    protected function loadAchievements(): void
    {
        $this->achievements = Auth::user()
            ->achievements()
            ->wherePivot('unlocked_at', '!=', null)
            ->orderByPivot('unlocked_at', 'desc')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.recent-achievements');
    }
}
