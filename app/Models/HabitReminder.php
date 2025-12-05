<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitReminder extends Model
{

    use HasFactory;

    protected $fillable = [
        'habit_id',
        'user_id',
        'scheduled_time',
        'is_sent',
        'sent_at'
    ];
    
    protected $casts = [
        '´scheduled_time' => 'datetime',
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shouldBeSent(): bool
    {
        return !$this->is_sent 
            && $this->scheduled_time->isPast();
    }

    public function markAsSent(): void
    {
        $this->update([
            'is_sent' => true,
            'sent_at' => now(),
        ]);
    }
}
