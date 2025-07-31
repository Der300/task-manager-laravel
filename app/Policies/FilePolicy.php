<?php

namespace App\Policies;

use App\Models\File;
use App\Models\Task;
use App\Models\User;

class FilePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function softDelete(User $user, File $file)
    {
        if ($user->hasRole('manager')) {
            return $file->task?->project?->assigned_to === $user->id;
        }
        if ($user->hasAnyRole(['leader', 'member'])) {
            return $file->task?->assigned_to === $user->id;
        }
        if ($user->hasRole('client')) {
            return false;
        }

        return true;
    }
}
