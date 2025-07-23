<?php

namespace App\Services\Comment;

use App\Models\Comment;
use App\Models\User;
use App\Services\Project\ProjectService;
use App\Services\Task\TaskService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CommentService
{
    /**
     * Lấy comments gan nhat
     *
     * @return \Illuminate\Support\Collection của các stdClass object
     */
    public function getRecentComments(User $currentUser): Collection
    {
        $query = Comment::with('user', 'task');
        $projectService = new ProjectService();
        $taskService = new TaskService();
        if ($currentUser->hasRole('manager')) {
            $projectIds = $projectService->getProjectAssignedIdsWithUser($currentUser->id);
            
            $taskIds = $taskService->getActiveTaskIdsWithArrayProjectId($projectIds);

            $query->whereIn('task_id', $taskIds);
        }elseif($currentUser->hasRole('member')){
            $taskIds = $taskService->getTaskWithUserId($currentUser->id)->pluck('id');

            $query->whereIn('task_id', $taskIds);
        }elseif($currentUser->hasRole('client')){
            $projectIds = $projectService->getProjects(['client_id'=> $currentUser->id])->pluck('id');

            $taskIds = $taskService->getActiveTaskIdsWithArrayProjectId($projectIds);

            $query->whereIn('task_id', $taskIds);
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }
}