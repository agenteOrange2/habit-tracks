<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class PomodoroTimer extends Component
{
    public int $timer = 1500; // 25 minutes in seconds
    public bool $running = false;

    public function toggleTimer()
    {
        $this->running = !$this->running;
    }

    public function tick()
    {
        if ($this->running && $this->timer > 0) {
            $this->timer--;
        }

        if ($this->timer === 0) {
            $this->running = false;
        }
    }

    public function formatTime()
    {
        $minutes = floor($this->timer / 60);
        $seconds = $this->timer % 60;
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function render()
    {
        return view('livewire.dashboard.pomodoro-timer');
    }
}
