<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLevel extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_level',
        'current_xp',
        'total_xp'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRequiredXpAttribute(): int
    {
        return $this->current_level * 100;
    }

    public function getProgressPercentageAttribute(): float
    {
        if($this->required_xp === 0)
        {
            return 0;
        }

        return($this->current_xp / $this->required_xp) * 100;
    }

    public function getLevelTitleAttribute(): string
    {
        return match(true){
            $this->current_level >= 100 => 'Leyenda 👑',
            $this->current_level >= 75 => 'Maestro 🎯',
            $this->current_level >= 50 => 'Experto ⭐',
            $this->current_level >= 25 => 'Avanzado 🚀',
            $this->current_level >= 10 => 'Intermedio 📈',
            default => 'Principiante 🌱',
        };
    }

    public function getMilestoneBadgesAttribute(): array
    {
        $milestones = [10, 25, 50, 75, 100];
        $badges = [];

        foreach ($milestones as $milestone) {
            $badges[] = [
                'level' => $milestone,
                'icon' => $this->getMilestoneIcon($milestone),
                'name' => $this->getMilestoneName($milestone),
                'achieved' => $this->current_level >= $milestone,
            ];
        }

        return $badges;
    }

    public function getAchievedBadgesAttribute(): array
    {
        return array_filter($this->milestone_badges, fn($badge) => $badge['achieved']);
    }

    private function getMilestoneIcon(int $level): string
    {
        return match ($level) {
            10 => '🥉',
            25 => '🥈',
            50 => '🥇',
            75 => '💎',
            100 => '👑',
            default => '⭐',
        };
    }

    private function getMilestoneName(int $level): string
    {
        return match ($level) {
            10 => 'Iniciado',
            25 => 'Dedicado',
            50 => 'Experto',
            75 => 'Maestro',
            100 => 'Leyenda',
            default => "Nivel $level",
        };
    }
}
