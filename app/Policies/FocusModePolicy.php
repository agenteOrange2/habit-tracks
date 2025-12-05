<?php

namespace App\Policies;

use App\Models\{User, FocusMode};

class FocusModePolicy
{
    /**
     * Determine if the user can view any focus modes.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the focus mode.
     */
    public function view(User $user, FocusMode $focusMode): bool
    {
        return $user->id === $focusMode->user_id;
    }

    /**
     * Determine if the user can create focus modes.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the focus mode.
     */
    public function update(User $user, FocusMode $focusMode): bool
    {
        return $user->id === $focusMode->user_id;
    }

    /**
     * Determine if the user can delete the focus mode.
     */
    public function delete(User $user, FocusMode $focusMode): bool
    {
        return $user->id === $focusMode->user_id;
    }

    /**
     * Determine if the user can toggle the focus mode.
     */
    public function toggle(User $user, FocusMode $focusMode): bool
    {
        return $user->id === $focusMode->user_id;
    }
}