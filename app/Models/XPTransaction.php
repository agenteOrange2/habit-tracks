<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class XPTransaction extends Model
{
    use HasFactory;

    protected $table = 'xp_transactions';

    protected $fillable = [
        'user_id',
        'amount',
        'source_type',
        'source_id',
        'source_name',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    // Source types
    public const SOURCE_HABIT = 'habit';
    public const SOURCE_POMODORO = 'pomodoro';
    public const SOURCE_LEVEL_BONUS = 'level_bonus';
    public const SOURCE_MILESTONE_BONUS = 'milestone_bonus';
    public const SOURCE_STREAK_BONUS = 'streak_bonus';
    public const SOURCE_DAILY_COMPLETION = 'daily_completion';
    public const SOURCE_EARLY_BIRD = 'early_bird';

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ]);
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ]);
    }

    public function scopeRecent(Builder $query, int $limit = 10): Builder
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeBySourceType(Builder $query, string $sourceType): Builder
    {
        return $query->where('source_type', $sourceType);
    }

    // Helpers
    public function getSourceIconAttribute(): string
    {
        return match ($this->source_type) {
            self::SOURCE_HABIT => '✅',
            self::SOURCE_POMODORO => '🍅',
            self::SOURCE_LEVEL_BONUS => '⬆️',
            self::SOURCE_MILESTONE_BONUS => '🏆',
            self::SOURCE_STREAK_BONUS => '🔥',
            self::SOURCE_DAILY_COMPLETION => '🎯',
            self::SOURCE_EARLY_BIRD => '🌅',
            default => '⭐',
        };
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source_type) {
            self::SOURCE_HABIT => 'Hábito completado',
            self::SOURCE_POMODORO => 'Pomodoro',
            self::SOURCE_LEVEL_BONUS => 'Bonus de nivel',
            self::SOURCE_MILESTONE_BONUS => 'Bonus milestone',
            self::SOURCE_STREAK_BONUS => 'Bonus de racha',
            self::SOURCE_DAILY_COMPLETION => 'Día completo',
            self::SOURCE_EARLY_BIRD => 'Madrugador',
            default => 'XP',
        };
    }
}
