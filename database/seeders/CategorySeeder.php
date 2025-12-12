<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Salud',
                'slug' => 'health',
                'icon' => '💪',
                'color' => '#10B981',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Productividad',
                'slug' => 'productivity',
                'icon' => '💼',
                'color' => '#3B82F6',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Aprendizaje',
                'slug' => 'learning',
                'icon' => '📚',
                'color' => '#8B5CF6',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Social',
                'slug' => 'social',
                'icon' => '👥',
                'color' => '#F59E0B',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Creatividad',
                'slug' => 'creative',
                'icon' => '🎨',
                'color' => '#EC4899',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Hogar',
                'slug' => 'household',
                'icon' => '🏠',
                'color' => '#6366F1',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Finanzas',
                'slug' => 'finance',
                'icon' => '💰',
                'color' => '#14B8A6',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Personal',
                'slug' => 'personal',
                'icon' => '⭐',
                'color' => '#F97316',
                'order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Desarrollo personal',
                'slug' => 'desarrollo-personal',
                'icon' => '💪',
                'color' => '#8B5CF6',
                'order' => 9,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
