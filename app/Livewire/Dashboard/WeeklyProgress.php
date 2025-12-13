<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WeeklyProgress extends Component
{
    public $weekDays = [];

    public function mount(): void
    {
        $this->loadWeekProgress();
    }

    #[On('habitCompleted')]
    #[On('habitUncompleted')]
    public function refreshProgress(): void
    {
        $this->loadWeekProgress();
    }

    public function loadWeekProgress(): void
    {
        $user = Auth::user();
        $startOfWeek = now()->startOfWeek();

        $this->weekDays = collect(range(0, 6))->map(function ($day) use ($user, $startOfWeek) {
            $date = $startOfWeek->copy()->addDays($day);
            
            $habitsCompleted = $user->habitLogs()
                ->whereDate('completed_date', $date)
                ->count();

            $scheduledHabits = $user->habits()
                ->where('is_active', true)
                ->get()
                ->filter(function ($habit) use ($date) {
                    return $habit->isScheduledForDay($date);
                })
                ->count();

            return [
                'date' => $date,
                'dayName' => $date->format('D'),
                'dayNumber' => $date->format('d'),
                'completed' => $habitsCompleted,
                'total' => $scheduledHabits,
                'percentage' => $scheduledHabits > 0 
                    ? round(($habitsCompleted / $scheduledHabits) * 100) 
                    : 0,
                'isToday' => $date->isToday(),
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard.weekly-progress');
    }
}