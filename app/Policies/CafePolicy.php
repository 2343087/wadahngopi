<?php

namespace App\Policies;

use App\Models\Cafe;
use App\Models\User;

class CafePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Filtered by scope in Resource, so allow entry
    }

    public function view(User $user, Cafe $cafe): bool
    {
        return $user->role === 'admin' || $user->id === $cafe->owner_id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Cafe $cafe): bool
    {
        return $user->role === 'admin' || $user->id === $cafe->owner_id;
    }

    public function delete(User $user, Cafe $cafe): bool
    {
        return $user->role === 'admin';
    }

    public function restore(User $user, Cafe $cafe): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Cafe $cafe): bool
    {
        return $user->role === 'admin';
    }
}
