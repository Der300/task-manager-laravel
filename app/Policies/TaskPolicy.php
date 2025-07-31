<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        if ($user->hasRole('client')) {
            // Client chỉ xem được nếu thuộc task
            return $task->project?->client_id === $user->id;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->hasRole('manager')) {
            return $user->id === $task->project?->assigned_to;
        }

        if ($user->hasAnyRole(['member', 'leader'])) {
            // member chỉ được edit task của chính mình
            return $user->id === $task->assigned_to;
        }
        if ($user->hasRole('client')) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user soft delete the model.
     */
    public function softDelete(User $user, Task $task): bool
    {
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }
        if ($user->hasRole('manager')) {
            return $user->id === $task->project?->assigned_to;
        }

        if ($user->hasRole('leader')) {
            return $user->id === $task->assigned_to;
        }

        return false;
    }

    /**
     * Determine whether the user restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return $this->softDelete($user, $task);
    }

    public function upload(User $user, Task $task)
    {
        if ($user->hasRole('manager')) {
            return $task->project?->assigned_to === $user->id;
        }
        if ($user->hasAnyRole(['leader', 'member'])) {
            return $task->assigned_to === $user->id;
        }
        if ($user->hasRole('client')) {
            return false;
        }

        return true;
    }
}
