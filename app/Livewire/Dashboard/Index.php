<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Index extends Component
{
    public string $greeting = '';
    public float $completionRate = 0;
    public $todayHabits;

    public function mount(): void
    {
        $this->greeting = $this->getGreeting();
        $this->loadTodayHabits();
        $this->calculateCompletionRate();
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

    protected function getGreeting(): string
    {
        $hour = now()->hour;
        $userName = Auth::user()->name;

        if ($hour >= 0 && $hour < 12) {
            return "Buenos días, {$userName}";
        } elseif ($hour >= 12 && $hour < 19) {
            return "Buenas tardes, {$userName}";
        } else {
            return "Buenas noches, {$userName}";
        }
    }

    protected function loadTodayHabits(): void
    {
        $user = Auth::user();
        
        $this->todayHabits = $user->habits()
            ->where('is_active', true)
            ->with([
                'logs' => fn($q) => $q->whereDate('completed_date', today()),
                'category'
            ])
            ->get()
            ->filter(fn($habit) => $habit->isScheduledForToday());
    }

    protected function calculateCompletionRate(): void
    {
        $scheduledCount = $this->todayHabits->count();
        
        if ($scheduledCount === 0) {
            $this->completionRate = 0;
            return;
        }

        $completedCount = $this->todayHabits->filter(fn($habit) => $habit->isCompletedToday())->count();
        $this->completionRate = ($completedCount / $scheduledCount) * 100;
    }

    public function render()
    {
        return view('livewire.dashboard.index')
            ->layout('components.layouts.app');
    }
}
