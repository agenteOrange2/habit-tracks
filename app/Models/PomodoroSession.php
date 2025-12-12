<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PomodoroSession extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'habit_id',
        'user_id',
        'duration_minutes',
        'session_type',
        'resumed_from_id',
        'remaining_seconds',
        'started_at',
        'completed_at',
        'was_interrupted',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'was_interrupted' => 'boolean',
    ];

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resumedFrom(): BelongsTo
    {
        return $this->belongsTo(PomodoroSession::class, 'resumed_from_id');
    }

    public function resumedSessions()
    {
        return $this->hasMany(PomodoroSession::class, 'resumed_from_id');
    }

    public function isCompleted(): bool
    {
        return !is_null($this->completed_at) && !$this->was_interrupted;
    }

    public function canBeResumed(): bool
    {
        return $this->was_interrupted 
            && !is_null($this->remaining_seconds) 
            && $this->remaining_seconds > 0
            && !$this->resumedSessions()->exists();
    }

    public function getResumedSession(): ?PomodoroSession
    {
        return $this->resumedSessions()->first();
    }

    public function isBreak(): bool
    {
        return in_array($this->session_type, ['short_break', 'long_break']);
    }

    public function getSessionTypeLabel(): string
    {
        return match($this->session_type) {
            'pomodoro' => 'Pomodoro',
            'short_break' => 'Descanso Corto',
            'long_break' => 'Descanso Largo',
            default => 'Pomodoro',
        };
    }

    public function getDurationInSecondsAttribute(): int
    {
        return $this->duration_minutes * 60;
    }

    public function getElapsedTimeAttribute(): ?int
    {
        if(!$this->completed_at){
            return now()->diffInSeconds($this->started_at);
        }

        return $this->completed_at->diffInSeconds($this->started_at);
    }

    public function getFormattedDurationAttribute(): string
    {
        return $this->duration_minutes . ' min';
    }

    public function getStatusAttribute(): string
    {
        if ($this->was_interrupted) {
            return 'interrupted';
        }

        if ($this->completed_at) {
            return 'completed';
        }

        return 'in_progress';
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('was_interrupted', false)
            ->whereNotNull('completed_at');
    }

    public function scopeInterrupted($query)
    {
        return $query->where('was_interrupted', true);
    }

    public function scopeBreaks($query)
    {
        return $query->whereIn('session_type', ['short_break', 'long_break']);
    }

    public function scopePomodoros($query)
    {
        return $query->where('session_type', 'pomodoro');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('started_at', today());
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }
}
