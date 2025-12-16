<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class MilestoneBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'icon',
        'level_required',
        'description',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'level_required' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Default milestone badges configuration
     */
    public const DEFAULT_BADGES = [
        ['name' => 'Iniciado', 'icon' => '🥉', 'level_required' => 10, 'description' => 'Alcanza el nivel 10'],
        ['name' => 'Dedicado', 'icon' => '🥈', 'level_required' => 25, 'description' => 'Alcanza el nivel 25'],
        ['name' => 'Experto', 'icon' => '🥇', 'level_required' => 50, 'description' => 'Alcanza el nivel 50'],
        ['name' => 'Maestro', 'icon' => '💎', 'level_required' => 75, 'description' => 'Alcanza el nivel 75'],
        ['name' => 'Leyenda', 'icon' => '👑', 'level_required' => 100, 'description' => 'Alcanza el nivel 100'],
    ];

    /**
     * Get the user that owns the badge
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this badge is achieved by a user
     */
    public function isAchievedBy(User $user): bool
    {
        $currentLevel = $user->level?->current_level ?? 1;
        return $currentLevel >= $this->level_required;
    }

    /**
     * Check if this badge is achieved (uses the owner user)
     */
    public function getIsAchievedAttribute(): bool
    {
        return $this->isAchievedBy($this->user);
    }

    /**
     * Scope to order by sort_order
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('level_required');
    }

    /**
     * Scope to get only default badges
     */
    public function scopeDefaults(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to get only custom badges
     */
    public function scopeCustom(Builder $query): Builder
    {
        return $query->where('is_default', false);
    }

    /**
     * Create default badges for a user
     */
    public static function createDefaultsForUser(User $user): void
    {
        foreach (self::DEFAULT_BADGES as $index => $badge) {
            self::create([
                'user_id' => $user->id,
                'name' => $badge['name'],
                'icon' => $badge['icon'],
                'level_required' => $badge['level_required'],
                'description' => $badge['description'],
                'is_default' => true,
                'sort_order' => $index,
            ]);
        }
    }
}
