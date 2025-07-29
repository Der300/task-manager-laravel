<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
use App\Models\Status;
use App\Models\User;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskSoftDeleted;
use App\Notifications\TaskUpdated;
use App\Services\Comment\CommentService;
use App\Services\File\FileService;
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
    protected $fileService;
    public function __construct(TaskService $taskService, CommentService $commentService, FileService $fileService)
    {
        $this->taskService = $taskService;
        $this->commentService = $commentService;
        $this->fileService = $fileService;
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
        $user = Auth::user();
        try {
            DB::beginTransaction();
            $task = Task::create($data);
            DB::commit();

            // gửi thông báo
            if ($user->id !== $task->assigned_to) {
                $task->assignedUser?->notify(new TaskAssigned($task, $user->name));
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
        $data['files'] = $this->fileService->getFilesByTaskId($task->id);
        return view('tasks.show', $data);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        $data = $request->validated();
        $user = Auth::user();
        try {

            $task->update($data);
            if ($user->id !== $task->assigned_to) {
                $task->assignedUser?->notify(new TaskUpdated($task, $user->name));
            }
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
        $user = Auth::user();
        try {
            $task->delete(); // Soft delete
            if ($user->id !== $task->assigned_to) {
                $task->assignedUser?->notify(new TaskSoftDeleted($task, $user->name));
            }
            $this->fileService->moveTaskFilesToTrash($task->id);
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
        $user = Auth::user();
        try {
            if ($task->trashed()) {
                $task->restore();
                if ($user->id !== $task->assigned_to) {
                    $task->assignedUser?->notify(new TaskSoftDeleted($task, $user->name));
                }
                $this->fileService->restoreTaskFilesFromTrash($task->id);
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
        $user = Auth::user();
        if ($result['success']) {
            if ($user->id !== $task->assigned_to) {
                $task->assignedUser?->notify(new TaskSoftDeleted($task, $user->name));
            }
            $this->fileService->deleteTaskFilesPermanently($task->id);
            return back()->with('success', $result['message']);
        }
        return back()->with('error', $result['message']);
    }
}
