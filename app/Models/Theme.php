<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Theme extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'cost_points',
        'primary_color',
        'secondary_color',
        'accent_color',
        'background_image',
        'is_premium',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
    ];

    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function canBeUnlockedBy(User $user): bool
    {
        if($this->cost_points === 0)
        {
            return true;
        }

        return $user->stats->available_points >= $this->cost_points;
    }

    public function isUnlockedBy(User $user): bool
    {
        // Si es gratis, está desbloqueado por defecto
        if ($this->cost_points === 0) {
            return true;
        }

        // Verificar si el usuario ya compró este tema
        // Podrías agregar una tabla pivot para esto
        return $user->theme_id === $this->id;
    }
}
