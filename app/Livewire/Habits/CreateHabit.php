<?php
// app/Livewire/Habits/CreateHabit.php

namespace App\Livewire\Habits;

use Livewire\Component;
use App\Models\Habit;
use App\Enums\{HabitDifficulty, HabitFrequency, HabitCategory};
use Illuminate\Support\Facades\Auth;

class CreateHabit extends Component
{
    public $name = '';
    public $description = '';
    public $category = 'productivity';
    public $difficulty = 'medium';
    public $frequency = 'daily';
    public $selectedDays = [];
    public $time = '09:00';
    public $color = '#3B82F6';
    public $icon = '📝';
    public $estimated_pomodoros = null;
    public $reminder_enabled = false;
    public $reminder_time = null;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string',
            'difficulty' => 'required|string',
            'frequency' => 'required|string',
            'selectedDays' => 'array',
            'time' => 'nullable|date_format:H:i',
            'color' => 'required|string',
            'icon' => 'required|string',
            'estimated_pomodoros' => 'nullable|integer|min:1',
            'reminder_enabled' => 'boolean',
            'reminder_time' => 'nullable|date_format:H:i',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $schedule = null;
        if ($this->frequency === 'weekly') {
            $schedule = [
                'days' => $this->selectedDays,
                'time' => $this->time,
            ];
        }

        $difficulty = HabitDifficulty::from($this->difficulty);

        $habit = Auth::user()->habits()->create([
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'difficulty' => $this->difficulty,
            'frequency' => $this->frequency,
            'schedule' => $schedule,
            'is_recurring' => true,
            'points_reward' => $difficulty->points(),
            'color' => $this->color,
            'icon' => $this->icon,
            'estimated_pomodoros' => $this->estimated_pomodoros,
            'reminder_enabled' => $this->reminder_enabled,
            'reminder_time' => $this->reminder_time,
        ]);

        $this->dispatch('habitCreated');

        session()->flash('success', '¡Hábito creado exitosamente! 🎉');

        return $this->redirect(route('habits.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.habits.create-habit', [
            'categories' => HabitCategory::cases(),
            'difficulties' => HabitDifficulty::cases(),
            'frequencies' => HabitFrequency::cases(),
            'daysOfWeek' => [
                0 => 'Domingo',
                1 => 'Lunes',
                2 => 'Martes',
                3 => 'Miércoles',
                4 => 'Jueves',
                5 => 'Viernes',
                6 => 'Sábado',
            ],
        ])->layout('layouts.app');
    }
}