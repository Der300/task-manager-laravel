<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // nếu client không có project nào thì không vào project list được
        $hasTasks = Task::whereHas('project', function ($q) use ($user) {
            $q->where('client_id', $user->id);
        })->exists();

        return !$user->hasRole('client') || $hasTasks;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $model): bool
    {
        if ($user->hasRole('client')) {
            // Client chỉ xem được nếu thuộc task
            return Task::whereHas('project', function ($q) use ($user) {
                $q->where('client_id', $user->id);
            })->exists();
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->hasRole('member')) {
            // member chỉ được edit task của chính mình
            return $user->id === $task->assigned_to;
        }

        return true;
    }

    /**
     * Determine whether the user soft delete the model.
     */
    public function softDelete(User $user, Task $model): bool
    {
        if ($user->hasAnyRole(['super-admin', 'admin','manager'])) {
            return true;
        }
        if ($user->hasRole('leader') && $user->id === $model->assigned_to) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user restore the model.
     */
    public function restore(User $user, Task $model): bool
    {
        return $this->softDelete($user, $model);
    }

    
}
