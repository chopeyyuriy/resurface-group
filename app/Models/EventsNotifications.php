<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EventsNotifications extends Model
{
    protected $table = 'events_notifications';

    protected $fillable = [
        'admin_id',
        'user_id',
        'event_id',
        'status',
        'title',
        'message'
    ];

    public static function changeNotificationStatus($request)
    {
        if ($request->eid) {

            $notification = EventsNotifications::where('event_id', $request->eid)
                ->where('user_id', Auth::user()->userable_id)
                ->first();

            if ($notification->status == 'new') {
                $notification->update(['status' => 'read']);
                Session::flash('event_id', $request->eid);
            }

        }

        return true;
    }


}
