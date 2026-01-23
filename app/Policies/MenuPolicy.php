<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;

class MenuPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin cannot see menus listing
        if ($user->role === 'admin') {
            return false;
        }

        return true;
    }

    public function view(User $user, Menu $menu): bool
    {
        // Admin cannot see menu details
        if ($user->role === 'admin') {
            return false;
        }

        return $user->id === $menu->cafe->owner_id;
    }

    public function create(User $user): bool
    {
        // Admin cannot create menus
        if ($user->role === 'admin') {
            return false;
        }

        return $user->role === 'user' && $user->cafes()->exists();
    }

    public function update(User $user, Menu $menu): bool
    {
        if ($user->role === 'admin') {
            return false;
        }

        return $user->id === $menu->cafe->owner_id;
    }

    public function delete(User $user, Menu $menu): bool
    {
        if ($user->role === 'admin') {
            return false;
        }

        return $user->id === $menu->cafe->owner_id;
    }

    public function restore(User $user, Menu $menu): bool
    {
        if ($user->role === 'admin') {
            return false;
        }

        return $user->id === $menu->cafe->owner_id;
    }

    public function forceDelete(User $user, Menu $menu): bool
    {
        return false;
    }
}
