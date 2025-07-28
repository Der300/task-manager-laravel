<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use App\Services\Comment\CommentService;

class CommentPolicy
{

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can soft-delete the model.
     */
    public function softDelete(User $user, Comment $comment): bool
    {
        if ($user->hasAnyRole(['super-admin', 'admin','manager'])) {
            return true;
        }

        return $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Comment $comment): bool
    {
        return $this->softDelete($user, $comment);
    }
}
