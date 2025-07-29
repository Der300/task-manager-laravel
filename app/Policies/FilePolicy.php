<?php

namespace App\Policies;

use App\Models\File;
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

    public function delete(User $user, File $file)
    {
        if ($user->hasAnyRole(['leader', 'member'])) {
            return $file->task_id === $file->task?->assigned_to;
        }
        if ($user->hasRole('client')) {
            return $file->task_id === $file->task?->project?->client_id;
        }

        return true;
    }

     public function forceDelete(User $user, File $file)
    {
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return true;
        }
        return false;
    }
}
