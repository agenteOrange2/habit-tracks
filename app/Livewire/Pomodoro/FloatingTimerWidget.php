<?php

namespace App\Livewire\Pomodoro;

use Livewire\Component;
use Livewire\Attributes\On;

class FloatingTimerWidget extends Component
{
    public bool $visible = false;
    public bool $collapsed = false;
    public array $position = ['x' => 20, 'y' => 20];

    public function mount(): void
    {
        // Widget visibility is controlled by Alpine.js store
        // Position is loaded from localStorage in the view
    }

    public function toggle(): void
    {
        $this->visible = !$this->visible;
    }

    public function updatePosition(int $x, int $y): void
    {
        $this->position = ['x' => $x, 'y' => $y];
    }

    public function syncState(): void
    {
        // Refresh component to sync with Alpine.js store
        $this->dispatch('widget-synced');
    }

    #[On('timer-started')]
    public function onTimerStarted(): void
    {
        $this->visible = true;
    }

    #[On('timer-paused')]
    public function onTimerPaused(): void
    {
        // Keep visible when paused
    }

    #[On('timer-resumed')]
    public function onTimerResumed(): void
    {
        // Keep visible when resumed
    }

    #[On('timer-completed')]
    public function onTimerCompleted(): void
    {
        // Hide widget after completion (unless auto-starting break)
        // This is handled by Alpine.js store
    }

    #[On('timer-stopped')]
    public function onTimerStopped(): void
    {
        $this->visible = false;
    }

    #[On('break-started')]
    public function onBreakStarted(): void
    {
        $this->visible = true;
    }

    public function render()
    {
        return view('livewire.pomodoro.floating-timer-widget');
    }
}
