<?php

namespace App\Livewire\Achievements;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class UnlockedAchievements extends Component
{
    use WithPagination;

    public $totalPoints;

    public function mount(): void
    {
        $this->totalPoints = Auth::user()->achievements()
            ->whereNotNull('unlocked_at')
            ->sum('points_reward');
    }

    public function render()
    {
        $achievements = Auth::user()->achievements()
            ->whereNotNull('user_achievements.unlocked_at')
            ->withPivot(['unlocked_at'])
            ->orderBy('user_achievements.unlocked_at', 'desc')
            ->paginate(12);

        return view('livewire.achievements.unlocked-achievements', [
            'achievements' => $achievements,
        ])->layout('layouts.app');
    }
}