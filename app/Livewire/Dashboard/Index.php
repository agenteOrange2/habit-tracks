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

    protected $listeners = [
        'habitCompleted' => 'refreshStats',
        'habitUncompleted' => 'refreshStats',
    ];

    public function mount(): void
    {
        $this->greeting = $this->getGreeting();
        $this->loadTodayHabits();
        $this->calculateCompletionRate();
    }

    public function refreshStats(): void
    {
        // Reload habits and recalculate completion rate
        $this->loadTodayHabits();
        $this->calculateCompletionRate();
        
        // Force refresh of the entire component to update computed properties
        $this->dispatch('$refresh');
    }

    #[Computed]
    public function userLevel()
    {
        return Auth::user()->fresh()->level;
    }

    #[Computed]
    public function userStats()
    {
        return Auth::user()->fresh()->stats;
    }

    #[Computed]
    public function currentStreak()
    {
        return Auth::user()->fresh()->stats->current_global_streak ?? 0;
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
                'logs' => fn($q) => $q->whereDate('completed_date', today())
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
        return view('livewire.dashboard.index');
    }
}
