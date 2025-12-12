<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPomodoroSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'short_break_duration',
        'long_break_duration',
        'auto_start_breaks',
        'sound_enabled',
        'cycle_count',
    ];

    protected $casts = [
        'auto_start_breaks' => 'boolean',
        'sound_enabled' => 'boolean',
        'short_break_duration' => 'integer',
        'long_break_duration' => 'integer',
        'cycle_count' => 'integer',
    ];

    protected $attributes = [
        'short_break_duration' => 5,
        'long_break_duration' => 15,
        'auto_start_breaks' => true,
        'sound_enabled' => true,
        'cycle_count' => 0,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Increment the cycle count (0-4)
     */
    public function incrementCycle(): void
    {
        $this->cycle_count = ($this->cycle_count + 1) % 5;
        $this->save();
    }

    /**
     * Reset the cycle count to 0
     */
    public function resetCycle(): void
    {
        $this->cycle_count = 0;
        $this->save();
    }

    /**
     * Check if next break should be long
     */
    public function shouldBeLongBreak(): bool
    {
        return $this->cycle_count >= 4;
    }

    /**
     * Get the appropriate break duration based on cycle
     */
    public function getBreakDuration(): int
    {
        return $this->shouldBeLongBreak() 
            ? $this->long_break_duration 
            : $this->short_break_duration;
    }

    /**
     * Get the break type based on cycle
     */
    public function getBreakType(): string
    {
        return $this->shouldBeLongBreak() ? 'long_break' : 'short_break';
    }
}
