<?php

namespace App\Livewire\Achievements;

use Livewire\Component;
use App\Models\Achievement;
use Illuminate\Support\Facades\Auth;

class AchievementProgress extends Component
{
    public $totalAchievements;
    public $unlockedAchievements;
    public $progressPercentage;
    public $recentUnlocks;

    protected $listeners = [
        'achievementUnlocked' => 'refresh',
    ];

    public function mount(): void
    {
        $this->refresh();
    }

    public function refresh(): void
    {
        $user = Auth::user();
        
        $this->totalAchievements = Achievement::count();
        $this->unlockedAchievements = $user->achievements()
            ->whereNotNull('unlocked_at')
            ->count();
        
        $this->progressPercentage = $this->totalAchievements > 0
            ? round(($this->unlockedAchievements / $this->totalAchievements) * 100)
            : 0;

        $this->recentUnlocks = $user->achievements()
            ->whereNotNull('unlocked_at')
            ->orderBy('user_achievements.unlocked_at', 'desc')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.achievements.achievement-progress');
    }
}