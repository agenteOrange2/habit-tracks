<?php
// app/Livewire/Habits/CreateHabit.php

namespace App\Livewire\Habits;

use Livewire\Component;
use App\Models\Habit;
use App\Models\Category;
use App\Models\Difficulty;
use App\Enums\{HabitDifficulty, HabitFrequency, HabitCategory};
use Illuminate\Support\Facades\Auth;

class CreateHabit extends Component
{
    public $name = '';
    public $description = '';
    public $category_id = null;
    public $difficulty_id = null;
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
            'category_id' => 'required|exists:categories,id',
            'difficulty_id' => 'required|exists:difficulties,id',
            'frequency' => 'required|string',
            'selectedDays' => 'array',
            'time' => 'nullable|date_format:H:i',
            'color' => 'required|string',
            'icon' => 'required|string',
            'estimated_pomodoros' => 'nullable|integer|min:1',
            'reminder_enabled' => 'boolean',
            'reminder_time' => $this->reminder_enabled ? 'required|date_format:H:i' : 'nullable',
        ];
    }

    public function save()
    {
        $this->validate();

        $schedule = null;
        if ($this->frequency === 'weekly' || $this->frequency === 'custom') {
            $schedule = [
                'days' => $this->selectedDays,
                'time' => $this->time,
            ];
        }

        // Get category and difficulty to retrieve data
        $category = Category::findOrFail($this->category_id);
        $difficulty = Difficulty::findOrFail($this->difficulty_id);

        $habit = Auth::user()->habits()->create([
            'name' => $this->name,
            'description' => $this->description,
            // New dynamic fields
            'category_id' => $this->category_id,
            'difficulty_id' => $this->difficulty_id,
            // Old enum fields (for backward compatibility during transition)
            'category' => $category->slug,
            'difficulty' => $difficulty->slug,
            'frequency' => $this->frequency,
            'schedule' => $schedule,
            'is_recurring' => true,
            'is_active' => true,
            'points_reward' => $difficulty->points,
            'color' => $this->color,
            'icon' => $this->icon,
            'estimated_pomodoros' => $this->estimated_pomodoros,
            'reminder_enabled' => $this->reminder_enabled,
            'reminder_time' => $this->reminder_enabled && !empty($this->reminder_time) ? $this->reminder_time : null,
        ]);

        $this->dispatch('habitCreated');

        session()->flash('success', '¡Hábito creado exitosamente! 🎉');

        return $this->redirect(route('admin.habits.index'), navigate: true);
    }

    public function render()
    {
        // Load only active categories and difficulties from database
        // Ordered by their display order
        return view('livewire.habits.create-habit', [
            'categories' => Category::active()->ordered()->get(),
            'difficulties' => Difficulty::active()->ordered()->get(),
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
        ])->layout('components.layouts.app');
    }
}