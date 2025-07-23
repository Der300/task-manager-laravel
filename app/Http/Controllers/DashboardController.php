<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(DashboardService $dashboardService)
    {
        $user = Auth::user();
        $data = [
            'boxes' => $dashboardService->getDataOverviewBoxes(),
            'comments' => $dashboardService->getDataComment($user),
        ];

        if (!$user->hasRole('client')) {
            $data['projectSumary'] = $dashboardService->getDataProjectSummaryByStatus();
            $data['projectAttention'] = $dashboardService->getDataProjectNeedingAttention();
            $data['tracking'] = $dashboardService->getDataOverviewProjectTracking();

            $data['taskSumary'] = $dashboardService->getDataTaskSummaryByStatus();
            $data['taskWorkload'] = $dashboardService->getDataTaskWorkLoad();
            $data['taskStatus'] = $dashboardService->getDataTaskStatusChange();
            $data['taskOverdue'] = $dashboardService->getDataTaskOverdue();
            $data['myTasks'] = $dashboardService->getDataMyTask($user);
        } else {
            $clientProjects = $dashboardService->getDataClientProject($user->id);
            $data['clientProjecs'] = $clientProjects;
            $data['clientProgress'] = $dashboardService->getDataClientProjectProgress($clientProjects);
            $data['myTasks'] = $dashboardService->getDataMyTask($user, $clientProjects);
        }

        return view('dashboard', $data);
    }

    public function returnJsonFromSearch(Request $request)
    {
        $q = $request->input('search');
        $user = Auth::user();
        $users = collect();
        $projects = collect();
        $tasks = collect();

        if ($user->hasRole('client')) {
            $projects = Project::where('name', 'like', "%{$q}%")
                ->whereHas('clientUser', function ($query) use ($user) {
                    $query->where('id', $user->id);
                })
                ->select('id', 'name')->limit(5)->get();

            $tasks = Task::where('name', 'like', "%{$q}%")
                ->where(function ($query) use ($user) {
                    $query->whereHas('project.clientUser', function ($q) use ($user) {
                        $q->where('id', $user->id);
                    });
                })
                ->select('id', 'name')->limit(5)->get();
        } else {
            $users = User::where('name', 'like', "%{$q}%")->select('id', 'name')->limit(5)->get();
            $projects = Project::where('name', 'like', "%{$q}%")->select('id', 'name')->limit(5)->get();
            $tasks = Task::where('name', 'like', "%{$q}%")->select('id', 'name')->limit(5)->get();
        }
        return response()->json([
            'users' => $users,
            'projects' => $projects,
            'tasks' => $tasks,
        ]);
    }
}
