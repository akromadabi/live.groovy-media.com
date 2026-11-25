<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PerformanceService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PerformanceController extends Controller
{
    protected PerformanceService $performanceService;

    public function __construct(PerformanceService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    /**
     * Compute performance leaderboard and metrics data based on request filters
     */
    public function getPerformanceData(Request $request): array
    {
        return $this->performanceService->getPerformanceData($request);
    }

    /**
     * Display top performance leaderboard
     */
    public function index(Request $request)
    {
        $data = $this->performanceService->getPerformanceData($request);
        return view('admin.performance.index', $data);
    }

    /**
     * Display formal print/PDF view of the report
     */
    public function printReport(Request $request)
    {
        $data = $this->performanceService->getPerformanceData($request);
        return view('admin.performance.print', $data);
    }

    /**
     * Get user attendance details for performance drilldown modal
     */
    public function userDetail(User $user, Request $request)
    {
        return response()->json($this->performanceService->getUserDetail($user, $request, false));
    }

    /**
     * Export performance leaderboard to CSV
     */
    public function export(Request $request): StreamedResponse
    {
        $data = $this->performanceService->getPerformanceData($request);
        return $this->performanceService->exportCsv($data);
    }
}
