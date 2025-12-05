<?php

namespace App\Enums;

enum ChallengeStatus: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    
    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Activo',
            self::COMPLETED => 'Completado',
            self::FAILED => 'Fallido',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'bg-blue-100 text-blue-800',
            self::COMPLETED => 'bg-green-100 text-green-800',
            self::FAILED => 'bg-red-100 text-red-800',
        };
    }
    
    public function icon(): string
    {
        return match($this) {
            self::ACTIVE => '⏳',
            self::COMPLETED => '✅',
            self::FAILED => '❌',
        };
    }
}