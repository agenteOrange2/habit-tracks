<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Difficulty;
use App\Models\User;
use App\Models\UserStats;
use App\Models\UserLevel;

class DefaultDataService
{
    /**
     * Create default categories for a new user.
     */
    public function createDefaultCategories(User $user): void
    {
        // Skip if user already has categories
        $existingCount = Category::withoutGlobalScope('user')
            ->where('user_id', $user->id)
            ->count();
        
        if ($existingCount > 0) {
            return;
        }

        $categories = [
            ['name' => 'Salud', 'slug' => 'health', 'icon' => '💪', 'color' => '#10B981', 'order' => 1],
            ['name' => 'Productividad', 'slug' => 'productivity', 'icon' => '💼', 'color' => '#3B82F6', 'order' => 2],
            ['name' => 'Aprendizaje', 'slug' => 'learning', 'icon' => '📚', 'color' => '#8B5CF6', 'order' => 3],
            ['name' => 'Personal', 'slug' => 'personal', 'icon' => '🌟', 'color' => '#F59E0B', 'order' => 4],
            ['name' => 'Hogar', 'slug' => 'household', 'icon' => '🏠', 'color' => '#6366F1', 'order' => 5],
            ['name' => 'Social', 'slug' => 'social', 'icon' => '👥', 'color' => '#F59E0B', 'order' => 6],
            ['name' => 'Creatividad', 'slug' => 'creative', 'icon' => '🎨', 'color' => '#EC4899', 'order' => 7],
            ['name' => 'Finanzas', 'slug' => 'finance', 'icon' => '💰', 'color' => '#14B8A6', 'order' => 8],
        ];

        foreach ($categories as $category) {
            Category::withoutGlobalScope('user')->create([
                'user_id' => $user->id,
                ...$category,
            ]);
        }
    }

    /**
     * Create default difficulty levels for a new user.
     */
    public function createDefaultDifficulties(User $user): void
    {
        // Skip if user already has difficulties
        $existingCount = Difficulty::withoutGlobalScope('user')
            ->where('user_id', $user->id)
            ->count();
        
        if ($existingCount > 0) {
            return;
        }

        $difficulties = [
            ['name' => 'Fácil', 'slug' => 'easy', 'points' => 10, 'icon' => '⭐', 'order' => 1],
            ['name' => 'Medio', 'slug' => 'medium', 'points' => 25, 'icon' => '⭐⭐', 'order' => 2],
            ['name' => 'Difícil', 'slug' => 'hard', 'points' => 50, 'icon' => '⭐⭐⭐', 'order' => 3],
            ['name' => 'Épico', 'slug' => 'epic', 'points' => 100, 'icon' => '🔥', 'order' => 4],
        ];

        foreach ($difficulties as $difficulty) {
            Difficulty::withoutGlobalScope('user')->create([
                'user_id' => $user->id,
                ...$difficulty,
            ]);
        }
    }

    /**
     * Create default user stats for a new user.
     */
    public function createDefaultStats(User $user): void
    {
        // Skip if user already has stats
        if ($user->stats()->exists()) {
            return;
        }

        UserStats::create([
            'user_id' => $user->id,
            'total_points' => 0,
            'available_points' => 0,
            'total_habits_completed' => 0,
            'total_pomodoros' => 0,
            'current_global_streak' => 0,
            'best_global_streak' => 0,
            'total_focus_time' => 0,
            'weekly_points' => 0,
            'monthly_points' => 0,
            'week_start' => now()->startOfWeek(),
            'month_start' => now()->startOfMonth(),
        ]);
    }

    /**
     * Create default user level for a new user.
     */
    public function createDefaultLevel(User $user): void
    {
        // Skip if user already has level
        if ($user->level()->exists()) {
            return;
        }

        UserLevel::create([
            'user_id' => $user->id,
            'current_level' => 1,
            'current_xp' => 0,
            'total_xp' => 0,
        ]);
    }

    /**
     * Create all default data for a new user.
     */
    public function createAllDefaults(User $user): void
    {
        $this->createDefaultCategories($user);
        $this->createDefaultDifficulties($user);
        $this->createDefaultStats($user);
        $this->createDefaultLevel($user);
    }
}
