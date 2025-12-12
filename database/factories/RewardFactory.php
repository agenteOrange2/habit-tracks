<?php

namespace Database\Factories;

use App\Models\{Reward, User};
use App\Enums\RewardCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reward>
 */
class RewardFactory extends Factory
{
    protected $model = Reward::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = RewardCategory::cases();
        $category = fake()->randomElement($categories);

        return [
            'user_id' => User::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->sentence(10),
            'category' => $category,
            'cost_points' => fake()->numberBetween(10, 200),
            'icon' => $category->icon(),
            'is_available' => true,
        ];
    }

    /**
     * Indicate that the reward is unavailable.
     */
    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
        ]);
    }

    /**
     * Indicate that the reward is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => true,
        ]);
    }
}
