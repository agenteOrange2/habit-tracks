<?php

namespace App\Enums;

enum HabitDifficulty: string
{
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';
    case EPIC = 'epic';
    
    public function points(): int
    {
        return match($this) {
            self::EASY => 10,
            self::MEDIUM => 25,
            self::HARD => 50,
            self::EPIC => 100,
        };
    }
    
    public function label(): string
    {
        return match($this) {
            self::EASY => 'Fácil',
            self::MEDIUM => 'Medio',
            self::HARD => 'Difícil',
            self::EPIC => 'Épico',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::EASY => 'bg-green-100 text-green-800 border-green-200',
            self::MEDIUM => 'bg-blue-100 text-blue-800 border-blue-200',
            self::HARD => 'bg-purple-100 text-purple-800 border-purple-200',
            self::EPIC => 'bg-red-100 text-red-800 border-red-200',
        };
    }
    
    public function icon(): string
    {
        return match($this) {
            self::EASY => '⭐',
            self::MEDIUM => '⭐⭐',
            self::HARD => '⭐⭐⭐',
            self::EPIC => '🔥',
        };
    }
}