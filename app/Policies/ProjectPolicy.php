<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        if ($user->hasRole('client')) {
            // Client chỉ xem được nếu thuộc project
            return $user->id === $project->client_id;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }
        if ($user->hasRole('manager')) {
            return $user->id === $project->assigned_to;
        }

        return false;
    }


    /**
     * Determine whether the user soft delete the model.
     */
    public function softDelete(User $user, Project $project): bool
    {
        return $this->update($user, $project);
    }

    /**
     * Determine whether the user restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return $this->update($user, $project);
    }
}
