<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Auth;

class TimeReportingController extends Controller 
{
    /**
     * This is the route of the index page.
     * @return type
     */
    public function index()
    {
        return view('time-reporting');
    }
    
    /**
     * This is the route to requesting table data.
     * 
     * @param Request $request
     * @return type
     */
    public function data(Request $request)
    {             
        $dateStart = \Carbon\Carbon::parse($request->dateStart)->startOfDay();
        $dateEnd = \Carbon\Carbon::parse($request->dateEnd)->endOfDay();
        $clinician = $request->clinician;
        $client = $request->client;
        $activityType = $request->activityType;
        
        $entries = TimeEntry::with('clinicians')
            ->with('client')
            ->whereBetween('date', [$dateStart, $dateEnd])
            ->when(!Auth::user()->hasRole('admin'), function ($query) use ($clinician) {
                $sql = "select *
                          from time_entry_to_clinician 
                         where time_entry_to_clinician.time_entry_id = time_entries.id
                           and time_entry_to_clinician.clinician_id = ".Auth::user()->userable_id;
                $query->whereRaw('exists ('.$sql.')');
                
                //$query->whereClinicianId(Auth::user()->userable_id);
            })
            ->when($clinician, function ($query) use ($clinician) {
                $ids = array_values($clinician);
                $sql = "select *
                          from time_entry_to_clinician 
                         where time_entry_to_clinician.time_entry_id = time_entries.id
                           and time_entry_to_clinician.clinician_id in (".implode(',', $ids).")";
                $query->whereRaw('exists ('.$sql.')');
            })
            ->when($client > 0, function ($query) use ($client) {
                $query->whereClientId($client);
            })
            ->when($activityType > 0, function ($query) use ($activityType) {
                $query->whereActivityType($activityType);
            })
            ->get();
        
        return datatables()->of($entries)
            ->editColumn('date', function ($entry) {
                return \Carbon\Carbon::parse($entry->date)->format('m/d/Y');
            })
            ->editColumn('clinician', function ($entry) {
                $clinicians = [];
                foreach($entry->clinicians as $row) {
                    $clinicians[] = $row->getNameAttribute();
                }
                return implode(', ', $clinicians);
            })
            ->editColumn('client', function ($entry) {
                return $entry->client->getNameAttribute();
            })
            ->editColumn('activity_type', function ($entry) {
                return $entry::ACTIVITY_TYPES[$entry->activity_type];
            })
            ->addColumn('time', function ($entry) {
                return TimeEntry::spentToTime($entry->spent);
            })
            ->addColumn('action', function ($entry) {
                return '<ul class="list-inline font-size-20 contact-links mb-0">
                    <li class="list-inline-item pe-2"><a href="#" data-id="'.$entry->id.'" title="Edit" class="time-entry-edit"><i class="mdi mdi-circle-edit-outline"></i></a></li>
                    <li class="list-inline-item px-2"><a href="#" data-id="'.$entry->id.'" title="Delete" class="time-entry-del"><i class="mdi mdi-close-circle-outline"></i></a></li>
                </ul>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
