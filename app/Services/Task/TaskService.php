<?php

namespace App\Services\Task;

use App\Models\IssueType;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Services\Comment\CommentService;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskService
{
    protected $table = 'tasks';

    protected string $fileFolder = 'files/';
    protected string $fileFolderTrash = 'files/trash/';

    private function getNow(): Carbon
    {
        return Carbon::now();
    }

    private function getStartOfWeek(): Carbon
    {
        return $this->getNow()->copy()->startOfWeek();
    }
    private function getEndOfWeek(): Carbon
    {
        return $this->getNow()->copy()->endOfWeek();
    }
    /**
     * Lấy danh sách task trong hệ thống hoặc theo filters cụ thể.
     *
     * @param array $filters VD: ['task_id' => 1, 'status_id' => 23, 'issue_type_id' => 2]
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
        $startOfWeek = $this->getStartOfWeek();
        $endOfWeek = $this->getEndOfWeek();

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
        $startOfWeek = $this->getStartOfWeek();
        $endOfWeek = $this->getEndOfWeek();

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
        return $this->baseActiveTaskQuery()
            ->whereNotNull('due_date')
            ->where('due_date', '<=', $this->getNow())
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
        return $this->baseActiveTaskQuery()
            ->whereHas('project', function ($query) use ($projectIds) {
                $query->whereIn('id', $projectIds);
            })
            ->whereNotNull('due_date')
            ->where('due_date', '<=', $this->getNow())
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

    /**
     * Lấy tổng số task trong hệ thống theo bộ lọc.
     *
     * @param ?string $clientId nếu current user là client
     * @param array $filters filters
     * @return LengthAwarePaginator chứa tasks theo các điều kiện lọc tuỳ chọn.
     */
    public function getDataTaskTable(?string $clientId = null, array $filters = []): LengthAwarePaginator
    {
        $itemsPerPage = env('ITEM_PER_PAGE', 20);
        $user = Auth::user();
        $query = Task::query()->whereHas('project')->with([
            'status:id,name,color',
            'assignedUser:id,name',
            'project:id,name,client_id,assigned_to',
            'project.clientUser:id,name',
        ])
            ->leftJoin('users as assigned_users', 'tasks.assigned_to', '=', 'assigned_users.id')
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->select('tasks.*');

        // ----------- filters
        if (!empty($filters['search_task'])) {
            $value = strtolower($filters['search_task']);
            $query->where(function ($q) use ($value) {
                $q->whereRaw('LOWER(tasks.name) LIKE ?', ["%{$value}%"])
                    ->orWhereRaw('LOWER(tasks.description) LIKE ?', ["%{$value}%"]);
            });
        }

        if (!empty($filters['search_user'])) {
            $value = strtolower($filters['search_user']);
            $query->where(function ($q) use ($value) {
                $q->whereHas('assignedUser', function ($q2) use ($value) {
                    $q2->whereRaw('LOWER(assigned_users.name) LIKE ?', ["%{$value}%"]);
                });
            });
        }

        if (!empty($filters['status'])) {
            $query->whereHas('status', function ($q) use ($filters) {
                $q->where('id', $filters['status']);
            });
        }

        if (!empty($filters['time'])) {
            switch ($filters['time']) {
                case 'due_date_soon':
                    $query->orderBy('due_date', 'asc');
                    break;
                case 'updated_at_new':
                    $query->orderBy('updated_at', 'desc');
                    break;
            }
        }
        // ----------- -----------
        if ($clientId) {
            $query->whereHas('project', function ($q) use ($clientId) {
                $q->where('client_id', $clientId);
            });
        }

        // lọc các task của current user
        if ($user->hasAnyRole(['leader', 'member'])) {
            $query->orderByRaw("
                CASE 
                    WHEN tasks.assigned_to = ? THEN 0
                    WHEN assigned_users.department = ? THEN 1
                    ELSE 2
                END", [$user->id, $user->department]);
        }

        if ($user->hasRole('manager')) {
            $query->orderByRaw("
                CASE 
                    WHEN projects.assigned_to = ? THEN 0
                    ELSE 1
                END", [$user->id]);
        }

        return $query
            ->orderBy('status_id', 'asc')
            ->orderBy('due_date', 'asc')
            ->paginate($itemsPerPage);
    }

    /**
     * Lấy dữ liệu để điền form tạo hoặc edit task
     * @param ?Task $task tên task nếu có
     * @return void
     */
    public function getTaskToShowOrEdit(?Task $task = null, bool $isCreate = false): array
    {
        $statuses = Status::orderBy('id')->pluck('name', 'id');
        $issueTypes = IssueType::orderBy('name')->pluck('name', 'id');

        $createdUsers = User::whereIn('role', ['super-admin', 'admin', 'manager', 'leader'])->orderBy('role')->withTrashed()->pluck('name', 'id');
        $assignedUsers = User::whereIn('role', ['leader', 'member'])->orderBy('name')->withTrashed()->pluck('name', 'id');
        $projects = Project::withTrashed()->get(['id', 'name', 'assigned_to', 'client_id']);;
        return [
            'task' => $isCreate ? null : $task,
            'statuses' => $statuses,
            'issueTypes' => $issueTypes,
            'createdUsers' => $createdUsers,
            'assignedUsers' => $assignedUsers,
            'projects' => $projects,
            'isCreate' => $isCreate,

        ];
    }

    /**
     * Lấy tổng số task đã soft-delete
     * 
     * * @param ?string $assignedId nếu current user là assigned_to user
     * @return LengthAwarePaginator chứa task theo các điều kiện lọc tuỳ chọn.
     */
    public function getDataTaskRecycleTable(?string $assignedId = null): LengthAwarePaginator
    {
        $itemsPerPage = env('ITEM_PER_PAGE', 20);
        $query = Task::onlyTrashed()->with('status:id,name,color', 'assignedUser:id,name', 'project:id,name');

        if ($assignedId) {
            $query->where('assigned_to', $assignedId);
        }

        return $query->orderByDesc('deleted_at')->paginate($itemsPerPage);
    }

    /**
     * Kiểm tra xem task có dữ liệu liên quan không.
     * Trả về mảng dữ liệu liên quan (key => count) nếu có, hoặc mảng rỗng nếu không.
     */
    public function checkRelatedData(Task $task): array
    {
        $relatedData = [
            'tasks' => app(CommentService::class)->countComments(['task_id' => $task->id]),
            'files' => DB::table('files')->where('task_id', $task->id)->count(),
        ];

        // Lọc ra những dữ liệu liên quan có tồn tại
        return collect($relatedData)->filter(fn($count) => $count > 0)->toArray();
    }

    /**
     * Xóa task vĩnh viễn (force delete) nếu không có dữ liệu liên quan.
     * Trả về mảng ['success' => bool, 'message' => string].
     */
    public function forceDeleteProject(Task $task): array
    {
        $task = Task::withTrashed()->findOrFail($task->id);

        $relatedData = $this->checkRelatedData($task);

        if (!empty($relatedData)) {
            $details = collect($relatedData)->map(fn($count, $key) => "$key ($count)")->implode(', ');
            return [
                'success' => false,
                'message' => "Cannot delete this task. Related data found: $details.",
            ];
        }

        try {
            DB::beginTransaction();

            // Xoá task
            $task->forceDelete();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Task has been permanently deleted.',
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'An error occurred while deleting task.',
            ];
        }
    }
}
