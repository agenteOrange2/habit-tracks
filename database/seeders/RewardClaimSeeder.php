<?php

namespace Database\Seeders;

use App\Models\{User, Reward, RewardClaim};
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RewardClaimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::has('rewards')->get();

        if ($users->isEmpty()) {
            $this->command->warn('No users with rewards found. Please run RewardSeeder first.');
            return;
        }

        foreach ($users as $user) {
            $this->createClaimsForUser($user);
        }

        $this->command->info('Reward claims seeded successfully!');
    }

    /**
     * Create sample claims for a user
     */
    protected function createClaimsForUser(User $user): void
    {
        $rewards = $user->rewards()->where('is_available', true)->get();

        if ($rewards->isEmpty()) {
            return;
        }

        // Create 5-15 random claims over the past 30 days
        $claimCount = rand(5, 15);

        for ($i = 0; $i < $claimCount; $i++) {
            $reward = $rewards->random();
            $claimedAt = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23));

            RewardClaim::create([
                'reward_id' => $reward->id,
                'user_id' => $user->id,
                'points_spent' => $reward->cost_points,
                'claimed_at' => $claimedAt,
                'was_enjoyed' => rand(0, 100) > 20, // 80% enjoyed
                'notes' => $this->getRandomNote(),
            ]);
        }
    }

    /**
     * Get a random note or null
     */
    protected function getRandomNote(): ?string
    {
        $notes = [
            null,
            null,
            null, // 60% chance of no note
            '¡Muy buena recompensa!',
            'Me lo merecía después de tanto trabajo',
            'Perfecto para relajarme',
            'Debería hacer esto más seguido',
            'Excelente motivación',
            '10/10 lo volvería a canjear',
        ];

        return $notes[array_rand($notes)];
    }
}
