<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use App\Enums\RewardCategory;

class Reward extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'cost_points',
        'icon',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'category' => RewardCategory::class,        
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(RewardClaim::class);
    }

    public function getTimesClaimedAttribute(): int
    {
        return $this->claims()->count();
    }

    public function canBeClaimed(User $user): bool
    {
        return $this->is_available && $user->stats->available_points >= $this->cost_points;
    }
}
