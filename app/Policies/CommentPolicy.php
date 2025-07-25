<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): bool
    {
        if ($user->hasRole(['super-admin', 'admin'])) {
            return $user->id === $comment->user_id;
        }

        return true;
    }
}
