<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStats extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_points',
        'available_points',
        'total_habits_completed',
        'total_pomodoros',
        'current_global_streak',
        'best_global_streak',
        'last_activity_date',
        'total_focus_time',
        'weekly_points',
        'monthly_points',
        'week_start',
        'month_start',
    ];

    protected $casts = [
        'last_activity_date' => 'date',
        'week_start' => 'date',
        'month_start' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function getPointsSpentAttribute(): int
    {
        return $this->total_points - $this->available_points;
    }

    public function getTotalFocusTimeHoursAttribute(): float
    {
        return round($this->total_focus_time / 60, 2);
    }

    
}
