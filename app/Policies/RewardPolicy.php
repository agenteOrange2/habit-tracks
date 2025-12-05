<?php

namespace App\Policies;

use App\Models\{User, Reward};

class RewardPolicy
{
    /**
     * Determine if the user can view any rewards.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the reward.
     */
    public function view(User $user, Reward $reward): bool
    {
        return $user->id === $reward->user_id;
    }

    /**
     * Determine if the user can create rewards.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the reward.
     */
    public function update(User $user, Reward $reward): bool
    {
        return $user->id === $reward->user_id;
    }

    /**
     * Determine if the user can delete the reward.
     */
    public function delete(User $user, Reward $reward): bool
    {
        return $user->id === $reward->user_id;
    }

    /**
     * Determine if the user can claim the reward.
     */
    public function claim(User $user, Reward $reward): bool
    {
        return $user->id === $reward->user_id 
            && $reward->is_available 
            && $user->stats->available_points >= $reward->cost_points;
    }
}