<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\DefaultDataService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'maubr170295@gmail.com',
            'password' => 'Admin2025+?',
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);

        // Create all default data for user (stats, level, categories, difficulties)
        $defaultDataService = app(DefaultDataService::class);
        $defaultDataService->createAllDefaults($user);

        // Seed habits
        $this->call([
            HabitSeeder::class,
        ]);

        // Seed rewards and claims
        $this->call([
            RewardSeeder::class,
            RewardClaimSeeder::class,
        ]);
    }
}
