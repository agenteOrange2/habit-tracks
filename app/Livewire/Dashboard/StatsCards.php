<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class StatsCards extends Component
{
    #[On('habitCompleted')]
    #[On('habitUncompleted')]
    public function refreshStats()
    {
        // Clear cached computed properties when a habit is completed or uncompleted
        unset($this->userLevel);
        unset($this->userStats);
        unset($this->currentStreak);
        unset($this->last7Days);
    }

    #[Computed(cache: true, seconds: 300)]
    public function userLevel()
    {
        return Auth::user()->level;
    }

    #[Computed(cache: true, seconds: 300)]
    public function userStats()
    {
        return Auth::user()->stats;
    }

    #[Computed(cache: true, seconds: 300)]
    public function currentStreak()
    {
        return Auth::user()->stats->current_global_streak ?? 0;
    }

    #[Computed]
    public function completionRate()
    {
        $user = Auth::user();
        
        $todayHabits = $user->habits()
            ->where('is_active', true)
            ->with([
                'logs' => fn($q) => $q->whereDate('completed_date', today())
            ])
            ->get()
            ->filter(fn($habit) => $habit->isScheduledForToday());

        $scheduledCount = $todayHabits->count();
        
        if ($scheduledCount === 0) {
            return 0;
        }

        $completedCount = $todayHabits->filter(fn($habit) => $habit->isCompletedToday())->count();
        return ($completedCount / $scheduledCount) * 100;
    }

    #[Computed(cache: true, seconds: 300)]
    public function last7Days()
    {
        $user = Auth::user();
        $days = [];
        
        // Get all habit logs for the last 7 days in a single query
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(today()->subDays($i));
        }
        
        $logsGrouped = $user->habitLogs()
            ->whereDate('completed_date', '>=', today()->subDays(6))
            ->whereDate('completed_date', '<=', today())
            ->get()
            ->groupBy(fn($log) => $log->completed_date->format('Y-m-d'));
        
        foreach ($dates as $date) {
            $days[] = [
                'date' => $date,
                'hasActivity' => $logsGrouped->has($date->format('Y-m-d'))
            ];
        }
        
        return $days;
    }

    public function render()
    {
        return view('livewire.dashboard.stats-cards');
    }
}
