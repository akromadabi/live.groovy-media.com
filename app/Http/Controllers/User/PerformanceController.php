<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PerformanceService;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    protected PerformanceService $performanceService;

    public function __construct(PerformanceService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    /**
     * Display top performance leaderboard for hosts / users
     */
    public function index(Request $request)
    {
        $data = $this->performanceService->getPerformanceData($request);

        $currentUser = auth()->user();
        $myPerformance = $data['leaderboard']->firstWhere('user_id', $currentUser->id);
        $myRank = $myPerformance ? $myPerformance['rank'] : null;
        $totalHosts = $data['leaderboard']->count();

        return view('user.performance.index', array_merge($data, compact('myPerformance', 'myRank', 'totalHosts')));
    }

    /**
     * Get user attendance details for performance drilldown modal (sensitive details hidden)
     */
    public function userDetail(User $user, Request $request)
    {
        return response()->json($this->performanceService->getUserDetail($user, $request, true));
    }
}
