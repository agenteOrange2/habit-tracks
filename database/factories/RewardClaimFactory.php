<?php

namespace Database\Factories;

use App\Models\{RewardClaim, Reward, User};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RewardClaim>
 */
class RewardClaimFactory extends Factory
{
    protected $model = RewardClaim::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reward_id' => Reward::factory(),
            'user_id' => User::factory(),
            'points_spent' => fake()->numberBetween(10, 200),
            'claimed_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'was_enjoyed' => fake()->boolean(80),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
