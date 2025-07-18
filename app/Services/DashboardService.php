<?php

namespace App\Services;

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
            $this->buildBox('Total Projects', 'fas fa-project-diagram', 'info', $this->projectService->countProjects(), 'project'),
            $this->buildBox('Total Tasks', 'fas fa-tasks', 'success', $this->taskService->countTasks(), 'task'),
            $this->buildBox('Member', 'fas fa-users', 'warning', $this->userService->countUsers(['not_in' => ['role' => ['client']]]), 'user'),
            $this->buildBox('Customer', 'fas fa-users', 'primary', $this->userService->countUsers(['role' => 'client']), 'user')
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
        $projects = $this->projectService->getActiveProjects();
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
                $manager = $this->userService->getUsers(['id' => $project->assigned_to])->first()->name;
                $result[] = [
                    'project_name' => $project->name,
                    'manager' => $manager,
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
        $users = $this->userService->getUsers([
            'status' => 'active',
            'not_in' => ['department' => [config('departments.management'), config('departments.project_mamagement')]]
        ]);

        $labels = $users->pluck('name')->toArray();
        $data = $users->map(fn($u) => $this->taskService->countTasks(['assigned_to' => $u->id]) ?? 0)->toArray();
        $heightCanvas = count($labels) * 30 ?? 300;

        return [
            'heightCanvas' => $heightCanvas,
            'data' => $data,
            'labels' => $labels,
        ];
    }

    // {{-- Thong ke tasks co thay doi trang thai trong tuan --}}
    public function getDataTaskStatusChange(): array
    {
        $tasks = $this->taskService->getActiveTasksStatusChangedThisWeek();
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
        $tasks = $this->taskService->getActiveTasksOverdue();
        return $tasks->map(function ($task) {
            return [
                'name' => $task->name,
                'due_date' => $task->due_date,
                'amount_day' => ceil(Carbon::parse($task->due_date)->diffInDays($this->now)),
                'assignee' => $task->assignedUser?->name,
            ];
        })->toArray();
    }

    // {{-- Hien thi comments gan nhat --}}
    public function getDataComment(): array
    {
        $comments = $this->commentService->getComments();

        return $comments->map(function ($comment) {
            return [
                'body' => $comment->body,
                'updated_at' => $comment->updated_at,

                'user_name' => $comment->user->name ?? null,
                'user_email' => $comment->user->email ?? null,
                'user_image' => $comment->user->image ?? null,
                'user_position' => $comment->user->position ?? null,
                'user_department' => $comment->user->department ?? null,

                'task_name' => $comment->task->name,
            ];
        })->toArray();
    }

    // {{-- Hien thi my tasks  --}}
    public function getDataMyTask(): array
    {
        $user_id = Auth::user()->id;
        $tasks = $this->taskService->getTaskWithUserId($user_id);

        return $tasks->map(function ($task) {
            return [
                'name' => $task->name,
                'status_name' => $task->status?->name,
                'status_color' =>  $task->status?->color,
                'updated_at' => $task->updated_at,
            ];
        })->toArray();
    }
}
