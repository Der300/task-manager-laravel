<?php

namespace App\Http\Controllers;

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
}
