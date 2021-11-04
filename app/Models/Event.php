<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'clinician_id',
        'host_id',
        'type',
        'status',
        'subject',
        'location',
        'from',
        'to',
        'all_day',
        'date',
        'commentary',
        'notes',
    ];

    public function participant()
    {
        return $this->hasMany(EventParticipants::class, 'event_id', 'id');
    }

    public function host()
    {
        return $this->belongsTo(Clinician::class, 'clinician_id', 'id');
    }

    public static function saveEvent($request)
    {        
        $clinician_id = $request->host_id ? $request->host_id : Auth::user()->userable_id;
        $host_id = Auth::user()->hasRole('admin') ? $request->host_id : Auth::user()->userable_id;

        $event = new Event();
        $event->clinician_id = $clinician_id;
        $event->host_id = $host_id;
        $event->type = $request->type;
        $event->location = $request->location;
        $event->from = date("H:i", strtotime($request->from));
        $event->to = date("H:i", strtotime($request->to));
        $event->date = date('Y-m-d', strtotime($request->date));
        $event->commentary = $request->commentary;
        $event->notes = $request->notes;
        $event->subject = $request->subject;

        if ($event->save()) {

            self::createEventsNotifications($host_id, $event);

            foreach ($request->participants_id as $participant) {
                $ep = new EventParticipants();
                $ep->event_id = $event->id;
                $ep->participant_id = $participant;
                $ep->save();
            }
        }

        return true;
    }

    public static function editEvent($request)
    {
        $event = Event::find($request->event_id);
        $event->clinician_id = $request->host_id ? $request->host_id : Auth::user()->userable_id;
        $event->host_id = $request->host_id;
        $event->type = $request->type;
        $event->location = $request->location;
        $event->from = date("H:i", strtotime($request->from));
        $event->to = date("H:i", strtotime($request->to));
        $event->date = date('Y-m-d', strtotime($request->date));
        $event->commentary = $request->commentary;
        $event->notes = $request->notes;
        $event->subject = $request->subject;

        if ($event->save()) {
            $ep = EventParticipants::where('event_id', $event->id)->delete();

            foreach ($request->participants_id as $participant) {
                $ep = new EventParticipants();
                $ep->event_id = $event->id;
                $ep->participant_id = $participant;
                $ep->save();
            }
        }

        return true;

    }

    public static function mergeClinicians($participants_id)
    {
        $selectClinicians = Clinician::whereIn('id', $participants_id)->get();

        foreach ($selectClinicians as $clinician) {
            $clinician->selected = 'selected';
        }
        $allClinicians = Clinician::whereNotIn('id', $participants_id)->get();

        return array_merge($selectClinicians->toArray(), $allClinicians->toArray());
    }

    public static function getParticipant($event)
    {
        if (isset($event->participant)) {

            $participants_id = $event->participant->pluck('participant_id');
            $clinicians = self::mergeClinicians($participants_id);

        } else {

            $clinicians = Clinician::all();
        }

        return $clinicians;
    }

    public static function isAdmin()
    {
        return User::role('admin')->where('id', Auth::id())->first();
    }

    public static function getEvent($keyword, $limit, $cliniciansId = null)
    {
        $query = Event::query();

        $query->select(
            'id',
            DB::raw('subject AS text'),
            DB::raw('CONCAT(\'/calendar\') AS url')
        )->where('subject', 'like', "%$keyword%")
            ->take($limit);

        if ($cliniciansId) {
            $query->where('clinician_id', $cliniciansId);
        }

        $events = $query->get()->toArray();

        return $events;
    }

    public static function eventType($event)
    {
        if ($event->date && $event->id) {
            $eventType = 'old_event';
        } else {
            $eventType = 'new_event';
        }

        return $eventType;
    }

    public static function createEventsNotifications($host_id, $event)
    {
        if (Auth::user()->userable_id != $host_id){
            $notification = new EventsNotifications();
            $notification->admin_id = Auth::user()->userable_id;
            $notification->user_id = $host_id;
            $notification->event_id = $event->id;
            $notification->status = 'new';
            $notification->title = 'Notification from ' . Auth::user()->name;
            $notification->message = 'The ' . Auth::user()->name . ' has added to your calendar an event "'. $event->date . " - " . $event->subject .'"';
            $notification->save();
        }

        return true;
    }


}
