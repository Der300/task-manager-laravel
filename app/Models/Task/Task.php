<?php

namespace App\Models\Task;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    // thiet lap lai duong dan file factory do Task trong App\Models\Task mac dinh factory lay duong dan Database\Factories\Task
    protected static function newFactory()
    {
        return TaskFactory::new();
    }
}
