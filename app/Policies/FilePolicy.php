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

    public function softDelete(User $user, File $file)
    {
        if ($user->hasAnyRole(['leader', 'member'])) {
            return $file->task?->assigned_to === $user->id;
        }
        if ($user->hasRole('client')) {
            return false;
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
