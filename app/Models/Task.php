<?php

namespace App\Models;

use App\Services\Task\TaskService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'task_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function issueType()
    {
        return $this->belongsTo(IssueType::class, 'issue_type_id');
    }

    // observer xóa file
    protected static function booted(): void
    {
        static::deleting(function (Task $task) {
            // Nếu là force delete
            if ($task->isForceDeleting()) {
                app(TaskService::class)->deleteTaskFilesPermanently($task);
            } else {
                app(TaskService::class)->moveTaskFilesToTrash($task);
            }
        });

        static::restoring(function (Task $task) {
            app(TaskService::class)->restoreTaskFilesFromTrash($task);
        });
    }
}
