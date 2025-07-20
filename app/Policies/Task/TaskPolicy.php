<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->hasRole(['member'])) {
            // member chỉ được edit task của chính mình
            return $user->id === $task->assigned_to;
        }

        return true;
    }
}
