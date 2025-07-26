<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Status;
use App\Services\Project\ProjectService;
use App\Services\Task\TaskService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Str;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    protected $projectService;
    protected $taskService;
    public function __construct(ProjectService $projectService, TaskService $taskService)
    {
        $this->projectService = $projectService;
        $this->taskService = $taskService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Project::class);
        $user = Auth::user();

        $statuses = Status::orderBy('order', 'asc')->pluck('name', 'id');
        $filters = $request->only(['search_project', 'search_user', 'status', 'time']);

        if ($user->hasRole('client')) {
            $data = $this->projectService->getDataProjectTable(clientId: $user->id, filters: $filters);
        } elseif ($user->hasRole('manager')) {
            $data = $this->projectService->getDataProjectTable(assignedId: $user->id, filters: $filters);
        } else {
            $data = $this->projectService->getDataProjectTable(filters: $filters);
        }

        return view('projects.index', compact('data', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->projectService->getProjectToShowOrEdit(isCreate: true);
        return view('projects.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();

            Project::create($data);


            DB::commit();

            return redirect()->route('projects.index')->with('success', 'Projects created successfully!');
        } catch (Exception $e) {
            DB::rollback();

            return back()->with('error', 'Failed to create project. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $data = $this->projectService->getProjectToShowOrEdit($project);

        $data['projectTasks'] = $this->taskService->getAllTasksWithProjectId($project->id);

        return view('projects.show', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);
        $data = $request->validated();

        try {
            // Cập nhật user, trả về true/false
            $project->update($data);

            return back()->with('success', 'Project updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Project updated fail!');
        }
    }

    /**
     * Display the recylce.
     */
    public function recycle()
    {
        $user = Auth::user();

        if ($user->hasRole('manager')) {
            $data = $this->projectService->getDataProjectRecycleTable($user->id);
        } else {
            $data = $this->projectService->getDataProjectRecycleTable();
        }
        return view('projects.recycle', ['data' => $data]);
    }

    /**
     * Move to recylce.
     */
    public function softDelete(Project $project)
    {
        $this->authorize('softDelete', $project);
        try {
            $project->delete(); // Soft delete
            return redirect()->route('projects.index')->with('success', 'Project moved to recycle successfully.');
        } catch (\Exception $e) {
            return redirect()->route('projects.index')->with('error', 'Failed to move project to recycle.');
        }
    }

    /**
     * restore from recylce.
     */
    public function restore(Project $project)
    {
        $this->authorize('restore', $project);
        try {
            if ($project->trashed()) {
                $project->restore();
                return redirect()->route('projects.index')->with('success', 'Project restored successfully.');
            }
            return back()->with('error', 'Project is not deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore project.');
        }
    }

    /**
     * Delete project.
     */
    public function forceDelete(Project $project)
    {
        $result = $this->projectService->forceDeleteProject($project);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }
        return back()->with('error', $result['message']);
    }

    /**
     * Create slug.
     */
    public function makeSlug(Request $request)
    {
        $slug = Str::slug($request->slug);
        $result = $this->projectService->countProjects(['slug' => $slug]);
        // neu ton tai slug roi thi them ma uniqid de khong trung nhau
        if ($result) {
            $slug .= '-' . uniqid();
        }
        return response()->json(['slug' => $slug]);
    }
}
