<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GibranDashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(GibranDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        // Check if admin is logged in
        if (!session()->has('admin_id')) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Get dashboard statistics from the unified backend
        $statisticsResult = $this->dashboardService->getStatistics();
        $statistics = $statisticsResult['success'] ? $statisticsResult['data'] : [];

        // Get news/berita bencana data
        $newsResult = $this->dashboardService->getBeritaBencana();
        $news = $newsResult['success'] ? $newsResult['data'] : [];

        // Get system overview
        $overviewResult = $this->dashboardService->getSystemOverview();
        $overview = $overviewResult['success'] ? $overviewResult['data'] : [];

        // Pass admin session data to view
        $adminData = [
            'admin_id' => session('admin_id'),
            'admin_name' => session('admin_name'),
            'admin_username' => session('admin_username')
        ];

        return view('dashboard', compact('statistics', 'news', 'overview', 'adminData'));
    }

    /**
     * API endpoint for dashboard statistics (AJAX calls)
     */
    public function getStatistics()
    {
        $result = $this->dashboardService->getStatistics();

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'data' => $result['data']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 500);
    }

    /**
     * API endpoint for system overview (AJAX calls)
     */
    public function getOverview()
    {
        $result = $this->dashboardService->getSystemOverview();

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'data' => $result['data']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 500);
    }
}
