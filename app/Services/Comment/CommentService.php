<?php

namespace App\Services\Comment;

use App\Models\Comment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CommentService
{
    /**
     * Lấy comments gan nhat
     *
     * @return \Illuminate\Support\Collection của các stdClass object
     */
    public function getComments(): Collection
    {
        return Comment::with(['user', 'task']) // can thiet lap relationship ben comment model
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get();
    }
}
