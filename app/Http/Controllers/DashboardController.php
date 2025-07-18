<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(DashboardService $dashboardService)
    {
        return view('dashboard', [
            'boxes' => $dashboardService->getDataOverviewBoxes(),

            'projectSumary' => $dashboardService->getDataProjectSummaryByStatus(),
            'projectAttention' => $dashboardService->getDataProjectNeedingAttention(),
            'tracking' => $dashboardService->getDataOverviewProjectTracking(),

            'taskSumary' => $dashboardService->getDataTaskSummaryByStatus(),
            'taskWordload' => $dashboardService->getDataTaskWorkLoad(),
            'taskStatus' => $dashboardService->getDataTaskStatusChange(),
            'taskOverdue' => $dashboardService->getDataTaskOverdue(),

            'comments' => $dashboardService->getDataComment(),
            'myTasks' => $dashboardService->getDataMyTask(),
        ]);
    }
}
