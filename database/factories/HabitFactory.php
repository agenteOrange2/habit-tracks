<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Habit>
 */
class HabitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['productivity', 'health', 'learning', 'finance', 'social', 'creative', 'household', 'personal']),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
            'frequency' => 'daily',
            'schedule' => ['days' => [0, 1, 2, 3, 4, 5, 6]],
            'is_recurring' => true,
            'points_reward' => fake()->numberBetween(10, 100),
            'current_streak' => 0,
            'best_streak' => 0,
            'estimated_pomodoros' => fake()->numberBetween(1, 4),
            'is_active' => true,
            'color' => fake()->hexColor(),
            'icon' => '📝',
            'reminder_enabled' => false,
        ];
    }
}
