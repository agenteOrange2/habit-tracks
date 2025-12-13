<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'habit_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'recurrence_type',
        'recurrence_days',
        'recurrence_end',
        'parent_event_id',
        'google_event_id',
        'sync_to_google',
        'reminder_minutes',
        'color',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'recurrence_days' => 'array',
        'recurrence_end' => 'date',
        'sync_to_google' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }

    public function parentEvent(): BelongsTo
    {
        return $this->belongsTo(CalendarEvent::class, 'parent_event_id');
    }

    public function childEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class, 'parent_event_id');
    }

    // Scopes
    public function scopeForDateRange($query, Carbon $start, Carbon $end)
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->whereBetween('start_time', [$start, $end])
              ->orWhereBetween('end_time', [$start, $end])
              ->orWhere(function ($q2) use ($start, $end) {
                  $q2->where('start_time', '<=', $start)
                     ->where('end_time', '>=', $end);
              });
        });
    }

    public function scopeForDate($query, Carbon $date)
    {
        return $query->whereDate('start_time', $date);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeWithGoogleSync($query)
    {
        return $query->where('sync_to_google', true);
    }

    // Helpers
    public function getDurationInMinutes(): int
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    public function isRecurring(): bool
    {
        return !empty($this->recurrence_type);
    }

    public function hasGoogleSync(): bool
    {
        return !empty($this->google_event_id);
    }
}
