<?php

namespace App\Policies;

use App\Models\{User, Habit};

class HabitPolicy
{
    /**
     * Determine if the user can view any habits.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the habit.
     */
    public function view(User $user, Habit $habit): bool
    {
        return $user->id === $habit->user_id;
    }

    /**
     * Determine if the user can create habits.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the habit.
     */
    public function update(User $user, Habit $habit): bool
    {
        return $user->id === $habit->user_id;
    }

    /**
     * Determine if the user can delete the habit.
     */
    public function delete(User $user, Habit $habit): bool
    {
        return $user->id === $habit->user_id;
    }

    /**
     * Determine if the user can restore the habit.
     */
    public function restore(User $user, Habit $habit): bool
    {
        return $user->id === $habit->user_id;
    }

    /**
     * Determine if the user can permanently delete the habit.
     */
    public function forceDelete(User $user, Habit $habit): bool
    {
        return $user->id === $habit->user_id;
    }

    /**
     * Determine if the user can complete the habit.
     */
    public function complete(User $user, Habit $habit): bool
    {
        return $user->id === $habit->user_id && $habit->is_active;
    }

    /**
     * Determine if the user can archive the habit.
     */
    public function archive(User $user, Habit $habit): bool
    {
        return $user->id === $habit->user_id;
    }
}