<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        // Create user stats and level
        $user->stats()->create([
            'total_points' => 0,
            'available_points' => 0,
            'weekly_points' => 0,
            'monthly_points' => 0,
            'current_global_streak' => 0,
            'best_global_streak' => 0,
        ]);

        $user->level()->create([
            'current_level' => 1,
            'current_xp' => 0,
            'total_xp' => 0,
        ]);

        // Seed categories and difficulties
        $this->call([
            CategorySeeder::class,
            DifficultySeeder::class,
        ]);

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
