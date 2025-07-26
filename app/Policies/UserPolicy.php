<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{

    private function isAdminOrSuperAdmin(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            if ($user->id === $model->id) return true;

            if (!in_array($model->role, ['admin', 'super-admin'])) return true;

            return false;
        }

        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $this->isAdminOrSuperAdmin($user) || $user->id === $model->id;
    }

}
