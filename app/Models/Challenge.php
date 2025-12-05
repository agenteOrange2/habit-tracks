<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Challenge extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'target_value',
        'points_rewar',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $cast = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_challenges')
            ->withPivot(['status', 'progress', 'completed_at'])
            ->withTimestamps();
    }

    public function isActive(): bool
    {
        return $this->is_active 
            && now()->between($this->starts_at, $this->ends_at);
    }

    public function isCompleted(): bool
    {
        return now()->isAfter($this->ends_at);
    }

    public function daysRemaining(): int
    {
        if ($this->isCompleted()){
            return 0;
        }

        return now()->diffInDays($this->ends_at);
    }

    public function getProgressFor(User $user): int
    {
        $pivot = $this->user()
            ->where('user_id', $user->id)
            ->first()?->pivot;

        return $pivot?->progress ?? 0;
    }

    public function getProgressPercentageFor(User $user): float
    {
        $progress = $this->getProgressFor($user);

        if ($this->target_value === 0){
            return 0;
        }

        return min(($progress / $this->target_value) *100, 100);
    }
}
