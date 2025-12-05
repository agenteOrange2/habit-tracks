<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasOne};


class HabitLog extends Model
{
    use HasFactory;

    protected $fillable = 
    [
        'habit_id',
        'user_id',
        'completed_date',
        'completed_time',
        'pomodoros_used',
        'points_earned',
        'notes',
    ];

    protected $casts = [
        'completed_date' => 'date',
        'completed_time' => 'datetime',
    ];

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function journalEntry(): HasOne
    {
        return $this->hasOne(journalEntry::class);
    }
}
