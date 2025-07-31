<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{

    private function isAdminOrSuperAdmin(User $currentUser): bool
    {
        return $currentUser->hasAnyRole(['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $currentUser, User $user): bool
    {
        if ($currentUser->hasRole('super-admin')) {
            return true;
        }

        if ($currentUser->hasRole('admin')) {
            if ($currentUser->id === $user->id) return true;

            if (!in_array($user->role, ['admin', 'super-admin'])) return true;

            return false;
        }

        return $currentUser->id === $user->id;
    }

    public function update(User $currentUser, User $user): bool
    {
        return $this->isAdminOrSuperAdmin($currentUser) || $currentUser->id === $user->id;
    }


    public function resetPassword(User $currentUser, User $user): bool
    {
        if ($this->isAdminOrSuperAdmin($currentUser) && $currentUser->id !== $user->id) return true;

        if ($currentUser->hasAnyRole(['manager', 'leader'])) {
            return $currentUser->department === $user->department && $currentUser->id !== $user->id;
        }
        return false;
    }
}
