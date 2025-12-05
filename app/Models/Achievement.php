<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Enums\AchievementType;

class Achievement extends Model
{
    
    protected $fillable = [
        'name',
        'description',
        'icon',
        'category',
        'requirement_type',
        'requirement_value',
        'points_reward',
        'is_secret',
    ];

    protected $cast = [
        'is_secret' => 'boolean',
        'requirement_type' => AchievementType::class,
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot(['unlocked_at', 'progress'])
            ->withTimestamps();
    }

    public function isUnlockedBy(User $user): bool
    {
        return $this->users()
            ->where('user_id', $user->id)
            ->whereNotNull('unlocked_at')
            ->exists();
    }

    public function getProgressFor(User $user): int
    {
        $pivot = $this->users()
            ->where('user_id', $user->id)
            ->first()?->pivot;

        return $pivot?->progress ?? 0;
    }
}
