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
        return in_array($user->role, ['developer', 'admin']);
    }

    public function view(User $user, Facility $facility): bool
    {
        return $user->role === 'developer' || ($user->role === 'admin' && $user->id === $facility->cafe->owner_id);
    }

    public function create(User $user): bool
    {
        return $user->role === 'developer' || ($user->role === 'admin' && $user->cafes()->exists());
    }

    public function update(User $user, Facility $facility): bool
    {
        return $user->role === 'developer' || ($user->role === 'admin' && $user->id === $facility->cafe->owner_id);
    }

    public function delete(User $user, Facility $facility): bool
    {
        return $user->role === 'developer' || ($user->role === 'admin' && $user->id === $facility->cafe->owner_id);
    }

    public function restore(User $user, Facility $facility): bool
    {
        return $user->role === 'developer';
    }

    public function forceDelete(User $user, Facility $facility): bool
    {
        return $user->role === 'developer';
    }
}
