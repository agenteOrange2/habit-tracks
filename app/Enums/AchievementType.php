<?php

namespace App\Enums;

enum AchievementType: string
{
    case HABIT_STREAK = 'habit_streak';
    case TOTAL_HABITS = 'total_habits';
    case POMODOROS = 'pomodoros';
    case POINTS = 'points';
    case CONSECUTIVE_DAYS = 'consecutive_days';
    case SPECIFIC_CATEGORY = 'specific_category';
    
    public function label(): string
    {
        return match($this) {
            self::HABIT_STREAK => 'Racha de Hábito',
            self::TOTAL_HABITS => 'Total de Hábitos',
            self::POMODOROS => 'Pomodoros Completados',
            self::POINTS => 'Puntos Acumulados',
            self::CONSECUTIVE_DAYS => 'Días Consecutivos',
            self::SPECIFIC_CATEGORY => 'Categoría Específica',
        };
    }
    
    public function description(): string
    {
        return match($this) {
            self::HABIT_STREAK => 'Mantén un hábito durante X días consecutivos',
            self::TOTAL_HABITS => 'Completa X hábitos en total',
            self::POMODOROS => 'Completa X sesiones de pomodoro',
            self::POINTS => 'Acumula X puntos en total',
            self::CONSECUTIVE_DAYS => 'Completa hábitos durante X días seguidos',
            self::SPECIFIC_CATEGORY => 'Completa X hábitos de una categoría',
        };
    }
}