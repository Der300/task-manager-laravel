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
        } elseif ($currentUser->hasRole('member')) {
            $taskIds = $taskService->getTaskWithUserId($currentUser->id)->pluck('id');

            $query->whereIn('task_id', $taskIds);
        } elseif ($currentUser->hasRole('client')) {
            $projectIds = $projectService->getProjects(['client_id' => $currentUser->id])->pluck('id');

            $taskIds = $taskService->getActiveTaskIdsWithArrayProjectId($projectIds);

            $query->whereIn('task_id', $taskIds);
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }

    /**
     * Lấy danh sách comment trong hệ thống hoặc theo filters cụ thể.
     *
     * @param array $filters VD: ['user_id' => 1] $field === 'not_in' value phải là mảng kiểu ['not_in' => ['key' => ['value1', 'value2']]]
     * @return \Illuminate\Support\Collection
     */
    public function getComments(array $filters = []): Collection
    {
        $query = Comment::query();

        foreach ($filters as $field => $value) {
            if ($field === 'not_in' && is_array($value)) {
                foreach ($value as $notInField => $notInValues) {
                    $query->whereNotIn($notInField, $notInValues);
                }
            } elseif (is_array($value) && !empty($value)) {
                $query->whereIn($field, $value);
            } elseif (!is_null($value)) {
                $query->where($field, $value);
            }
        }
        return $query->get();
    }

    /**
     * Lấy tổng số comment trong hệ thống hoặc theo filters cụ thể.
     *
     * @param array $filters VD: ['user_id' => 1]
     * @return int Tổng số project
     */
    public function countComments(array $filters = []): int
    {
        return $this->getComments($filters)->count();
    }

    public function getAllCommentWithTaskId(string $taskId): Collection
    {   
        return Comment::with('user:id,name,image,position,department,role')
        ->where('task_id', $taskId)
            ->orderBy('updated_at', 'desc')
            ->get();
    }
}
