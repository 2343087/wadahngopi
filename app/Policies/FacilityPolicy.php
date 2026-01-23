<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\User;

class FacilityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin cannot see facilities
        if ($user->role === 'admin') {
            return false;
        }

        return true;
    }

    public function view(User $user, Facility $facility): bool
    {
        if ($user->role === 'admin') {
            return false;
        }

        return $user->id === $facility->cafe->owner_id;
    }

    public function create(User $user): bool
    {
        if ($user->role === 'admin') {
            return false;
        }

        return $user->role === 'user' && $user->cafes()->exists();
    }

    public function update(User $user, Facility $facility): bool
    {
        if ($user->role === 'admin') {
            return false;
        }

        return $user->id === $facility->cafe->owner_id;
    }

    public function delete(User $user, Facility $facility): bool
    {
        if ($user->role === 'admin') {
            return false;
        }

        return $user->id === $facility->cafe->owner_id;
    }

    public function restore(User $user, Facility $facility): bool
    {
        if ($user->role === 'admin') {
            return false;
        }

        return $user->id === $facility->cafe->owner_id;
    }

    public function forceDelete(User $user, Facility $facility): bool
    {
        return false;
    }
}
