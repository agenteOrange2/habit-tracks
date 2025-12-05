<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use App\Models\{Habit, HabitLog};
use App\Services\{PointsService, StreakService, AchievementService};
use App\Enums\AchievementType;

class HabitsList extends Component
{
    public $habits;

    public function mount(): void
    {
        $this->habits = $this->loadTodayHabits();
    }

    protected function loadTodayHabits(): Collection
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return collect([]);
            }
            
            return $user->habits()
                ->where('is_active', true)
                ->with([
                    'logs' => fn($q) => $q->whereDate('completed_date', today()),
                    'category'
                ])
                ->get()
                ->filter(fn($habit) => $habit->isScheduledForToday());
                
        } catch (\Exception $e) {
            Log::error('Failed to load today\'s habits', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            
            return collect([]);
        }
    }

    public function toggleHabit(int $habitId): void
    {
        $habit = Habit::findOrFail($habitId);
        
        // Check if habit belongs to current user
        if ($habit->user_id !== Auth::id()) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'No tienes permiso para completar este hábito.'
            ]);
            return;
        }

        // Check if already completed today
        if ($habit->isCompletedToday()) {
            $this->dispatch('notification', [
                'type' => 'info',
                'message' => 'Este hábito ya fue completado hoy.'
            ]);
            return;
        }

        // Complete the habit
        $this->completeHabit($habitId);
    }

    public function completeHabit(int $habitId): void
    {
        try {
            DB::transaction(function () use ($habitId) {
                $habit = Habit::findOrFail($habitId);
                $user = Auth::user();
                
                // Verify habit belongs to user
                if ($habit->user_id !== $user->id) {
                    throw new \Exception('Unauthorized habit access');
                }
                
                // Verify habit is not already completed today
                if ($habit->isCompletedToday()) {
                    throw new \Exception('Habit already completed today');
                }
                
                // Create habit log
                $log = HabitLog::create([
                    'habit_id' => $habit->id,
                    'user_id' => $user->id,
                    'completed_date' => today(),
                    'completed_time' => now(),
                    'points_earned' => $habit->points_reward,
                    'pomodoros_used' => 0,
                ]);
                
                // Award points
                $pointsService = app(PointsService::class);
                $pointsAwarded = $pointsService->awardPoints($user, $habit, 0, false);
                
                // Update streaks
                $streakService = app(StreakService::class);
                $streakService->updateStreak($habit);
                $streakService->updateGlobalStreak($user);
                
                // Check achievements
                $achievementService = app(AchievementService::class);
                $totalHabits = $user->habitLogs()->count();
                $achievementService->checkAndUnlock($user, AchievementType::TOTAL_HABITS, $totalHabits);
                
                // Update log with actual points awarded (including bonuses)
                $log->update(['points_earned' => $pointsAwarded]);
            });
            
            // Refresh habits list
            $this->habits = $this->loadTodayHabits();
            
            // Dispatch success event
            $this->dispatch('habitCompleted');
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => '¡Hábito completado! Has ganado puntos.'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Habit not found', [
                'habit_id' => $habitId,
                'user_id' => Auth::id(),
            ]);
            
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'El hábito no existe.'
            ]);
            
            // Refresh list to remove stale data
            $this->habits = $this->loadTodayHabits();
            
        } catch (\Exception $e) {
            Log::error('Habit completion failed', [
                'habit_id' => $habitId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'No se pudo completar el hábito. Intenta de nuevo.'
            ]);
            
            // Ensure UI state is consistent by refreshing
            $this->habits = $this->loadTodayHabits();
        }
    }

    public function render()
    {
        return view('livewire.dashboard.habits-list');
    }
}
