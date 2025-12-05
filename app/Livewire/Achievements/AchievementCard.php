<?php

namespace App\Livewire\Achievements;

use Livewire\Component;
use App\Models\Achievement;
use Illuminate\Support\Facades\Auth;

class AchievementCard extends Component
{
    public Achievement $achievement;
    public $isUnlocked = false;
    public $progress = 0;
    public $progressPercentage = 0;

    public function mount(): void
    {
        $this->loadProgress();
    }

    public function loadProgress(): void
    {
        $user = Auth::user();
        
        $this->isUnlocked = $this->achievement->isUnlockedBy($user);
        $this->progress = $this->achievement->getProgressFor($user);
        
        if (!$this->isUnlocked && $this->achievement->requirement_value > 0) {
            $this->progressPercentage = min(
                ($this->progress / $this->achievement->requirement_value) * 100,
                100
            );
        }
    }

    public function render()
    {
        return view('livewire.achievements.achievement-card');
    }
}