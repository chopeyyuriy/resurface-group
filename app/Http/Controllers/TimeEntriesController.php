<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeEntryAddRequest;
use App\Http\Requests\TimeEntryEditRequest;
use App\Models\TimeEntry;
use App\Models\TimeEntryToClinician;
use Auth;

class TimeEntriesController extends Controller
{
    /**
     * This is the route to requesting time entries data.
     * 
     * @param int $id
     * @return type
     */
    public function jsonData(int $id)
    {
        $entry = TimeEntry::find($id);
        
        $entry->date = \Carbon\Carbon::parse($entry->date)->format('m/d/Y');
        $entry->time = TimeEntry::spentToTime($entry->spent);

        $ids = [];
        foreach($entry->clinicians as $row) {
            $ids[] = $row->id;
        }
        $entry->clinicianIds = $ids;
        
        return response()->json([
            'result' => 'success',
            'data' => $entry,
        ]);
    }
    
    /**
     * This is the route to create a time entry.
     * 
     * @param TimeEntryRequest $request
     * @return type
     */
    public function create(TimeEntryAddRequest $request)
    {
        $entry = TimeEntry::createFromRequest($request);
        TimeEntryToClinician::linkFromRequest($request, $entry->id);
        
        return response()->json([
            'result' => 'success',
            'data' => null
        ]);
    }
    
    /**
     * This is the route to update the time entry.
     * 
     * @param TimeEntryRequest $request
     * @return type
     */
    public function update(TimeEntryEditRequest $request)
    {
        $entry = TimeEntry::updateFromRequest($request);
        if (Auth::user()->hasRole('admin')) {
            TimeEntryToClinician::linkFromRequest($request, $entry->id);
        }
        
        return response()->json([
            'result' => 'success',
            'data' => null
        ]);
    }
    
    /**
     * This is the route to delete the time entry.
     * 
     * @param int $id
     * @return type
     */
    public function delete(int $id)
    {
        TimeEntryToClinician::whereTimeEntryId($id);
        TimeEntry::deleteById($id);
        
        return response()->json([
            'result' => 'success',
            'data' => null
        ]);                
    }
}
