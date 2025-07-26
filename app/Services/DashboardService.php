<?php

namespace App\Services;

use App\Models\User;
use App\Services\Comment\CommentService;
use App\Services\Project\ProjectService;
use App\Services\Status\StatusService;
use App\Services\Task\TaskService;
use App\Services\User\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class DashboardService
{
    protected $projectService;
    protected $taskService;
    protected $userService;
    protected $commentService;
    protected $statusService;
    protected Carbon $now;

    // __construct
    public function __construct(ProjectService $projectService, TaskService $taskService, CommentService $commentService, UserService $userService, StatusService $statusService)
    {
        $this->projectService = $projectService;
        $this->taskService = $taskService;
        $this->userService = $userService;
        $this->commentService = $commentService;
        $this->statusService = $statusService;
        $this->now = Carbon::now();
    }

    // main
    // {{-- thong ke tong project, task, nhan vien, khach hang --}}
    private function buildBox(string $label, string $icon, string $bg, int $count, string $routeName): array
    {
        return [
            'label' => $label,
            'icon' => $icon,
            'bg' => $bg,
            'count' => $count ?? 0,
            'link' => $routeName,
        ];
    }
    public function getDataOverviewBoxes(): array
    {
        return [
            $this->buildBox('Total Projects', 'fas fa-project-diagram', 'info', $this->projectService->countProjects(), 'projects'),
            $this->buildBox('Total Tasks', 'fas fa-tasks', 'success', $this->taskService->countTasks(), 'tasks'),
            $this->buildBox('Member', 'fas fa-users', 'warning', $this->userService->countUsers(['not_in' => ['role' => ['client']]]), 'users'),
            $this->buildBox('Customer', 'fas fa-users', 'primary', $this->userService->countUsers(['role' => 'client']), 'users')
        ];
    }

    // {{-- Thong ke project theo trang thai --}}
    public function getDataProjectSummaryByStatus(): array
    {
        $statuses = $this->statusService->getStatuses();

        return [
            'labels' => $statuses->pluck('name')->toArray(),
            'backgroundColor' => $statuses->pluck('color')->map(fn($c) => $c ?: sprintf('#%06X', mt_rand(0, 0xffffff)))->toArray(),
            'data' => $statuses->map(fn($s) => $this->projectService->countProjects(['status_id' => $s->id]) ?? 0)->toArray(),
        ];
    }

    // {{-- hien thi tien do project co chua cho tasks, tre dealine, ca tuan khong comment --}}
    public function getDataProjectNeedingAttention(): array
    {
        $result = [];
        $currentUser = Auth::user();

        if ($currentUser->hasRole('manager') || $currentUser->hasRole('leader')) {
            $projectIds = $currentUser->projects->pluck('id')->unique();
            $projects = $this->projectService->getActiveProjectsByIds($projectIds);
        } else {
            $projects = $this->projectService->getActiveProjects();
        }

        $projectHasTasks = $this->taskService->getAllProjectIdsWithTasks();
        $sevenDaysAgo = $this->now->copy()->subDays(7);

        foreach ($projects as $project) {
            $issues = [];
            if (!in_array($project->id, $projectHasTasks)) {
                $issues[] = [
                    'label' => 'No Tasks',
                    'color' => 'warning',
                ];
            } else {
                $hasInactiveTask = $this->taskService->hasTaskWithoutRecentComment($project->id, $sevenDaysAgo);
                if ($hasInactiveTask) {
                    $issues[] = [
                        'label' => 'No Activity 1w',
                        'color' => 'secondary',
                    ];
                }
            }

            if (Carbon::parse($project->due_date) < $this->now) {
                $issues[] = [
                    'label' => 'Overdue',
                    'color' => 'danger',
                ];
            }

            if (!empty($issues)) {
                $result[] = [
                    'id' => $project->id,
                    'project_name' => $project->name,
                    'manager' => $project->assignedUser?->name,
                    'due_date' => $project->due_date,
                    'issues' => $issues,
                ];
            }
        }
        return $result;
    }

    //  {{-- Project Tracking Overview --}}
    public function getDataOverviewProjectTracking(): array
    {
        // Initialize data arrays
        $result = [
            'monthYear' => $this->now->format('F Y'),
            'labels' => [],
            'labelInfo' => [],
            'totalTasks' => [],
            'completedTasks' => [],
            'workedDays' => [],
            'remainingDays' => [],
        ];

        $projects = $this->projectService->getActiveProjectsInMonth();
        $taskProjectIds = $this->taskService->getAllProjectIdsWithTasks();
        $doneStatusId = $this->statusService->getIdByCode('done');

        foreach ($projects as $project) {
            if (!in_array($project->id, $taskProjectIds)) {
                continue;
            }

            $start = Carbon::parse($project->start_date);
            $due = Carbon::parse($project->due_date);

            $result['labels'][] = Str::limit($project->name, 10);
            $result['labelInfo'][] = "$project->name – {$project->assignedUser?->name} - {$project->status?->name}";
            $result['totalTasks'][] = $this->taskService->countTasks(['project_id' => $project->id]) ?? 0;
            $result['completedTasks'][] = $this->taskService->countTasks(['project_id' => $project->id, 'status_id' => $doneStatusId]) ?? 0;

            $result['workedDays'][] = max(ceil($start->diffInDays($this->now, false)), 0);
            $result['remainingDays'][] = max(ceil($this->now->diffInDays($due, false)), 0);
        }

        return $result;
    }

    // {{-- Thong ke task theo trang thai --}}
    public function getDataTaskSummaryByStatus(): array
    {
        $statuses = $this->statusService->getStatuses();

        return [
            'labels' => $statuses->pluck('name')->toArray(),
            'backgroundColor' => $statuses->pluck('color')->map(fn($c) => $c ?: sprintf('#%06X', mt_rand(0, 0xffffff)))->toArray(),
            'data' => $statuses->map(fn($s) => $this->taskService->countTasks(['status_id' => $s->id]) ?? 0)->toArray(),
        ];
    }

    // {{-- Thong ke so luong task cua moi nhan vien --}}
    public function getDataTaskWorkLoad(): array
    {

        $currentUser = Auth::user();
        if ($currentUser->hasRole('manager') || $currentUser->hasRole('leader')) {
            $users = $this->userService->getUsers([
                'status' => 'active',
                'not_in' => ['department' => [config('departments.management')]],
                'department' => $currentUser->department,
            ]);
        } else {
            $users = $this->userService->getUsers([
                'status' => 'active',
                'not_in' => ['department' => [config('departments.management')]]
            ]);
        }

        $labels = $users->pluck('name')->toArray();
        $labelInfo = [];
        foreach ($users as $user) {
            $labelInfo[] = "$user->name - $user->department - $user->position";
        }
        $data = $users->map(fn($u) => $this->taskService->countTasks(['assigned_to' => $u->id]) ?? 0)->toArray();
        $heightCanvas = count($labels) * 30 ?? 300;

        return [
            'heightCanvas' => $heightCanvas,
            'data' => $data,
            'labels' => $labels,
            'labelInfo' => $labelInfo,
        ];
    }

    // {{-- Thong ke tasks co thay doi trang thai trong tuan --}}
    public function getDataTaskStatusChange(): array
    {
        $currentUser = Auth::user();
        if ($currentUser->hasRole('manager') || $currentUser->hasRole('leader')) {
            $projectIds = $currentUser->projects->pluck('id')->unique();
            $tasks = $this->taskService->getActiveTasksStatusChangedThisWeekInProjects($projectIds);
        } else {
            $tasks = $this->taskService->getActiveTasksStatusChangedThisWeek();
        }

        return $tasks->map(function ($task) {
            return [
                'task_name' => $task->name,
                'status_name' => $task->status?->name,
                'status_color' => $task->status?->color,
                'project_name' => $task->project?->name,
                'assignee' => $task->assignedUser?->name,
            ];
        })->toArray();
    }

    // {{-- Hien thi task qua han --}}
    public function getDataTaskOverdue(): array
    {
        $currentUser = Auth::user();
        if ($currentUser->hasRole('manager') || $currentUser->hasRole('leader')) {
            $projectIds = $currentUser->projects->pluck('id')->unique();
            $tasks = $this->taskService->getActiveTasksOverdueInProjects($projectIds);
        } else {
            $tasks = $this->taskService->getActiveTasksOverdue();
        }

        return $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'name' => $task->name,
                'due_date' => $task->due_date,
                'amount_day' => ceil(Carbon::parse($task->due_date)->diffInDays($this->now)),
                'assignee' => $task->assignedUser?->name,
            ];
        })->toArray();
    }

    // {{-- Hien thi my tasks  --}}
    public function getDataMyTask(User $currentUser, array $clientProjects = []): array
    {
        $isClient = $currentUser->hasRole('client');
        $result = [];

        if ($isClient) {
            foreach ($clientProjects as $project) {
                $tasks = $this->taskService->getActiveTasksWithProjectId($project['id']);

                foreach ($tasks as $task) {
                    $result[] = [
                        'id' => $task->id,
                        'project_name' => $task->project?->name,
                        'name' => $task->name,
                        'status_name' => $task->status?->name,
                        'status_color' => $task->status?->color,
                        'assignee' => $task->assignedUser?->name,
                        'due_date' => $task->due_date,
                    ];
                }
            }
        } else {
            $tasks = $this->taskService->getTaskWithUserId($currentUser->id);

            foreach ($tasks as $task) {
                $result[] = [
                    'id' => $task->id,
                    'project_name' => $task->project?->name,
                    'name' => $task->name,
                    'status_name' => $task->status?->name,
                    'status_color' => $task->status?->color,
                    'assignee' => $task->assignedUser?->name,
                    'due_date' => $task->due_date,
                    'updated_at' => $task->updated_at,
                ];
            }
        }
        return $result;
    }

    // {{-- Hien thi comments gan nhat --}}
    public function getDataComment(User $currentUser): array
    {
        $comments = $this->commentService->getRecentComments($currentUser);

        return $comments->map(function ($comment) {
            return [
                'body' => $comment->body,
                'updated_at' => $comment->updated_at,

                'user_name' => $comment->user->name ?? null,
                'user_email' => $comment->user->email ?? null,
                'user_image' => $comment->user->image ?? null,
                'user_position' => $comment->user->position ?? null,
                'user_department' => $comment->user->department ?? null,

                'task_id' => $comment->task->id,
                'task_name' => $comment->task->name,
                'project_id' => $comment->task->project?->id,
                'project_name' => $comment->task->project?->name,
            ];
        })->toArray();
    }

    //-------------------------------------------client function 
    // {{-- Hien thi project cua client  --}}
    public function getDataClientProject(string $clientId): array
    {
        $projects = $this->projectService->getProjects(['client_id' => $clientId]);
        $result = $projects->map(function ($project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'status_name' => $project->status?->name,
                'status_color' => $project->status?->color,
                'status_code' => $project->status?->code,
                'manager' => $project->assignedUser?->name,
                'due_date' => $project->due_date,
            ];
        })->toArray();

        return collect($result)->sortByDesc('status_code')->values()->toArray();
    }

    // {{-- Hien thi progress client tasks  --}}
    public function getDataClientProjectProgress(array $clientProjects): array
    {
        $result = [];
        $theme = [
            'open' => 'info',
            'in_progress' => 'warning',
            'in_review' => 'primary',
            'done' => 'success',
            'cancel' => 'danger',
            'pending' => 'secondary',
        ];

        foreach ($clientProjects as $project) {
            $tatusIdDone = $this->statusService->getIdByCode('done');
            $tatusIdCancel = $this->statusService->getIdByCode('cancel');
            $totalTask = $this->taskService->countTasks(['not_in' => ['status_id' => [$tatusIdCancel]], 'project_id' => $project['id']]);

            $taskDone = $this->taskService->countTasks(['status_id' => $tatusIdDone, 'project_id' => $project['id']]);
            $valuePercent = $totalTask > 0
                ? ceil((($taskDone) / $totalTask) * 100)
                : 0;
            $result[] = [
                'totalTask' => $totalTask,
                'taskActive' => $taskDone,
                'project_name' => $project['name'],
                'valuePercent' => $valuePercent,
                'theme' => $theme[$project['status_code']],
                'status_color' => $project['status_color'],
                'status_name' => $project['status_name'],
                'manager' => $project['manager'],
                'due_date' => $project['due_date'],
            ];
        }
        return $result;
    }
}
