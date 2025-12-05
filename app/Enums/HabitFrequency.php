<?php

namespace App\Enums;

enum HabitFrequency: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case CUSTOM = 'custom';
    
    public function label(): string
    {
        return match($this) {
            self::DAILY => 'Diario',
            self::WEEKLY => 'Semanal',
            self::CUSTOM => 'Personalizado',
        };
    }
    
    public function description(): string
    {
        return match($this) {
            self::DAILY => 'Todos los días',
            self::WEEKLY => 'Días específicos de la semana',
            self::CUSTOM => 'Horario personalizado',
        };
    }
    
    public function icon(): string
    {
        return match($this) {
            self::DAILY => '📅',
            self::WEEKLY => '📆',
            self::CUSTOM => '⚙️',
        };
    }
}