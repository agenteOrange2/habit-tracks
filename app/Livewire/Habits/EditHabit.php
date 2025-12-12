<?php

namespace App\Livewire\Habits;

use Livewire\Component;
use App\Models\Habit;
use App\Models\Category;
use App\Models\Difficulty;
use App\Enums\{HabitDifficulty, HabitFrequency, HabitCategory};

class EditHabit extends Component
{
    public Habit $habit;
    public $name;
    public $description;
    public $category_id;
    public $difficulty_id;
    public $frequency;
    public $selectedDays = [];
    public $time;
    public $color;
    public $icon;
    public $estimated_pomodoros;
    public $reminder_enabled;
    public $reminder_time;
    public $is_active;

    public function mount(Habit $habit): void
    {
        $this->authorize('update', $habit);

        $this->habit = $habit;
        $this->name = $habit->name;
        $this->description = $habit->description;
        
        // Use new dynamic relationships if available, fallback to enum
        if ($habit->category_id) {
            $this->category_id = $habit->category_id;
        } elseif ($habit->category) {
            // Fallback: try to find matching category by enum value
            $this->category_id = null;
        }
        
        if ($habit->difficulty_id) {
            $this->difficulty_id = $habit->difficulty_id;
        } elseif ($habit->difficulty) {
            // Fallback: try to find matching difficulty by enum value
            $this->difficulty_id = null;
        }
        
        $this->frequency = $habit->frequency->value;
        $this->selectedDays = $habit->schedule['days'] ?? [];
        $this->time = $habit->schedule['time'] ?? '09:00';
        $this->color = $habit->color;
        $this->icon = $habit->icon;
        $this->estimated_pomodoros = $habit->estimated_pomodoros;
        $this->reminder_enabled = $habit->reminder_enabled;
        // Convertir reminder_time de H:i:s a H:i si es necesario
        if (!empty($habit->reminder_time)) {
            // Si viene en formato H:i:s, convertir a H:i
            $this->reminder_time = substr($habit->reminder_time, 0, 5);
        } else {
            $this->reminder_time = null;
        }
        $this->is_active = $habit->is_active;
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'required|exists:categories,id',
            'difficulty_id' => 'required|exists:difficulties,id',
            'frequency' => 'required|string',
            'selectedDays' => 'array',
            'time' => 'nullable|date_format:H:i',
            'color' => 'required|string',
            'icon' => 'required|string',
            'estimated_pomodoros' => 'nullable|integer|min:1',
            'reminder_enabled' => 'boolean',
            'reminder_time' => $this->reminder_enabled ? 'required|date_format:H:i' : 'nullable',
            'is_active' => 'boolean',
        ];
    }

    public function update()
    {
        $this->validate();

        $schedule = null;
        if ($this->frequency === 'weekly' || $this->frequency === 'custom') {
            $schedule = [
                'days' => $this->selectedDays,
                'time' => $this->time,
            ];
        }

        // Get category and difficulty to retrieve data
        $category = Category::findOrFail($this->category_id);
        $difficulty = Difficulty::findOrFail($this->difficulty_id);

        $this->habit->update([
            'name' => $this->name,
            'description' => $this->description,
            // New dynamic fields
            'category_id' => $this->category_id,
            'difficulty_id' => $this->difficulty_id,
            // Old enum fields (for backward compatibility during transition)
            'category' => $category->slug,
            'difficulty' => $difficulty->slug,
            'frequency' => $this->frequency,
            'schedule' => $schedule,
            'points_reward' => $difficulty->points,
            'color' => $this->color,
            'icon' => $this->icon,
            'estimated_pomodoros' => $this->estimated_pomodoros,
            'reminder_enabled' => $this->reminder_enabled,
            'reminder_time' => $this->reminder_enabled && !empty($this->reminder_time) ? $this->reminder_time : null,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('habitUpdated');

        session()->flash('success', '¡Hábito actualizado exitosamente! ✅');

        return $this->redirect(route('admin.habits.index'), navigate: true);
    }

    public function delete()
    {
        $this->authorize('delete', $this->habit);

        $this->habit->delete();

        $this->dispatch('habitDeleted');

        session()->flash('success', 'Hábito eliminado correctamente.');

        return $this->redirect(route('admin.habits.index'), navigate: true);
    }

    public function archive()
    {
        $isCurrentlyActive = $this->habit->is_active;
        
        $this->habit->update([
            'is_active' => !$isCurrentlyActive,
            'archived_at' => $isCurrentlyActive ? now() : null,
        ]);

        $message = $isCurrentlyActive 
            ? 'Hábito archivado correctamente.' 
            : 'Hábito desarchivado correctamente.';
            
        session()->flash('success', $message);

        return $this->redirect(route('admin.habits.index'), navigate: true);
    }

    public function toggleHabit(): void
    {
        // Check if already completed today
        if ($this->habit->isCompletedToday()) {
            // Uncomplete the habit
            $this->uncompleteHabit();
        } else {
            // Complete the habit
            $this->completeHabit();
        }
    }

    protected function uncompleteHabit(): void
    {
        try {
            \Illuminate\Support\Facades\DB::transaction(function () {
                $user = \Illuminate\Support\Facades\Auth::user();
                
                // Find and delete today's log
                $log = \App\Models\HabitLog::where('habit_id', $this->habit->id)
                    ->where('user_id', $user->id)
                    ->whereDate('completed_date', today())
                    ->first();
                
                if (!$log) {
                    throw new \Exception('No completion log found for today');
                }
                
                // Remove points that were awarded
                $pointsToRemove = $log->points_earned ?? $this->habit->points_reward;
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
                $streakService->updateStreak($this->habit);
            });
            
            // Refresh habit data
            $this->habit->refresh();
            
            $this->dispatch('habitUncompleted');
            $this->dispatch('notification', [
                'type' => 'info',
                'message' => 'Hábito desmarcado. Los puntos han sido revertidos.'
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Habit uncompletion failed', [
                'habit_id' => $this->habit->id,
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'error' => $e->getMessage(),
            ]);
            
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'No se pudo desmarcar el hábito. Intenta de nuevo.'
            ]);
        }
    }

    protected function completeHabit(): void
    {
        try {
            \Illuminate\Support\Facades\DB::transaction(function () {
                $user = \Illuminate\Support\Facades\Auth::user();
                
                // Verify habit is not already completed today
                if ($this->habit->isCompletedToday()) {
                    throw new \Exception('Habit already completed today');
                }
                
                // Create habit log
                $log = \App\Models\HabitLog::create([
                    'habit_id' => $this->habit->id,
                    'user_id' => $user->id,
                    'completed_date' => today(),
                    'completed_time' => now(),
                    'points_earned' => $this->habit->points_reward,
                    'pomodoros_used' => 0,
                ]);
                
                // Award points
                $pointsService = app(\App\Services\PointsService::class);
                $pointsAwarded = $pointsService->awardPoints($user, $this->habit, 0, false);
                
                // Update habit streak
                $streakService = app(\App\Services\StreakService::class);
                $streakService->updateStreak($this->habit);
                
                // Check achievements
                $achievementService = app(\App\Services\AchievementService::class);
                $totalHabits = $user->habitLogs()->count();
                $achievementService->checkAndUnlock($user, \App\Enums\AchievementType::TOTAL_HABITS, $totalHabits);
                
                // Update log with actual points awarded
                $log->update(['points_earned' => $pointsAwarded]);
            });
            
            // Refresh habit data
            $this->habit->refresh();
            
            $this->dispatch('habitCompleted');
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => '¡Hábito completado! Has ganado puntos.'
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Habit completion failed', [
                'habit_id' => $this->habit->id,
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
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
        // Load active categories and difficulties
        $activeCategories = Category::active()->ordered()->get();
        $activeDifficulties = Difficulty::active()->ordered()->get();
        
        // If editing a habit with an inactive category/difficulty, include it but mark as deprecated
        $categories = $activeCategories;
        $difficulties = $activeDifficulties;
        
        // Check if current category is inactive and add it if needed
        if ($this->habit->category_id) {
            $currentCategory = Category::find($this->habit->category_id);
            if ($currentCategory && !$currentCategory->is_active) {
                // Add the inactive category to the list
                $categories = $categories->push($currentCategory);
            }
        }
        
        // Check if current difficulty is inactive and add it if needed
        if ($this->habit->difficulty_id) {
            $currentDifficulty = Difficulty::find($this->habit->difficulty_id);
            if ($currentDifficulty && !$currentDifficulty->is_active) {
                // Add the inactive difficulty to the list
                $difficulties = $difficulties->push($currentDifficulty);
            }
        }
        
        return view('livewire.habits.edit-habit', [
            'categories' => $categories,
            'difficulties' => $difficulties,
            'frequencies' => HabitFrequency::cases(),
            'daysOfWeek' => [
                0 => 'Domingo',
                1 => 'Lunes',
                2 => 'Martes',
                3 => 'Miércoles',
                4 => 'Jueves',
                5 => 'Viernes',
                6 => 'Sábado',
            ],
        ])->layout('components.layouts.app');
    }
}