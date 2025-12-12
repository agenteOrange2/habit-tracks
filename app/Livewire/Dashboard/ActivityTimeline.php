<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * ActivityTimeline Component
 * 
 * Displays a chronological timeline of today's events including completed habits
 * and scheduled habits that are still pending.
 * 
 * Usage in Blade:
 * <livewire:dashboard.activity-timeline />
 * 
 * Requirements: 4.1, 4.2, 4.3, 4.4, 4.5
 */
class ActivityTimeline extends Component
{
    public array $events = [];

    public function mount(): void
    {
        $this->loadEvents();
    }

    #[On('habitCompleted')]
    #[On('habitUncompleted')]
    public function refreshTimeline(): void
    {
        $this->loadEvents();
    }

    protected function loadEvents(): void
    {
        $user = Auth::user();
        
        if (!$user) {
            $this->events = [];
            return;
        }

        // Get completed habits for today
        $completedHabits = $user->habitLogs()
            ->with('habit')
            ->whereDate('completed_date', today())
            ->orderBy('completed_time')
            ->get()
            ->map(function ($log) {
                return [
                    'type' => 'completed',
                    'name' => $log->habit->name,
                    'time' => $log->completed_time ? Carbon::parse($log->completed_time) : now(),
                    'time_formatted' => $log->completed_time ? Carbon::parse($log->completed_time)->format('g:i A') : 'N/A',
                    'points' => $log->points_earned,
                    'icon' => $log->habit->icon ?? '✓',
                    'color' => $log->habit->color ?? 'green',
                ];
            });

        // Get scheduled habits that are not yet completed
        $scheduledHabits = $user->habits()
            ->where('is_active', true)
            ->whereDoesntHave('logs', fn($q) => $q->whereDate('completed_date', today()))
            ->get()
            ->filter(fn($habit) => $habit->isScheduledForToday())
            ->map(function ($habit) {
                // Use reminder time if available, otherwise use a default time
                $scheduledTime = $habit->reminder_time 
                    ? Carbon::parse($habit->reminder_time) 
                    : now()->setTime(12, 0); // Default to noon if no reminder time

                return [
                    'type' => 'pending',
                    'name' => $habit->name,
                    'time' => $scheduledTime,
                    'time_formatted' => $scheduledTime->format('g:i A'),
                    'points' => null,
                    'icon' => $habit->icon ?? '○',
                    'color' => $habit->color ?? 'gray',
                ];
            });

        // Merge and sort chronologically
        $this->events = $completedHabits
            ->concat($scheduledHabits)
            ->sortBy('time')
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard.activity-timeline');
    }
}
