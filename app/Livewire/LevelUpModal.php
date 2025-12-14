<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class LevelUpModal extends Component
{
    public bool $show = false;
    public int $newLevel = 1;
    public int $bonusPoints = 0;
    public bool $isMilestone = false;
    public string $levelTitle = '';

    #[On('user-leveled-up')]
    public function showCelebration(array $data): void
    {
        $this->newLevel = $data['new_level'] ?? 1;
        $this->bonusPoints = $data['bonus_points'] ?? 0;
        $this->isMilestone = $data['is_milestone'] ?? false;
        $this->levelTitle = $data['level_title'] ?? '';
        $this->show = true;
    }

    public function dismiss(): void
    {
        $this->show = false;
        $this->dispatch('xp-gained'); // Refresh level badge
    }

    public function render()
    {
        return view('livewire.level-up-modal');
    }
}
