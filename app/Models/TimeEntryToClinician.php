<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TimeEntryToClinician extends Model
{
    public $table = 'time_entry_to_clinician';
    
    protected $fillable = [
        'clinician_id',
        'time_entry_id',
    ];
    
    public static function linkFromRequest(Request $request, int $timeEntryId)
    {
        TimeEntryToClinician::whereTimeEntryId($timeEntryId)->delete();
        foreach($request->clinicians as $key => $val) {
            TimeEntryToClinician::create([
                'clinician_id' => $val,
                'time_entry_id' => $timeEntryId,
            ]);
        }
    }
}
