<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminDashboardService;

class DashboardController extends Controller
{
    public function __construct(private readonly AdminDashboardService $dashboardService) {}

    public function index()
    {
        return view('admin.pages.dashboard.index', [
            'summary' => $this->dashboardService->summary(),
            'latestPengajuan' => $this->dashboardService->latestPengajuan(),
            'monthlyStats' => $this->dashboardService->monthlyStats(),
        ]);
    }
}
