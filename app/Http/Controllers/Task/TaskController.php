<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
use App\Models\Status;
use App\Models\User;
use App\Notifications\TaskAssigned;
use App\Services\Comment\CommentService;
use App\Services\Task\TaskService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    use AuthorizesRequests;
    protected $taskService;
    protected $commentService;
    public function __construct(TaskService $taskService, CommentService $commentService)
    {
        $this->taskService = $taskService;
        $this->commentService = $commentService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $statuses = Status::orderBy('order', 'asc')->pluck('name', 'id');
        $filters = $request->only(['search_task', 'search_user', 'status', 'time']);

        if ($user->hasRole('client')) {
            // Kiểm tra client có task không
            $hasTasks = Task::whereHas('project', fn($q) => $q->where('client_id', $user->id))->exists();

            if (!$hasTasks) {
                abort(403, 'You do not have any tasks to view.');
            }

            $data = $this->taskService->getDataTaskTable(clientId: $user->id, filters: $filters);
        } else {
            $data = $this->taskService->getDataTaskTable(filters: $filters);
        }
        return view('tasks.index', compact('data', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->taskService->getTaskToShowOrEdit(isCreate: true);
        return view('tasks.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $task = Task::create($data);
            DB::commit();

            // gửi thông báo
            if ($data['assigned_to']) {
                User::find($data['assigned_to'])->notify(new TaskAssigned($task));
            }

            return redirect()->route('tasks.index')->with('success', 'Tasks created successfully!');
        } catch (Exception $e) {
            DB::rollback();

            return back()->with('error', 'Failed to create task. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        $data = $this->taskService->getTaskToShowOrEdit(task: $task, isCreate: false);
        $data['comments'] = $this->commentService->getAllCommentWithTaskId($task->id);
        return view('tasks.show', $data);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        $data = $request->validated();
        try {
            // Cập nhật user, trả về true/false
            $task->update($data);

            return back()->with('success', 'Task updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Task updated fail!');
        }
    }

    /**
     * Move to recycle the specified resource from storage.
     */
    public function softDelete(Task $task)
    {
        $this->authorize('softDelete', $task);
        try {
            $task->delete(); // Soft delete

            return redirect()->route('tasks.index')->with('success', 'Task moved to recycle successfully.');
        } catch (\Exception $e) {

            return redirect()->route('tasks.index')->with('error', 'Failed to move task to recycle.');
        }
    }

    /**
     * Display the recylce.
     */
    public function recycle()
    {
        $user = Auth::user();

        if ($user->hasRole('leader')) {
            $data = $this->taskService->getDataTaskRecycleTable($user->id);
        } else {
            $data = $this->taskService->getDataTaskRecycleTable();
        }
        return view('tasks.recycle', ['data' => $data]);
    }

    /**
     * restore from recylce.
     */
    public function restore(Task $task)
    {
        $this->authorize('restore', $task);
        try {
            if ($task->trashed()) {
                $task->restore();
                return redirect()->route('tasks.index')->with('success', 'Task restored successfully.');
            }
            return back()->with('error', 'Task is not deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore task.');
        }
    }

    /**
     * Delete task.
     */
    public function forceDelete(Task $task)
    {
        $result = $this->taskService->forceDeleteProject($task);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }
        return back()->with('error', $result['message']);
    }
}
