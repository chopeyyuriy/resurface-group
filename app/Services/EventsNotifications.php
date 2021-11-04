<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class EventsNotifications
{
    public function EventsNotifications()
    {
        $notifications = \App\Models\EventsNotifications::where('user_id', Auth::user()->userable_id)
            ->where('status', 'new')
            ->get();

        $data = [
            'events_notifications' => $notifications,
            'count_notifications' => count($notifications)
        ];

        return (object)$data;
    }
}