<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Auth;

class DashboardController extends Controller
{
    private $_dashboardService;
    
    public function __construct(DashboardService $dashboardService) 
    {
        parent::__construct();
        
        $this->_dashboardService = $dashboardService;
    }
    
    public function index()
    {   
        $user = Auth::user();
        /*if (!$user->hasRole('admin')) {
            return redirect(route('erm'));
        }*/
        
        $clinician = $user->userable;
        
        return view('index', [
            'clinician' => $clinician,
            'time_report' => $this->_dashboardService->weeklyTimeReport($clinician->id),
        ]);
    }
    
    public function dashJson(string $now)
    {
        $user = Auth::user();
        /*if (!$user->hasRole('admin')) {
            return abort(404);
        }*/
        
        $clinician = $user->userable;
        $events = $this->_dashboardService->actualEventsList($now, $clinician->id);
        
        return response()->json([
            'table' => view('index-events', ['events' => $events])->render(),
            'data' => [
                'count' => count($events),
                'next' => $this->_dashboardService->nextEvent($events),
            ],
        ]);
    }
    
    public function eventJson(int $id)
    {
        return $this->_dashboardService->eventJson($id);
    }
}
