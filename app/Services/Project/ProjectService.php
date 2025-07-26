<?php

namespace App\Services\Project;

use App\Models\IssueType;
use App\Models\Project;
use App\Models\Status;
use App\Models\User;
use App\Services\Task\TaskService;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    /**
     * Lấy danh sách project trong hệ thống hoặc theo filters cụ thể.
     *
     * @param array $filters VD: ['client_id' => 1, 'status_id' => 23, 'issue_type_id' => 2] $field === 'not_in' value phải là mảng kiểu ['not_in' => ['key' => ['value1', 'value2']]]
     * @return \Illuminate\Support\Collection
     */
    public function getProjects(array $filters = []): Collection
    {
        $query = Project::query();

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
     * Lấy tổng số project trong hệ thống hoặc theo filters cụ thể.
     *
     * @param array $filters VD: ['client_id' => 1, 'status_id' => 23, 'issue_type_id' => 2]
     * @return int Tổng số project
     */
    public function countProjects(array $filters = []): int
    {
        return $this->getProjects($filters)->count();
    }

    /**
     * Trả về query builder cho project active (status != done, cancel).
     *
     * @return \Illuminate\Database\Query\Builder
     */
    private function baseActiveProjectQuery(): Builder
    {
        return Project::with([
            'status:id,name,color,code',
            'assignedUser:id,name',
        ])
            ->whereHas(
                'status',
                fn($s) => $s->whereNotIn('code', ['done', 'cancel'])
            );
    }

    /**
     * Lấy projects trong hệ thống đang active(status khác done, cancel)
     *
     * @return \Illuminate\Support\Collection của các stdClass object
     */
    public function getActiveProjects(): Collection
    {
        return $this->baseActiveProjectQuery()->get();
    }

    /**
     * Lấy projects của 1 client trong hệ thống đang active(status khác done, cancel)
     *
     * @return \Illuminate\Support\Collection của các stdClass object
     */
    public function getActiveProjectsWithClientId(string $clientId): Collection
    {
        return $this->baseActiveProjectQuery()
            ->where('client_id', $clientId)
            ->get();
    }

    /**
     * Lấy projects của 1 manager trong hệ thống đang active(status khác done, cancel)
     *
     * @return \Illuminate\Support\Collection
     */
    public function getActiveProjectsWithManagerId(string $clientId): Collection
    {
        return $this->baseActiveProjectQuery()
            ->where('assigned_to', $clientId)
            ->get();
    }

    /**
     * Lấy projects trong hệ thống đang active(status khác done, cancel) theo tháng hiện tại
     *
     * @return \Illuminate\Support\Collection của các stdClass object
     */
    public function getActiveProjectsInMonth(): Collection
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return $this->baseActiveProjectQuery()
            ->whereDate('start_date', '<=', $startOfMonth)
            ->whereDate('due_date', '>=', $endOfMonth)
            ->get();
    }

    /**
     * Lấy id projects theo assigned_to trong hệ thống
     *
     * @return \Illuminate\Support\Collection của các stdClass object
     */
    public function getProjectAssignedIdsWithUser(string $userId): Collection
    {
        return Project::where('assigned_to', $userId)->pluck('id');
    }

    /**
     * Lấy projects theo id trong hệ thống
     *
     * @return \Illuminate\Support\Collection của các stdClass object
     */
    public function getActiveProjectsByIds(Collection $ids): Collection
    {
        return Project::whereIn('id', $ids)->get();
    }


    /**
     * Lấy tổng số project trong hệ thống theo bộ lọc.
     *
     * @param ?string $assignedId nếu current user là assigned_to user
     * @param ?string $clientId nếu current user là client
     * @param array $filters filters
     * @return LengthAwarePaginator chứa projects theo các điều kiện lọc tuỳ chọn.
     */
    public function getDataProjectTable(?string $assignedId = null, ?string $clientId = null, array $filters = []): LengthAwarePaginator
    {
        $itemsPerPage = env('ITEM_PER_PAGE', 20);

        $query = Project::query()->with(['status:id,name,color', 'assignedUser', 'clientUser']);

        // ----------- filters
        if (!empty($filters['search_project'])) {
            $value = strtolower($filters['search_project']);
            $query->where(function ($q) use ($value) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$value}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$value}%"]);
            });
        }

        if (!empty($filters['search_user'])) {
            $value = strtolower($filters['search_user']);
            $query->where(function ($q) use ($value) {
                $q
                    ->whereHas('assignedUser', function ($q2) use ($value) {
                        $q2->whereRaw('LOWER(name) LIKE ?', ["%{$value}%"]);
                    })
                    ->orWhereHas('clientUser', function ($q2) use ($value) {
                        $q2->whereRaw('LOWER(name) LIKE ?', ["%{$value}%"]);
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
        if ($assignedId) {
            $query->orderByRaw("CASE WHEN assigned_to = ? THEN 0 ELSE 1 END", [$assignedId]);
        }
        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        return $query
            ->orderBy('due_date', 'asc')
            ->orderBy('status_id', 'asc')
            ->paginate($itemsPerPage);
    }

    /**
     * Lấy tổng số project đã soft-delete
     * 
     * * @param ?string $assignedId nếu current user là assigned_to user
     * @return LengthAwarePaginator chứa project theo các điều kiện lọc tuỳ chọn.
     */
    public function getDataProjectRecycleTable(?string $assignedId = null): LengthAwarePaginator
    {
        $itemsPerPage = env('ITEM_PER_PAGE', 20);
        $query = Project::onlyTrashed();

        if ($assignedId) {
            $query->where('assigned_to', $assignedId);
        }

        return $query->orderByDesc('deleted_at')->paginate($itemsPerPage);
    }

    /**
     * Kiểm tra xem project có dữ liệu liên quan không.
     * Trả về mảng dữ liệu liên quan (key => count) nếu có, hoặc mảng rỗng nếu không.
     */
    public function checkRelatedData(Project $project): array
    {
        $relatedData = [
            'tasks' => app(TaskService::class)->countTasks(['project_id' => $project->id]),
        ];

        // Lọc ra những dữ liệu liên quan có tồn tại
        return collect($relatedData)->filter(fn($count) => $count > 0)->toArray();
    }

    /**
     * Xóa project vĩnh viễn (force delete) nếu không có dữ liệu liên quan.
     * Trả về mảng ['success' => bool, 'message' => string].
     */
    public function forceDeleteProject(Project $project): array
    {
        $project = Project::withTrashed()->findOrFail($project->id);

        $relatedData = $this->checkRelatedData($project);

        if (!empty($relatedData)) {
            $details = collect($relatedData)->map(fn($count, $key) => "$key ($count)")->implode(', ');
            return [
                'success' => false,
                'message' => "Cannot delete this project. Related data found: $details.",
            ];
        }

        try {
            DB::beginTransaction();

            // Xoá project
            $project->forceDelete();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Project has been permanently deleted.',
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'An error occurred while deleting project.',
            ];
        }
    }


    /**
     * Lấy dữ liệu để điền form tạo hoặc edit project
     * @param ?Project $project tên project nếu có
     * @return void
     */
    public function getProjectToShowOrEdit(?Project $project = null, bool $isCreate = false): array
    {
        $statuses = Status::orderBy('id')->pluck('name', 'id');
        $issueTypes = IssueType::orderBy('name')->pluck('name', 'id');

        $createdUsers = User::whereIn('role',['super-admin','admin','manager'])->orderBy('role')->withTrashed()->pluck('name', 'id');
        $assignedUsers = User::where('role',['manager'])->orderBy('name')->withTrashed()->pluck('name', 'id');
        $clients = User::where('role','client')->orderBy('created_at')->withTrashed()->pluck('name', 'id');

        return [
            'project' => $isCreate ? null : $project,
            'statuses' => $statuses,
            'issueTypes' => $issueTypes,
            'createdUsers' => $createdUsers,
            'assignedUsers' => $assignedUsers,
            'clients' => $clients,
            'isCreate' => $isCreate,

        ];
    }
}
