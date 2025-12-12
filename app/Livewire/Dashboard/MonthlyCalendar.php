<?php

namespace App\Livewire\Dashboard;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class MonthlyCalendar extends Component
{
    public $currentMonth;
    public $activityDays = [];
    public $selectedDay = null;
    public $selectedDayHabits = [];

    public function mount(): void
    {
        $this->currentMonth = now();
        $this->loadActivityDays();
    }

    public function previousMonth(): void
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->subMonth();
        $this->loadActivityDays();
        $this->selectedDay = null;
        $this->selectedDayHabits = [];
    }

    public function nextMonth(): void
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->addMonth();
        $this->loadActivityDays();
        $this->selectedDay = null;
        $this->selectedDayHabits = [];
    }

    public function selectDay(string $date): void
    {
        $this->selectedDay = Carbon::parse($date);
        
        // Load habits completed on selected day
        $this->selectedDayHabits = Auth::user()->habitLogs()
            ->with('habit')
            ->whereDate('completed_date', $this->selectedDay)
            ->orderBy('completed_time')
            ->get()
            ->map(function ($log) {
                return [
                    'name' => $log->habit->name,
                    'time' => $log->completed_time ? Carbon::parse($log->completed_time)->format('g:i A') : 'N/A',
                    'points' => $log->points_earned,
                ];
            })
            ->toArray();
    }

    #[On('habitCompleted')]
    #[On('habitUncompleted')]
    public function refreshCalendar(): void
    {
        $this->loadActivityDays();
    }

    public function loadActivityDays(): void
    {
        $currentMonth = Carbon::parse($this->currentMonth);
        $year = $currentMonth->year;
        $month = $currentMonth->month;

        $this->activityDays = Auth::user()->habitLogs()
            ->whereYear('completed_date', $year)
            ->whereMonth('completed_date', $month)
            ->select('completed_date')
            ->distinct()
            ->get()
            ->map(fn($log) => Carbon::parse($log->completed_date)->day)
            ->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard.monthly-calendar');
    }
}
