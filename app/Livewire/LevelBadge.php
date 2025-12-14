<?php

namespace App\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Component;

class LevelBadge extends Component
{
    protected $listeners = ['xp-gained' => '$refresh'];

    #[Computed]
    public function userLevel()
    {
        return auth()->user()?->level;
    }

    #[Computed]
    public function currentLevel(): int
    {
        return $this->userLevel?->current_level ?? 1;
    }

    #[Computed]
    public function currentXP(): int
    {
        return $this->userLevel?->current_xp ?? 0;
    }

    #[Computed]
    public function requiredXP(): int
    {
        return $this->userLevel?->required_xp ?? 100;
    }

    #[Computed]
    public function progressPercentage(): float
    {
        return min(100, $this->userLevel?->progress_percentage ?? 0);
    }

    #[Computed]
    public function levelTitle(): string
    {
        return $this->userLevel?->level_title ?? 'Principiante 🌱';
    }

    public function render()
    {
        return view('livewire.level-badge');
    }
}
