<?php

namespace App\Enums;

enum RewardCategory: string
{
    case ENTERTAINMENT = 'entertainment';
    case FOOD = 'food';
    case LEISURE = 'leisure';
    case GAMING = 'gaming';
    case SHOPPING = 'shopping';
    case SOCIAL = 'social';
    
    public function label(): string
    {
        return match($this) {
            self::ENTERTAINMENT => 'Entretenimiento',
            self::FOOD => 'Comida',
            self::LEISURE => 'Ocio',
            self::GAMING => 'Videojuegos',
            self::SHOPPING => 'Compras',
            self::SOCIAL => 'Social',
        };
    }
    
    public function icon(): string
    {
        return match($this) {
            self::ENTERTAINMENT => '🎬',
            self::FOOD => '🍕',
            self::LEISURE => '🎯',
            self::GAMING => '🎮',
            self::SHOPPING => '🛍️',
            self::SOCIAL => '🎉',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::ENTERTAINMENT => 'bg-purple-100 text-purple-800',
            self::FOOD => 'bg-orange-100 text-orange-800',
            self::LEISURE => 'bg-blue-100 text-blue-800',
            self::GAMING => 'bg-red-100 text-red-800',
            self::SHOPPING => 'bg-pink-100 text-pink-800',
            self::SOCIAL => 'bg-green-100 text-green-800',
        };
    }
}