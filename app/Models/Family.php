<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    protected $fillable = [
        'client_id',
        'title',
        'location',
        'status',
        'admission',
        'directory'
    ];

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function getAdmissionFormatAttribute()
    {
        return date('m/d/Y', strtotime($this->admission));
    }

    public function locationData()
    {
        //return $this->hasOne(Location::class, 'id', 'location');
        return $this->hasOne(Directories::class, 'id', 'location')->whereNull('parent_id');
    }

    public function mainPatient($field = null)
    {
        $client = $this->clients()->where('relationship_status', 1)->first();

        if(!empty($field)) {
            return data_get($client, $field);
        } else {
            return $client;
        }
    }

    public function numFamilyMembers()
    {
        return $this->clients()->where('relationship_status', '!=', 1)->count();
    }

    public function getAdmissionAttribute($value)
    {
        return date('m/d/Y', strtotime($value));
    }
}
