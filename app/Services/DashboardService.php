<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Clinician;
use DB;

class DashboardService 
{
    public function actualEventsList(string $now, int $clinicianId)
    {
        $now_date = \Carbon\Carbon::parse($now)->startOfDay();
        $now_time = \Carbon\Carbon::parse($now);
        
        $sql = "select a.*
                  from (select e.*, CONCAT(e.date, ' ', e.to) datetime
                          from events e
                         where (e.clinician_id = $clinicianId
                             or exists (select * 
                                          from events_participants p 
                                         where p.event_id = e.id
                                           and p.participant_id = $clinicianId))
                           and e.date >= :now_date) a
                 where a.datetime >= :now_time
                order by a.datetime";
        
        $res = DB::select($sql, [
            'now_date' => $now_date,
            'now_time' => $now_time
        ]);
        
        return $res;
    }
    
    public function nextEvent($events)
    {
        if (count($events)) {
            return (object)[
                'id' => $events[0]->id,
                'text' => $events[0]->subject.' &middot '.
                          \Carbon\Carbon::parse($events[0]->date)->format('l, F d').' &middot '.
                          \Carbon\Carbon::parse($events[0]->from)->format('g:i A')
            ];
        }
        return false;
    }
    
    public function eventJson(int $id)
    {
        $types = [
            1 => 'Business', 
            2 => 'Session',
        ];
        
        $event = Event::findOrFail($id);
        $event->type_name = $types[$event->type];
        $event->host_name = $event->host->getNameAttribute();
        $event->from_text = \Carbon\Carbon::parse($event->from)->format('g:i A');
        $event->to_text = \Carbon\Carbon::parse($event->to)->format('g:i A');
        
        $participants = Clinician::join('events_participants', function ($join) {
                $join->on('events_participants.participant_id', 'clinicians.id');
            })
            ->where('events_participants.event_id', $id)
            ->select(['clinicians.*'])
            ->get();
        
        foreach($participants as $row) {
            $row->name = $row->getNameAttribute();
        }
        
        return response()->json([
            'event' => $event,
            'participants' => $participants,
        ]);
    }
    
    public function weeklyTimeReport(int $id)
    {
        $sql = "select DATE(t.date) date, sum(t.spent) spent
                  from time_entries t
                 where DATE(t.date) between :start and :end
                   and exists (select *
                                 from time_entry_to_clinician tc
                                where tc.time_entry_id = t.id
                                  and tc.clinician_id = $id)
                group by date
                order by t.date";
        
        $data = DB::select($sql, [
            'start' => \Carbon\Carbon::now()->startOfWeek()->startOfDay(),
            'end' => \Carbon\Carbon::now()->endOfWeek()->endOfDay(),
        ]);
        
        $res = [];
        foreach($data as $row) {
            $day = \Carbon\Carbon::parse($row->date)->dayOfWeek;
            if ($day == 0) $day = 7;
            $res[$day] = $row;
        }
        
        return $res;
    }
}
