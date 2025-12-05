<?php

namespace App\Enums;

enum Mood: string
{
    case GREAT = 'great';
    case GOOD = 'good';
    case NEUTRAL = 'neutral';
    case BAD = 'bad';
    case TERRIBLE = 'terrible';
    
    public function label(): string
    {
        return match($this) {
            self::GREAT => 'Excelente',
            self::GOOD => 'Bien',
            self::NEUTRAL => 'Normal',
            self::BAD => 'Mal',
            self::TERRIBLE => 'Terrible',
        };
    }
    
    public function emoji(): string
    {
        return match($this) {
            self::GREAT => '😄',
            self::GOOD => '🙂',
            self::NEUTRAL => '😐',
            self::BAD => '😞',
            self::TERRIBLE => '😢',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::GREAT => 'text-green-500',
            self::GOOD => 'text-blue-500',
            self::NEUTRAL => 'text-gray-500',
            self::BAD => 'text-orange-500',
            self::TERRIBLE => 'text-red-500',
        };
    }
    
    public function bgColor(): string
    {
        return match($this) {
            self::GREAT => 'bg-green-100',
            self::GOOD => 'bg-blue-100',
            self::NEUTRAL => 'bg-gray-100',
            self::BAD => 'bg-orange-100',
            self::TERRIBLE => 'bg-red-100',
        };
    }
}
