<?php

namespace App\Policies;

use App\Models\{User, Difficulty};

class DifficultyPolicy
{
    /**
     * Determine if the user can view any difficulties.
     */
    public function viewAny(User $user): bool
    {
        // For now, all authenticated users can view difficulties
        // In the future, this could be restricted to admins only
        return true;
    }

    /**
     * Determine if the user can view the difficulty.
     */
    public function view(User $user, Difficulty $difficulty): bool
    {
        return true;
    }

    /**
     * Determine if the user can create difficulties.
     */
    public function create(User $user): bool
    {
        // For now, all authenticated users can create difficulties
        // In the future, this could be restricted to admins only
        return true;
    }

    /**
     * Determine if the user can update the difficulty.
     */
    public function update(User $user, Difficulty $difficulty): bool
    {
        // For now, all authenticated users can update difficulties
        // In the future, this could be restricted to admins only
        return true;
    }

    /**
     * Determine if the user can delete the difficulty.
     */
    public function delete(User $user, Difficulty $difficulty): bool
    {
        // For now, all authenticated users can delete difficulties
        // In the future, this could be restricted to admins only
        return true;
    }
}
