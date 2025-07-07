<?php

namespace App\Models\Comment;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;
    // thiet lap lai duong dan file factory do Comment trong App\Models\Comment mac dinh factory lay duong dan Database\Factories\Comment
    protected static function newFactory()
    {
        return CommentFactory::new();
    }
}
