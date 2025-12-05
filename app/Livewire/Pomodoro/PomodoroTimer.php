<?php

namespace App\Livewire\Pomodoro;

use Livewire\Component;
use App\Models\{Habit, PomodoroSession};
use App\Services\EnergyService;
use Illuminate\Support\Facades\Auth;

class PomodoroTimer extends Component
{
    public $selectedHabit = null;
    public $duration = 25; // minutos
    public $isRunning = false;
    public $currentSession = null;
    public $habits;

    protected $listeners = [
        'timerCompleted' => 'handleTimerCompleted',
    ];

    public function mount(): void
    {
        $this->loadHabits();
    }

    public function loadHabits(): void
    {
        $this->habits = Auth::user()->habits()
            ->where('is_active', true)
            ->get();
    }

    public function startTimer(EnergyService $energyService): void
    {
        $user = Auth::user();

        // Verificar energía
        if (!$energyService->consumeForPomodoro($user)) {
            session()->flash('error', '⚡ No tienes suficiente energía. Descansa un poco.');
            return;
        }

        $this->currentSession = PomodoroSession::create([
            'user_id' => $user->id,
            'habit_id' => $this->selectedHabit,
            'duration_minutes' => $this->duration,
            'started_at' => now(),
        ]);

        $this->isRunning = true;
    }

    public function stopTimer(): void
    {
        if ($this->currentSession) {
            $this->currentSession->update([
                'was_interrupted' => true,
                'completed_at' => now(),
            ]);

            $this->currentSession = null;
        }

        $this->isRunning = false;
    }

    public function handleTimerCompleted(): void
    {
        if ($this->currentSession) {
            $this->currentSession->update([
                'completed_at' => now(),
            ]);

            $user = Auth::user();
            $user->stats->increment('total_pomodoros');
            $user->stats->increment('total_focus_time', $this->duration);

            session()->flash('success', '🍅 ¡Pomodoro completado! ¡Gran trabajo!');

            $this->currentSession = null;
        }

        $this->isRunning = false;
        $this->dispatch('energyUpdated');
    }

    public function render()
    {
        return view('livewire.pomodoro.pomodoro-timer')
            ->layout('layouts.app');
    }
}