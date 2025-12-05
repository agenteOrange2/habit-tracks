<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class HabitReminders extends Component
{
    public $habits;

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

    public function toggleReminder($habitId): void
    {
        $habit = Auth::user()->habits()->findOrFail($habitId);
        
        $habit->update([
            'reminder_enabled' => !$habit->reminder_enabled,
        ]);

        $this->loadHabits();
    }

    public function updateReminderTime($habitId, $time): void
    {
        $habit = Auth::user()->habits()->findOrFail($habitId);
        
        $habit->update([
            'reminder_time' => $time,
        ]);

        session()->flash('success', 'Horario de recordatorio actualizado.');
        
        $this->loadHabits();
    }

    public function render()
    {
        return view('livewire.settings.habit-reminders')
            ->layout('layouts.app');
    }
}