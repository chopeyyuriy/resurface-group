<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventParticipants extends Model
{
    protected $table = 'events_participants';

    protected $fillable = [
        'event_id',
        'participant_id',
    ];

    public $timestamps = false;
}
