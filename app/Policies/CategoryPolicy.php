<?php

namespace App\Policies;

use App\Models\{User, Category};

class CategoryPolicy
{
    /**
     * Determine if the user can view any categories.
     */
    public function viewAny(User $user): bool
    {
        // For now, all authenticated users can view categories
        // In the future, this could be restricted to admins only
        return true;
    }

    /**
     * Determine if the user can view the category.
     */
    public function view(User $user, Category $category): bool
    {
        return true;
    }

    /**
     * Determine if the user can create categories.
     */
    public function create(User $user): bool
    {
        // For now, all authenticated users can create categories
        // In the future, this could be restricted to admins only
        return true;
    }

    /**
     * Determine if the user can update the category.
     */
    public function update(User $user, Category $category): bool
    {
        // For now, all authenticated users can update categories
        // In the future, this could be restricted to admins only
        return true;
    }

    /**
     * Determine if the user can delete the category.
     */
    public function delete(User $user, Category $category): bool
    {
        // For now, all authenticated users can delete categories
        // In the future, this could be restricted to admins only
        return true;
    }
}
