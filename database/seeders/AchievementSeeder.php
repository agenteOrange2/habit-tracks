<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Enums\AchievementType;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            // ============================================
            // TOTAL_HABITS - Por completar X hábitos
            // ============================================
            [
                'name' => 'Primer Paso',
                'description' => 'Completa tu primer hábito',
                'icon' => '🌱',
                'category' => 'habits',
                'requirement_type' => AchievementType::TOTAL_HABITS->value,
                'requirement_value' => 1,
                'points_reward' => 10,
                'is_secret' => false,
            ],
            [
                'name' => 'En Marcha',
                'description' => 'Completa 5 hábitos en total',
                'icon' => '🚶',
                'category' => 'habits',
                'requirement_type' => AchievementType::TOTAL_HABITS->value,
                'requirement_value' => 5,
                'points_reward' => 15,
                'is_secret' => false,
            ],
            [
                'name' => 'Dedicado',
                'description' => 'Completa 10 hábitos en total',
                'icon' => '💪',
                'category' => 'habits',
                'requirement_type' => AchievementType::TOTAL_HABITS->value,
                'requirement_value' => 10,
                'points_reward' => 25,
                'is_secret' => false,
            ],
            [
                'name' => 'Constante',
                'description' => 'Completa 25 hábitos en total',
                'icon' => '🎯',
                'category' => 'habits',
                'requirement_type' => AchievementType::TOTAL_HABITS->value,
                'requirement_value' => 25,
                'points_reward' => 40,
                'is_secret' => false,
            ],
            [
                'name' => 'Disciplinado',
                'description' => 'Completa 50 hábitos en total',
                'icon' => '⭐',
                'category' => 'habits',
                'requirement_type' => AchievementType::TOTAL_HABITS->value,
                'requirement_value' => 50,
                'points_reward' => 75,
                'is_secret' => false,
            ],
            [
                'name' => 'Maestro de Hábitos',
                'description' => 'Completa 100 hábitos en total',
                'icon' => '🏆',
                'category' => 'habits',
                'requirement_type' => AchievementType::TOTAL_HABITS->value,
                'requirement_value' => 100,
                'points_reward' => 150,
                'is_secret' => false,
            ],
            [
                'name' => 'Leyenda',
                'description' => 'Completa 500 hábitos en total',
                'icon' => '👑',
                'category' => 'habits',
                'requirement_type' => AchievementType::TOTAL_HABITS->value,
                'requirement_value' => 500,
                'points_reward' => 500,
                'is_secret' => false,
            ],

            // ============================================
            // HABIT_STREAK - Por mantener racha
            // ============================================
            [
                'name' => 'Racha Inicial',
                'description' => 'Mantén una racha de 3 días',
                'icon' => '🔥',
                'category' => 'streaks',
                'requirement_type' => AchievementType::HABIT_STREAK->value,
                'requirement_value' => 3,
                'points_reward' => 15,
                'is_secret' => false,
            ],
            [
                'name' => 'Una Semana',
                'description' => 'Mantén una racha de 7 días',
                'icon' => '📅',
                'category' => 'streaks',
                'requirement_type' => AchievementType::HABIT_STREAK->value,
                'requirement_value' => 7,
                'points_reward' => 30,
                'is_secret' => false,
            ],
            [
                'name' => 'Dos Semanas',
                'description' => 'Mantén una racha de 14 días',
                'icon' => '🌟',
                'category' => 'streaks',
                'requirement_type' => AchievementType::HABIT_STREAK->value,
                'requirement_value' => 14,
                'points_reward' => 50,
                'is_secret' => false,
            ],
            [
                'name' => 'Un Mes Completo',
                'description' => 'Mantén una racha de 30 días',
                'icon' => '🗓️',
                'category' => 'streaks',
                'requirement_type' => AchievementType::HABIT_STREAK->value,
                'requirement_value' => 30,
                'points_reward' => 100,
                'is_secret' => false,
            ],
            [
                'name' => 'Imparable',
                'description' => 'Mantén una racha de 60 días',
                'icon' => '💎',
                'category' => 'streaks',
                'requirement_type' => AchievementType::HABIT_STREAK->value,
                'requirement_value' => 60,
                'points_reward' => 200,
                'is_secret' => false,
            ],
            [
                'name' => 'Racha Centenaria',
                'description' => 'Mantén una racha de 100 días',
                'icon' => '🏅',
                'category' => 'streaks',
                'requirement_type' => AchievementType::HABIT_STREAK->value,
                'requirement_value' => 100,
                'points_reward' => 500,
                'is_secret' => false,
            ],

            // ============================================
            // POMODOROS - Por completar pomodoros
            // ============================================
            [
                'name' => 'Primer Pomodoro',
                'description' => 'Completa tu primer pomodoro',
                'icon' => '🍅',
                'category' => 'pomodoro',
                'requirement_type' => AchievementType::POMODOROS->value,
                'requirement_value' => 1,
                'points_reward' => 10,
                'is_secret' => false,
            ],
            [
                'name' => 'Enfocado',
                'description' => 'Completa 10 pomodoros',
                'icon' => '🎧',
                'category' => 'pomodoro',
                'requirement_type' => AchievementType::POMODOROS->value,
                'requirement_value' => 10,
                'points_reward' => 25,
                'is_secret' => false,
            ],
            [
                'name' => 'Productivo',
                'description' => 'Completa 25 pomodoros',
                'icon' => '⚡',
                'category' => 'pomodoro',
                'requirement_type' => AchievementType::POMODOROS->value,
                'requirement_value' => 25,
                'points_reward' => 50,
                'is_secret' => false,
            ],
            [
                'name' => 'Máquina de Productividad',
                'description' => 'Completa 50 pomodoros',
                'icon' => '🚀',
                'category' => 'pomodoro',
                'requirement_type' => AchievementType::POMODOROS->value,
                'requirement_value' => 50,
                'points_reward' => 100,
                'is_secret' => false,
            ],
            [
                'name' => 'Pomodoro Master',
                'description' => 'Completa 100 pomodoros',
                'icon' => '🧠',
                'category' => 'pomodoro',
                'requirement_type' => AchievementType::POMODOROS->value,
                'requirement_value' => 100,
                'points_reward' => 200,
                'is_secret' => false,
            ],

            // ============================================
            // POINTS - Por ganar puntos
            // ============================================
            [
                'name' => 'Primeros Puntos',
                'description' => 'Gana tus primeros 100 puntos',
                'icon' => '💰',
                'category' => 'points',
                'requirement_type' => AchievementType::POINTS->value,
                'requirement_value' => 100,
                'points_reward' => 10,
                'is_secret' => false,
            ],
            [
                'name' => 'Acumulador',
                'description' => 'Gana 500 puntos en total',
                'icon' => '💵',
                'category' => 'points',
                'requirement_type' => AchievementType::POINTS->value,
                'requirement_value' => 500,
                'points_reward' => 25,
                'is_secret' => false,
            ],
            [
                'name' => 'Rico en Puntos',
                'description' => 'Gana 1000 puntos en total',
                'icon' => '💎',
                'category' => 'points',
                'requirement_type' => AchievementType::POINTS->value,
                'requirement_value' => 1000,
                'points_reward' => 50,
                'is_secret' => false,
            ],
            [
                'name' => 'Millonario',
                'description' => 'Gana 5000 puntos en total',
                'icon' => '🤑',
                'category' => 'points',
                'requirement_type' => AchievementType::POINTS->value,
                'requirement_value' => 5000,
                'points_reward' => 200,
                'is_secret' => false,
            ],

            // ============================================
            // CONSECUTIVE_DAYS - Días perfectos
            // ============================================
            [
                'name' => 'Día Perfecto',
                'description' => 'Completa todos los hábitos del día',
                'icon' => '✨',
                'category' => 'daily',
                'requirement_type' => AchievementType::CONSECUTIVE_DAYS->value,
                'requirement_value' => 1,
                'points_reward' => 20,
                'is_secret' => false,
            ],
            [
                'name' => 'Tres Días Perfectos',
                'description' => '3 días seguidos completando todos los hábitos',
                'icon' => '🌈',
                'category' => 'daily',
                'requirement_type' => AchievementType::CONSECUTIVE_DAYS->value,
                'requirement_value' => 3,
                'points_reward' => 40,
                'is_secret' => false,
            ],
            [
                'name' => 'Semana Perfecta',
                'description' => '7 días seguidos completando todos los hábitos',
                'icon' => '🎖️',
                'category' => 'daily',
                'requirement_type' => AchievementType::CONSECUTIVE_DAYS->value,
                'requirement_value' => 7,
                'points_reward' => 100,
                'is_secret' => false,
            ],
            [
                'name' => 'Mes Perfecto',
                'description' => '30 días seguidos completando todos los hábitos',
                'icon' => '🏆',
                'category' => 'daily',
                'requirement_type' => AchievementType::CONSECUTIVE_DAYS->value,
                'requirement_value' => 30,
                'points_reward' => 500,
                'is_secret' => true, // Logro secreto!
            ],

            // ============================================
            // LOGROS SECRETOS
            // ============================================
            [
                'name' => 'Madrugador',
                'description' => 'Completa un hábito antes de las 6 AM',
                'icon' => '🌅',
                'category' => 'special',
                'requirement_type' => AchievementType::SPECIFIC_CATEGORY->value,
                'requirement_value' => 1,
                'points_reward' => 50,
                'is_secret' => true,
            ],
            [
                'name' => 'Búho Nocturno',
                'description' => 'Completa un hábito después de las 11 PM',
                'icon' => '🦉',
                'category' => 'special',
                'requirement_type' => AchievementType::SPECIFIC_CATEGORY->value,
                'requirement_value' => 2,
                'points_reward' => 50,
                'is_secret' => true,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::updateOrCreate(
                [
                    'name' => $achievement['name'],
                    'requirement_type' => $achievement['requirement_type'],
                ],
                $achievement
            );
        }

        $this->command->info('✅ ' . count($achievements) . ' logros creados/actualizados');
    }
}
