<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Clinician extends Model
{
    protected $fillable = [
        'type',
        //'location',
        'photo',
        'first_name',
        'last_name',
        'middle_name',
        'directory'
    ];

    public function user()
    {
        return $this->morphOne(User::class, 'userable');
    }

    public function phones()
    {
        return $this->morphMany(Phone::class, 'phoneable');
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'clinician_client', 'clinician_id', 'client_id');
    }
    
    public function locationIDs()
    {
        $list = ClinicianToLocation::whereClinicianId($this->id)->get();
        $res = [];
        foreach($list as $row) {
            $res[] = $row->location_id;
        }
        return $res;
    }
    
    public function locationNames()
    {
        $id = $this->id;
        $sql = "select d.title
                  from clinician_to_location c, directories d
                 where c.clinician_id = $id
                   and c.location_id = d.id
                order by d.title";
        $res = [];
        foreach(DB::select($sql) as $row) {
            $res[] = $row->title;
        }
        return implode(', ', $res);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function getNameAttribute()
    {
        $fName = data_get($this, 'first_name');
        $mName = data_get($this, 'middle_name');
        $lName = data_get($this, 'last_name');

        return sprintf('%s %s %s', $fName, $mName, $lName);
    }

    public static function getSortList()
    {
        return Clinician::orderBy('first_name')
            ->orderBy('middle_name')
            ->orderBy('last_name')
            ->get();
    }
    
    public static function getSortListWithAuth()
    {
        if (Auth::user()->hasRole('admin')) {
            return self::getSortList();
        } else {
            return Clinician::whereId(Auth::user()->userable_id)
                ->orderBy('first_name')
                ->orderBy('middle_name')
                ->orderBy('last_name')
                ->get();
        }
    }

    public static function isClinician($request)
    {
        if ($request->clinician_id) {

            $isClinician = Clinician::find($request->clinician_id);

            if (!$isClinician) {
                $clinicians = new Clinician();
                $clinicians->id = $request->clinician_id;
                $clinicians->type = 1;
                $clinicians->location = 14408;
                $clinicians->photo = '';
                $clinicians->photo = '';
                $clinicians->first_name = Auth::user()->name;
                $clinicians->last_name = Auth::user()->name;
                $clinicians->middle_name = 'mi';
                $clinicians->save();

            }
        }

        return true;
    }

    public static function getClinician($keyword, $limit = 30)
    {
        $clinician = Clinician::select(
            'id',
            DB::raw('CONCAT(first_name, " ", last_name) AS text'),
            DB::raw('CONCAT(\'/clinician-directory/form/\', "", id) AS url')
        )
            ->where('first_name', 'like', "%$keyword%")
            ->orWhere('last_name', 'like', "%$keyword%")
            ->take($limit)
            ->get()
            ->toArray();

        return $clinician;
    }

}
