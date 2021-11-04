<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Helpers\DateFormat;

class TimeEntry extends Model
{
    const ACTIVITY_TYPES = [
        1 => 'Travel',
        2 => 'Session',
        3 => 'Virtual Session'
    ];
    
    protected $fillable = [
        'date', 
        'spent', 
        'client_id', 
        'activity_type', 
        'notes'
    ];
    
    public function clinicians()
    {
        return $this->belongsToMany(Clinician::class, 'time_entry_to_clinician');
    }
    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    public static function createFromRequest(Request $request)
    {   
        return self::create([
            'date' => DateFormat::setter($request->date),
            'spent' => self::timeToSpent($request->time),
            'client_id' => $request->client,
            'activity_type' => $request->activity_type,
            'notes' => $request->notes,
        ]);
    }
    
    public static function updateFromRequest(Request $request)
    {
        $item = TimeEntry::findOrFail($request->id);
        
        $item->update([
            'date' => DateFormat::setter($request->date),
            'spent' => self::timeToSpent($request->time),
            'client_id' => $request->client,
            'activity_type' => $request->activity_type,
            'notes' => $request->notes,
        ]);
        
        return $item;
    }
    
    public static function deleteById(int $id)
    {
        $item = TimeEntry::findOrFail($id);
        
        $item->delete();
    }
    
    public static function timeToSpent($time)
    {
        $a = explode(':', $time);
        
        return $a[0] * 60 + $a[1];
    }
    
    public static function spentToTime($spent)
    {
        return sprintf("%'.02d:%'.02d", intdiv($spent, 60), $spent % 60);
    }
}
