<?php

namespace App\Policies;

use App\Models\Roastery;
use App\Models\User;

class RoasteryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['developer', 'roastery']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Roastery $roastery): bool
    {
        return $user->role === 'developer' || ($user->role === 'roastery' && $user->id === $roastery->owner_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'developer' || $user->role === 'roastery';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Roastery $roastery): bool
    {
        return $user->role === 'developer' || ($user->role === 'roastery' && $user->id === $roastery->owner_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Roastery $roastery): bool
    {
        return $user->role === 'developer';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Roastery $roastery): bool
    {
        return $user->role === 'developer';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Roastery $roastery): bool
    {
        return $user->role === 'developer';
    }
}
