<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Mood;

class JournalEntry extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'habit_log_id',
        'content',
        'mood',
        'energy_level',
    ];

    protected $casts = [
        'mood' => Mood::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function habitLog(): BelongsTo
    {
        return $this->belongsTo(HabitLog::class);
    }

    
    public function getWordCountAttribute(): int
    {
        return str_word_count($this->content);
    }
    
}
