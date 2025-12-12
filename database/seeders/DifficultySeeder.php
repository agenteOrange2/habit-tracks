<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DifficultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $difficulties = [
            [
                'name' => 'Fácil',
                'slug' => 'easy',
                'points' => 10,
                'icon' => '⭐',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Medio',
                'slug' => 'medium',
                'points' => 25,
                'icon' => '⭐⭐',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Difícil',
                'slug' => 'hard',
                'points' => 50,
                'icon' => '⭐⭐⭐',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Épico',
                'slug' => 'epic',
                'points' => 100,
                'icon' => '🔥',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($difficulties as $difficulty) {
            \App\Models\Difficulty::create($difficulty);
        }
    }
}
