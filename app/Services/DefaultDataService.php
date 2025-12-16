<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Category;
use App\Models\Difficulty;
use App\Models\User;
use App\Models\UserStats;
use App\Models\UserLevel;
use App\Enums\AchievementType;

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
        $this->ensureDefaultAchievementsExist();
    }

    /**
     * Ensure default achievements exist (global, not per-user).
     */
    public function ensureDefaultAchievementsExist(): void
    {
        // Only create if no achievements exist
        if (Achievement::count() > 0) {
            return;
        }

        $achievements = [
            // 5 logros básicos iniciales
            [
                'name' => 'Primer Paso',
                'description' => 'Completa tu primer hábito',
                'icon' => '🌱',
                'category' => 'habits',
                'requirement_type' => AchievementType::TOTAL_HABITS->value,
                'requirement_value' => 1,
                'points_reward' => 10,
                'is_secret' => false,
            ],
            [
                'name' => 'En Marcha',
                'description' => 'Completa 5 hábitos en total',
                'icon' => '🚶',
                'category' => 'habits',
                'requirement_type' => AchievementType::TOTAL_HABITS->value,
                'requirement_value' => 5,
                'points_reward' => 15,
                'is_secret' => false,
            ],
            [
                'name' => 'Racha Inicial',
                'description' => 'Mantén una racha de 3 días',
                'icon' => '🔥',
                'category' => 'streaks',
                'requirement_type' => AchievementType::HABIT_STREAK->value,
                'requirement_value' => 3,
                'points_reward' => 20,
                'is_secret' => false,
            ],
            [
                'name' => 'Primer Pomodoro',
                'description' => 'Completa tu primer pomodoro',
                'icon' => '🍅',
                'category' => 'pomodoro',
                'requirement_type' => AchievementType::POMODOROS->value,
                'requirement_value' => 1,
                'points_reward' => 10,
                'is_secret' => false,
            ],
            [
                'name' => 'Día Perfecto',
                'description' => 'Completa todos los hábitos del día',
                'icon' => '✨',
                'category' => 'daily',
                'requirement_type' => AchievementType::CONSECUTIVE_DAYS->value,
                'requirement_value' => 1,
                'points_reward' => 25,
                'is_secret' => false,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::create($achievement);
        }
    }
}
