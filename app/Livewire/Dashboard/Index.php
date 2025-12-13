<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use App\Services\DefaultDataService;
use Carbon\Carbon;

class Index extends Component
{
    public float $completionRate = 0;
    public $todayHabits;

    protected $listeners = [
        'habitCompleted' => 'refreshStats',
        'habitUncompleted' => 'refreshStats',
    ];

    public function mount(): void
    {
        // Ensure user has stats and level
        $this->ensureUserData();
        
        $this->loadTodayHabits();
        $this->calculateCompletionRate();
    }

    protected function ensureUserData(): void
    {
        $user = Auth::user();
        
        // Create stats if not exists
        if (!$user->stats) {
            app(DefaultDataService::class)->createDefaultStats($user);
            $user->refresh();
        }
        
        // Create level if not exists
        if (!$user->level) {
            app(DefaultDataService::class)->createDefaultLevel($user);
            $user->refresh();
        }
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
    public function greeting(): string
    {
        $hour = (int) now()->format('H');
        $userName = Auth::user()->name;

        if ($hour >= 5 && $hour < 12) {
            return "Buenos días, {$userName}";
        } elseif ($hour >= 12 && $hour < 19) {
            return "Buenas tardes, {$userName}";
        } else {
            return "Buenas noches, {$userName}";
        }
    }

    #[Computed]
    public function userLevel()
    {
        $level = Auth::user()->fresh()->level;
        
        // Return default values if level doesn't exist
        if (!$level) {
            return (object) [
                'current_level' => 1,
                'current_xp' => 0,
                'total_xp' => 0,
                'required_xp' => 100,
                'progress_percentage' => 0,
                'level_title' => 'Principiante 🌱',
            ];
        }
        
        return $level;
    }

    #[Computed]
    public function userStats()
    {
        $stats = Auth::user()->fresh()->stats;
        
        // Return default values if stats doesn't exist
        if (!$stats) {
            return (object) [
                'current_global_streak' => 0,
                'best_global_streak' => 0,
                'total_points' => 0,
                'available_points' => 0,
            ];
        }
        
        return $stats;
    }

    #[Computed]
    public function currentStreak()
    {
        return $this->userStats->current_global_streak ?? 0;
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
