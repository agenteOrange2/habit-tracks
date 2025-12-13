<?php

namespace App\Policies;

use App\Models\Difficulty;
use App\Models\User;

class DifficultyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Difficulty $difficulty): bool
    {
        return $user->id === $difficulty->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Difficulty $difficulty): bool
    {
        return $user->id === $difficulty->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Difficulty $difficulty): bool
    {
        return $user->id === $difficulty->user_id;
    }
}
