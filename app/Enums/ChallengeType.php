<?php

namespace App\Enums;

enum ChallengeType: string
{
    case WEEKLY_STREAK = 'weekly_streak';
    case DAILY_ALL = 'daily_all';
    case POMODORO_MARATHON = 'pomodoro_marathon';
    case CATEGORY_FOCUS = 'category_focus';
    case POINT_GOAL = 'point_goal';
    
    public function label(): string
    {
        return match($this) {
            self::WEEKLY_STREAK => 'Racha Semanal',
            self::DAILY_ALL => 'Día Perfecto',
            self::POMODORO_MARATHON => 'Maratón Pomodoro',
            self::CATEGORY_FOCUS => 'Enfoque en Categoría',
            self::POINT_GOAL => 'Meta de Puntos',
        };
    }
    
    public function description(): string
    {
        return match($this) {
            self::WEEKLY_STREAK => 'Completa al menos un hábito cada día de la semana',
            self::DAILY_ALL => 'Completa todos tus hábitos programados en un día',
            self::POMODORO_MARATHON => 'Completa múltiples sesiones de pomodoro',
            self::CATEGORY_FOCUS => 'Completa hábitos de una categoría específica',
            self::POINT_GOAL => 'Alcanza una meta de puntos',
        };
    }
    
    public function icon(): string
    {
        return match($this) {
            self::WEEKLY_STREAK => '🔥',
            self::DAILY_ALL => '⭐',
            self::POMODORO_MARATHON => '🍅',
            self::CATEGORY_FOCUS => '🎯',
            self::POINT_GOAL => '💎',
        };
    }
}