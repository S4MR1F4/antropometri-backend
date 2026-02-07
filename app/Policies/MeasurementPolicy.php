<?php

namespace App\Policies;

use App\Models\Measurement;
use App\Models\User;

/**
 * MeasurementPolicy per 09_security_access.md §3.3
 * 
 * Authorization rules:
 * - viewAny: all authenticated users
 * - view/update/delete: owner or admin
 * - create: all authenticated users
 */
class MeasurementPolicy
{
    /**
     * Determine whether the user can view any models.
     * All authenticated users can view measurements (scoped by BelongsToUser trait).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Owner or admin only.
     */
    public function view(User $user, Measurement $measurement): bool
    {
        return $user->isAdmin() || $measurement->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     * All authenticated users can create measurements.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * Owner or admin only.
     */
    public function update(User $user, Measurement $measurement): bool
    {
        return $user->isAdmin() || $measurement->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Owner or admin only.
     */
    public function delete(User $user, Measurement $measurement): bool
    {
        return $user->isAdmin() || $measurement->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     * Owner or admin only.
     */
    public function restore(User $user, Measurement $measurement): bool
    {
        return $user->isAdmin() || $measurement->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Admin only.
     */
    public function forceDelete(User $user, Measurement $measurement): bool
    {
        return $user->isAdmin();
    }
}
