<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'reward_id',
        'user_id',
        'points_spent',
        'claimed_at',
        'was_enjoyed',
        'notes',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'was_enjoyed' => 'boolean',
    ];

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
