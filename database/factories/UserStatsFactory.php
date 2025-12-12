<?php

namespace Database\Factories;

use App\Models\{UserStats, User};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserStats>
 */
class UserStatsFactory extends Factory
{
    protected $model = UserStats::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalPoints = fake()->numberBetween(0, 1000);
        $availablePoints = fake()->numberBetween(0, $totalPoints);

        return [
            'user_id' => User::factory(),
            'total_points' => $totalPoints,
            'available_points' => $availablePoints,
            'total_habits_completed' => fake()->numberBetween(0, 100),
            'total_pomodoros' => fake()->numberBetween(0, 50),
            'current_global_streak' => fake()->numberBetween(0, 30),
            'best_global_streak' => fake()->numberBetween(0, 60),
            'last_activity_date' => fake()->dateTimeBetween('-7 days', 'now'),
            'total_focus_time' => fake()->numberBetween(0, 3000),
            'weekly_points' => fake()->numberBetween(0, 200),
            'monthly_points' => fake()->numberBetween(0, 800),
            'week_start' => now()->startOfWeek(),
            'month_start' => now()->startOfMonth(),
        ];
    }
}
