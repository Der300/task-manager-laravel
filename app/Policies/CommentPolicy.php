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
        return $user->id === $comment->user_id || $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can soft-delete the model.
     */
    public function softDelete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->hasAnyRole(['super-admin', 'admin', 'manager']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Comment $comment): bool
    {
        return $this->softDelete($user, $comment);
    }

    public function updateOrSoftDelete(User $user, Comment $comment): bool
    {
        return $this->update($user, $comment) || $this->softDelete($user, $comment);
    }
}
