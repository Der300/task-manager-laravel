<?php

namespace App\Services\Comment;

use App\Models\Comment;
use App\Models\User;
use App\Services\Project\ProjectService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CommentService
{
    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Lấy comments gần nhất.
     */
    public function getRecentComments(string $role, User $user): Collection
    {
        $query = Comment::with(['user', 'task']);

        if ($role === 'manager') {
            $query->whereHas('task.project', fn($q) => $q->where('assigned_to', $user->id));
        } elseif ($role === 'member') {
            $query->whereHas('task', fn($q) => $q->where('assigned_to', $user->id));
        } elseif ($role === 'client') {
            $query->whereHas('task.project', fn($q) => $q->where('client_id', $user->id));
        }

        return $query->latest('updated_at')->limit(50)->get();
    }


    /**
     * Lấy danh sách comment có phân trang.
     */
    public function getCommentsWithPagination(?string $userId = null): LengthAwarePaginator
    {
        $perPage = (int) env('ITEM_PER_PAGE', 20);
        $query = Comment::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }
        return $query->latest('deleted_at')->paginate($perPage);
    }

    /**
     * Đếm số lượng comment theo filter.
     */
    public function countComments(array $filters = []): int
    {
        return $this->applyFilters(Comment::query(), $filters)->count();
    }

    /**
     * Lấy tất cả comment của 1 task.
     */
    public function getAllCommentWithTaskId(string $taskId): Collection
    {
        return Comment::with('user:id,name,image,position,department,role')
            ->where('task_id', $taskId)
            ->latest('updated_at')
            ->get();
    }

    /**
     * Lấy comment đã xóa mềm theo user.
     */
    public function getDataCommentRecycleTable(?string $userId = null): LengthAwarePaginator
    {
        $query = Comment::onlyTrashed();
        $perPage = (int) env('ITEM_PER_PAGE', 5);
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->latest('deleted_at')
            ->paginate($perPage);
    }

    /**
     * Gắn filter vào query.
     */
    protected function applyFilters($query, array $filters)
    {
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

        return $query;
    }
}
