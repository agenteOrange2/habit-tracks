<?php

namespace App\Enums;

enum HabitCategory: string
{
    case PRODUCTIVITY = 'productivity';
    case HEALTH = 'health';
    case LEARNING = 'learning';
    case SOCIAL = 'social';
    case CREATIVE = 'creative';
    case HOUSEHOLD = 'household';
    case FINANCE = 'finance';
    case PERSONAL = 'personal';
    
    public function label(): string
    {
        return match($this) {
            self::PRODUCTIVITY => 'Productividad',
            self::HEALTH => 'Salud',
            self::LEARNING => 'Aprendizaje',
            self::SOCIAL => 'Social',
            self::CREATIVE => 'Creatividad',
            self::HOUSEHOLD => 'Hogar',
            self::FINANCE => 'Finanzas',
            self::PERSONAL => 'Personal',
        };
    }
    
    public function icon(): string
    {
        return match($this) {
            self::PRODUCTIVITY => '💼',
            self::HEALTH => '💪',
            self::LEARNING => '📚',
            self::SOCIAL => '👥',
            self::CREATIVE => '🎨',
            self::HOUSEHOLD => '🏠',
            self::FINANCE => '💰',
            self::PERSONAL => '⭐',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::PRODUCTIVITY => '#3B82F6',
            self::HEALTH => '#10B981',
            self::LEARNING => '#8B5CF6',
            self::SOCIAL => '#F59E0B',
            self::CREATIVE => '#EC4899',
            self::HOUSEHOLD => '#6366F1',
            self::FINANCE => '#14B8A6',
            self::PERSONAL => '#F97316',
        };
    }
    
    public function bgClass(): string
    {
        return match($this) {
            self::PRODUCTIVITY => 'bg-blue-500',
            self::HEALTH => 'bg-green-500',
            self::LEARNING => 'bg-purple-500',
            self::SOCIAL => 'bg-yellow-500',
            self::CREATIVE => 'bg-pink-500',
            self::HOUSEHOLD => 'bg-indigo-500',
            self::FINANCE => 'bg-teal-500',
            self::PERSONAL => 'bg-orange-500',
        };
    }
    
    public function textClass(): string
    {
        return match($this) {
            self::PRODUCTIVITY => 'text-blue-600',
            self::HEALTH => 'text-green-600',
            self::LEARNING => 'text-purple-600',
            self::SOCIAL => 'text-yellow-600',
            self::CREATIVE => 'text-pink-600',
            self::HOUSEHOLD => 'text-indigo-600',
            self::FINANCE => 'text-teal-600',
            self::PERSONAL => 'text-orange-600',
        };
    }
}