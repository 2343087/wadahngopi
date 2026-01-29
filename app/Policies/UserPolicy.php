<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'developer';
    }

    public function view(User $user, User $model): bool
    {
        return $user->role === 'developer';
    }

    public function create(User $user): bool
    {
        return $user->role === 'developer';
    }

    public function update(User $user, User $model): bool
    {
        return $user->role === 'developer';
    }

    public function delete(User $user, User $model): bool
    {
        return $user->role === 'developer';
    }

    public function restore(User $user, User $model): bool
    {
        return $user->role === 'developer';
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->role === 'developer';
    }
}
