<?php

namespace App\Policies;

use App\Models\{User, JournalEntry};

class JournalEntryPolicy
{
    /**
     * Determine if the user can view any journal entries.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the journal entry.
     */
    public function view(User $user, JournalEntry $entry): bool
    {
        return $user->id === $entry->user_id;
    }

    /**
     * Determine if the user can create journal entries.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the journal entry.
     */
    public function update(User $user, JournalEntry $entry): bool
    {
        return $user->id === $entry->user_id;
    }

    /**
     * Determine if the user can delete the journal entry.
     */
    public function delete(User $user, JournalEntry $entry): bool
    {
        return $user->id === $entry->user_id;
    }
}