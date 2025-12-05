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
        'category' => \App\Enums\HabitCategory::class,
        'difficulty' => HabitDifficulty::class,
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

    public function isCompletedToday(): bool
    {
        return $this->logs()
            ->whereDate('completed_date', today())
            ->exists();
    }

    public function isScheduledForToday(): bool
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
            $todayNumber = now()->dayOfWeek;
            return in_array($todayNumber, $this->schedule['days'] ?? []);
        }

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
        $totalDays = $this->creted_at->diffInDays(now());
        $completedDays = $this->logs()->count();

        if($totalDays === 0)
        {
            return 0;
        }

        return ($completedDays / $totalDays) * 100;
    }

}
