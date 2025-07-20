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
}
