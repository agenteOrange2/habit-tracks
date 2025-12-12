<?php

namespace Database\Seeders;

use App\Models\{User, Reward};
use App\Enums\RewardCategory;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    /**
     * Sample rewards data organized by category
     */
    protected array $sampleRewards = [
        'entertainment' => [
            ['name' => 'Ver un episodio de serie', 'icon' => '📺', 'cost' => 30],
            ['name' => 'Ver una película', 'icon' => '🎬', 'cost' => 60],
            ['name' => 'Escuchar música 30 min', 'icon' => '🎧', 'cost' => 20],
        ],
        'food' => [
            ['name' => 'Snack favorito', 'icon' => '🍿', 'cost' => 25],
            ['name' => 'Café especial', 'icon' => '☕', 'cost' => 35],
            ['name' => 'Postre delicioso', 'icon' => '🍰', 'cost' => 50],
            ['name' => 'Comida favorita', 'icon' => '🍕', 'cost' => 80],
        ],
        'leisure' => [
            ['name' => 'Siesta de 20 min', 'icon' => '😴', 'cost' => 40],
            ['name' => 'Paseo al aire libre', 'icon' => '🚶', 'cost' => 30],
            ['name' => 'Leer un capítulo', 'icon' => '📖', 'cost' => 25],
        ],
        'gaming' => [
            ['name' => '30 min de videojuegos', 'icon' => '🎮', 'cost' => 45],
            ['name' => '1 hora de gaming', 'icon' => '🕹️', 'cost' => 90],
        ],
        'shopping' => [
            ['name' => 'Compra pequeña online', 'icon' => '🛒', 'cost' => 100],
            ['name' => 'Artículo de wishlist', 'icon' => '🎁', 'cost' => 200],
        ],
        'social' => [
            ['name' => 'Llamar a un amigo', 'icon' => '📞', 'cost' => 20],
            ['name' => 'Salir con amigos', 'icon' => '🎉', 'cost' => 75],
            ['name' => 'Redes sociales 15 min', 'icon' => '📱', 'cost' => 15],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        foreach ($users as $user) {
            $this->createRewardsForUser($user);
        }

        $this->command->info('Rewards seeded successfully!');
    }

    /**
     * Create sample rewards for a user
     */
    protected function createRewardsForUser(User $user): void
    {
        foreach ($this->sampleRewards as $category => $rewards) {
            foreach ($rewards as $rewardData) {
                // Skip if reward already exists for this user
                if ($user->rewards()->where('name', $rewardData['name'])->exists()) {
                    continue;
                }

                $user->rewards()->create([
                    'name' => $rewardData['name'],
                    'description' => $this->generateDescription($rewardData['name']),
                    'category' => $category,
                    'cost_points' => $rewardData['cost'],
                    'icon' => $rewardData['icon'],
                    'is_available' => true,
                ]);
            }
        }
    }

    /**
     * Generate a simple description for a reward
     */
    protected function generateDescription(string $name): string
    {
        return "Disfruta de: {$name}. ¡Te lo mereces por tu esfuerzo!";
    }
}
