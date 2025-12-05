<?php

namespace App\Livewire\Habits;

use Livewire\Component;
use App\Models\Habit;
use App\Enums\{HabitDifficulty, HabitFrequency, HabitCategory};

class EditHabit extends Component
{
    public Habit $habit;
    public $name;
    public $description;
    public $category;
    public $difficulty;
    public $frequency;
    public $selectedDays = [];
    public $time;
    public $color;
    public $icon;
    public $estimated_pomodoros;
    public $reminder_enabled;
    public $reminder_time;
    public $is_active;

    public function mount(Habit $habit): void
    {
        $this->authorize('update', $habit);

        $this->habit = $habit;
        $this->name = $habit->name;
        $this->description = $habit->description;
        $this->category = $habit->category;
        $this->difficulty = $habit->difficulty->value;
        $this->frequency = $habit->frequency->value;
        $this->selectedDays = $habit->schedule['days'] ?? [];
        $this->time = $habit->schedule['time'] ?? '09:00';
        $this->color = $habit->color;
        $this->icon = $habit->icon;
        $this->estimated_pomodoros = $habit->estimated_pomodoros;
        $this->reminder_enabled = $habit->reminder_enabled;
        $this->reminder_time = $habit->reminder_time?->format('H:i');
        $this->is_active = $habit->is_active;
    }

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
            'is_active' => 'boolean',
        ];
    }

    public function update(): void
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

        $this->habit->update([
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'difficulty' => $this->difficulty,
            'frequency' => $this->frequency,
            'schedule' => $schedule,
            'points_reward' => $difficulty->points(),
            'color' => $this->color,
            'icon' => $this->icon,
            'estimated_pomodoros' => $this->estimated_pomodoros,
            'reminder_enabled' => $this->reminder_enabled,
            'reminder_time' => $this->reminder_time,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('habitUpdated');

        session()->flash('success', '¡Hábito actualizado exitosamente! ✅');

        return $this->redirect(route('habits.index'), navigate: true);
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->habit);

        $this->habit->delete();

        $this->dispatch('habitDeleted');

        session()->flash('success', 'Hábito eliminado correctamente.');

        return $this->redirect(route('habits.index'), navigate: true);
    }

    public function archive(): void
    {
        $this->habit->update([
            'is_active' => false,
            'archived_at' => now(),
        ]);

        session()->flash('success', 'Hábito archivado correctamente.');

        return $this->redirect(route('habits.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.habits.edit-habit', [
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