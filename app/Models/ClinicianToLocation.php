<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicianToLocation extends Model
{
    public $table = 'clinician_to_location';
    
    protected $fillable = [
        'clinician_id',
        'location_id',
    ];
    
    public static function syncClinicianLocations($clinicianId, $locations)
    {
        ClinicianToLocation::whereClinicianId($clinicianId)->delete();
        foreach($locations as $key => $val) {
            ClinicianToLocation::create([
                'clinician_id' => $clinicianId,
                'location_id' => $val,
            ]);
        }
    }
}
