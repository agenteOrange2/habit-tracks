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

    public function isCompleted(): bool
    {
        return !is_null($this->completed_at) && !$this->was_interrupted;
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
}
