<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Clinician;
use App\Models\Event;
use App\Models\User;
use App\Models\EventsNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Log;

class CalendarController extends Controller
{
    public function index(Request $request, $user_id = null)
    {
        $user_id = $user_id ? $user_id : Auth::user()->userable_id;

        EventsNotifications::changeNotificationStatus($request);

        $isAdmin = Event::isAdmin();

        $clinicians = Clinician::all();
        return view('calendar', compact('isAdmin', 'user_id', 'clinicians'));
    }

    public function create(EventRequest $request)
    {
        if ($request->event_id) {
            Event::editEvent($request);
        } else {
            Event::saveEvent($request);
        }

        return redirect(route('calendar', $request->host_id));

    }

    public function viewEvents(Request $request, $user_id)
    {        
        $dateFrom = date('Y-m-d', strtotime($request->start));
        $dateTo = date('Y-m-d', strtotime($request->end));

        $events = Event::select(
            'id',
            DB::raw('CONCAT(DATE_FORMAT(events.date, "%m/%d/%Y"), " - ", events.subject) AS title'),
            'date as start',
            'commentary as description',
            'type as event_type'
        );
        $sql = '(exists(select p.* from events_participants p
                         where p.event_id = events.id 
                           and p.participant_id = '.(int)$user_id.') 
                     or events.clinician_id = '.(int)$user_id.')';
        $events->whereRaw($sql);
        //$events->where('clinician_id', $user_id);
        
        $events->whereBetween('date', [$dateFrom, $dateTo]);

        return response()->json($events->get());
    }

    public function eventModal(Request $request)
    {
        $event = Event::query();

        if ($request->date) {
            $event->where('date', $request->date);
        }

        if ($request->event_id) {
            $event->where('id', $request->event_id);
        }

        $event = $event->first();

        if ($event) {

            $clinicians = Event::getParticipant($event);

        } else {

            $event = new Event();
            $event->date = $request->date == 'undefined' ? '' : $request->date;

            $clinicians = Clinician::all();
        }

        $cliniciansHosts = Clinician::all();

        return response()->json(
            [
                'modal' => view('_event_modal', [
                    'event_type' => Event::eventType($event),
                    'event' => $event,
                    'clinicians_hosts' => $cliniciansHosts,
                    'clinicians' => $clinicians,
                    'admin' => Event::isAdmin() or ''
                ])->render()
            ]);

    }

    public function remove($event_id)
    {
        $event = Event::find($event_id);
        if ($event) {
            $event->delete();
            EventsNotifications::whereEventId($event_id)->delete();
        }

        return redirect()->back();
    }

    public function isEvent()
    {
        if (Event::isAdmin()){
            $status = 'admin';
        }else {
            $status = 'clinicians';
        }

        return response()->json(['status' => $status]);
    }

}
