<?php

namespace App\Services\Task;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TaskService
{
    protected $table = 'tasks';

    /**
     * Lấy danh sách task trong hệ thống hoặc theo filters cụ thể.
     *
     * @param array $filters VD: ['project_id' => 1, 'status_id' => 23, 'issue_type_id' => 2]
     * $field === 'not_in' value phải là mảng kiểu ['not_in' => ['key' => ['value1', 'value2']]]
     * @return \Illuminate\Support\Collection
     */
    public function getTasks(array $filters = []): Collection
    {
        $query = DB::table($this->table);
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
     * Lấy tổng số task trong hệ thống hoặc theo filters cụ thể.
     *
     * @param array $filters VD: ['project_id' => 1, 'status_id' => 23, 'issue_type_id' => 2]
     * @return int Tổng số task
     */
    public function countTasks(array $filters = []): int
    {
        return $this->getTasks($filters)->count();
    }

    /**
     * Lấy toàn bộ project_id trong hệ thống để kiểm tra xem project nào chưa có task.
     *
     * @return array array id projects
     */
    public function getAllProjectIdsWithTasks(): array
    {
        return DB::table($this->table)
            ->distinct()
            ->pluck('project_id')
            ->toArray();
    }

    /**
     * Kiểm tra project đã có tasks hay chưa
     *
     * @return bool true => project_id không có task nào.
     */
    public function hasTaskWithoutRecentComment(int $projectId, Carbon $sevenDaysAgo): bool
    {
        return Task::with('project')
            ->whereDoesntHave('comments', function ($q) use ($sevenDaysAgo) {
                $q->where('created_at', '>=', $sevenDaysAgo);
            })->exists();
    }

    /**
     * Trả về query builder cho task active (status != done, cancel).
     *
     * @return \Illuminate\Database\Query\Builder
     */
    private function baseActiveTaskQuery(): Builder
    {
        return Task::with([
            'status:id,name,color',
            'assignedUser:id,name',
            'project'
        ])
            ->whereHas('status', fn($s) => $s->whereNotIn('code', ['done', 'cancel']));
    }

    /**
     * Lấy tasks co trang thai thai doi trong tuan đang active(status khác done, cancel)
     *
     * @return \Illuminate\Support\Collection của các stdClass object
     */
    public function getActiveTasksStatusChangedThisWeek(): Collection
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return $this->baseActiveTaskQuery()
            ->whereBetween('updated_at', [$startOfWeek, $endOfWeek])
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get();
    }

    /**
     * Lấy tasks co trang thai thai doi trong tuan đang active(status khác done, cancel) trong projects
     *
     * @return \Illuminate\Support\Collection của các stdClass object
     */
    public function getActiveTasksStatusChangedThisWeekInProjects(Collection $projectIds): Collection
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return $this->baseActiveTaskQuery()
            ->whereHas('project', function ($query) use ($projectIds) {
                $query->whereIn('id', $projectIds);
            })
            ->whereBetween('updated_at', [$startOfWeek, $endOfWeek])
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get();
    }

    /**
     * Lấy tasks đang active(status khác done, cancel) co project id
     *
     * @param string $projectId id project
     * @return \Illuminate\Support\Collection tasks
     */
    public function getActiveTasksWithProjectId(string $projectId): Collection
    {
        return $this->baseActiveTaskQuery()
            ->where('project_id', $projectId)
            ->limit(50)
            ->get();
    }

    /**
     * Lấy toàn bộ tasks co project id
     *
     * @param string $projectId id project
     * @return \Illuminate\Support\Collection tasks
     */
    public function getAllTasksWithProjectId(string $projectId): Collection
    {
        return Task::with([
            'status:id,name,color',
            'assignedUser:id,name',
            'project'
        ])
            ->where('project_id', $projectId)
            ->limit(50)
            ->get();
    }

    /**
     * Lấy tasks đang active(status khác done, cancel) co project id []
     *
     * @return \Illuminate\Support\Collection tasks
     */
    public function getActiveTaskIdsWithArrayProjectId(Collection $projectIds): Collection
    {
        return $this->baseActiveTaskQuery()
            ->whereIn('project_id', $projectIds)
            ->pluck('id');
    }

    /**
     * Lấy tasks overdue đang active(status khác done, cancel)
     *
     * @return \Illuminate\Support\Collection tasks
     */
    public function getActiveTasksOverdue(): Collection
    {
        $now = Carbon::now();

        return $this->baseActiveTaskQuery()
            ->whereNotNull('due_date')
            ->where('due_date', '<=', $now)
            ->orderBy('due_date', 'asc')
            ->limit(50)
            ->get();
    }

    /**
     * Lấy tasks overdue đang active(status khác done, cancel) trong projects
     *
     * @return \Illuminate\Support\Collection tasks
     */
    public function getActiveTasksOverdueInProjects(Collection $projectIds): Collection
    {
        $now = Carbon::now();

        return $this->baseActiveTaskQuery()
            ->whereHas('project', function ($query) use ($projectIds) {
                $query->whereIn('id', $projectIds);
            })
            ->whereNotNull('due_date')
            ->where('due_date', '<=', $now)
            ->orderBy('due_date', 'asc')
            ->limit(50)
            ->get();
    }

    /**
     * Lấy toan bo tasks co user_id = $user_id
     *
     * @return \Illuminate\Support\Collection tasks
     */
    public function getTaskWithUserId(string $user_id): Collection
    {
        return Task::with(['status:id,name,color'])
            ->where('assigned_to', '=', $user_id)
            ->get();
    }
}
