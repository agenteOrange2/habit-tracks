<?php

namespace App\Livewire\Habits;

use Livewire\Component;
use App\Models\Habit;
use Carbon\Carbon;

class HabitStats extends Component
{
    public Habit $habit;
    public $stats = [];
    public $chartData = [];

    public function mount(Habit $habit): void
    {
        $this->authorize('view', $habit);
        
        $this->habit = $habit;
        $this->loadStats();
    }

    public function loadStats(): void
    {
        $logs = $this->habit->logs()
            ->orderBy('completed_date', 'desc')
            ->limit(30)
            ->get();

        $this->stats = [
            'total_completions' => $logs->count(),
            'current_streak' => $this->habit->current_streak,
            'best_streak' => $this->habit->best_streak,
            'completion_rate' => $this->habit->completion_rate,
            'total_points_earned' => $logs->sum('points_earned'),
            'total_pomodoros' => $logs->sum('pomodoros_used'),
            'average_time' => $logs->avg('pomodoros_used') * 25, // minutos
        ];

        // Datos para gráfico de los últimos 30 días
        $this->chartData = $logs->groupBy(function ($log) {
            return Carbon::parse($log->completed_date)->format('Y-m-d');
        })->map->count()->toArray();
    }

    public function render()
    {
        return view('livewire.habits.habit-stats')
            ->layout('layouts.app');
    }
}