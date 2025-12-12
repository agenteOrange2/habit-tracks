<?php

namespace Database\Factories;

use App\Models\Difficulty;
use Illuminate\Database\Eloquent\Factories\Factory;

class DifficultyFactory extends Factory
{
    protected $model = Difficulty::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'icon' => $this->faker->randomElement(['⭐', '🔥', '💪', '🚀', '⚡']),
            'points' => $this->faker->numberBetween(10, 100),
            'order' => $this->faker->numberBetween(1, 100),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
