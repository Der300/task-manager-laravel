<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // nếu user không có project nào thì vào project list được
        return !$user->hasRole('client') || $user->projects()->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $model): bool
    {
        if ($user->hasRole('client')) {
            // Client chỉ xem được nếu thuộc project
            return $user->id === $model->client_id;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $model): bool
    {
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }
        if ($user->hasRole('manager') && $user->id === $model->assigned_to) {
            return true;
        }

        return false;
    }


    /**
     * Determine whether the user soft delete the model.
     */
    public function softDelete(User $user, Project $model): bool
    {
        return $this->update($user, $model);
    }

    /**
     * Determine whether the user restore the model.
     */
    public function restore(User $user, Project $model): bool
    {
        return $this->update($user, $model);
    }
}
