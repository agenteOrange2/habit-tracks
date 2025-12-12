<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use App\Enums\{HabitDifficulty, HabitFrequency};
use Carbon\Carbon;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'difficulty',
        'category_id',
        'difficulty_id',
        'frequency',
        'schedule',
        'is_recurring',
        'points_reward',
        'current_streak',
        'best_streak',
        'estimated_pomodoros',
        'is_active',
        'color',
        'icon',
        'reminder_enabled',
        'reminder_time',
        'archived_atd'
    ];

    protected $casts = [
        'schedule' => 'array',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'reminder_enabled' => 'boolean',
        'archived_at' => 'datetime',
        // category and difficulty are now strings (slugs) for backward compatibility
        // Use category_id and difficulty_id relationships instead
        'frequency' => HabitFrequency::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    public function pomodoroSessions(): HasMany
    {
        return $this->hasMany(PomodoroSession::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(HabitReminder::class);
    }

    // New dynamic relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function difficulty(): BelongsTo
    {
        return $this->belongsTo(Difficulty::class, 'difficulty_id');
    }

    // Deprecated: Use category() relationship instead
    public function categoryRelation(): BelongsTo
    {
        return $this->category();
    }

    // Deprecated: Use difficulty() relationship instead
    public function difficultyRelation(): BelongsTo
    {
        return $this->difficulty();
    }

    public function isCompletedToday(): bool
    {
        return $this->logs()
            ->whereDate('completed_date', today())
            ->exists();
    }

    public function isScheduledForToday(): bool
    {
        // Si el hábito no está activo, no se muestra
        if (!$this->is_active) {
            return false;
        }

        // Si no es recurrente, verificar si tiene una fecha específica
        if (!$this->is_recurring) {
            return false;
        }

        // Hábitos diarios siempre se muestran
        if ($this->frequency === HabitFrequency::DAILY) {
            return true;
        }

        // Hábitos semanales: verificar si hoy está en los días seleccionados
        if ($this->frequency === HabitFrequency::WEEKLY) {
            $todayNumber = now()->dayOfWeek;
            $scheduledDays = $this->schedule['days'] ?? [];
            
            // Si no hay días configurados, mostrar todos los días por defecto
            if (empty($scheduledDays)) {
                return true;
            }
            
            // Convertir ambos a string para comparación (los días se guardan como strings)
            return in_array((string)$todayNumber, array_map('strval', $scheduledDays));
        }

        // Hábitos personalizados: mostrar por defecto
        if ($this->frequency === HabitFrequency::CUSTOM) {
            return true;
        }

        // Por defecto, no mostrar
        return false;
    }

    public function isScheduledForDay(Carbon $date): bool
    {
        if(!$this->is_recurring)
        {
            return false;
        }

        if($this->frequency === HabitFrequency::DAILY)
        {
            return true;
        }

        if($this->frequency === HabitFrequency::WEEKLY)
        {
            $dayNumber = $date->dayOfWeek;
            return in_array($dayNumber, $this->schedule['days'] ?? []);
        }

        return false;
    }

    public function getCompletionRateAttribute(): float
    {
        if (!$this->created_at) {
            return 0;
        }

        $totalDays = $this->created_at->diffInDays(now());
        $completedDays = $this->logs()->count();

        if($totalDays === 0)
        {
            return 0;
        }

        return round(($completedDays / $totalDays) * 100, 1);
    }

    /**
     * Get the category name from the relationship or fallback to enum
     * This provides backward compatibility during migration
     */
    public function getCategoryName(): ?string
    {
        // Try to get from relationship first
        if ($this->category_id) {
            $categoryRelation = $this->category()->first();
            if ($categoryRelation) {
                return $categoryRelation->name;
            }
        }

        // Fallback to old enum field
        if ($this->attributes['category'] ?? null) {
            return $this->attributes['category'];
        }

        return null;
    }

    /**
     * Get the category icon from the relationship or fallback to default
     */
    public function getCategoryIcon(): ?string
    {
        if ($this->category_id) {
            $categoryRelation = $this->category()->first();
            if ($categoryRelation) {
                return $categoryRelation->icon;
            }
        }

        return null;
    }

    /**
     * Get the category color from the relationship or fallback to default
     */
    public function getCategoryColor(): ?string
    {
        if ($this->category_id) {
            $categoryRelation = $this->category()->first();
            if ($categoryRelation) {
                return $categoryRelation->color;
            }
        }

        return null;
    }

    /**
     * Get the difficulty name from the relationship or fallback to enum
     * This provides backward compatibility during migration
     */
    public function getDifficultyName(): ?string
    {
        // Try to get from relationship first
        if ($this->difficulty_id) {
            $difficultyRelation = $this->difficulty()->first();
            if ($difficultyRelation) {
                return $difficultyRelation->name;
            }
        }

        // Fallback to old enum field
        if ($this->attributes['difficulty'] ?? null) {
            return $this->attributes['difficulty'];
        }

        return null;
    }

    /**
     * Get the difficulty points from the relationship or fallback to enum
     */
    public function getDifficultyPoints(): ?int
    {
        // Try to get from relationship first
        if ($this->difficulty_id) {
            $difficultyRelation = $this->difficulty()->first();
            if ($difficultyRelation) {
                return $difficultyRelation->points;
            }
        }

        // Fallback to points_reward field if using old system
        return $this->points_reward;
    }

    /**
     * Get the difficulty icon from the relationship
     */
    public function getDifficultyIcon(): ?string
    {
        if ($this->difficulty_id) {
            $difficultyRelation = $this->difficulty()->first();
            if ($difficultyRelation) {
                return $difficultyRelation->icon;
            }
        }

        return null;
    }

    /**
     * Check if this habit is using the new dynamic system
     */
    public function isUsingDynamicSettings(): bool
    {
        return $this->category_id !== null && $this->difficulty_id !== null;
    }

    /**
     * Check if this habit is using the old enum system
     */
    public function isUsingEnumSettings(): bool
    {
        return !$this->isUsingDynamicSettings() && 
               (isset($this->attributes['category']) || isset($this->attributes['difficulty']));
    }

}
