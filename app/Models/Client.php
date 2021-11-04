<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
    protected $fillable = [
        'family_id',
        'status',
        'marital_status',
        'photo',
        'first_name',
        'last_name',
        'middle_name',
        'relationship_status',
        'date_birth',
        'admission_date',
        'gender',
        'race',
        'referred_name',
        'referred_company',
        'referred_phone',
        'referred_email',
        'address',
        'city',
        'state',
        'zipcode',
        'email',
        'directory'
    ];

    public function phones()
    {
        return $this->morphMany(Phone::class, 'phoneable');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function clinician()
    {
        return $this->belongsToMany(Clinician::class, 'clinician_client', 'client_id', 'clinician_id');
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

    public function checkClinician($id)
    {
        if ($this->clinician->where('id', $id)->first()) {
            return true;
        }

        return false;
    }

    public function mainPatient($field = null)
    {
        $client = $this->family->clients()->where('relationship_status', 1)->first();

        if (!empty($field)) {
            return data_get($client, $field);
        } else {
            return $client;
        }
    }

    public function numFamilyMembers()
    {
        return $this->family->clients()->where('relationship_status', '!=', 1)->count();
    }

    public function familyMembers()
    {
        return $this->family->clients()->orderBy('relationship_status')->get();
    }

    public function getDateBirthAttribute($value)
    {
        return date('m/d/Y', strtotime($value));
    }

    public function getAdmissionDateAttribute($value)
    {
        return date('m/d/Y', strtotime($value));
    }

    public static function getSortList()
    {
        return Client::where('first_name', '>', '')
            ->orderBy('first_name')
            ->orderBy('middle_name')
            ->orderBy('last_name')
            ->get();
    }

    public static function getClients($keyword, $limit = 30, $cliniciansId = null)
    {

        $query = Client::query();

        $query->select(
            'id',
            DB::raw('CONCAT(first_name, " ", last_name) AS text'),
            DB::raw('CONCAT(\'/client/\', "", id) AS url')
        )->where('first_name', 'like', "%$keyword%")
            ->orWhere('last_name', 'like', "%$keyword%")
            ->take(5);

        $clients = $query->get()->toArray();

        return $clients;
    }

}
