<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Habit, Category, Difficulty};
use App\Enums\HabitFrequency;

class HabitSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener el primer usuario (o crear uno si no existe)
        $user = User::first();
        
        if (!$user) {
            $this->command->error('No hay usuarios en la base de datos. Crea un usuario primero.');
            return;
        }

        // Obtener mapeo de categorías y dificultades por slug
        $categories = Category::pluck('id', 'slug')->toArray();
        $difficulties = Difficulty::pluck('id', 'slug')->toArray();

        $this->command->info("Creando hábitos para el usuario: {$user->name}");

        $habits = [
            // HÁBITOS DIARIOS - Productividad
            [
                'name' => 'Revisar emails y planificar el día',
                'description' => 'Revisar correos importantes y crear lista de tareas del día',
                'category' => 'productivity',
                'difficulty' => 'easy',
                'frequency' => 'daily',
                'schedule' => null,
                'color' => '#3B82F6',
                'icon' => '📧',
                'estimated_pomodoros' => 1,
                'reminder_enabled' => true,
                'reminder_time' => '08:00',
            ],
            /*
            [
                'name' => 'Estudiar programación 1 hora',
                'description' => 'Dedicar tiempo a aprender nuevas tecnologías o mejorar habilidades',
                'category' => 'learning',
                'difficulty' => 'medium',
                'frequency' => 'daily',
                'schedule' => null,
                'color' => '#8B5CF6',
                'icon' => '💻',
                'estimated_pomodoros' => 2,
                'reminder_enabled' => true,
                'reminder_time' => '19:00',
            ],
            [
                'name' => 'Leer 30 minutos',
                'description' => 'Leer libros técnicos, novelas o artículos educativos',
                'category' => 'learning',
                'difficulty' => 'easy',
                'frequency' => 'daily',
                'schedule' => null,
                'color' => '#8B5CF6',
                'icon' => '📚',
                'estimated_pomodoros' => 1,
                'reminder_enabled' => true,
                'reminder_time' => '21:00',
            ],
            */

            // HÁBITOS DIARIOS - Salud
            [
                'name' => 'Hacer ejercicio',
                'description' => 'Rutina de ejercicio: cardio, pesas o yoga',
                'category' => 'health',
                'difficulty' => 'medium',
                'frequency' => 'daily',
                'schedule' => null,
                'color' => '#10B981',
                'icon' => '🏃',
                'estimated_pomodoros' => 2,
                'reminder_enabled' => true,
                'reminder_time' => '07:00',
            ],
            /*
            [
                'name' => 'Beber 2 litros de agua',
                'description' => 'Mantener hidratación adecuada durante el día',
                'category' => 'health',
                'difficulty' => 'easy',
                'frequency' => 'daily',
                'schedule' => null,
                'color' => '#10B981',
                'icon' => '💧',
                'estimated_pomodoros' => null,
                'reminder_enabled' => false,
                'reminder_time' => null,
            ],
            [
                'name' => 'Meditar 10 minutos',
                'description' => 'Práctica de mindfulness o meditación guiada',
                'category' => 'health',
                'difficulty' => 'easy',
                'frequency' => 'daily',
                'schedule' => null,
                'color' => '#10B981',
                'icon' => '🧘',
                'estimated_pomodoros' => 1,
                'reminder_enabled' => true,
                'reminder_time' => '06:30',
            ],
            */

            // HÁBITOS SEMANALES - Hogar
            [
                'name' => 'Limpiar la cocina a fondo',
                'description' => 'Limpieza profunda de cocina, refrigerador y despensa',
                'category' => 'household',
                'difficulty' => 'medium',
                'frequency' => 'weekly',
                'schedule' => [
                    'days' => ['6'], // Sábado
                    'time' => '10:00',
                ],
                'color' => '#6366F1',
                'icon' => '🧹',
                'estimated_pomodoros' => 2,
                'reminder_enabled' => true,
                'reminder_time' => '10:00',
            ],
            /*
            [
                'name' => 'Lavar ropa',
                'description' => 'Lavar, secar y doblar la ropa de la semana',
                'category' => 'household',
                'difficulty' => 'easy',
                'frequency' => 'weekly',
                'schedule' => [
                    'days' => ['0', '3'], // Domingo y Miércoles
                    'time' => '14:00',
                ],
                'color' => '#6366F1',
                'icon' => '👕',
                'estimated_pomodoros' => 1,
                'reminder_enabled' => true,
                'reminder_time' => '14:00',
            ],
            [
                'name' => 'Organizar escritorio',
                'description' => 'Ordenar espacio de trabajo y eliminar desorden',
                'category' => 'household',
                'difficulty' => 'easy',
                'frequency' => 'weekly',
                'schedule' => [
                    'days' => ['5'], // Viernes
                    'time' => '18:00',
                ],
                'color' => '#6366F1',
                'icon' => '🗂️',
                'estimated_pomodoros' => 1,
                'reminder_enabled' => false,
                'reminder_time' => null,
            ],
            */

            // HÁBITOS SEMANALES - Desarrollo Personal
            [
                'name' => 'Practicar algoritmos',
                'description' => 'Resolver problemas de LeetCode, HackerRank o similar',
                'category' => 'learning',
                'difficulty' => 'hard',
                'frequency' => 'weekly',
                'schedule' => [
                    'days' => ['1', '3', '5'], // Lunes, Miércoles, Viernes
                    'time' => '20:00',
                ],
                'color' => '#8B5CF6',
                'icon' => '🧠',
                'estimated_pomodoros' => 2,
                'reminder_enabled' => true,
                'reminder_time' => '20:00',
            ],     
            /*       
            [
                'name' => 'Trabajar en proyecto personal',
                'description' => 'Dedicar tiempo a proyectos side projects o portfolio',
                'category' => 'productivity',
                'difficulty' => 'medium',
                'frequency' => 'weekly',
                'schedule' => [
                    'days' => ['6', '0'], // Sábado y Domingo
                    'time' => '16:00',
                ],
                'color' => '#3B82F6',
                'icon' => '🚀',
                'estimated_pomodoros' => 4,
                'reminder_enabled' => true,
                'reminder_time' => '16:00',
            ],
            */

            // HÁBITOS SEMANALES - Social y Creatividad
            [
                'name' => 'Llamar a familia/amigos',
                'description' => 'Mantener contacto con seres queridos',
                'category' => 'social',
                'difficulty' => 'easy',
                'frequency' => 'weekly',
                'schedule' => [
                    'days' => ['0'], // Domingo
                    'time' => '11:00',
                ],
                'color' => '#F59E0B',
                'icon' => '📞',
                'estimated_pomodoros' => 1,
                'reminder_enabled' => true,
                'reminder_time' => '11:00',
            ],
            /*
            [
                'name' => 'Cocinar comida saludable (meal prep)',
                'description' => 'Preparar comidas saludables para la semana',
                'category' => 'health',
                'difficulty' => 'medium',
                'frequency' => 'weekly',
                'schedule' => [
                    'days' => ['0'], // Domingo
                    'time' => '15:00',
                ],
                'color' => '#10B981',
                'icon' => '🍎',
                'estimated_pomodoros' => 3,
                'reminder_enabled' => true,
                'reminder_time' => '15:00',
            ],
            [
                'name' => 'Escribir en diario/journal',
                'description' => 'Reflexionar sobre la semana y establecer metas',
                'category' => 'personal',
                'difficulty' => 'easy',
                'frequency' => 'weekly',
                'schedule' => [
                    'days' => ['0'], // Domingo
                    'time' => '22:00',
                ],
                'color' => '#F97316',
                'icon' => '✍️',
                'estimated_pomodoros' => 1,
                'reminder_enabled' => false,
                'reminder_time' => null,
            ],
            */

            // HÁBITOS ÉPICOS - Desafíos
            [
                'name' => 'Completar curso online',
                'description' => 'Avanzar en curso de Udemy, Platzi o similar',
                'category' => 'learning',
                'difficulty' => 'epic',
                'frequency' => 'weekly',
                'schedule' => [
                    'days' => ['2', '4'], // Martes y Jueves
                    'time' => '19:00',
                ],
                'color' => '#8B5CF6',
                'icon' => '🎓',
                'estimated_pomodoros' => 3,
                'reminder_enabled' => true,
                'reminder_time' => '19:00',
            ],
        ];

        foreach ($habits as $habitData) {
            // Obtener IDs de category y difficulty
            $categoryId = $categories[$habitData['category']] ?? null;
            $difficultyId = $difficulties[$habitData['difficulty']] ?? null;

            // Obtener puntos de la dificultad
            $difficulty = Difficulty::find($difficultyId);
            $pointsReward = $difficulty ? $difficulty->points : 10;

            Habit::create([
                'user_id' => $user->id,
                'name' => $habitData['name'],
                'description' => $habitData['description'],
                'category' => $habitData['category'],
                'difficulty' => $habitData['difficulty'],
                'category_id' => $categoryId,
                'difficulty_id' => $difficultyId,
                'frequency' => $habitData['frequency'],
                'schedule' => $habitData['schedule'],
                'is_recurring' => true,
                'is_active' => true,
                'points_reward' => $pointsReward,
                'color' => $habitData['color'],
                'icon' => $habitData['icon'],
                'estimated_pomodoros' => $habitData['estimated_pomodoros'],
                'reminder_enabled' => $habitData['reminder_enabled'],
                'reminder_time' => $habitData['reminder_time'],
                'current_streak' => 0,
                'best_streak' => 0,
            ]);

            $this->command->info("✓ Creado: {$habitData['name']}");
        }

        $this->command->info("\n🎉 Se crearon " . count($habits) . " hábitos exitosamente!");
    }
}
