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
}
