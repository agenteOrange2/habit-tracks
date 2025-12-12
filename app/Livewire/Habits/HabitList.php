<?php

namespace App\Livewire\Habits;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class HabitList extends Component
{
    use WithPagination;

    public $filter = 'active'; // active, archived, all
    public $categoryFilter = 'all';
    public $search = '';

    protected $listeners = [
        'habitCreated' => '$refresh',
        'habitUpdated' => '$refresh',
        'habitDeleted' => '$refresh',
        'toggleHabit' => 'toggleHabit',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function setFilter($filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function setCategoryFilter($category): void
    {
        $this->categoryFilter = $category;
        $this->resetPage();
    }

    public function toggleHabit(int $habitId): void
    {
        $habit = \App\Models\Habit::findOrFail($habitId);
        
        // Check if habit belongs to current user
        if ($habit->user_id !== Auth::id()) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'No tienes permiso para modificar este hábito.'
            ]);
            return;
        }

        // Check if already completed today
        if ($habit->isCompletedToday()) {
            // Uncomplete the habit
            $this->uncompleteHabit($habitId);
        } else {
            // Complete the habit
            $this->completeHabit($habitId);
        }
    }

    protected function uncompleteHabit(int $habitId): void
    {
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($habitId) {
                $habit = \App\Models\Habit::findOrFail($habitId);
                $user = Auth::user();
                
                // Verify habit belongs to user
                if ($habit->user_id !== $user->id) {
                    throw new \Exception('Unauthorized habit access');
                }
                
                // Find and delete today's log
                $log = \App\Models\HabitLog::where('habit_id', $habit->id)
                    ->where('user_id', $user->id)
                    ->whereDate('completed_date', today())
                    ->first();
                
                if (!$log) {
                    throw new \Exception('No completion log found for today');
                }
                
                // Remove points that were awarded
                $pointsToRemove = $log->points_earned ?? $habit->points_reward;
                $user->stats()->decrement('total_points', $pointsToRemove);
                
                // Remove XP from user level
                $userLevel = $user->level;
                if ($userLevel) {
                    $newCurrentXp = max(0, $userLevel->current_xp - $pointsToRemove);
                    $userLevel->current_xp = $newCurrentXp;
                    $userLevel->total_xp = max(0, $userLevel->total_xp - $pointsToRemove);
                    
                    while ($userLevel->current_level > 1 && $newCurrentXp < 0) {
                        $userLevel->current_level--;
                        $previousLevelXp = ($userLevel->current_level) * 100;
                        $newCurrentXp += $previousLevelXp;
                    }
                    
                    $userLevel->current_xp = max(0, $newCurrentXp);
                    $userLevel->save();
                }
                
                // Delete the log
                $log->delete();
                
                // Update habit streak
                $streakService = app(\App\Services\StreakService::class);
                $streakService->updateStreak($habit);
            });
            
            $this->dispatch('habitUncompleted');
            $this->dispatch('notification', [
                'type' => 'info',
                'message' => 'Hábito desmarcado. Los puntos han sido revertidos.'
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Habit uncompletion failed', [
                'habit_id' => $habitId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'No se pudo desmarcar el hábito. Intenta de nuevo.'
            ]);
        }
    }

    protected function completeHabit(int $habitId): void
    {
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($habitId) {
                $habit = \App\Models\Habit::findOrFail($habitId);
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
                $log = \App\Models\HabitLog::create([
                    'habit_id' => $habit->id,
                    'user_id' => $user->id,
                    'completed_date' => today(),
                    'completed_time' => now(),
                    'points_earned' => $habit->points_reward,
                    'pomodoros_used' => 0,
                ]);
                
                // Award points
                $pointsService = app(\App\Services\PointsService::class);
                $pointsAwarded = $pointsService->awardPoints($user, $habit, 0, false);
                
                // Update habit streak
                $streakService = app(\App\Services\StreakService::class);
                $streakService->updateStreak($habit);
                
                // Check achievements
                $achievementService = app(\App\Services\AchievementService::class);
                $totalHabits = $user->habitLogs()->count();
                $achievementService->checkAndUnlock($user, \App\Enums\AchievementType::TOTAL_HABITS, $totalHabits);
                
                // Update log with actual points awarded
                $log->update(['points_earned' => $pointsAwarded]);
            });
            
            $this->dispatch('habitCompleted');
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => '¡Hábito completado! Has ganado puntos.'
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Habit completion failed', [
                'habit_id' => $habitId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'No se pudo completar el hábito. Intenta de nuevo.'
            ]);
        }
    }

    public function render()
    {
        $query = Auth::user()->habits()
            ->with(['logs' => function ($query) {
                $query->whereDate('completed_date', today());
            }]);

        // Filtro de estado
        if ($this->filter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->filter === 'archived') {
            $query->where('is_active', false);
        }

        // Filtro de categoría
        if ($this->categoryFilter !== 'all') {
            $query->where('category', $this->categoryFilter);
        }

        // Búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $habits = $query->latest()->paginate(12);

        return view('livewire.habits.habit-list', [
            'habits' => $habits,
        ])->layout('components.layouts.app');
    }
}