<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Models\Habit;

class DailyHabitsList extends Component
{
    public string $filter = 'pending';
    public Collection $habits;
    public int $completedCount = 0;
    public int $totalCount = 0;

    protected $listeners = [
        'habitCompleted' => 'refreshHabits',
        'habitUncompleted' => 'refreshHabits',
    ];

    public function mount(): void
    {
        $this->refreshHabits();
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->loadHabits();
    }

    public function refreshHabits(): void
    {
        $this->loadHabits();
    }

    protected function loadHabits(): void
    {
        $user = Auth::user();
        
        if (!$user) {
            $this->habits = collect([]);
            $this->completedCount = 0;
            $this->totalCount = 0;
            return;
        }

        // Load all today's scheduled habits with their logs
        $allHabits = $user->habits()
            ->where('is_active', true)
            ->with(['logs' => fn($q) => $q->whereDate('completed_date', today())])
            ->get()
            ->filter(fn($habit) => $habit->isScheduledForToday());

        $this->totalCount = $allHabits->count();
        $this->completedCount = $allHabits->filter(fn($habit) => $habit->isCompletedToday())->count();

        // Apply filter
        if ($this->filter === 'pending') {
            $this->habits = $allHabits->filter(fn($habit) => !$habit->isCompletedToday());
        } else {
            $this->habits = $allHabits;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.daily-habits-list');
    }
}
