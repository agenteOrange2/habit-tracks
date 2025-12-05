<?php

namespace App\Livewire\Habits;


use Livewire\Component;
use App\Models\Habit;
use App\Services\{PointsService, StreakService};
use App\Events\HabitCompleted;
use Illuminate\Support\Facades\Auth;

class HabitCard extends Component
{
    public Habit $habit;
    public $isCompleted = false;

    protected $listeners = [
        'habitCompleted' => 'checkCompletion',
    ];

    public function mount(): void
    {
        $this->checkCompletion();
    }

    public function checkCompletion(): void
    {
        $this->isCompleted = $this->habit->isCompletedToday();
    }

    public function toggleComplete(
        PointsService $pointsService,
        StreakService $streakService
    ): void {
        $user = Auth::user();

        if ($this->isCompleted) {
            // Desmarcar
            $log = $this->habit->logs()
                ->whereDate('completed_date', today())
                ->first();

            if ($log) {
                // Restar puntos
                $user->stats->decrement('available_points', $log->points_earned);
                $user->stats->decrement('total_points', $log->points_earned);
                $user->stats->decrement('total_habits_completed');

                $log->delete();
            }

            $this->isCompleted = false;
            $this->dispatch('habitUncompleted');
        } else {
            // Marcar como completado
            $isFirstTimeToday = !$user->habitLogs()
                ->whereDate('completed_date', today())
                ->exists();

            $pointsEarned = $pointsService->awardPoints(
                $user,
                $this->habit,
                0,
                $isFirstTimeToday
            );

            $log = $this->habit->logs()->create([
                'user_id' => $user->id,
                'completed_date' => today(),
                'completed_time' => now(),
                'pomodoros_used' => 0,
                'points_earned' => $pointsEarned,
            ]);

            // Actualizar stats
            $user->stats->increment('total_habits_completed');
            $streakService->updateStreak($this->habit);
            $streakService->updateGlobalStreak($user);

            // Dispatch event
            event(new HabitCompleted($user, $this->habit, $log, $pointsEarned));

            $this->isCompleted = true;
            $this->dispatch('habitCompleted');

            // Notificación
            session()->flash('success', "¡+{$pointsEarned} puntos! 🎉");
        }

        $this->habit->refresh();
    }

    public function render()
    {
        return view('livewire.habits.habit-card');
    }
}